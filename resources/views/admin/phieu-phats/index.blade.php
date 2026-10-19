@extends('layouts.app')

@section('title', 'Quản lý phiếu phạt quá hạn')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="section-label text-danger">
                <i class="bi bi-exclamation-octagon-fill me-1"></i> Xử lý vi phạm
            </div>
            <h1 class="page-heading h3 mb-1">Quản lý phiếu phạt quá hạn</h1>
            <p class="text-muted small mb-0">Theo dõi, phát sinh và thu tiền nộp phạt đối với độc giả trả sách trễ hạn quy định.</p>
        </div>
        <a href="{{ route('admin.phieu-muons.index', ['trang_thai' => 'qua_han']) }}" class="btn btn-outline-danger">
            <i class="bi bi-clock-history me-1"></i> Xem các phiếu mượn quá hạn
        </a>
    </div>

    <!-- Metric Cards (Gentelella Tiles) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="text-muted small mb-1 fw-semibold">Tổng số phiếu phạt</div>
                <div class="stat-value">{{ number_format($stats['tong_so']) }}</div>
                <div class="small text-muted mt-1">Ghi nhận trong hệ thống</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card border-start border-danger border-3">
                <div class="text-danger small mb-1 fw-bold">Chưa nộp phạt</div>
                <div class="stat-value text-danger">{{ number_format($stats['chua_nop_so']) }}</div>
                <div class="small text-muted mt-1">Đang chờ độc giả thanh toán</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card border-start border-warning border-3">
                <div class="text-warning-emphasis small mb-1 fw-bold">Tiền phạt chưa thu</div>
                <div class="stat-value text-danger fs-3">{{ number_format($stats['chua_nop_tien'], 0, ',', '.') }} đ</div>
                <div class="small text-muted mt-1">5.000 VNĐ / ngày trễ</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card border-start border-success border-3">
                <div class="text-success small mb-1 fw-bold">Tiền phạt đã thu</div>
                <div class="stat-value text-success fs-3">{{ number_format($stats['da_nop_tien'], 0, ',', '.') }} đ</div>
                <div class="small text-muted mt-1">Đã vào quỹ thư viện</div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.phieu-phats.index') }}" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-icon-group">
                        <span class="input-icon"><i class="bi bi-search"></i></span>
                        <input type="search" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên độc giả, email, SĐT hoặc tên sách..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả trạng thái phiếu</option>
                        <option value="chua_nop" @selected($trangThai === 'chua_nop')>Chưa nộp phạt</option>
                        <option value="da_nop" @selected($trangThai === 'da_nop')>Đã nộp phạt</option>
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

    <!-- Penalties Table Card -->
    <div class="card">
        <div class="card-header py-3 px-3 px-md-4">
            <h2 class="card-header-title">
                <i class="bi bi-cash-coin text-danger"></i>
                <span>Danh sách phiếu phạt</span>
            </h2>
            <span class="badge badge-subtle-danger rounded-pill">
                {{ $phieuPhats->total() }} phiếu
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Mã</th>
                            <th>Độc giả</th>
                            <th>Sách vi phạm</th>
                            <th>Số ngày trễ</th>
                            <th>Tiền phạt</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($phieuPhats as $phieuPhat)
                            <tr>
                                <td>
                                    <span class="badge badge-subtle-neutral fw-bold font-monospace">
                                        #PP-{{ $phieuPhat->id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="width: 2rem; height: 2rem; font-size: 0.75rem;">
                                            {{ mb_strtoupper(mb_substr($phieuPhat->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $phieuPhat->user->name }}</div>
                                            <div class="small text-muted">{{ $phieuPhat->user->email }}</div>
                                            @if($phieuPhat->user->so_dien_thoai)
                                                <div class="small text-muted font-monospace"><i class="bi bi-telephone me-1"></i>{{ $phieuPhat->user->so_dien_thoai }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href="{{ route('admin.phieu-muons.show', $phieuPhat->phieuMuon) }}" class="fw-semibold text-dark text-decoration-none small">
                                            {{ $phieuPhat->phieuMuon->sach->ten_sach ?? 'Không xác định' }}
                                        </a>
                                        <div class="small text-muted">
                                            Mã mượn: <span class="font-monospace">#PM-{{ $phieuPhat->phieu_muon_id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-subtle-danger fw-bold px-2 py-1">
                                        <i class="bi bi-clock me-1"></i>{{ $phieuPhat->so_ngay_qua_han }} ngày
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-danger">
                                        {{ number_format($phieuPhat->tong_tien_phat, 0, ',', '.') }} đ
                                    </div>
                                    <div class="small text-muted">5.000 đ/ngày</div>
                                </td>
                                <td>
                                    @if ($phieuPhat->isDaNop())
                                        <span class="badge badge-subtle-success d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-check-circle-fill"></i> Đã nộp phạt
                                        </span>
                                        @if($phieuPhat->ngay_nop)
                                            <div class="small text-muted mt-1">{{ $phieuPhat->ngay_nop->format('d/m/Y H:i') }}</div>
                                        @endif
                                    @else
                                        <span class="badge badge-subtle-danger d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-exclamation-circle-fill"></i> Chưa nộp
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('admin.phieu-phats.show', $phieuPhat) }}" class="btn btn-sm btn-outline-secondary" title="Xem chi tiết phiếu phạt">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if ($phieuPhat->isChuaNop())
                                            <button type="button" class="btn btn-sm btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNopPhat{{ $phieuPhat->id }}">
                                                <i class="bi bi-cash-coin me-1"></i> Thu tiền
                                            </button>

                                            <!-- Modal Xác nhận thu tiền phạt -->
                                            <div class="modal fade text-start" id="modalNopPhat{{ $phieuPhat->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $phieuPhat->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <form method="POST" action="{{ route('admin.phieu-phats.xac-nhan', $phieuPhat) }}">
                                                            @csrf
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title fw-bold" id="modalLabel{{ $phieuPhat->id }}">
                                                                    <i class="bi bi-cash-stack me-1"></i> Xác nhận thu tiền phạt #PP-{{ $phieuPhat->id }}
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="alert alert-light border mb-3">
                                                                    <div class="d-flex justify-content-between mb-1">
                                                                        <span class="text-muted">Độc giả:</span>
                                                                        <strong class="text-dark">{{ $phieuPhat->user->name }}</strong>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between mb-1">
                                                                        <span class="text-muted">Sách quá hạn:</span>
                                                                        <strong class="text-dark">{{ $phieuPhat->phieuMuon->sach->ten_sach ?? '' }}</strong>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between mb-1">
                                                                        <span class="text-muted">Số ngày trễ:</span>
                                                                        <span class="badge bg-danger">{{ $phieuPhat->so_ngay_qua_han }} ngày</span>
                                                                    </div>
                                                                    <hr class="my-2">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span class="fw-semibold">Số tiền cần thu:</span>
                                                                        <span class="fs-4 fw-bold text-danger">{{ number_format($phieuPhat->tong_tien_phat, 0, ',', '.') }} VNĐ</span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="ghi_chu_{{ $phieuPhat->id }}" class="form-label small fw-semibold">Ghi chú xác nhận thu tiền (tùy chọn):</label>
                                                                    <input type="text" name="ghi_chu" id="ghi_chu_{{ $phieuPhat->id }}" class="form-control" placeholder="Ví dụ: Đã nộp tiền mặt tại quầy thủ thư">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-success fw-semibold">
                                                                    <i class="bi bi-check2-circle me-1"></i> Xác nhận đã nhận tiền
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('admin.phieu-phats.destroy', $phieuPhat) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phiếu phạt này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa phiếu phạt">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-shield-check display-6 d-block mb-2 text-success"></i>
                                        <p class="mb-0 fw-medium">Không có phiếu phạt nào được tìm thấy.</p>
                                        <small class="text-secondary">Tất cả độc giả đang chấp hành tốt thời hạn mượn sách!</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($phieuPhats->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $phieuPhats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
