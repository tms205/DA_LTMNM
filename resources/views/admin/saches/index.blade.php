@extends('layouts.app')

@section('title', 'Quản lý sách')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="section-label">
                <i class="bi bi-shield-lock-fill me-1"></i> Quản trị danh mục
            </div>
            <h1 class="page-heading h3 mb-1">Quản lý sách</h1>
            <p class="text-muted small mb-0">Quản trị danh mục đầu sách, tác giả, thể loại và tình trạng tồn kho khả dụng.</p>
        </div>
        <a href="{{ route('admin.saches.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm sách mới
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.saches.index') }}" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-icon-group">
                        <span class="input-icon"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên sách, tác giả, nhà xuất bản..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="the_loai_id" class="form-select">
                        <option value="">Tất cả thể loại</option>
                        @foreach ($theLoais as $theLoai)
                            <option value="{{ $theLoai->id }}" @selected($theLoaiId === $theLoai->id)>{{ $theLoai->ten_the_loai }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-filter me-1"></i> Lọc dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Table Card -->
    <div class="card">
        <div class="card-header py-3 px-3 px-md-4">
            <h2 class="card-header-title">
                <i class="bi bi-journal-album text-primary"></i>
                <span>Danh mục đầu sách</span>
            </h2>
            <span class="badge badge-subtle-primary rounded-pill">
                {{ $saches->total() }} đầu sách
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Thông tin sách</th>
                            <th>Tác giả</th>
                            <th>Thể loại</th>
                            <th>Khả dụng</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($saches as $sach)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="book-cover-thumbnail">
                                            <x-book-cover :sach="$sach" class="w-100" />
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.saches.show', $sach) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $sach->ten_sach }}
                                            </a>
                                            <div class="small text-muted">
                                                Năm XB: {{ $sach->nam_xuat_ban ?: 'Chưa rõ' }} {{ $sach->nha_xuat_ban ? '· '.$sach->nha_xuat_ban : '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="small text-secondary">{{ $sach->tacGias->pluck('ten_tac_gia')->join(', ') ?: 'Chưa rõ' }}</span>
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
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.saches.show', $sach) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('admin.saches.edit', $sach) }}" class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.saches.destroy', $sach) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" data-confirm="Bạn chắc chắn muốn xóa sách này?">
                                            <i class="bi bi-trash3"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="bi bi-journal-x"></i></div>
                                        <h3 class="h6 fw-bold text-dark">Không tìm thấy sách phù hợp</h3>
                                        <p class="text-muted small mb-0">Thử tìm kiếm với từ khóa khác hoặc bỏ lọc thể loại.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($saches->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $saches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
