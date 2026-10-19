@extends('layouts.app')

@section('title', 'Chi tiết phiếu phạt #PP-' . $phieuPhat->id)

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb py-1 px-3 bg-white border rounded-2 shadow-sm small mb-0 d-inline-flex">
                    <li class="breadcrumb-item"><a href="{{ route('admin.phieu-phats.index') }}">Quản lý phiếu phạt</a></li>
                    <li class="breadcrumb-item active">#PP-{{ $phieuPhat->id }}</li>
                </ol>
            </nav>
            <h1 class="page-heading h3 mb-1">
                Phiếu phạt vi phạm #PP-{{ $phieuPhat->id }}
            </h1>
            <p class="text-muted small mb-0">Lập vào lúc: {{ $phieuPhat->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print();">
                <i class="bi bi-printer me-1"></i> In biên lai phạt
            </button>
        </div>
    </div>

        <div class="row g-4">
            <!-- Left Column: Fine Details & Payment Confirmation -->
            <div class="col-lg-7">
                <div class="card border shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">
                            <i class="bi bi-cash-stack text-danger me-1"></i> Quyết định xử phạt vi phạm
                        </span>
                        @if ($phieuPhat->isDaNop())
                            <span class="badge badge-subtle-success fs-6 px-3 py-2">
                                <i class="bi bi-check-circle-fill me-1"></i> Đã nộp phạt
                            </span>
                        @else
                            <span class="badge badge-subtle-danger fs-6 px-3 py-2">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Chưa nộp tiền
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="p-3 bg-danger-subtle rounded-3 text-center mb-4">
                            <div class="text-danger-emphasis small text-uppercase fw-bold letter-spacing-1">Tổng tiền phạt vi phạm</div>
                            <div class="display-6 fw-bold text-danger my-1">
                                {{ number_format($phieuPhat->tong_tien_phat, 0, ',', '.') }} VNĐ
                            </div>
                            <div class="small text-muted">
                                Mức phạt quy định: <strong>{{ number_format($phieuPhat->tien_phat_moi_ngay, 0, ',', '.') }} đ</strong> / ngày quá hạn
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="bg-light" style="width: 35%;">Lý do xử phạt</th>
                                        <td class="fw-semibold text-danger">{{ $phieuPhat->ly_do }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Số ngày quá hạn</th>
                                        <td>
                                            <span class="badge bg-danger fs-6 px-2 py-1">
                                                {{ $phieuPhat->so_ngay_qua_han }} ngày trễ
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Trạng thái thanh toán</th>
                                        <td>
                                            @if ($phieuPhat->isDaNop())
                                                <span class="text-success fw-bold">
                                                    <i class="bi bi-check2-circle me-1"></i> Đã thanh toán đầy đủ
                                                </span>
                                            @else
                                                <span class="text-danger fw-bold">
                                                    <i class="bi bi-x-circle me-1"></i> Chưa thu tiền phạt
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($phieuPhat->isDaNop())
                                        <tr>
                                            <th class="bg-light">Thời điểm nộp</th>
                                            <td>{{ $phieuPhat->ngay_nop ? $phieuPhat->ngay_nop->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Người xác nhận thu</th>
                                            <td>{{ $phieuPhat->nguoiThu->name ?? 'Hệ thống tự động' }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="bg-light">Ghi chú</th>
                                        <td>{{ $phieuPhat->ghi_chu ?: 'Không có ghi chú' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if ($phieuPhat->isChuaNop())
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold text-dark mb-3">Xác nhận thu nộp phạt</h6>
                                <form method="POST" action="{{ route('admin.phieu-phats.xac-nhan', $phieuPhat) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="ghi_chu" class="form-label small fw-semibold">Ghi chú xác nhận thu tiền:</label>
                                        <input type="text" name="ghi_chu" id="ghi_chu" class="form-control" placeholder="Ví dụ: Đã thu tiền mặt 15.000 đ tại quầy thủ thư">
                                    </div>
                                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i> Xác nhận đã nhận đủ tiền phạt
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Reader and Loan Information -->
            <div class="col-lg-5">
                <!-- Reader Card -->
                <div class="card border shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <span class="fw-bold text-dark">
                            <i class="bi bi-person-fill text-primary me-1"></i> Thông tin độc giả
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="user-avatar" style="width: 3rem; height: 3rem; font-size: 1.25rem;">
                                {{ mb_strtoupper(mb_substr($phieuPhat->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $phieuPhat->user->name }}</h5>
                                <span class="badge badge-subtle-primary small">Độc giả thư viện</span>
                            </div>
                        </div>

                        <ul class="list-unstyled small mb-0 d-flex flex-column gap-2 text-secondary">
                            <li><i class="bi bi-envelope me-2 text-primary"></i> {{ $phieuPhat->user->email }}</li>
                            @if ($phieuPhat->user->so_dien_thoai)
                                <li><i class="bi bi-telephone me-2 text-primary"></i> {{ $phieuPhat->user->so_dien_thoai }}</li>
                            @endif
                            @if ($phieuPhat->user->dia_chi)
                                <li><i class="bi bi-geo-alt me-2 text-primary"></i> {{ $phieuPhat->user->dia_chi }}</li>
                            @endif
                            @if ($phieuPhat->user->ngay_sinh)
                                <li><i class="bi bi-calendar me-2 text-primary"></i> {{ $phieuPhat->user->ngay_sinh->format('d/m/Y') }}</li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Loan Reference Card -->
                <div class="card border shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">
                            <i class="bi bi-book-half text-success me-1"></i> Thông tin phiếu mượn gốc
                        </span>
                        <a href="{{ route('admin.phieu-muons.show', $phieuPhat->phieuMuon) }}" class="small text-decoration-none">
                            Xem phiếu #{{ $phieuPhat->phieu_muon_id }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-1">
                            {{ $phieuPhat->phieuMuon->sach->ten_sach ?? 'Không xác định' }}
                        </h6>
                        <p class="small text-muted mb-3">Mã phiếu mượn: #PM-{{ $phieuPhat->phieu_muon_id }}</p>

                        <div class="table-responsive small">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 45%;">Ngày mượn:</td>
                                    <td class="fw-semibold">{{ $phieuPhat->phieuMuon->ngay_muon->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Hạn trả dự kiến:</td>
                                    <td class="fw-bold text-danger">{{ $phieuPhat->phieuMuon->ngay_tra_du_kien->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Ngày trả thực tế:</td>
                                    <td>
                                        @if ($phieuPhat->phieuMuon->ngay_tra_thuc_te)
                                            <span class="fw-semibold text-dark">{{ $phieuPhat->phieuMuon->ngay_tra_thuc_te->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-danger fw-bold">Chưa trả sách</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Trạng thái mượn:</td>
                                    <td>
                                        @if ($phieuPhat->phieuMuon->trang_thai === 'da_tra')
                                            <span class="badge badge-subtle-success">Đã trả</span>
                                        @else
                                            <span class="badge badge-subtle-danger">Quá hạn chưa trả</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
