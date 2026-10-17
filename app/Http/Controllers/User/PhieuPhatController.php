<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PhieuPhat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhieuPhatController extends Controller
{
    /**
     * Display the authenticated reader's penalty tickets.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $phieuPhats = PhieuPhat::query()
            ->cuaNguoiDung($user)
            ->with(['phieuMuon.sach', 'nguoiThu'])
            ->latest('id')
            ->paginate(10);

        $tongChuaNop = (int) PhieuPhat::query()
            ->cuaNguoiDung($user)
            ->chuaNop()
            ->sum('tong_tien_phat');

        $tongDaNop = (int) PhieuPhat::query()
            ->cuaNguoiDung($user)
            ->daNop()
            ->sum('tong_tien_phat');

        return view('user.phieu-phats.index', compact('phieuPhats', 'tongChuaNop', 'tongDaNop'));
    }
}
