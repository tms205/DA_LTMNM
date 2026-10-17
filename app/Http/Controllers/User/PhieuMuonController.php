<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PhieuMuon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PhieuMuonController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = $request->string('q')->trim()->toString();
        if (class_exists(\Normalizer::class)) {
            $keyword = \Normalizer::normalize($keyword, \Normalizer::FORM_C) ?: $keyword;
        }
        $trangThai = $request->string('trang_thai')->toString();

        $phieuMuons = PhieuMuon::query()
            ->cuaNguoiDung($request->user())
            ->with(['sach.theLoai', 'phieuPhat'])
            ->when($keyword !== '', function ($query) use ($keyword): void {
                $cleanId = ltrim($keyword, '#');
                $isNumeric = ctype_digit($cleanId);

                $nfc = class_exists(\Normalizer::class) ? (\Normalizer::normalize($keyword, \Normalizer::FORM_C) ?: $keyword) : $keyword;
                $nfd = class_exists(\Normalizer::class) ? (\Normalizer::normalize($keyword, \Normalizer::FORM_D) ?: $keyword) : $keyword;

                $baseTerms = array_unique(array_filter([$keyword, $nfc, $nfd]));
                $variants = [];
                foreach ($baseTerms as $bt) {
                    $variants[] = $bt;
                    $variants[] = str_replace(['đ', 'Đ'], ['d', 'D'], $bt);
                    $variants[] = str_replace(['d', 'D'], ['đ', 'Đ'], $bt);
                }
                $variants = array_values(array_unique(array_filter($variants)));

                $query->where(function ($query) use ($variants, $isNumeric, $cleanId): void {
                    if ($isNumeric) {
                        $query->orWhere('id', (int) $cleanId);
                    }

                    foreach ($variants as $variant) {
                        $query->orWhereHas('sach', function ($query) use ($variant): void {
                            $query->where('ten_sach', 'like', "%{$variant}%")
                                ->orWhereHas('tacGias', fn ($q) => $q->where('ten_tac_gia', 'like', "%{$variant}%"));
                        });
                    }
                });
            })
            ->when($trangThai === 'da_tra', fn ($query) => $query->where('trang_thai', 'da_tra'))
            ->when($trangThai === 'dang_muon', function ($query): void {
                $query->where('trang_thai', '!=', 'da_tra')
                    ->whereDate('ngay_tra_du_kien', '>=', today());
            })
            ->when($trangThai === 'qua_han', function ($query): void {
                $query->where('trang_thai', '!=', 'da_tra')
                    ->whereDate('ngay_tra_du_kien', '<', today());
            })
            ->orderByDesc('ngay_muon')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('user.phieu-muons.index', compact('phieuMuons', 'keyword', 'trangThai'));
    }

    public function show(Request $request, PhieuMuon $phieuMuon): View
    {
        abort_unless($phieuMuon->user_id === $request->user()->id, 404);

        $phieuMuon->load(['sach.theLoai', 'sach.tacGias', 'phieuPhat.nguoiThu']);

        return view('user.phieu-muons.show', compact('phieuMuon'));
    }
}
