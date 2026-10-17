@extends('layouts.app')

@section('title', 'Khôi phục mật khẩu')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem;">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Khôi phục mật khẩu</h1>
                        <p class="text-muted small mb-0">Nhập email đăng ký để nhận liên kết đặt lại mật khẩu</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        @if (session('status'))
                            <div class="alert alert-success d-flex align-items-center gap-2 small rounded-3 mb-4" role="alert">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>{{ session('status') }}</div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold text-dark small">Địa chỉ Email</label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                    <input id="email" 
                                           type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="nhap.email@domain.com"
                                           required 
                                           autocomplete="email" 
                                           autofocus>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-outline-secondary w-100 py-2 mb-3">
                                <i class="bi bi-send me-1"></i> Gửi link đặt lại qua Email
                            </button>

                            <div class="p-3 bg-light rounded-3 border text-center mb-3">
                                <div class="small text-muted mb-2">Phương thức xác thực OTP tiện lợi & nhanh chóng:</div>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('password.email_otp.request') }}" class="btn btn-primary btn-sm fw-semibold shadow-sm">
                                        <i class="bi bi-envelope-check-fill me-1"></i> Nhận mã OTP 6 số qua Email
                                    </a>
                                    <a href="{{ route('password.google.request') }}" class="btn btn-outline-primary btn-sm fw-semibold">
                                        <i class="bi bi-qr-code me-1"></i> Dùng Google Authenticator (TOTP)
                                    </a>
                                </div>
                            </div>

                            <div class="text-center small text-secondary">
                                <a href="{{ route('login') }}" class="text-muted text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i> Quay lại trang Đăng nhập
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
