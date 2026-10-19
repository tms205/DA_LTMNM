@extends('layouts.app')

@section('title', $sach->ten_sach)

@section('content')
<div class="{{ Auth::check() ? 'container-fluid px-0' : 'container py-4' }}">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0 py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item">
                <a href="{{ route('catalog.index') }}" class="text-decoration-none d-flex align-items-center gap-1">
                    <i class="bi bi-house-door"></i> Kho sách
                </a>
            </li>
            <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 400px;">
                {{ $sach->ten_sach }}
            </li>
        </ol>
    </nav>

    <!-- Main Showcase Card (Gentelella x_panel) -->
    <div class="card overflow-hidden">
        <div class="row g-0">
            <!-- Book Cover Column -->
            <div class="col-md-5 col-lg-4 bg-light d-flex flex-column align-items-center justify-content-center p-4 p-lg-5 border-end">
                <div class="position-relative w-100" style="max-width: 280px;">
                    <div class="shadow-sm rounded-2 overflow-hidden border">
                        <x-book-cover :sach="$sach" class="w-100" />
                    </div>
                    
                    <div class="mt-4 p-3 bg-white rounded-2 border text-center shadow-sm">
                        <div class="small text-muted mb-1">Tình trạng sách trong kho</div>
                        @if ($sach->so_luong_con_lai > 0)
                            <div class="badge badge-subtle-success rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                                <span class="status-dot bg-success"></span>
                                <span>Còn <strong>{{ $sach->so_luong_con_lai }}</strong> / {{ $sach->so_luong }} cuốn khả dụng</span>
                            </div>
                        @else
                            <div class="badge badge-subtle-danger rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                                <span class="status-dot bg-danger"></span>
                                <span>Tạm thời hết sách (0 / {{ $sach->so_luong }})</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Book Information Column -->
            <div class="col-md-7 col-lg-8">
                <div class="card-body p-4 p-lg-5 d-flex flex-column h-100">
                    <div class="mb-3">
                        <span class="badge badge-subtle-primary rounded-pill px-3 py-1 fw-semibold small">
                            <i class="bi bi-tag-fill me-1"></i> {{ $sach->theLoai->ten_the_loai }}
                        </span>
                    </div>

                    <h1 class="page-heading h3 mb-3 text-dark">{{ $sach->ten_sach }}</h1>

                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="text-muted small">Tác giả:</span>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse ($sach->tacGias as $tacGia)
                                <span class="badge badge-subtle-neutral rounded-pill px-3 py-1 text-dark fw-semibold">
                                    <i class="bi bi-person-fill text-primary me-1"></i> {{ $tacGia->ten_tac_gia }}
                                </span>
                            @empty
                                <span class="text-muted small">Chưa rõ tác giả</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Metadata Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-sm-3">
                            <div class="p-3 bg-light rounded-2 border h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-calendar-event text-primary"></i> Năm XB
                                </div>
                                <div class="fw-bold text-dark">{{ $sach->nam_xuat_ban ?: 'Chưa rõ' }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 bg-light rounded-2 border h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-building text-info"></i> Nhà XB
                                </div>
                                <div class="fw-bold text-dark text-truncate" title="{{ $sach->nha_xuat_ban ?: 'Chưa rõ' }}">{{ $sach->nha_xuat_ban ?: 'Chưa rõ' }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 bg-light rounded-2 border h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-collection text-warning"></i> Tổng số bản
                                </div>
                                <div class="fw-bold text-dark">{{ $sach->so_luong }} cuốn</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 bg-light rounded-2 border h-100">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-check-circle text-success"></i> Có thể mượn
                                </div>
                                <div class="fw-bold {{ $sach->so_luong_con_lai > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $sach->so_luong_con_lai }} cuốn
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary / Description -->
                    <div class="mb-4 flex-grow-1">
                        <h2 class="h6 fw-bold text-uppercase text-muted letter-spacing-1 mb-2">
                            <i class="bi bi-file-text me-1 text-primary"></i> Giới thiệu nội dung
                        </h2>
                        <div class="p-3 bg-light rounded-2 border text-secondary" style="line-height: 1.6;">
                            {{ $sach->mo_ta ?: 'Tác phẩm hiện chưa có nội dung giới thiệu tóm tắt trong hệ thống thư viện.' }}
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="pt-3 border-top d-flex flex-wrap gap-2">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập để theo dõi mượn sách
                            </a>
                        @else
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('admin.phieu-muons.create', ['sach_id' => $sach->id]) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Lập phiếu mượn sách này
                                </a>
                                <a href="{{ route('admin.saches.edit', $sach) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil me-1"></i> Chỉnh sửa thông tin
                                </a>
                            @else
                                <a href="{{ route('user.phieu-muons.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-clock-history me-1"></i> Xem lịch sử mượn của tôi
                                </a>
                            @endif
                        @endguest
                        <a href="{{ route('catalog.index') }}" class="btn btn-link text-muted ms-auto">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại danh mục
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
