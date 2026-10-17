@extends('layouts.app')

@section('title', 'Phiếu phạt quá hạn của tôi')

@section('content')
<section class="py-4 py-lg-5">
    <div class="container">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <div class="section-label text-danger">
                    <i class="bi bi-cash-coin me-1"></i> Nghĩa vụ độc giả
                </div>
                <h1 class="page-heading h3 mb-1">Phiếu phạt quá hạn của tôi</h1>
                <p class="text-muted small mb-0">Theo dõi các khoản tiền phạt phát sinh do trả sách quá hạn quy định của thư viện.</p>
            </div>
            <a href="{{ route('user.phieu-muons.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-clock-history me-1"></i> Xem lịch sử mượn sách
            </a>
        </div>

        <!-- Fine Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-4">
                <div class="card border shadow-sm h-100 border-start border-danger border-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-danger fw-bold small text-uppercase">Tiền phạt cần thanh toán</span>
                            <span class="badge bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle-fill"></i></span>
                        </div>
                        <div class="display-6 fw-bold text-danger mb-1">
                            {{ number_format($tongChuaNop, 0, ',', '.') }} VNĐ
                        </div>
                        <p class="small text-muted mb-0">Vui lòng nộp trực tiếp tại bàn thủ thư</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border shadow-sm h-100 border-start border-success border-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-success fw-bold small text-uppercase">Tiền phạt đã thanh toán</span>
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill"></i></span>
                        </div>
                        <div class="display-6 fw-bold text-success mb-1">
                            {{ number_format($tongDaNop, 0, ',', '.') }} VNĐ
                        </div>
                        <p class="small text-muted mb-0">Đã hoàn thành nghĩa vụ tài chính</p>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-4">
                <div class="card border shadow-sm h-100 bg-light">
                    <div class="card-body p-3 p-md-4 small text-secondary">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="bi bi-info-circle-fill text-primary me-1"></i> Quy định mượn trả
                        </h6>
                        <ul class="mb-0 ps-3">
                            <li>Đơn giá phạt: <strong>5.000 VNĐ / ngày trễ hạn</strong>.</li>
                            <li>Độc giả còn nợ phạt chưa thể tiếp tục mượn thêm sách mới.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penalty List Card -->
        <div class="card border shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">
                    <i class="bi bi-list-check me-1 text-primary"></i> Danh sách các phiếu phạt vi phạm
                </span>
                <span class="badge badge-subtle-neutral">{{ $phieuPhats->total() }} phiếu ghi nhận</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Mã</th>
                                <th>Sách vi phạm</th>
                                <th>Lý do vi phạm</th>
                                <th>Số ngày trễ</th>
                                <th>Số tiền phạt</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Chi tiết mượn</th>
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
                                        <div class="fw-semibold text-dark">
                                            {{ $phieuPhat->phieuMuon->sach->ten_sach ?? 'Không xác định' }}
                                        </div>
                                        <div class="small text-muted">
                                            Phiếu mượn: <span class="font-monospace">#PM-{{ $phieuPhat->phieu_muon_id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="small text-secondary">{{ $phieuPhat->ly_do }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger fw-bold">
                                            {{ $phieuPhat->so_ngay_qua_han }} ngày
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
                                                <div class="small text-muted mt-1">{{ $phieuPhat->ngay_nop->format('d/m/Y') }}</div>
                                            @endif
                                        @else
                                            <span class="badge badge-subtle-danger d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-exclamation-triangle-fill"></i> Chưa thanh toán
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('user.phieu-muons.show', $phieuPhat->phieuMuon) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> Xem phiếu mượn
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-shield-check display-6 d-block mb-2 text-success"></i>
                                            <p class="mb-0 fw-medium">Bạn không có khoản phạt nào!</p>
                                            <small class="text-secondary">Cảm ơn bạn đã luôn trả sách đúng hạn quy định.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($phieuPhats->hasPages())
                    <div class="p-3 border-top d-flex justify-content-center">
                        {{ $phieuPhats->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
