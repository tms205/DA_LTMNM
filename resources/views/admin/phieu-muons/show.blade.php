@extends('layouts.app')

@section('title', 'Phiếu mượn #'.$phieuMuon->id)

@section('content')
<div class="container-fluid px-0" style="max-width: 1000px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.phieu-muons.index') }}">Phiếu mượn - trả</a></li>
            <li class="breadcrumb-item active">Phiếu #{{ $phieuMuon->id }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="section-label mb-1">
                <i class="bi bi-file-earmark-text me-1"></i> Chi tiết nghiệp vụ
            </div>
            <div class="d-flex align-items-center gap-3">
                <h1 class="page-heading h3 mb-0">Phiếu mượn #{{ $phieuMuon->id }}</h1>
                <x-loan-status :phieu-muon="$phieuMuon" />
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.phieu-muons.edit', $phieuMuon) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i> Chỉnh sửa
            </a>
            @if ($phieuMuon->trang_thai !== 'da_tra')
                <form action="{{ route('admin.phieu-muons.tra-sach', $phieuMuon) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success btn-sm shadow-sm" data-confirm="Xác nhận độc giả đã trả sách?">
                        <i class="bi bi-check2-circle me-1"></i> Xác nhận trả sách
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Content Grid -->
    <div class="row g-4 mb-4">
        <!-- Reader and Loan Info -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header py-3 px-4">
                    <h2 class="card-header-title">
                        <i class="bi bi-person-lines-fill me-1 text-primary"></i>
                        <span>Thông tin mượn trả</span>
                    </h2>
                </div>
                <div class="card-body p-4">
                    <dl class="row mb-0 gy-3">
                        <dt class="col-sm-4 text-muted small fw-semibold">Độc giả mượn</dt>
                        <dd class="col-sm-8 mb-0">
                            <div class="fw-bold text-dark">{{ $phieuMuon->user->name }}</div>
                            <div class="text-secondary small">{{ $phieuMuon->user->email }}</div>
                        </dd>

                        <dt class="col-sm-4 text-muted small fw-semibold">Ngày mượn</dt>
                        <dd class="col-sm-8 mb-0">
                            <span class="badge badge-subtle-neutral text-dark font-monospace">
                                {{ $phieuMuon->ngay_muon->format('d/m/Y') }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-muted small fw-semibold">Hạn trả dự kiến</dt>
                        <dd class="col-sm-8 mb-0">
                            <span class="badge {{ $phieuMuon->trangThaiHienThi() === 'qua_han' ? 'badge-subtle-danger' : 'badge-subtle-primary' }} font-monospace">
                                {{ $phieuMuon->ngay_tra_du_kien->format('d/m/Y') }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-muted small fw-semibold">Ngày trả thực tế</dt>
                        <dd class="col-sm-8 mb-0">
                            @if ($phieuMuon->ngay_tra_thuc_te)
                                <span class="badge badge-subtle-success font-monospace">
                                    {{ $phieuMuon->ngay_tra_thuc_te->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">Chưa hoàn trả</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted small fw-semibold">Ghi chú nghiệp vụ</dt>
                        <dd class="col-sm-8 mb-0">
                            <div class="p-2 bg-light rounded border small text-secondary">
                                {{ $phieuMuon->ghi_chu ?: 'Không có ghi chú lưu ý.' }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Book Info -->
        <div class="col-lg-5">
            <div class="card h-100 overflow-hidden">
                <div class="card-header py-3 px-4">
                    <h2 class="card-header-title">
                        <i class="bi bi-book me-1 text-primary"></i>
                        <span>Cuốn sách được mượn</span>
                    </h2>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex gap-3 mb-3">
                        <div style="width: 75px; flex: 0 0 75px;" class="rounded overflow-hidden border shadow-sm">
                            <x-book-cover :sach="$phieuMuon->sach" class="w-100" />
                        </div>
                        <div>
                            <span class="badge badge-subtle-primary rounded-pill small mb-1">
                                {{ $phieuMuon->sach->theLoai->ten_the_loai }}
                            </span>
                            <h3 class="h6 fw-bold mb-1 text-dark">{{ $phieuMuon->sach->ten_sach }}</h3>
                            <div class="small text-muted mb-2">
                                {{ $phieuMuon->sach->tacGias->pluck('ten_tac_gia')->join(', ') }}
                            </div>
                            <div class="small text-muted">
                                Tồn kho: <span class="fw-bold {{ $phieuMuon->sach->so_luong_con_lai > 0 ? 'text-success' : 'text-danger' }}">{{ $phieuMuon->sach->so_luong_con_lai }}/{{ $phieuMuon->sach->so_luong }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-3 border-top">
                        <a href="{{ route('admin.saches.show', $phieuMuon->sach) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Xem thông tin sách
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Footer -->
    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
        <a href="{{ route('admin.phieu-muons.index') }}" class="btn btn-link text-muted px-0">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>

        <form action="{{ route('admin.phieu-muons.destroy', $phieuMuon) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger btn-sm" data-confirm="Bạn chắc chắn muốn xóa phiếu mượn này?">
                <i class="bi bi-trash3 me-1"></i> Xóa phiếu mượn
            </button>
        </form>
    </div>
</div>
@endsection
