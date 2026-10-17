<?php

namespace App\Http\Controllers;

use App\Models\PhieuMuon;
use App\Models\PhieuPhat;
use App\Models\Sach;
use App\Models\TacGia;
use App\Models\TheLoai;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $thongKe = [
                'sach' => Sach::query()->count(),
                'tac_gia' => TacGia::query()->count(),
                'the_loai' => TheLoai::query()->count(),
                'doc_gia' => User::query()->where('role', 'user')->count(),
                'dang_muon' => PhieuMuon::query()->whereIn('trang_thai', ['dang_muon', 'qua_han'])->count(),
                'qua_han' => PhieuMuon::query()
                    ->where('trang_thai', '!=', 'da_tra')
                    ->whereDate('ngay_tra_du_kien', '<', today())
                    ->count(),
                'phieu_phat' => PhieuPhat::query()->count(),
                'tien_phat_chua_thu' => (int) PhieuPhat::query()->chuaNop()->sum('tong_tien_phat'),
                'tien_phat_da_thu' => (int) PhieuPhat::query()->daNop()->sum('tong_tien_phat'),
            ];

            $phieuMuons = PhieuMuon::query()
                ->with(['user', 'sach.theLoai'])
                ->latest('id')
                ->limit(6)
                ->get();

            return view('home', compact('thongKe', 'phieuMuons'));
        }

        $thongKe = [
            'tong' => $user->phieuMuons()->count(),
            'dang_muon' => $user->phieuMuons()->whereIn('trang_thai', ['dang_muon', 'qua_han'])->count(),
            'da_tra' => $user->phieuMuons()->where('trang_thai', 'da_tra')->count(),
            'qua_han' => $user->phieuMuons()
                ->where('trang_thai', '!=', 'da_tra')
                ->whereDate('ngay_tra_du_kien', '<', today())
                ->count(),
            'tien_phat_chua_nop' => (int) $user->phieuPhats()->chuaNop()->sum('tong_tien_phat'),
        ];

        $phieuMuons = $user->phieuMuons()
            ->with('sach.theLoai')
            ->latest('id')
            ->limit(6)
            ->get();

        return view('home', compact('thongKe', 'phieuMuons'));
    }
}
