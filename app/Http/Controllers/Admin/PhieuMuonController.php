<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhieuMuonRequest;
use App\Models\PhieuMuon;
use App\Models\PhieuPhat;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PhieuMuonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $keyword = $request->string('q')->trim()->toString();
        if (class_exists(\Normalizer::class)) {
            $keyword = \Normalizer::normalize($keyword, \Normalizer::FORM_C) ?: $keyword;
        }
        $trangThai = $request->string('trang_thai')->toString();

        $phieuMuons = $this->filteredQuery($keyword, $trangThai)
            ->orderByDesc('ngay_muon')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.phieu-muons.index', compact('phieuMuons', 'keyword', 'trangThai'));
    }

    public function export(Request $request): StreamedResponse
    {
        $keyword = $request->string('q')->trim()->toString();
        $trangThai = $request->string('trang_thai')->toString();
        $fileName = 'bao-cao-phieu-muon-'.today()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($keyword, $trangThai): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Mã phiếu',
                'Độc giả',
                'Email',
                'Sách',
                'Ngày mượn',
                'Hạn trả',
                'Ngày trả thực tế',
                'Trạng thái',
                'Ghi chú',
            ], ',', '"', '\\', "\r\n");

            $this->filteredQuery($keyword, $trangThai)
                ->orderBy('id')
                ->lazyById()
                ->each(function (PhieuMuon $phieuMuon) use ($output): void {
                    fputcsv($output, [
                        '#'.$phieuMuon->id,
                        $this->safeCsvValue($phieuMuon->user->name),
                        $this->safeCsvValue($phieuMuon->user->email),
                        $this->safeCsvValue($phieuMuon->sach->ten_sach),
                        $phieuMuon->ngay_muon->format('d/m/Y'),
                        $phieuMuon->ngay_tra_du_kien->format('d/m/Y'),
                        $phieuMuon->ngay_tra_thuc_te?->format('d/m/Y') ?? '',
                        match ($phieuMuon->trangThaiHienThi()) {
                            'da_tra' => 'Đã trả',
                            'qua_han' => 'Quá hạn',
                            default => 'Đang mượn',
                        },
                        $this->safeCsvValue($phieuMuon->ghi_chu ?? ''),
                    ], ',', '"', '\\', "\r\n");
                });

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::query()->where('role', 'user')->orderBy('name')->get();
        $saches = Sach::query()
            ->withCount([
                'phieuMuons as dang_muon_count' => fn ($query) => $query->whereIn('trang_thai', ['dang_muon', 'qua_han']),
            ])
            ->orderBy('ten_sach')
            ->get();

        return view('admin.phieu-muons.create', compact('users', 'saches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PhieuMuonRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $phieuMuon = DB::transaction(function () use ($data): PhieuMuon {
            $user = User::query()->lockForUpdate()->findOrFail($data['user_id']);
            $lyLich = $user->danhGiaLyLich();

            if (! $lyLich['co_the_muon']) {
                throw ValidationException::withMessages([
                    'user_id' => $lyLich['ly_do_khong_the_muon'],
                ]);
            }

            $sach = Sach::query()->lockForUpdate()->findOrFail($data['sach_id']);
            $dangMuon = $sach->phieuMuonsDangMuon()->count();

            if ($dangMuon >= $sach->so_luong) {
                throw ValidationException::withMessages([
                    'sach_id' => 'Sách này hiện đã được mượn hết.',
                ]);
            }

            return PhieuMuon::query()->create([
                'user_id' => $data['user_id'],
                'sach_id' => $data['sach_id'],
                'ngay_muon' => $data['ngay_muon'],
                'ngay_tra_du_kien' => $data['ngay_tra_du_kien'],
                'ngay_tra_thuc_te' => null,
                'trang_thai' => 'dang_muon',
                'ghi_chu' => $data['ghi_chu'] ?? null,
            ]);
        });

        return redirect()->route('admin.phieu-muons.show', $phieuMuon)
            ->with('success', 'Đã lập phiếu mượn thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PhieuMuon $phieuMuon): View
    {
        $phieuMuon->load(['user', 'sach.theLoai', 'sach.tacGias']);

        return view('admin.phieu-muons.show', compact('phieuMuon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhieuMuon $phieuMuon): View
    {
        $users = User::query()->where('role', 'user')->orderBy('name')->get();
        $saches = Sach::query()
            ->withCount([
                'phieuMuons as dang_muon_count' => fn ($query) => $query->whereIn('trang_thai', ['dang_muon', 'qua_han']),
            ])
            ->orderBy('ten_sach')
            ->get();

        return view('admin.phieu-muons.edit', compact('phieuMuon', 'users', 'saches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PhieuMuonRequest $request, PhieuMuon $phieuMuon): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $phieuMuon): void {
            $sach = Sach::query()->lockForUpdate()->findOrFail($data['sach_id']);
            $trangThai = $data['trang_thai'] ?? $phieuMuon->trang_thai;

            if ($trangThai !== 'da_tra') {
                $dangMuon = $sach->phieuMuonsDangMuon()
                    ->where('phieu_muons.id', '!=', $phieuMuon->id)
                    ->count();

                if ($dangMuon >= $sach->so_luong) {
                    throw ValidationException::withMessages([
                        'sach_id' => 'Sách này hiện đã được mượn hết.',
                    ]);
                }

                $trangThai = Carbon::parse($data['ngay_tra_du_kien'])->isBefore(today())
                    ? 'qua_han'
                    : 'dang_muon';
                $data['ngay_tra_thuc_te'] = null;
            } else {
                $data['ngay_tra_thuc_te'] = $data['ngay_tra_thuc_te'] ?? today()->toDateString();
            }

            $data['trang_thai'] = $trangThai;
            $phieuMuon->update($data);

            if ($trangThai === 'da_tra' && ! empty($data['ngay_tra_thuc_te'])) {
                $ngayTraThucTe = Carbon::parse($data['ngay_tra_thuc_te']);
                if ($phieuMuon->ngay_tra_du_kien && $ngayTraThucTe->isAfter($phieuMuon->ngay_tra_du_kien)) {
                    $phieuPhat = $phieuMuon->kiemTraVaTaoPhieuPhat($ngayTraThucTe);
                    if ($phieuPhat && $phieuPhat->so_ngay_qua_han > PhieuPhat::MOC_NGAY_CAM_MUON) {
                        $phieuMuon->user->update([
                            'khoa_muon_sach' => true,
                            'ly_do_khoa' => "Tự động cấm mượn: Trả sách trễ {$phieuPhat->so_ngay_qua_han} ngày (vượt quá 20 ngày quy định).",
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.phieu-muons.show', $phieuMuon)
            ->with('success', 'Đã cập nhật phiếu mượn thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PhieuMuon $phieuMuon): RedirectResponse
    {
        $phieuMuon->delete();

        return redirect()->route('admin.phieu-muons.index')
            ->with('success', 'Đã xóa phiếu mượn thành công.');
    }

    public function traSach(PhieuMuon $phieuMuon): RedirectResponse
    {
        if ($phieuMuon->trang_thai === 'da_tra') {
            return back()->with('info', 'Phiếu mượn này đã được trả trước đó.');
        }

        $today = today();
        $isQuaHan = $phieuMuon->ngay_tra_du_kien && Carbon::parse($phieuMuon->ngay_tra_du_kien)->isBefore($today);

        $phieuMuon->update([
            'trang_thai' => 'da_tra',
            'ngay_tra_thuc_te' => $today->toDateString(),
        ]);

        $message = 'Đã xác nhận trả sách thành công.';

        if ($isQuaHan) {
            $phieuPhat = $phieuMuon->kiemTraVaTaoPhieuPhat(Carbon::parse($today));
            if ($phieuPhat) {
                $tienPhatFmt = number_format($phieuPhat->tong_tien_phat, 0, ',', '.').' VNĐ';
                $message .= " Sách trả chậm {$phieuPhat->so_ngay_qua_han} ngày. Đã phát sinh phiếu phạt: {$tienPhatFmt}.";

                if ($phieuPhat->so_ngay_qua_han > PhieuPhat::MOC_NGAY_CAM_MUON) {
                    $phieuMuon->user->update([
                        'khoa_muon_sach' => true,
                        'ly_do_khoa' => "Tự động cấm mượn: Trả sách trễ {$phieuPhat->so_ngay_qua_han} ngày (vượt quá 20 ngày quy định).",
                    ]);
                    $message .= ' Độc giả đã bị chuyển sang diện CẤM MƯỢN do trả trễ quá 20 ngày.';
                }
            }
        }

        return back()->with('success', $message);
    }

    private function filteredQuery(string $keyword, string $trangThai): Builder
    {
        if (class_exists(\Normalizer::class)) {
            $keyword = \Normalizer::normalize($keyword, \Normalizer::FORM_C) ?: $keyword;
        }

        return PhieuMuon::query()
            ->with(['user', 'sach'])
            ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                $cleanId = ltrim($keyword, '#');
                $isNumeric = ctype_digit($cleanId);
                $nfc = class_exists(\Normalizer::class) ? (\Normalizer::normalize($keyword, \Normalizer::FORM_C) ?: $keyword) : $keyword;
                $nfd = class_exists(\Normalizer::class) ? (\Normalizer::normalize($keyword, \Normalizer::FORM_D) ?: $keyword) : $keyword;
                $baseTerms = array_unique(array_filter([$keyword, $nfc, $nfd]));
                $variants = [];

                foreach ($baseTerms as $baseTerm) {
                    $variants[] = $baseTerm;
                    $variants[] = str_replace(['đ', 'Đ'], ['d', 'D'], $baseTerm);
                    $variants[] = str_replace(['d', 'D'], ['đ', 'Đ'], $baseTerm);
                }

                $variants = array_values(array_unique(array_filter($variants)));

                $query->where(function (Builder $query) use ($variants, $isNumeric, $cleanId): void {
                    if ($isNumeric) {
                        $query->orWhere('id', (int) $cleanId);
                    }

                    foreach ($variants as $variant) {
                        $query->orWhereHas('user', function (Builder $query) use ($variant): void {
                            $query->where('name', 'like', "%{$variant}%")
                                ->orWhere('email', 'like', "%{$variant}%");
                        })->orWhereHas('sach', fn (Builder $query) => $query->where('ten_sach', 'like', "%{$variant}%"));
                    }
                });
            })
            ->when($trangThai === 'da_tra', fn (Builder $query) => $query->where('trang_thai', 'da_tra'))
            ->when($trangThai === 'dang_muon', function (Builder $query): void {
                $query->where('trang_thai', '!=', 'da_tra')
                    ->whereDate('ngay_tra_du_kien', '>=', today());
            })
            ->when($trangThai === 'qua_han', function (Builder $query): void {
                $query->where('trang_thai', '!=', 'da_tra')
                    ->whereDate('ngay_tra_du_kien', '<', today());
            });
    }

    private function safeCsvValue(string $value): string
    {
        return preg_match('/^[=+\-@]/', $value) === 1 ? "'{$value}" : $value;
    }
}
