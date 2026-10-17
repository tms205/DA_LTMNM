@extends('layouts.app')

@section('title', 'Xác thực đăng ký tài khoản')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem; background: linear-gradient(135deg, #2563eb, #1e40af);">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Xác thực đăng ký tài khoản</h1>
                        <p class="text-muted small mb-0">Bước 2: Xác minh OTP hoặc quét mã QR để kích hoạt</p>
                    </div>

                    <!-- Method Switcher Pills using nav-segment -->
                    <div class="px-4 pt-3 pb-3 bg-white border-bottom">
                        <ul class="nav nav-segment nav-fill" id="verifyTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-tab-pane" type="button" role="tab">
                                    <i class="bi bi-envelope-check-fill me-1"></i> Nhận mã qua Email
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="google-tab" data-bs-toggle="tab" data-bs-target="#google-tab-pane" type="button" role="tab">
                                    <i class="bi bi-qr-code me-1"></i> Quét mã QR (Google)
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="alert alert-light border d-flex align-items-center gap-3 rounded-3 mb-4">
                            <i class="bi bi-person-badge-fill fs-4 text-primary"></i>
                            <div class="small">
                                Tài khoản đăng ký: <strong class="text-dark">{{ $pending['email'] }}</strong> ({{ $pending['name'] }})
                            </div>
                        </div>

                        <div class="tab-content" id="verifyTabContent">
                            <!-- Tab 1: Email OTP -->
                            <div class="tab-pane fade show active" id="email-tab-pane" role="tabpanel" tabindex="0">
                                <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis rounded-3 p-3 mb-4 small">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-info-circle-fill fs-5 mt-n1 text-info"></i>
                                        <div>
                                            Mã xác thực OTP đã được gửi đến: <strong class="text-dark">{{ $pending['email'] }}</strong>.<br>
                                            Vui lòng kiểm tra hộp thư đến (Inbox) hoặc thư rác (Spam) để lấy mã 6 chữ số.
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('register.verify.post') }}">
                                    @csrf
                                    <input type="hidden" name="method" value="email">

                                    <div class="mb-4 text-center">
                                        <label for="otp_code_email" class="form-label fw-semibold text-dark mb-2">
                                            Nhập mã OTP 6 số từ Email
                                        </label>
                                        <div style="max-width: 260px; margin-inline: auto;">
                                            <input type="text" 
                                                   id="otp_code_email" 
                                                   name="otp_code" 
                                                   class="form-control font-monospace fs-3 text-center fw-bold letter-spacing-3 @error('otp_code') is-invalid @enderror" 
                                                   maxlength="6" 
                                                   placeholder="••••••" 
                                                   inputmode="numeric"
                                                   pattern="[0-9]{6}"
                                                   autocomplete="one-time-code"
                                                   autofocus 
                                                   required>
                                        </div>
                                        @error('otp_code')
                                            <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                        <div class="text-muted small mt-2">
                                            <i class="bi bi-clock me-1"></i> Mã có hiệu lực trong vòng 10 phút.
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                        <i class="bi bi-check2-circle me-1"></i> Xác thực & Hoàn tất đăng ký
                                    </button>
                                </form>

                                <!-- Resend OTP Action -->
                                <div class="text-center mt-3 pt-3 border-top">
                                    <span class="small text-secondary">Chưa nhận được mã qua email? </span>
                                    <form method="POST" action="{{ route('register.resend') }}" class="d-inline" id="form-register-resend">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 text-decoration-none small fw-semibold" id="btn-register-resend" @if(($remainingWait ?? 0) > 0) disabled @endif>
                                            <i class="bi @if(($remainingWait ?? 0) > 0) bi-hourglass-split @else bi-arrow-clockwise @endif me-1" id="icon-register-resend"></i>
                                            <span id="text-register-resend">
                                                @if(($remainingWait ?? 0) > 0)
                                                    Gửi lại mã sau (<span id="countdown-reg-sec">{{ $remainingWait }}</span>s)
                                                @else
                                                    Gửi lại mã OTP
                                                @endif
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Tab 2: Google Authenticator QR -->
                            <div class="tab-pane fade" id="google-tab-pane" role="tabpanel" tabindex="0">
                                <div class="text-center mb-4">
                                    <p class="small text-secondary mb-3">
                                        Mở ứng dụng <strong>Google Authenticator</strong> (hoặc Authy / Microsoft Authenticator) trên điện thoại và quét mã QR dưới đây:
                                    </p>
                                    <div class="d-inline-block p-2 bg-white rounded-3 border shadow-sm mb-2">
                                        <img src="{{ $qrCodeUrl }}" alt="Mã QR Google Authenticator" class="img-fluid rounded" style="max-width: 160px;">
                                    </div>
                                    <div class="small text-muted">
                                        Khóa bí mật thủ công:
                                        <div class="p-1 px-2 bg-light rounded border font-monospace text-primary fw-bold d-inline-block small mt-1">
                                            {{ $secret }}
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('register.verify.post') }}">
                                    @csrf
                                    <input type="hidden" name="method" value="google">

                                    <div class="mb-4 text-center">
                                        <label for="otp_code_google" class="form-label fw-semibold text-dark mb-2">
                                            Nhập mã 6 chữ số từ ứng dụng điện thoại
                                        </label>
                                        <div style="max-width: 260px; margin-inline: auto;">
                                            <input type="text" 
                                                   id="otp_code_google" 
                                                   name="otp_code" 
                                                   class="form-control font-monospace fs-3 text-center fw-bold letter-spacing-3 @error('otp_code') is-invalid @enderror" 
                                                   maxlength="6" 
                                                   placeholder="••••••" 
                                                   inputmode="numeric"
                                                   pattern="[0-9]{6}"
                                                   autocomplete="one-time-code"
                                                   required>
                                        </div>
                                        @error('otp_code')
                                            <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                        <i class="bi bi-shield-check me-1"></i> Xác thực mã & Kích hoạt tài khoản
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-center mt-4 pt-3 border-top">
                            <a href="{{ route('register') }}" class="small text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Nhập lại thông tin đăng ký khác
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
    const btn = document.getElementById('btn-register-resend');
    const textSpan = document.getElementById('text-register-resend');
    const icon = document.getElementById('icon-register-resend');

    if (remaining > 0 && btn) {
        const timer = setInterval(function () {
            remaining--;
            const countSpan = document.getElementById('countdown-reg-sec');
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
