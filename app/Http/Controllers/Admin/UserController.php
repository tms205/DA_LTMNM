<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of readers with reliability credit status.
     */
    public function index(Request $request): View
    {

        $keyword = trim((string) $request->input('keyword'));
        $mucDo = (string) $request->input('muc_do');

        $query = User::query()
            ->where('role', 'user')
            ->withCount([
                'phieuMuons as dang_muon_count' => fn ($q) => $q->whereIn('trang_thai', ['dang_muon', 'qua_han']),
                'phieuMuons as qua_han_count' => fn ($q) => $q->where('trang_thai', '!=', 'da_tra')->whereDate('ngay_tra_du_kien', '<', today()),
                'phieuPhats as phat_chua_nop_count' => fn ($q) => $q->chuaNop(),
            ])
            ->withSum([
                'phieuPhats as tien_phat_no' => fn ($q) => $q->chuaNop(),
            ], 'tong_tien_phat');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword): void {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$keyword}%")
                    ->orWhere('dia_chi', 'like', "%{$keyword}%");
            });
        }

        $allUsers = $query->latest('id')->get();

        // Calculate statistics across all readers
        $stats = [
            'tong_so' => $allUsers->count(),
            'uy_tin' => 0,
            'binh_thuong' => 0,
            'canh_bao' => 0,
            'vi_pham' => 0,
            'cam_muon' => 0,
        ];

        foreach ($allUsers as $u) {
            $eval = $u->danhGiaLyLich();
            $level = $eval['muc_do'];
            if (isset($stats[$level])) {
                $stats[$level]++;
            }
        }

        // Filter collection if muc_do filter is selected
        if (in_array($mucDo, ['uy_tin', 'binh_thuong', 'canh_bao', 'vi_pham', 'cam_muon'], true)) {
            $filteredUsers = $allUsers->filter(fn ($u) => $u->danhGiaLyLich()['muc_do'] === $mucDo);
        } else {
            $filteredUsers = $allUsers;
        }

        // Manual pagination on the collection to preserve computed credit status
        $perPage = 12;
        $currentPage = (int) $request->input('page', 1);
        $pagedData = $filteredUsers->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $users = new LengthAwarePaginator(
            $pagedData,
            $filteredUsers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.users.index', compact('users', 'keyword', 'mucDo', 'stats'));
    }

    /**
     * Display reader profile and background check details.
     */
    public function show(User $user): View
    {
        abort_unless($user->isUser(), 404);

        $user->load([
            'phieuMuons' => fn ($q) => $q->with('sach')->latest('id'),
            'phieuPhats' => fn ($q) => $q->with(['phieuMuon.sach', 'nguoiThu'])->latest('id'),
        ]);

        $lyLich = $user->danhGiaLyLich();

        return view('admin.users.show', compact('user', 'lyLich'));
    }

    /**
     * Update librarian note and borrowing permission for reader.
     */
    public function updateNote(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isUser(), 404);

        $request->validate([
            'ghi_chu_thu_thu' => ['nullable', 'string', 'max:1000'],
            'khoa_muon_sach' => ['nullable', 'boolean'],
            'ly_do_khoa' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'ghi_chu_thu_thu' => $request->input('ghi_chu_thu_thu'),
            'khoa_muon_sach' => $request->boolean('khoa_muon_sach'),
            'ly_do_khoa' => $request->boolean('khoa_muon_sach') ? $request->input('ly_do_khoa') : null,
        ]);

        $statusMsg = $user->khoa_muon_sach
            ? "Đã cập nhật ghi chú và KHÓA quyền mượn sách của độc giả {$user->name}."
            : "Đã cập nhật ghi chú hồ sơ độc giả {$user->name} thành công.";

        return back()->with('success', $statusMsg);
    }
}
