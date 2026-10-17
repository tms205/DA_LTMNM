@extends('layouts.app')

@section('title', 'Nhập mã OTP Email - Quản Lý Thư Viện')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem; background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Xác thực mã OTP</h1>
                        <p class="text-muted small mb-0">Mã 6 chữ số đã được gửi qua email</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis rounded-3 p-3 mb-4 small">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill fs-5 mt-n1 text-info"></i>
                                <div>
                                    Mã OTP đã được gửi đến: <strong class="text-dark">{{ $otpData['email'] }}</strong>.<br>
                                    Vui lòng kiểm tra hộp thư đến (Inbox) hoặc mục Thư rác (Spam).
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('password.email_otp.verify.post') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="otp_code" class="form-label fw-semibold text-dark small text-center d-block">
                                    Nhập mã xác thực 6 số
                                </label>
                                <div class="position-relative">
                                    <input id="otp_code" 
                                           type="text" 
                                           class="form-control form-control-lg text-center fw-bold @error('otp_code') is-invalid @enderror" 
                                           name="otp_code" 
                                           placeholder="••••••" 
                                           maxlength="6"
                                           inputmode="numeric"
                                           pattern="[0-9]{6}"
                                           style="letter-spacing: 0.5rem; font-size: 1.75rem; font-family: 'Courier New', monospace;"
                                           required 
                                           autofocus 
                                           autocomplete="one-time-code">
                                </div>
                                @error('otp_code')
                                    <span class="invalid-feedback d-block text-center mt-2" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="text-center text-muted small mt-2">
                                    <i class="bi bi-clock me-1"></i> Mã có hiệu lực trong vòng 10 phút.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                <span>Xác thực mã & Tiếp tục</span>
                                <i class="bi bi-check2-circle ms-1"></i>
                            </button>
                        </form>

                        <!-- Resend OTP Action -->
                        <div class="text-center mt-3 pt-3 border-top">
                            <span class="small text-secondary">Chưa nhận được mã? </span>
                            <form method="POST" action="{{ route('password.email_otp.resend') }}" class="d-inline" id="form-resend">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-decoration-none small fw-semibold" id="btn-resend" @if(($remainingWait ?? 0) > 0) disabled @endif>
                                    <i class="bi @if(($remainingWait ?? 0) > 0) bi-hourglass-split @else bi-arrow-clockwise @endif me-1" id="icon-resend"></i>
                                    <span id="resend-text">
                                        @if(($remainingWait ?? 0) > 0)
                                            Gửi lại mã sau (<span id="countdown-sec">{{ $remainingWait }}</span>s)
                                        @else
                                            Gửi lại mã OTP
                                        @endif
                                    </span>
                                </button>
                            </form>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('password.email_otp.request') }}" class="small text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Sử dụng địa chỉ email khác
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let remaining = {{ (int) ($remainingWait ?? 0) }};
    const btn = document.getElementById('btn-resend');
    const textSpan = document.getElementById('resend-text');
    const icon = document.getElementById('icon-resend');

    if (remaining > 0 && btn) {
        const timer = setInterval(function () {
            remaining--;
            const countSpan = document.getElementById('countdown-sec');
            if (countSpan) {
                countSpan.textContent = remaining;
            }
            if (remaining <= 0) {
                clearInterval(timer);
                btn.removeAttribute('disabled');
                if (icon) {
                    icon.className = 'bi bi-arrow-clockwise me-1';
                }
                if (textSpan) {
                    textSpan.textContent = 'Gửi lại mã OTP';
                }
            }
        }, 1000);
    }
});
</script>
@endpush
@endsection
