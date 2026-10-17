<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SachRequest;
use App\Models\Sach;
use App\Models\TacGia;
use App\Models\TheLoai;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SachController extends Controller
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
        $theLoaiId = $request->integer('the_loai_id');

        $saches = Sach::query()
            ->with(['theLoai', 'tacGias'])
            ->withCount([
                'phieuMuons as dang_muon_count' => fn ($query) => $query->whereIn('trang_thai', ['dang_muon', 'qua_han']),
            ])
            ->timKiem($keyword)
            ->when($theLoaiId > 0, fn ($query) => $query->where('the_loai_id', $theLoaiId))
            ->orderBy('ten_sach')
            ->paginate(10)
            ->withQueryString();

        $theLoais = TheLoai::query()->orderBy('ten_the_loai')->get();

        return view('admin.saches.index', compact('saches', 'theLoais', 'keyword', 'theLoaiId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $theLoais = TheLoai::query()->orderBy('ten_the_loai')->get();
        $tacGias = TacGia::query()->orderBy('ten_tac_gia')->get();

        return view('admin.saches.create', compact('theLoais', 'tacGias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SachRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tacGiaIds = $data['tac_gia_ids'];
        unset($data['tac_gia_ids'], $data['hinh_anh']);

        $imagePath = $request->file('hinh_anh')?->store('sach-bia', 'public');

        if ($imagePath !== null) {
            $data['hinh_anh'] = $imagePath;
        }

        try {
            $sach = DB::transaction(function () use ($data, $tacGiaIds): Sach {
                $sach = Sach::query()->create($data);
                $sach->tacGias()->sync($tacGiaIds);

                return $sach;
            });
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()->route('admin.saches.show', $sach)
            ->with('success', 'Đã thêm sách thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sach $sach): View
    {
        $sach->load(['theLoai', 'tacGias'])->loadCount([
            'phieuMuons as dang_muon_count' => fn ($query) => $query->whereIn('trang_thai', ['dang_muon', 'qua_han']),
        ]);

        $phieuMuons = $sach->phieuMuons()
            ->with('user')
            ->latest('id')
            ->paginate(10);

        return view('admin.saches.show', compact('sach', 'phieuMuons'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sach $sach): View
    {
        $sach->load('tacGias');
        $theLoais = TheLoai::query()->orderBy('ten_the_loai')->get();
        $tacGias = TacGia::query()->orderBy('ten_tac_gia')->get();

        return view('admin.saches.edit', compact('sach', 'theLoais', 'tacGias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SachRequest $request, Sach $sach): RedirectResponse
    {
        $data = $request->validated();
        $tacGiaIds = $data['tac_gia_ids'];
        unset($data['tac_gia_ids'], $data['hinh_anh']);

        $oldImagePath = $sach->hinh_anh;
        $newImagePath = $request->file('hinh_anh')?->store('sach-bia', 'public');

        if ($newImagePath !== null) {
            $data['hinh_anh'] = $newImagePath;
        }

        try {
            DB::transaction(function () use ($sach, $data, $tacGiaIds): void {
                $sach->update($data);
                $sach->tacGias()->sync($tacGiaIds);
            });
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($newImagePath !== null && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('admin.saches.show', $sach)
            ->with('success', 'Đã cập nhật sách thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sach $sach): RedirectResponse
    {
        if ($sach->phieuMuons()->exists()) {
            return back()->with('error', 'Không thể xóa sách đã có lịch sử mượn.');
        }

        $imagePath = $sach->hinh_anh;
        $sach->delete();

        if ($imagePath !== null) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('admin.saches.index')
            ->with('success', 'Đã xóa sách thành công.');
    }
}
