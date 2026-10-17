@extends('layouts.app')

@section('title', 'Hồ sơ độc giả: ' . $user->name)

@section('content')
<section class="py-4 py-lg-5">
    <div class="container">
        <!-- Breadcrumb & Back -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách độc giả
                </a>
                <h1 class="page-heading h3 mb-1">
                    Hồ sơ lý lịch: {{ $user->name }}
                </h1>
                <p class="text-muted small mb-0">Tham gia thư viện ngày: {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                @if ($lyLich['co_the_muon'])
                    <a href="{{ route('admin.phieu-muons.create', ['user_id' => $user->id]) }}" class="btn btn-primary shadow-sm">
                        <i class="bi bi-journal-plus me-1"></i> Lập phiếu mượn cho độc giả này
                    </a>
                @else
                    <button class="btn btn-secondary" disabled title="{{ $lyLich['ly_do_khong_the_muon'] }}">
                        <i class="bi bi-slash-circle me-1"></i> Không thể mượn sách lúc này
                    </button>
                @endif
            </div>
        </div>

        <!-- Reliability Credit Banner -->
        <div class="card border shadow-sm mb-4 border-start border-{{ $lyLich['badge_class'] }} border-4">
            <div class="card-body p-4">
                <div class="row align-items-center gy-3">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge badge-subtle-{{ $lyLich['badge_class'] }} fs-6 px-3 py-2 fw-semibold shadow-sm">
                                <i class="bi {{ $lyLich['icon'] }} me-1"></i> {{ $lyLich['ten_muc_do'] }}
                            </span>
                            @if ($lyLich['co_the_muon'])
                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2.5 py-1.5 fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1"></i> Đủ điều kiện mượn sách
                                </span>
                            @else
                                <span class="badge bg-danger text-white px-2.5 py-1.5 fw-bold shadow-sm">
                                    <i class="bi bi-x-circle-fill me-1"></i> Chặn mượn sách
                                </span>
                            @endif
                        </div>
                        <p class="text-secondary mb-1">{{ $lyLich['mo_ta'] }}</p>
                        @if (!$lyLich['co_the_muon'])
                            <div class="alert alert-danger border-0 py-2 px-3 rounded-3 small mb-0 mt-2">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i> <strong>Lý do:</strong> {{ $lyLich['ly_do_khong_the_muon'] }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="p-2 bg-light rounded border">
                                    <div class="small text-muted">Tổng mượn</div>
                                    <div class="h5 fw-bold text-dark mb-0">{{ $lyLich['thong_ke']['tongMuon'] }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-light rounded border">
                                    <div class="small text-muted">Đang giữ</div>
                                    <div class="h5 fw-bold text-primary mb-0">{{ $lyLich['thong_ke']['dangGiu'] }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-light rounded border">
                                    <div class="small text-muted">Quá hạn</div>
                                    <div class="h5 fw-bold text-danger mb-0">{{ $lyLich['thong_ke']['quaHan'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded border">
                                    <div class="small text-muted">Số lần bị phạt</div>
                                    <div class="h6 fw-bold text-danger mb-0">{{ $lyLich['thong_ke']['tongLanViPham'] }} lần</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded border">
                                    <div class="small text-muted">Tiền phạt còn nợ</div>
                                    <div class="h6 fw-bold text-danger mb-0">{{ number_format($lyLich['thong_ke']['tienPhatNo'], 0, ',', '.') }} đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Personal info & Librarian notes form -->
            <div class="col-lg-4">
                <!-- Personal Info Card -->
                <div class="card border shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <span class="fw-bold text-dark">
                            <i class="bi bi-person-badge text-primary me-1"></i> Thông tin độc giả
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="user-avatar" style="width: 3.5rem; height: 3.5rem; font-size: 1.5rem;">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $user->name }}</h5>
                                <span class="badge badge-subtle-primary small">Mã: #{{ $user->id }}</span>
                            </div>
                        </div>

                        <ul class="list-unstyled small mb-0 d-flex flex-column gap-2 text-secondary">
                            <li><i class="bi bi-envelope text-primary me-2"></i> {{ $user->email }}</li>
                            <li><i class="bi bi-telephone text-primary me-2"></i> {{ $user->so_dien_thoai ?: 'Chưa cập nhật SĐT' }}</li>
                            <li><i class="bi bi-geo-alt text-primary me-2"></i> {{ $user->dia_chi ?: 'Chưa cập nhật địa chỉ' }}</li>
                            <li><i class="bi bi-calendar text-primary me-2"></i> {{ $user->ngay_sinh ? $user->ngay_sinh->format('d/m/Y') : 'Chưa cập nhật ngày sinh' }}</li>
                            <li>
                                <i class="bi bi-shield-lock text-primary me-2"></i> Bảo mật 2 lớp:
                                @if ($user->hasGoogle2Fa())
                                    <span class="badge badge-subtle-success small ms-1">Đã bật 2FA</span>
                                @else
                                    <span class="badge badge-subtle-neutral small ms-1">Chưa bật</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Librarian Notes & Permission Settings Card -->
                <div class="card border shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <span class="fw-bold text-dark">
                            <i class="bi bi-clipboard2-check text-warning me-1"></i> Chú ý &amp; Quyền hạn thủ thư
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('admin.users.update-note', $user) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="ghi_chu_thu_thu" class="form-label small fw-semibold text-dark">
                                    Ghi chú lưu ý về độc giả này:
                                </label>
                                <textarea name="ghi_chu_thu_thu" 
                                          id="ghi_chu_thu_thu" 
                                          rows="4" 
                                          class="form-control small" 
                                          placeholder="Ví dụ: Độc giả tích cực, thường xuyên đọc sách nghiên cứu; từng làm rách trang cuối cuốn sách #12...">{{ old('ghi_chu_thu_thu', $user->ghi_chu_thu_thu) }}</textarea>
                                <div class="form-text small text-muted">Ghi chú nội bộ giữa các thủ thư, độc giả không nhìn thấy.</div>
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           role="switch" 
                                           name="khoa_muon_sach" 
                                           value="1" 
                                           id="switchKhoaMuon" 
                                           @checked(old('khoa_muon_sach', $user->khoa_muon_sach))
                                           onchange="document.getElementById('khungLyDoKhoa').style.display = this.checked ? 'block' : 'none';">
                                    <label class="form-check-label fw-semibold text-danger" for="switchKhoaMuon">
                                        Khóa quyền mượn sách của độc giả
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3" id="khungLyDoKhoa" style="display: {{ old('khoa_muon_sach', $user->khoa_muon_sach) ? 'block' : 'none' }};">
                                <label for="ly_do_khoa" class="form-label small fw-semibold text-dark">Lý do khóa quyền mượn:</label>
                                <input type="text" 
                                       name="ly_do_khoa" 
                                       id="ly_do_khoa" 
                                       class="form-control small" 
                                       value="{{ old('ly_do_khoa', $user->ly_do_khoa) }}" 
                                       placeholder="Ví dụ: Chưa hoàn trả sách đã mượn nhiều tháng">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="bi bi-save me-1"></i> Lưu ghi chú &amp; Quyền hạn
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Loan History & Penalty Tickets Tabs -->
            <div class="col-lg-8">
                <div class="card border shadow-sm">
                    <div class="card-header bg-white pt-3 pb-0 px-4 border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" id="userRecordTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="loans-tab" data-bs-toggle="tab" data-bs-target="#loans-tab-pane" type="button" role="tab">
                                    <i class="bi bi-clock-history me-1"></i> Lịch sử mượn sách ({{ $user->phieuMuons->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold text-danger" id="fines-tab" data-bs-toggle="tab" data-bs-target="#fines-tab-pane" type="button" role="tab">
                                    <i class="bi bi-cash-coin me-1"></i> Phiếu phạt vi phạm ({{ $user->phieuPhats->count() }})
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        <div class="tab-content" id="userRecordTabsContent">
                            <!-- Tab 1: Borrowing History -->
                            <div class="tab-pane fade show active" id="loans-tab-pane" role="tabpanel" tabindex="0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 70px;">Mã</th>
                                                <th>Cuốn sách</th>
                                                <th>Ngày mượn</th>
                                                <th>Hạn trả</th>
                                                <th>Ngày trả</th>
                                                <th>Trạng thái</th>
                                                <th class="text-end">Chi tiết</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($user->phieuMuons as $pm)
                                                <tr>
                                                    <td><span class="font-monospace fw-bold">#PM-{{ $pm->id }}</span></td>
                                                    <td>
                                                        <a href="{{ route('admin.saches.show', $pm->sach) }}" class="fw-semibold text-dark text-decoration-none">
                                                            {{ $pm->sach->ten_sach ?? 'Không xác định' }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $pm->ngay_muon->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span class="{{ $pm->trangThaiHienThi() === 'qua_han' ? 'text-danger fw-bold' : '' }}">
                                                            {{ $pm->ngay_tra_du_kien->format('d/m/Y') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($pm->ngay_tra_thuc_te)
                                                            <span class="text-success fw-semibold">{{ $pm->ngay_tra_thuc_te->format('d/m/Y') }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <x-loan-status :phieu-muon="$pm" />
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.phieu-muons.show', $pm) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-4 text-muted">
                                                        Độc giả chưa từng mượn cuốn sách nào.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab 2: Penalty Tickets -->
                            <div class="tab-pane fade" id="fines-tab-pane" role="tabpanel" tabindex="0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 80px;">Mã phạt</th>
                                                <th>Sách mượn vi phạm</th>
                                                <th>Số ngày trễ</th>
                                                <th>Tiền phạt</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày nộp</th>
                                                <th class="text-end">Chi tiết</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($user->phieuPhats as $pp)
                                                <tr>
                                                    <td><span class="font-monospace fw-bold text-danger">#PP-{{ $pp->id }}</span></td>
                                                    <td>
                                                        <span class="fw-semibold text-dark">{{ $pp->phieuMuon->sach->ten_sach ?? '' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-danger-subtle text-danger">{{ $pp->so_ngay_qua_han }} ngày</span>
                                                    </td>
                                                    <td class="fw-bold text-danger">
                                                        {{ number_format($pp->tong_tien_phat, 0, ',', '.') }} đ
                                                    </td>
                                                    <td>
                                                        @if ($pp->isDaNop())
                                                            <span class="badge badge-subtle-success">Đã nộp phạt</span>
                                                        @else
                                                            <span class="badge badge-subtle-danger">Chưa nộp tiền</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pp->ngay_nop ? $pp->ngay_nop->format('d/m/Y') : '—' }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.phieu-phats.show', $pp) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-4 text-muted">
                                                        <i class="bi bi-shield-check text-success fs-4 d-block mb-1"></i>
                                                        Độc giả chưa từng có phiếu phạt nào!
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
