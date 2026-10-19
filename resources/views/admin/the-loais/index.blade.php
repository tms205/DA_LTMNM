@extends('layouts.app')

@section('title', 'Quản lý thể loại')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="section-label">
                <i class="bi bi-tags me-1"></i> Quản trị dữ liệu
            </div>
            <h1 class="page-heading h3 mb-1">Phân loại &amp; Thể loại sách</h1>
            <p class="text-muted small mb-0">Quản lý các danh mục chuyên ngành, thể loại sách phục vụ phân loại và tra cứu.</p>
        </div>
        <a href="{{ route('admin.the-loais.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm thể loại mới
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.the-loais.index') }}" class="row g-2 align-items-center">
                <div class="col-md-10">
                    <div class="input-icon-group">
                        <span class="input-icon"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên thể loại..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search me-1"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table Card -->
    <div class="card">
        <div class="card-header py-3 px-3 px-md-4">
            <h2 class="card-header-title">
                <i class="bi bi-tags-fill text-primary"></i>
                <span>Danh mục thể loại</span>
            </h2>
            <span class="badge badge-subtle-primary rounded-pill">
                {{ $theLoais->total() }} thể loại
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tên thể loại</th>
                            <th>Mô tả thể loại</th>
                            <th>Tác phẩm thuộc thể loại</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($theLoais as $theLoai)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge badge-subtle-primary rounded-pill p-2">
                                            <i class="bi bi-tag-fill"></i>
                                        </span>
                                        <a href="{{ route('admin.the-loais.show', $theLoai) }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ $theLoai->ten_the_loai }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary small">
                                        {{ Str::limit($theLoai->mo_ta, 90) ?: 'Chưa có mô tả' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-subtle-neutral rounded-pill font-monospace">
                                        {{ $theLoai->saches_count }} cuốn sách
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.the-loais.show', $theLoai) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('admin.the-loais.edit', $theLoai) }}" class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.the-loais.destroy', $theLoai) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" data-confirm="Bạn chắc chắn muốn xóa thể loại này?">
                                            <i class="bi bi-trash3"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="bi bi-tags"></i></div>
                                        <h3 class="h6 fw-bold text-dark">Chưa có thể loại phù hợp</h3>
                                        <p class="text-muted small mb-0">Thử tìm kiếm với từ khóa khác hoặc tạo thể loại mới.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($theLoais->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $theLoais->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
