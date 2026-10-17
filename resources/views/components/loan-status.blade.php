@props(['phieuMuon'])

@php
    $status = $phieuMuon->trangThaiHienThi();
    $config = match ($status) {
        'dang_muon' => [
            'label' => 'Đang mượn',
            'class' => 'badge-subtle-primary',
            'dotClass' => 'bg-primary status-dot-pulse',
            'icon' => 'bi-clock-history',
        ],
        'da_tra' => [
            'label' => 'Đã trả',
            'class' => 'badge-subtle-success',
            'dotClass' => 'bg-success',
            'icon' => 'bi-check-circle-fill',
        ],
        'qua_han' => [
            'label' => 'Quá hạn',
            'class' => 'badge-subtle-danger',
            'dotClass' => 'bg-danger',
            'icon' => 'bi-exclamation-triangle-fill',
        ],
        default => [
            'label' => $status,
            'class' => 'badge-subtle-neutral',
            'dotClass' => 'bg-secondary',
            'icon' => 'bi-info-circle',
        ],
    };
@endphp

<span class="badge rounded-pill {{ $config['class'] }} d-inline-flex align-items-center gap-2 py-1 px-2 fw-semibold">
    <span class="status-dot {{ $config['dotClass'] }}" aria-hidden="true"></span>
    <i class="bi {{ $config['icon'] }} small" aria-hidden="true"></i>
    <span>{{ $config['label'] }}</span>
</span>
