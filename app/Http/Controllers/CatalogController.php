<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\TheLoai;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $keyword = $request->string('q')->trim()->toString();
        if (class_exists(\Normalizer::class)) {
            $keyword = \Normalizer::normalize($keyword, \Normalizer::FORM_C) ?: $keyword;
        }
        $theLoaiId = $request->integer('the_loai_id');

        $saches = Sach::query()
            ->with(['theLoai', 'tacGias'])
            ->withCount([
                'phieuMuons as dang_muon_count' => fn ($query) => $query->whereIn('trang_thai', ['dang_muon', 'qua_han']),
            ])
            ->timKiem($keyword)
            ->when($theLoaiId > 0, fn ($query) => $query->where('the_loai_id', $theLoaiId))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('catalog.partials.book-grid', compact('saches'))->render(),
                'total' => $saches->total(),
            ]);
        }

        $theLoais = TheLoai::query()->orderBy('ten_the_loai')->get();

        return view('catalog.index', compact('saches', 'theLoais', 'keyword', 'theLoaiId'));
    }

    public function show(Sach $sach): View
    {
        $sach->load(['theLoai', 'tacGias'])->loadCount([
            'phieuMuons as dang_muon_count' => fn ($query) => $query->whereIn('trang_thai', ['dang_muon', 'qua_han']),
        ]);

        return view('catalog.show', compact('sach'));
    }
}
