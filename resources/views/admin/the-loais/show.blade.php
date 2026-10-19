@extends('layouts.app')

@section('title', $theLoai->ten_the_loai)

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.the-loais.index') }}">Quản lý thể loại</a></li>
            <li class="breadcrumb-item active">{{ $theLoai->ten_the_loai }}</li>
        </ol>
    </nav>

    <!-- Category Header Card -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar" style="width: 3.5rem; height: 3.5rem; font-size: 1.5rem; background: linear-gradient(135deg, #1abb9c, #169f85);">
                        <i class="bi bi-tag-fill"></i>
                    </div>
                    <div>
                        <div class="section-label mb-1">Chi tiết phân loại</div>
                        <h1 class="page-heading h3 mb-1 text-dark">{{ $theLoai->ten_the_loai }}</h1>
                        <p class="text-secondary small mb-0">{{ $theLoai->mo_ta ?: 'Chưa có thông tin mô tả chi tiết cho thể loại này.' }}</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.the-loais.edit', $theLoai) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Chỉnh sửa thể loại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Books in Category Table Card -->
    <div class="card">
        <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center">
            <h2 class="h5 fw-bold mb-0 text-dark">
                <i class="bi bi-book me-1 text-primary"></i> Sách thuộc thể loại này
            </h2>
            <span class="badge badge-subtle-primary rounded-pill">{{ $saches->total() }} đầu sách</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tên sách</th>
                            <th>Tác giả</th>
                            <th>Khả dụng</th>
                            <th class="text-end">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($saches as $sach)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="book-cover-thumbnail rounded overflow-hidden border">
                                            <x-book-cover :sach="$sach" class="w-100" />
                                        </div>
                                        <a href="{{ route('admin.saches.show', $sach) }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ $sach->ten_sach }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="small text-secondary">{{ $sach->tacGias->pluck('ten_tac_gia')->join(', ') ?: 'Chưa rõ' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $sach->so_luong_con_lai > 0 ? 'badge-subtle-success' : 'badge-subtle-danger' }} rounded-pill font-monospace">
                                        {{ $sach->so_luong_con_lai }}/{{ $sach->so_luong }} cuốn
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.saches.show', $sach) }}" class="btn btn-sm btn-outline-primary">
                                        Xem sách
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="bi bi-journal-x"></i></div>
                                        <h3 class="h6 fw-bold text-dark">Chưa có cuốn sách nào</h3>
                                        <p class="text-muted small mb-0">Thể loại này hiện chưa có tác phẩm nào được phân loại vào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($saches->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $saches->links() }}
        </div>
    @endif
</div>
@endsection
