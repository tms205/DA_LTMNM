<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TacGiaRequest;
use App\Models\TacGia;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TacGiaController extends Controller
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
        $tacGias = TacGia::query()
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
                        $subQuery->orWhere('ten_tac_gia', 'like', "%{$variant}%")
                            ->orWhere('quoc_tich', 'like', "%{$variant}%");
                    }
                });
            })
            ->orderBy('ten_tac_gia')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tac-gias.index', compact('tacGias', 'keyword'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.tac-gias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TacGiaRequest $request): RedirectResponse
    {
        $tacGia = TacGia::query()->create($request->validated());

        return redirect()->route('admin.tac-gias.show', $tacGia)
            ->with('success', 'Đã thêm tác giả thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TacGia $tacGia): View
    {
        $saches = $tacGia->saches()
            ->with(['theLoai', 'tacGias'])
            ->withCount([
                'phieuMuons as dang_muon_count' => fn ($query) => $query->whereIn('trang_thai', ['dang_muon', 'qua_han']),
            ])
            ->orderBy('ten_sach')
            ->paginate(10);

        return view('admin.tac-gias.show', compact('tacGia', 'saches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TacGia $tacGia): View
    {
        return view('admin.tac-gias.edit', compact('tacGia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TacGiaRequest $request, TacGia $tacGia): RedirectResponse
    {
        $tacGia->update($request->validated());

        return redirect()->route('admin.tac-gias.show', $tacGia)
            ->with('success', 'Đã cập nhật tác giả thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TacGia $tacGia): RedirectResponse
    {
        if ($tacGia->saches()->exists()) {
            return back()->with('error', 'Không thể xóa tác giả đang được gán cho sách.');
        }

        $tacGia->delete();

        return redirect()->route('admin.tac-gias.index')
            ->with('success', 'Đã xóa tác giả thành công.');
    }
}
