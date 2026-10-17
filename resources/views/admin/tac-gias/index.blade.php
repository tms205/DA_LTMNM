@extends('layouts.app')

@section('title', 'Quản lý tác giả')

@section('content')
<section class="py-4 py-lg-5">
    <div class="container">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <div class="section-label">
                    <i class="bi bi-people me-1"></i> Quản trị dữ liệu
                </div>
                <h1 class="page-heading h3 mb-1">Tác giả văn học &amp; chuyên khảo</h1>
                <p class="text-muted small mb-0">Hồ sơ thông tin tác giả, tiểu sử và thống kê tác phẩm đang lưu trữ trong thư viện.</p>
            </div>
            <a href="{{ route('admin.tac-gias.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Thêm tác giả mới
            </a>
        </div>

        <!-- Filter Card -->
        <div class="card border shadow-sm mb-4">
            <div class="card-body p-3 p-md-4">
                <form method="GET" action="{{ route('admin.tac-gias.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-10">
                        <div class="input-icon-group">
                            <span class="input-icon"><i class="bi bi-search"></i></span>
                            <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên tác giả..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search me-1"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Authors Table Card -->
        <div class="card border shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Họ và tên tác giả</th>
                                <th>Năm sinh</th>
                                <th>Quốc tịch</th>
                                <th>Tác phẩm lưu kho</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tacGias as $tacGia)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="user-avatar" style="width: 2.2rem; height: 2.2rem; font-size: 0.8rem; background: linear-gradient(135deg, #0284c7, #1d4ed8);">
                                                {{ mb_strtoupper(mb_substr($tacGia->ten_tac_gia, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.tac-gias.show', $tacGia) }}" class="fw-semibold text-dark text-decoration-none">
                                                    {{ $tacGia->ten_tac_gia }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-secondary small">{{ $tacGia->nam_sinh ?: '—' }}</span></td>
                                    <td>
                                        <span class="badge badge-subtle-neutral rounded-pill small">
                                            {{ $tacGia->quoc_tich ?: 'Chưa rõ' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-subtle-primary rounded-pill font-monospace">
                                            {{ $tacGia->saches_count }} cuốn
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.tac-gias.show', $tacGia) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('admin.tac-gias.edit', $tacGia) }}" class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <form action="{{ route('admin.tac-gias.destroy', $tacGia) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" data-confirm="Bạn chắc chắn muốn xóa tác giả này?">
                                                <i class="bi bi-trash3"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-state-icon"><i class="bi bi-person-x"></i></div>
                                            <h3 class="h6 fw-bold text-dark">Chưa có tác giả phù hợp</h3>
                                            <p class="text-muted small mb-0">Thử tìm kiếm với tên khác hoặc thêm tác giả mới vào thư viện.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $tacGias->links() }}
        </div>
    </div>
</section>
@endsection
