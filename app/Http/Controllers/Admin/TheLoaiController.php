<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TheLoaiRequest;
use App\Models\TheLoai;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TheLoaiController extends Controller
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
        $theLoais = TheLoai::query()
            ->withCount('saches')
            ->when($keyword !== '', function ($query) use ($keyword): void {
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

                $query->where(function ($subQuery) use ($variants): void {
                    foreach ($variants as $variant) {
                        $subQuery->orWhere('ten_the_loai', 'like', "%{$variant}%")
                            ->orWhere('mo_ta', 'like', "%{$variant}%");
                    }
                });
            })
            ->orderBy('ten_the_loai')
            ->paginate(10)
            ->withQueryString();

        return view('admin.the-loais.index', compact('theLoais', 'keyword'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.the-loais.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TheLoaiRequest $request): RedirectResponse
    {
        $theLoai = TheLoai::query()->create($request->validated());

        return redirect()->route('admin.the-loais.show', $theLoai)
            ->with('success', 'Đã thêm thể loại thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TheLoai $theLoai): View
    {
        $saches = $theLoai->saches()
            ->with(['theLoai', 'tacGias'])
            ->withCount([
                'phieuMuons as dang_muon_count' => fn ($query) => $query->whereIn('trang_thai', ['dang_muon', 'qua_han']),
            ])
            ->orderBy('ten_sach')
            ->paginate(10);

        return view('admin.the-loais.show', compact('theLoai', 'saches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TheLoai $theLoai): View
    {
        return view('admin.the-loais.edit', compact('theLoai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TheLoaiRequest $request, TheLoai $theLoai): RedirectResponse
    {
        $theLoai->update($request->validated());

        return redirect()->route('admin.the-loais.show', $theLoai)
            ->with('success', 'Đã cập nhật thể loại thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TheLoai $theLoai): RedirectResponse
    {
        if ($theLoai->saches()->exists()) {
            return back()->with('error', 'Không thể xóa thể loại đang có sách.');
        }

        $theLoai->delete();

        return redirect()->route('admin.the-loais.index')
            ->with('success', 'Đã xóa thể loại thành công.');
    }
}
