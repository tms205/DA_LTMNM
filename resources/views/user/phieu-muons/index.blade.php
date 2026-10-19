@extends('layouts.app')

@section('title', 'Lịch sử mượn sách của tôi')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="section-label">
                <i class="bi bi-person-circle me-1"></i> Tài khoản cá nhân
            </div>
            <h1 class="page-heading h3 mb-1">Lịch sử mượn sách</h1>
            <p class="text-muted small mb-0">Theo dõi chi tiết các lượt mượn sách, ngày đến hạn hoàn trả và lịch sử đọc cá nhân.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('user.phieu-phats.index') }}" class="btn btn-outline-danger">
                <i class="bi bi-cash-coin me-1"></i> Tra cứu phiếu phạt
            </a>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-search me-1"></i> Tìm thêm sách mới
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('user.phieu-muons.index') }}" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-icon-group">
                        <span class="input-icon"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo mã phiếu (#1), tên sách, tác giả..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="dang_muon" @selected($trangThai === 'dang_muon')>Đang mượn</option>
                        <option value="qua_han" @selected($trangThai === 'qua_han')>Quá hạn</option>
                        <option value="da_tra" @selected($trangThai === 'da_tra')>Đã trả</option>
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

    <!-- Loans Table Card -->
    <div class="card">
        <div class="card-header py-3 px-3 px-md-4">
            <h2 class="card-header-title">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Danh sách lượt mượn</span>
            </h2>
            <span class="badge badge-subtle-primary rounded-pill">
                {{ $phieuMuons->total() }} lượt
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Mã</th>
                            <th>Cuốn sách</th>
                            <th>Ngày mượn</th>
                            <th>Hạn trả</th>
                            <th>Ngày trả</th>
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
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="book-cover-thumbnail">
                                            <x-book-cover :sach="$phieuMuon->sach" class="w-100" />
                                        </div>
                                        <div>
                                            <a href="{{ route('catalog.show', $phieuMuon->sach) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $phieuMuon->sach->ten_sach }}
                                            </a>
                                            <div class="small text-muted">
                                                {{ $phieuMuon->sach->theLoai->ten_the_loai }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="small text-secondary">{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</span></td>
                                <td>
                                    <span class="small fw-semibold {{ $phieuMuon->trangThaiHienThi() === 'qua_han' ? 'text-danger' : 'text-secondary' }}">
                                        {{ $phieuMuon->ngay_tra_du_kien->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($phieuMuon->ngay_tra_thuc_te)
                                        <span class="small text-success fw-semibold">{{ $phieuMuon->ngay_tra_thuc_te->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <x-loan-status :phieu-muon="$phieuMuon" />
                                    @if ($phieuMuon->phieuPhat)
                                        <div class="mt-1">
                                            @if ($phieuMuon->phieuPhat->isChuaNop())
                                                <span class="badge bg-danger-subtle text-danger" title="Tiền phạt chưa nộp">
                                                    <i class="bi bi-exclamation-circle-fill me-1"></i>Phạt: {{ number_format($phieuMuon->phieuPhat->tong_tien_phat, 0, ',', '.') }} đ
                                                </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Đã nộp phạt
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('user.phieu-muons.show', $phieuMuon) }}" class="btn btn-sm btn-outline-primary">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="bi bi-clock-history"></i></div>
                                        <h3 class="h6 fw-bold text-dark">Bạn chưa có lượt mượn nào</h3>
                                        <p class="text-muted small mb-3">Hãy dạo quanh kho sách để tìm những cuốn sách bổ ích cho bạn nhé.</p>
                                        <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-search me-1"></i> Khám phá kho sách
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($phieuMuons->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $phieuMuons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
