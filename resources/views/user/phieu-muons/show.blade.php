@extends('layouts.app')

@section('title', 'Chi tiết lượt mượn #'.$phieuMuon->id)

@section('content')
<div class="container-fluid px-0" style="max-width: 950px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('user.phieu-muons.index') }}">Lịch sử mượn sách</a></li>
            <li class="breadcrumb-item active">Phiếu mượn #{{ $phieuMuon->id }}</li>
        </ol>
    </nav>

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <div class="section-label mb-1">
                    <i class="bi bi-ticket-detailed me-1"></i> Thông tin phiếu mượn
                </div>
                <h1 class="page-heading h3 mb-0">{{ $phieuMuon->sach->ten_sach }}</h1>
            </div>
            <x-loan-status :phieu-muon="$phieuMuon" />
        </div>

        <!-- Loan Pass Card -->
        <div class="card overflow-hidden border shadow-sm">
            <div class="row g-0">
                <div class="col-md-4 bg-light d-flex align-items-center justify-content-center p-4 border-end">
                    <div style="max-width: 200px;" class="w-100 shadow rounded-3 overflow-hidden border">
                        <x-book-cover :sach="$phieuMuon->sach" class="w-100" />
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-body p-4 p-lg-5">
                        <div class="mb-4 pb-3 border-bottom">
                            <span class="badge badge-subtle-primary rounded-pill small mb-2">
                                {{ $phieuMuon->sach->theLoai->ten_the_loai }}
                            </span>
                            <div class="text-secondary small">
                                Tác giả: <strong class="text-dark">{{ $phieuMuon->sach->tacGias->pluck('ten_tac_gia')->join(', ') ?: 'Chưa rõ tác giả' }}</strong>
                            </div>
                        </div>

                        <dl class="row mb-0 gy-3">
                            <dt class="col-sm-5 text-muted small fw-semibold">Mã số phiếu</dt>
                            <dd class="col-sm-7 mb-0">
                                <span class="badge badge-subtle-neutral font-monospace">#{{ $phieuMuon->id }}</span>
                            </dd>

                            <dt class="col-sm-5 text-muted small fw-semibold">Ngày nhận sách</dt>
                            <dd class="col-sm-7 mb-0">
                                <span class="text-dark fw-semibold">{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</span>
                            </dd>

                            <dt class="col-sm-5 text-muted small fw-semibold">Hạn hoàn trả</dt>
                            <dd class="col-sm-7 mb-0">
                                <span class="fw-bold {{ $phieuMuon->trangThaiHienThi() === 'qua_han' ? 'text-danger' : 'text-primary' }}">
                                    {{ $phieuMuon->ngay_tra_du_kien->format('d/m/Y') }}
                                </span>
                            </dd>

                            <dt class="col-sm-5 text-muted small fw-semibold">Ngày trả thực tế</dt>
                            <dd class="col-sm-7 mb-0">
                                @if ($phieuMuon->ngay_tra_thuc_te)
                                    <span class="badge badge-subtle-success font-monospace">
                                        {{ $phieuMuon->ngay_tra_thuc_te->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">Đang trong thời gian mượn</span>
                                @endif
                            </dd>

                            <dt class="col-sm-5 text-muted small fw-semibold">Ghi chú từ thủ thư</dt>
                            <dd class="col-sm-7 mb-0">
                                <div class="p-2 bg-light rounded border small text-secondary">
                                    {{ $phieuMuon->ghi_chu ?: 'Không có ghi chú.' }}
                                </div>
                            </dd>
                        </dl>

                        @if ($phieuMuon->phieuPhat)
                            <div class="alert @if($phieuMuon->phieuPhat->isChuaNop()) alert-danger @else alert-success @endif rounded-3 p-3 mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold mb-0">
                                        <i class="bi @if($phieuMuon->phieuPhat->isChuaNop()) bi-exclamation-octagon-fill @else bi-check-circle-fill @endif me-1"></i>
                                        Phiếu phạt vi phạm mượn sách #PP-{{ $phieuMuon->phieuPhat->id }}
                                    </h6>
                                    @if ($phieuMuon->phieuPhat->isChuaNop())
                                        <span class="badge bg-danger">Chưa nộp tiền</span>
                                    @else
                                        <span class="badge bg-success">Đã nộp phạt</span>
                                    @endif
                                </div>
                                <p class="small mb-1">{{ $phieuMuon->phieuPhat->ly_do }}</p>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-opacity-25 border-dark">
                                    <span class="small">Mức phạt ({{ $phieuMuon->phieuPhat->so_ngay_qua_han }} ngày quá hạn &times; 5.000 đ):</span>
                                    <span class="fw-bold fs-5">{{ number_format($phieuMuon->phieuPhat->tong_tien_phat, 0, ',', '.') }} VNĐ</span>
                                </div>
                                @if ($phieuMuon->phieuPhat->isChuaNop())
                                    <div class="small text-danger mt-2 fst-italic">
                                        <i class="bi bi-info-circle me-1"></i> Vui lòng liên hệ quầy thủ thư để nộp phạt và tiếp tục mượn sách.
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <a href="{{ route('catalog.show', $phieuMuon->sach) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-book me-1"></i> Xem thông tin sách này
                            </a>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
