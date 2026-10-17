@extends('layouts.app')

@section('title', 'Bảng điều khiển')

@section('content')
<section class="py-4 py-lg-5">
    <div class="container">
        <!-- Welcome Header Banner -->
        <div class="card border shadow-sm mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f2744 0%, #1e3a8a 100%);">
            <div class="card-body p-4 p-lg-5 text-white position-relative" style="z-index: 1;">
                <div class="row align-items-center gy-3">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 text-white-50 small mb-3">
                            <i class="bi {{ Auth::user()->isAdmin() ? 'bi-shield-check text-warning' : 'bi-person-check text-info' }}"></i>
                            <span class="text-white">{{ Auth::user()->isAdmin() ? 'Hệ thống Quản trị Thư viện' : 'Cổng thông tin Độc giả' }}</span>
                        </div>
                        <h1 class="h2 fw-bold text-white mb-2">
                            Xin chào, {{ Auth::user()->name }}!
                        </h1>
                        <p class="text-white-50 mb-0" style="max-width: 580px;">
                            {{ Auth::user()->isAdmin() ? 'Theo dõi tổng quan dữ liệu sách, độc giả và tiến độ xử lý phiếu mượn trả hôm nay.' : 'Quản lý thuận tiện danh sách sách đang mượn, hạn trả và xem lại toàn bộ lịch sử đọc của bạn.' }}
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('admin.phieu-muons.create') }}" class="btn btn-warning text-dark fw-bold shadow-sm">
                                    <i class="bi bi-plus-circle-fill me-1"></i> Lập phiếu mượn
                                </a>
                                <a href="{{ route('admin.saches.create') }}" class="btn btn-light text-primary fw-semibold shadow-sm">
                                    <i class="bi bi-book me-1"></i> Thêm sách mới
                                </a>
                            @else
                                <a href="{{ route('catalog.index') }}" class="btn btn-warning text-dark fw-bold shadow-sm">
                                    <i class="bi bi-search me-1"></i> Tra cứu kho sách
                                </a>
                                <a href="{{ route('user.phieu-muons.index') }}" class="btn btn-light text-primary fw-semibold shadow-sm">
                                    <i class="bi bi-clock-history me-1"></i> Lịch sử mượn
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrics Overview Section -->
        @if (Auth::user()->isAdmin())
            <div class="row g-3 mb-4">
                @php
                    $adminStats = [
                        [
                            'label' => 'Đầu sách',
                            'value' => $thongKe['sach'],
                            'icon' => 'bi-book-half',
                            'color' => '#1d4ed8',
                            'bg' => '#eff6ff',
                            'route' => route('admin.saches.index')
                        ],
                        [
                            'label' => 'Tác giả',
                            'value' => $thongKe['tac_gia'],
                            'icon' => 'bi-people-fill',
                            'color' => '#0284c7',
                            'bg' => '#f0f9ff',
                            'route' => route('admin.tac-gias.index')
                        ],
                        [
                            'label' => 'Thể loại',
                            'value' => $thongKe['the_loai'],
                            'icon' => 'bi-tags-fill',
                            'color' => '#d97706',
                            'bg' => '#fffbeb',
                            'route' => route('admin.the-loais.index')
                        ],
                        [
                            'label' => 'Độc giả',
                            'value' => $thongKe['doc_gia'],
                            'icon' => 'bi-person-badge-fill',
                            'color' => '#059669',
                            'bg' => '#ecfdf5',
                            'route' => route('admin.phieu-muons.index')
                        ],
                        [
                            'label' => 'Đang mượn',
                            'value' => $thongKe['dang_muon'],
                            'icon' => 'bi-arrow-left-right',
                            'color' => '#4f46e5',
                            'bg' => '#eef2ff',
                            'route' => route('admin.phieu-muons.index', ['trang_thai' => 'dang_muon'])
                        ],
                        [
                            'label' => 'Quá hạn',
                            'value' => $thongKe['qua_han'],
                            'icon' => 'bi-exclamation-triangle-fill',
                            'color' => '#dc2626',
                            'bg' => '#fef2f2',
                            'route' => route('admin.phieu-muons.index', ['trang_thai' => 'qua_han'])
                        ],
                    ];
                @endphp
                @foreach ($adminStats as $item)
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ $item['route'] }}" class="text-decoration-none">
                            <div class="card stat-card h-100 p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="stat-icon-wrapper" style="background-color: {{ $item['bg'] }}; color: {{ $item['color'] }}; width: 2.75rem; height: 2.75rem; font-size: 1.25rem;">
                                        <i class="bi {{ $item['icon'] }}"></i>
                                    </div>
                                    <i class="bi bi-arrow-up-right text-muted small"></i>
                                </div>
                                <div class="stat-value">{{ $item['value'] }}</div>
                                <div class="stat-label text-truncate">{{ $item['label'] }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
                <div class="col-12">
                    <div class="card border shadow-sm bg-white overflow-hidden">
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="stat-icon-wrapper" style="background-color: #fef2f2; color: #dc2626; width: 3rem; height: 3rem; font-size: 1.5rem;">
                                            <i class="bi bi-cash-coin"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small fw-semibold">Quản lý phiếu phạt vi phạm</div>
                                            <div class="h5 fw-bold text-dark mb-0">{{ $thongKe['phieu_phat'] }} phiếu ghi nhận</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="small text-danger fw-semibold">Tiền phạt chưa thu:</div>
                                    <div class="h5 fw-bold text-danger mb-0">{{ number_format($thongKe['tien_phat_chua_thu'], 0, ',', '.') }} đ</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="small text-success fw-semibold">Tiền phạt đã thu:</div>
                                    <div class="h5 fw-bold text-success mb-0">{{ number_format($thongKe['tien_phat_da_thu'], 0, ',', '.') }} đ</div>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <a href="{{ route('admin.phieu-phats.index') }}" class="btn btn-sm btn-outline-danger shadow-sm">
                                        Chi tiết phạt <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-3 mb-4">
                @php
                    $userStats = [
                        [
                            'label' => 'Tổng lượt mượn',
                            'value' => $thongKe['tong'],
                            'icon' => 'bi-collection-play-fill',
                            'color' => '#1d4ed8',
                            'bg' => '#eff6ff'
                        ],
                        [
                            'label' => 'Sách đang giữ',
                            'value' => $thongKe['dang_muon'],
                            'icon' => 'bi-book-half',
                            'color' => '#0284c7',
                            'bg' => '#f0f9ff'
                        ],
                        [
                            'label' => 'Đã trả hoàn tất',
                            'value' => $thongKe['da_tra'],
                            'icon' => 'bi-check2-circle',
                            'color' => '#059669',
                            'bg' => '#ecfdf5'
                        ],
                        [
                            'label' => 'Lượt quá hạn',
                            'value' => $thongKe['qua_han'],
                            'icon' => 'bi-exclamation-octagon-fill',
                            'color' => '#dc2626',
                            'bg' => '#fef2f2'
                        ],
                    ];
                @endphp
                @foreach ($userStats as $item)
                    <div class="col-6 col-lg-3">
                        <div class="card stat-card h-100 p-3 p-lg-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper flex-shrink-0" style="background-color: {{ $item['bg'] }}; color: {{ $item['color'] }};">
                                    <i class="bi {{ $item['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="stat-value">{{ $item['value'] }}</div>
                                    <div class="stat-label">{{ $item['label'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if (($thongKe['tien_phat_chua_nop'] ?? 0) > 0)
                    <div class="col-12">
                        <div class="alert alert-danger d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-0 rounded-3 shadow-sm border-0 bg-danger-subtle text-danger-emphasis">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-exclamation-triangle-fill fs-3 text-danger"></i>
                                <div>
                                    <div class="fw-bold">Bạn có khoản tiền phạt quá hạn cần thanh toán!</div>
                                    <div class="small">Số tiền phạt trả sách trễ hạn chưa nộp: <strong class="text-danger fs-6">{{ number_format($thongKe['tien_phat_chua_nop'], 0, ',', '.') }} VNĐ</strong>.</div>
                                </div>
                            </div>
                            <a href="{{ route('user.phieu-phats.index') }}" class="btn btn-danger btn-sm text-nowrap shadow-sm">
                                <i class="bi bi-cash-coin me-1"></i> Xem chi tiết phiếu phạt
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Recent Loans Table Card -->
        <div class="card border shadow-sm">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="brand-mark" style="width: 1.85rem; height: 1.85rem; font-size: 0.9rem;">
                        <i class="bi bi-clock-history"></i>
                    </span>
                    <h2 class="h5 fw-bold mb-0 text-dark">Phiếu mượn gần đây</h2>
                </div>
                <a href="{{ Auth::user()->isAdmin() ? route('admin.phieu-muons.index') : route('user.phieu-muons.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <span>Xem tất cả</span>
                    <i class="bi bi-chevron-right small ms-1"></i>
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Mã</th>
                                @if (Auth::user()->isAdmin())
                                    <th>Độc giả</th>
                                @endif
                                <th>Tên sách mượn</th>
                                <th>Ngày mượn</th>
                                <th>Hạn trả</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($phieuMuons as $phieuMuon)
                                <tr>
                                    <td>
                                        <span class="badge badge-subtle-neutral fw-bold font-monospace">
                                            #{{ $phieuMuon->id }}
                                        </span>
                                    </td>
                                    @if (Auth::user()->isAdmin())
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="user-avatar" style="width: 2rem; height: 2rem; font-size: 0.75rem;">
                                                    {{ mb_strtoupper(mb_substr($phieuMuon->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $phieuMuon->user->name }}</div>
                                                    <div class="small text-muted">{{ $phieuMuon->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="book-cover-thumbnail rounded overflow-hidden border">
                                                <x-book-cover :sach="$phieuMuon->sach" class="w-100" />
                                            </div>
                                            <div>
                                                <a href="{{ Auth::user()->isAdmin() ? route('admin.saches.show', $phieuMuon->sach) : route('catalog.show', $phieuMuon->sach) }}" class="fw-semibold text-dark text-decoration-none">
                                                    {{ $phieuMuon->sach->ten_sach }}
                                                </a>
                                                <div class="small text-muted">{{ $phieuMuon->sach->theLoai->ten_the_loai }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-secondary small">{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold small {{ $phieuMuon->trangThaiHienThi() === 'qua_han' ? 'text-danger' : 'text-secondary' }}">
                                            {{ $phieuMuon->ngay_tra_du_kien->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <x-loan-status :phieu-muon="$phieuMuon" />
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ Auth::user()->isAdmin() ? route('admin.phieu-muons.show', $phieuMuon) : route('user.phieu-muons.show', $phieuMuon) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            Xem
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isAdmin() ? 7 : 6 }}" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="bi bi-inbox"></i>
                                            </div>
                                            <h3 class="h6 fw-bold text-dark">Chưa có phiếu mượn nào</h3>
                                            <p class="text-muted small mb-0">Hệ thống chưa ghi nhận lượt mượn sách nào trong cơ sở dữ liệu.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
