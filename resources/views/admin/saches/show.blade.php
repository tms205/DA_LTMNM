@extends('layouts.app')

@section('title', $sach->ten_sach)

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0 py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.saches.index') }}" class="text-decoration-none">Quản lý sách</a></li>
            <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 400px;">{{ $sach->ten_sach }}</li>
        </ol>
    </nav>

    <!-- Book Detail Card (Gentelella x_panel) -->
    <div class="card overflow-hidden mb-4">
        <div class="row g-0">
            <div class="col-md-4 col-lg-3 bg-light d-flex align-items-center justify-content-center p-4 border-end">
                <div style="max-width: 200px;" class="w-100 shadow-sm rounded-2 overflow-hidden border">
                    <x-book-cover :sach="$sach" class="w-100" />
                </div>
            </div>
            <div class="col-md-8 col-lg-9">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-3">
                        <div>
                            <span class="badge badge-subtle-primary rounded-pill px-3 py-1 mb-2">
                                {{ $sach->theLoai->ten_the_loai }}
                            </span>
                            <h1 class="page-heading h3 mb-2 text-dark">{{ $sach->ten_sach }}</h1>
                            <p class="text-secondary small mb-0">
                                <i class="bi bi-person me-1"></i> Tác giả: 
                                <strong class="text-dark">{{ $sach->tacGias->pluck('ten_tac_gia')->join(', ') ?: 'Chưa rõ tác giả' }}</strong>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.saches.edit', $sach) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.phieu-muons.create', ['sach_id' => $sach->id]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Lập phiếu mượn
                            </a>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="row g-3 my-3">
                        <div class="col-6 col-md-3">
                            <div class="bg-light p-3 rounded-2 border">
                                <div class="small text-muted mb-1">Năm xuất bản</div>
                                <div class="fw-bold text-dark">{{ $sach->nam_xuat_ban ?: '—' }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-light p-3 rounded-2 border">
                                <div class="small text-muted mb-1">Nhà xuất bản</div>
                                <div class="fw-bold text-dark text-truncate">{{ $sach->nha_xuat_ban ?: '—' }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-light p-3 rounded-2 border">
                                <div class="small text-muted mb-1">Tổng bản in</div>
                                <div class="fw-bold text-dark">{{ $sach->so_luong }} cuốn</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-light p-3 rounded-2 border">
                                <div class="small text-muted mb-1">Hiện khả dụng</div>
                                <div class="fw-bold {{ $sach->so_luong_con_lai > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $sach->so_luong_con_lai }} cuốn
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="h6 fw-bold text-uppercase text-muted letter-spacing-1 mb-2">Tóm tắt nội dung</h2>
                        <p class="text-secondary mb-0 small" style="line-height: 1.6;">
                            {{ $sach->mo_ta ?: 'Chưa có nội dung tóm tắt.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan History Table Card -->
    <div class="card">
        <div class="card-header py-3 px-3 px-md-4 d-flex justify-content-between align-items-center">
            <h2 class="card-header-title">
                <i class="bi bi-clock-history me-1 text-primary"></i>
                <span>Lịch sử mượn sách này</span>
            </h2>
            <span class="badge badge-subtle-neutral rounded-pill">{{ $phieuMuons->total() }} lượt mượn</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Độc giả</th>
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
                                    <span class="badge badge-subtle-neutral font-monospace">#{{ $phieuMuon->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $phieuMuon->user->name }}</div>
                                    <div class="small text-muted">{{ $phieuMuon->user->email }}</div>
                                </td>
                                <td><span class="small text-secondary">{{ $phieuMuon->ngay_muon->format('d/m/Y') }}</span></td>
                                <td><span class="small text-secondary">{{ $phieuMuon->ngay_tra_du_kien->format('d/m/Y') }}</span></td>
                                <td><x-loan-status :phieu-muon="$phieuMuon" /></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.phieu-muons.show', $phieuMuon) }}" class="btn btn-sm btn-outline-primary">
                                        Xem phiếu
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                                        <h3 class="h6 fw-bold text-dark">Chưa có lượt mượn nào</h3>
                                        <p class="text-muted small mb-0">Đầu sách này hiện chưa có ai mượn trong hệ thống.</p>
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
