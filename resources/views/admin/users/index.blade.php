@extends('layouts.app')

@section('title', 'Quản lý độc giả & Lý lịch mượn sách')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="section-label">
                <i class="bi bi-people-fill me-1"></i> Quản trị người dùng
            </div>
            <h1 class="page-heading h3 mb-1">Hồ sơ độc giả &amp; Đánh giá lý lịch</h1>
            <p class="text-muted small mb-0">Theo dõi mức độ uy tín, tiền sử mượn trả và kiểm tra lý lịch trong sạch trước khi cấp sách mượn.</p>
        </div>
        <a href="{{ route('admin.phieu-muons.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-journal-plus me-1"></i> Lập phiếu mượn sách
        </a>
    </div>

    <!-- Metric Stat Cards (Gentelella Tiles) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-wrapper" style="background-color: #e6f7f4; color: #1abb9c; width: 2.35rem; height: 2.35rem; font-size: 1.1rem;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['tong_so'] }}</div>
                    <div class="stat-label text-truncate">Tổng độc giả</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.users.index', ['muc_do' => 'uy_tin']) }}" class="text-decoration-none">
                <div class="stat-card border-start border-success border-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-wrapper" style="background-color: #ecfdf5; color: #059669; width: 2.35rem; height: 2.35rem; font-size: 1.1rem;">
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                    <div class="stat-value text-success">{{ $stats['uy_tin'] }}</div>
                    <div class="stat-label text-truncate">🌟 Uy tín cao</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.users.index', ['muc_do' => 'binh_thuong']) }}" class="text-decoration-none">
                <div class="stat-card border-start border-info border-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-wrapper" style="background-color: #f0f9ff; color: #0284c7; width: 2.35rem; height: 2.35rem; font-size: 1.1rem;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                    <div class="stat-value text-info">{{ $stats['binh_thuong'] }}</div>
                    <div class="stat-label text-truncate">🟢 Bình thường</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.users.index', ['muc_do' => 'canh_bao']) }}" class="text-decoration-none">
                <div class="stat-card border-start border-warning border-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-wrapper" style="background-color: #fffbeb; color: #d97706; width: 2.35rem; height: 2.35rem; font-size: 1.1rem;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    </div>
                    <div class="stat-value text-warning-emphasis">{{ $stats['canh_bao'] }}</div>
                    <div class="stat-label text-truncate">🟡 Cần chú ý</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.users.index', ['muc_do' => 'vi_pham']) }}" class="text-decoration-none">
                <div class="stat-card border-start border-danger border-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-wrapper" style="background-color: #fef2f2; color: #dc2626; width: 2.35rem; height: 2.35rem; font-size: 1.1rem;">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                        </div>
                    </div>
                    <div class="stat-value text-danger">{{ $stats['vi_pham'] }}</div>
                    <div class="stat-label text-truncate">🔴 Vi phạm</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.users.index', ['muc_do' => 'cam_muon']) }}" class="text-decoration-none">
                <div class="stat-card border-start border-dark border-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-wrapper" style="background-color: #f3f4f6; color: #374151; width: 2.35rem; height: 2.35rem; font-size: 1.1rem;">
                            <i class="bi bi-slash-circle-fill"></i>
                        </div>
                    </div>
                    <div class="stat-value text-dark">{{ $stats['cam_muon'] }}</div>
                    <div class="stat-label text-truncate">⛔ Khóa mượn</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-icon-group">
                        <span class="input-icon"><i class="bi bi-search"></i></span>
                        <input type="search" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên độc giả, email, số điện thoại hoặc địa chỉ..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="muc_do" class="form-select">
                        <option value="">Tất cả mức độ lý lịch</option>
                        <option value="uy_tin" @selected($mucDo === 'uy_tin')>🌟 Uy tín cao (Trong sạch)</option>
                        <option value="binh_thuong" @selected($mucDo === 'binh_thuong')>🟢 Bình thường (Đủ điều kiện)</option>
                        <option value="canh_bao" @selected($mucDo === 'canh_bao')>🟡 Cần chú ý (Nhắc nhở nhẹ)</option>
                        <option value="vi_pham" @selected($mucDo === 'vi_pham')>🔴 Vi phạm (Quá hạn/Nợ phạt)</option>
                        <option value="cam_muon" @selected($mucDo === 'cam_muon')>⛔ Bị khóa quyền mượn</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-filter me-1"></i> Lọc độc giả
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Readers Table Card -->
    <div class="card">
        <div class="card-header py-3 px-3 px-md-4">
            <h2 class="card-header-title">
                <i class="bi bi-person-lines-fill text-primary"></i>
                <span>Danh sách độc giả</span>
            </h2>
            <span class="badge badge-subtle-primary rounded-pill">
                {{ $users->total() }} độc giả
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0 admin-users-table">
                    <thead>
                        <tr>
                            <th class="admin-user-id-column">Mã</th>
                            <th class="admin-user-identity">Độc giả</th>
                            <th class="admin-user-contact">Liên hệ &amp; Địa chỉ</th>
                            <th class="admin-user-loans">Tình trạng mượn</th>
                            <th class="admin-user-rating">Đánh giá lý lịch</th>
                            <th class="admin-user-note-cell">Lưu ý thủ thư</th>
                            <th class="admin-user-profile-column text-end">Hồ sơ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $lyLich = $user->danhGiaLyLich();
                            @endphp
                            <tr>
                                <td class="admin-user-id-column">
                                    <span class="badge badge-subtle-neutral fw-bold font-monospace">
                                        #{{ $user->id }}
                                    </span>
                                </td>
                                <td class="admin-user-identity">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="width: 2rem; height: 2rem; font-size: 0.8rem;">
                                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="admin-user-identity-details">
                                            <a href="{{ route('admin.users.show', $user) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $user->name }}
                                            </a>
                                            <div class="small text-muted admin-user-email text-break">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="admin-user-contact">
                                    <div class="small text-dark font-monospace">
                                        <i class="bi bi-telephone text-primary me-1"></i>{{ $user->so_dien_thoai ?: '—' }}
                                    </div>
                                    <div class="small text-muted mt-1 admin-user-address text-break">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $user->dia_chi ?: 'Chưa cập nhật địa chỉ' }}
                                    </div>
                                </td>
                                <td class="admin-user-loans">
                                    <div class="small">
                                        Đang giữ: <strong>{{ $user->dang_muon_count ?? 0 }}</strong> cuốn
                                        @if (($user->qua_han_count ?? 0) > 0)
                                            <span class="badge bg-danger ms-1">
                                                <i class="bi bi-clock me-1"></i>{{ $user->qua_han_count }} quá hạn
                                            </span>
                                        @endif
                                    </div>
                                    @if (($user->tien_phat_no ?? 0) > 0)
                                        <div class="small text-danger fw-bold mt-1">
                                            Nợ phạt: {{ number_format($user->tien_phat_no, 0, ',', '.') }} đ
                                        </div>
                                    @endif
                                </td>
                                <td class="admin-user-rating">
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <span class="badge badge-subtle-{{ $lyLich['badge_class'] }} d-inline-flex align-items-center gap-1 px-2.5 py-1 rounded-pill fw-semibold shadow-sm" style="font-size: 0.8rem;">
                                            <i class="bi {{ $lyLich['icon'] }}"></i> {{ $lyLich['ten_muc_do'] }}
                                        </span>
                                        <div>
                                            @if ($lyLich['co_the_muon'])
                                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Đủ điều kiện mượn
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white rounded-pill px-2 py-0.5 fw-bold shadow-sm" style="font-size: 0.72rem;">
                                                    <i class="bi bi-x-circle-fill me-1"></i>Chặn mượn sách
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="admin-user-note-cell">
                                    @if ($user->ghi_chu_thu_thu)
                                        <div class="small p-2 bg-warning-subtle text-warning-emphasis rounded border border-warning-subtle admin-user-note text-break">
                                            <i class="bi bi-chat-left-quote me-1"></i>{{ $user->ghi_chu_thu_thu }}
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">— Không có</span>
                                    @endif
                                </td>
                                <td class="admin-user-profile-column text-end text-nowrap">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Xem hồ sơ lý lịch chi tiết">
                                        <i class="bi bi-file-earmark-person me-1"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="bi bi-person-x"></i></div>
                                        <h3 class="h6 fw-bold text-dark">Không tìm thấy độc giả phù hợp</h3>
                                        <p class="text-muted small mb-0">Thử tìm kiếm với từ khóa khác hoặc điều chỉnh mức độ lý lịch.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
