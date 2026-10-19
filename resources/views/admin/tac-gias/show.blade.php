@extends('layouts.app')

@section('title', $tacGia->ten_tac_gia)

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.tac-gias.index') }}">Quản lý tác giả</a></li>
            <li class="breadcrumb-item active">{{ $tacGia->ten_tac_gia }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar" style="width: 3.5rem; height: 3.5rem; font-size: 1.5rem; background: linear-gradient(135deg, #1abb9c, #169f85);">
                        {{ mb_strtoupper(mb_substr($tacGia->ten_tac_gia, 0, 1)) }}
                    </div>
                    <div>
                        <div class="section-label mb-1">Hồ sơ tác giả</div>
                        <h1 class="page-heading h3 mb-1 text-dark">{{ $tacGia->ten_tac_gia }}</h1>
                        <div class="d-flex flex-wrap gap-2 align-items-center text-muted small">
                            <span><i class="bi bi-calendar3 me-1"></i> Năm sinh: <strong>{{ $tacGia->nam_sinh ?: 'Chưa rõ' }}</strong></span>
                            <span>·</span>
                            <span><i class="bi bi-globe me-1"></i> Quốc tịch: <strong>{{ $tacGia->quoc_tich ?: 'Chưa rõ' }}</strong></span>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.tac-gias.edit', $tacGia) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Chỉnh sửa thông tin
                    </a>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <h2 class="h6 fw-bold text-uppercase text-muted letter-spacing-1 mb-2">Tiểu sử &amp; Giới thiệu</h2>
                <div class="p-3 bg-light rounded-3 border text-secondary small" style="line-height: 1.7;">
                    {{ $tacGia->tieu_su ?: 'Chưa có thông tin tiểu sử chi tiết cho tác giả này.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Books by this Author -->
    <div class="card">
        <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center">
            <h2 class="h5 fw-bold mb-0 text-dark">
                <i class="bi bi-journal-bookmark me-1 text-primary"></i> Tác phẩm trong thư viện
            </h2>
            <span class="badge badge-subtle-primary rounded-pill">{{ $saches->total() }} tác phẩm</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tên sách</th>
                            <th>Thể loại</th>
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
                                    <span class="badge badge-subtle-primary rounded-pill small">
                                        {{ $sach->theLoai->ten_the_loai }}
                                    </span>
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
                                        <h3 class="h6 fw-bold text-dark">Chưa có tác phẩm nào</h3>
                                        <p class="text-muted small mb-0">Tác giả này chưa được gắn với cuốn sách nào trong thư viện.</p>
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
