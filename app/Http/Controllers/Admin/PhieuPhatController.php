<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhieuPhat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhieuPhatController extends Controller
{
    /**
     * Display a listing of penalty tickets.
     */
    public function index(Request $request): View
    {
        // Auto sync overdue active loans that need a penalty ticket

        $keyword = trim((string) $request->input('keyword'));
        $trangThai = (string) $request->input('trang_thai');

        $query = PhieuPhat::query()
            ->with(['user', 'phieuMuon.sach', 'nguoiThu']);

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword): void {
                $q->whereHas('user', function ($uq) use ($keyword): void {
                    $uq->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$keyword}%");
                })->orWhereHas('phieuMuon.sach', function ($sq) use ($keyword): void {
                    $sq->where('ten_sach', 'like', "%{$keyword}%");
                })->orWhere('ly_do', 'like', "%{$keyword}%");
            });
        }

        if (in_array($trangThai, [PhieuPhat::TRANG_THAI_CHUA_NOP, PhieuPhat::TRANG_THAI_DA_NOP], true)) {
            $query->where('trang_thai', $trangThai);
        }

        $phieuPhats = $query->latest('id')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'tong_so' => PhieuPhat::query()->count(),
            'chua_nop_so' => PhieuPhat::query()->chuaNop()->count(),
            'chua_nop_tien' => (int) PhieuPhat::query()->chuaNop()->sum('tong_tien_phat'),
            'da_nop_tien' => (int) PhieuPhat::query()->daNop()->sum('tong_tien_phat'),
        ];

        return view('admin.phieu-phats.index', compact('phieuPhats', 'keyword', 'trangThai', 'stats'));
    }

    /**
     * Display the specified penalty ticket.
     */
    public function show(PhieuPhat $phieuPhat): View
    {
        $phieuPhat->load(['user', 'phieuMuon.sach', 'nguoiThu']);

        return view('admin.phieu-phats.show', compact('phieuPhat'));
    }

    /**
     * Confirm fine payment.
     */
    public function xacNhanNopPhat(Request $request, PhieuPhat $phieuPhat): RedirectResponse
    {
        if ($phieuPhat->isDaNop()) {
            return back()->with('info', 'Phiếu phạt này đã được xác nhận nộp tiền trước đó.');
        }

        $request->validate([
            'ghi_chu' => ['nullable', 'string', 'max:500'],
        ]);

        $phieuPhat->xacNhanNop(
            nguoiThu: $request->user(),
            ghiChu: $request->input('ghi_chu')
        );

        $soTienFmt = number_format($phieuPhat->tong_tien_phat, 0, ',', '.').' VNĐ';

        return back()->with('success', "Đã xác nhận thu nộp phạt {$soTienFmt} từ độc giả {$phieuPhat->user->name} thành công.");
    }

    /**
     * Remove the specified penalty ticket.
     */
    public function destroy(PhieuPhat $phieuPhat): RedirectResponse
    {
        $phieuPhat->delete();

        return redirect()->route('admin.phieu-phats.index')
            ->with('success', 'Đã xóa phiếu phạt thành công.');
    }
}
