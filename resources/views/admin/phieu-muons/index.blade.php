@extends('layouts.app')

@section('title', 'Quản lý phiếu mượn')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="section-label">
                <i class="bi bi-arrow-left-right me-1"></i> Nghiệp vụ lưu thông
            </div>
            <h1 class="page-heading h3 mb-1">Phiếu mượn – trả sách</h1>
            <p class="text-muted small mb-0">Theo dõi tiến độ lưu thông sách, hạn trả và xác nhận hoàn trả sách của độc giả.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.phieu-muons.export', ['q' => $keyword, 'trang_thai' => $trangThai]) }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Xuất CSV
            </a>
            <a href="{{ route('admin.phieu-muons.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Lập phiếu mượn mới
            </a>
        </div>
    </div>

    <!-- Filter Card (Gentelella Toolbox) -->
    <div class="card mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.phieu-muons.index') }}" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-icon-group">
                        <span class="input-icon"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo mã phiếu (#1), tên độc giả, email hoặc tên sách..." autocomplete="off">
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
                <i class="bi bi-card-checklist text-primary"></i>
                <span>Danh sách phiếu mượn</span>
            </h2>
            <span class="badge badge-subtle-primary rounded-pill">
                {{ $phieuMuons->total() }} phiếu
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 75px;">Mã</th>
                            <th>Độc giả</th>
                            <th>Sách mượn</th>
                            <th>Ngày mượn</th>
                            <th>Hạn trả</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
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
                                        <div class="user-avatar" style="width: 1.85rem; height: 1.85rem; font-size: 0.75rem;">
                                            {{ mb_strtoupper(mb_substr($phieuMuon->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $phieuMuon->user->name }}</div>
                                            <div class="small text-muted">{{ $phieuMuon->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="book-cover-thumbnail rounded overflow-hidden border">
                                            <x-book-cover :sach="$phieuMuon->sach" class="w-100" />
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.saches.show', $phieuMuon->sach) }}" class="fw-semibold text-dark text-decoration-none small">
                                                {{ $phieuMuon->sach->ten_sach }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="small text-secondary">{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</span></td>
                                <td>
                                    <span class="small fw-semibold {{ $phieuMuon->trangThaiHienThi() === 'qua_han' ? 'text-danger' : 'text-secondary' }}">
                                        {{ $phieuMuon->ngay_tra_du_kien->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td><x-loan-status :phieu-muon="$phieuMuon" /></td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.phieu-muons.show', $phieuMuon) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('admin.phieu-muons.edit', $phieuMuon) }}" class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i> Sửa
                                    </a>
                                    @if ($phieuMuon->trang_thai !== 'da_tra')
                                        <form action="{{ route('admin.phieu-muons.tra-sach', $phieuMuon) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-success" data-confirm="Xác nhận độc giả đã trả sách?">
                                                <i class="bi bi-check2"></i> Trả sách
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                                        <h3 class="h6 fw-bold text-dark">Không có phiếu mượn phù hợp</h3>
                                        <p class="text-muted small mb-0">Thử tìm kiếm với từ khóa khác hoặc điều chỉnh bộ lọc trạng thái.</p>
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
