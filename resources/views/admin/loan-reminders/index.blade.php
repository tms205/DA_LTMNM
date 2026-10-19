@extends('layouts.app')

@section('title', 'Thông báo hạn trả')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
        <div>
            <div class="section-label">
                <i class="bi bi-envelope-paper me-1"></i> Chăm sóc độc giả
            </div>
            <h1 class="page-heading h3 mb-1">Thông báo hạn trả</h1>
            <p class="text-muted small mb-0">Chọn độc giả để gửi email tổng hợp các sách đến hạn hôm nay hoặc ngày mai.</p>
        </div>
        <div class="alert alert-info border-0 shadow-sm mb-0 py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history fs-5 text-primary"></i>
                <div class="small">
                    <strong>Tự động mỗi ngày lúc 08:00</strong><br>
                    <span class="opacity-75">Hệ thống tự bỏ qua email đã gửi trong ngày.</span>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.loan-reminders.store') }}" id="reminder-form">
        @csrf

        <div class="card mb-4">
            <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="select-all-readers">
                    <label class="form-check-label fw-semibold" for="select-all-readers">Chọn tất cả độc giả đủ điều kiện</label>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span><strong class="text-dark">{{ $dueTomorrow->count() }}</strong> sắp đến hạn</span>
                    <span aria-hidden="true">•</span>
                    <span><strong class="text-dark">{{ $dueToday->count() }}</strong> đến hạn hôm nay</span>
                </div>
            </div>
        </div>

        @foreach ([
            ['title' => 'Sắp đến hạn ngày mai', 'subtitle' => 'Nhắc trước để độc giả chủ động chuẩn bị trả sách.', 'icon' => 'bi-hourglass-split', 'color' => 'warning', 'readers' => $dueTomorrow],
            ['title' => 'Đến hạn hôm nay', 'subtitle' => 'Cần hoàn trả trong hôm nay để tránh phát sinh quá hạn.', 'icon' => 'bi-calendar-check', 'color' => 'danger', 'readers' => $dueToday],
        ] as $section)
            <div class="card mb-4 overflow-hidden">
                <div class="card-header py-3 px-3 px-md-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="stat-icon-wrapper bg-{{ $section['color'] }}-subtle text-{{ $section['color'] }}" style="width: 2.25rem; height: 2.25rem; font-size: 1rem;">
                            <i class="bi {{ $section['icon'] }}"></i>
                        </span>
                        <div>
                            <h2 class="h6 fw-bold mb-0 text-dark">{{ $section['title'] }}</h2>
                            <div class="small text-muted">{{ $section['subtitle'] }}</div>
                        </div>
                    </div>
                    <span class="badge rounded-pill bg-{{ $section['color'] }}-subtle text-{{ $section['color'] }}">
                        {{ $section['readers']->count() }} độc giả
                    </span>
                </div>

                <div class="card-body p-0">
                    @forelse ($section['readers'] as $reader)
                        <div class="p-3 p-md-4 border-bottom reminder-reader-row">
                            <div class="row g-3 align-items-start">
                                <div class="col-auto pt-1">
                                    <input class="form-check-input reminder-reader-checkbox" type="checkbox" name="user_ids[]" value="{{ $reader['user']->id }}" aria-label="Chọn {{ $reader['user']->name }}">
                                </div>
                                <div class="col">
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="user-avatar" style="width: 2rem; height: 2rem; font-size: 0.8rem;">
                                                {{ mb_strtoupper(mb_substr($reader['user']->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $reader['user']->name }}</div>
                                                <div class="small text-muted">{{ $reader['user']->email }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            @if ($reader['sent_today'])
                                                <span class="badge badge-subtle-success rounded-pill">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Đã gửi hôm nay
                                                </span>
                                            @else
                                                <span class="badge badge-subtle-neutral rounded-pill">Chưa gửi hôm nay</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        @foreach ($reader['loans'] as $loan)
                                            <div class="col-12 col-xl-6">
                                                <div class="d-flex align-items-center gap-2 rounded-2 border bg-light p-2">
                                                    <div class="book-cover-thumbnail">
                                                        <x-book-cover :sach="$loan->sach" class="w-100" />
                                                    </div>
                                                    <div class="min-w-0 flex-grow-1">
                                                        <div class="fw-semibold text-truncate small text-dark">{{ $loan->sach->ten_sach }}</div>
                                                        <div class="small text-muted">
                                                            Hạn: <strong class="{{ $loan->trangThaiHienThi() === 'qua_han' ? 'text-danger' : 'text-dark' }}">{{ $loan->ngay_tra_du_kien->format('d/m/Y') }}</strong>
                                                        </div>
                                                    </div>
                                                    <x-loan-status :phieu-muon="$loan" />
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-check-circle fs-3 text-success d-block mb-1"></i>
                            <span class="small">Không có độc giả nào thuộc nhóm này cần gửi nhắc.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach

        @error('user_ids')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        @error('user_ids.*')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <div class="card">
            <div class="card-body p-3 p-md-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="fw-semibold text-dark"><span id="selected-reader-count">0</span> độc giả được chọn</div>
                    <div class="small text-muted">“Gửi lại” sẽ bỏ qua cơ chế chống trùng cho các tài khoản đã chọn.</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" name="force" value="1" class="btn btn-outline-secondary" disabled data-reminder-submit>
                        <i class="bi bi-arrow-repeat me-1"></i> Gửi lại
                    </button>
                    <button type="submit" class="btn btn-primary" disabled data-reminder-submit>
                        <i class="bi bi-send-fill me-1"></i> Gửi thông báo
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('select-all-readers');
    const checkboxes = [...document.querySelectorAll('.reminder-reader-checkbox')];
    const selectedCount = document.getElementById('selected-reader-count');
    const submitButtons = document.querySelectorAll('[data-reminder-submit]');

    const updateSelection = () => {
        const selectedUserIds = new Set(checkboxes.filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value));
        selectedCount.textContent = selectedUserIds.size;
        submitButtons.forEach((button) => button.disabled = selectedUserIds.size === 0);
        selectAll.checked = checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);
        selectAll.indeterminate = !selectAll.checked && checkboxes.some((checkbox) => checkbox.checked);
    };

    selectAll.addEventListener('change', () => {
        checkboxes.forEach((checkbox) => checkbox.checked = selectAll.checked);
        updateSelection();
    });

    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));
    updateSelection();
});
</script>
@endpush
