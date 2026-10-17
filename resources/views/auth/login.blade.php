@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem;">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Đăng nhập tài khoản</h1>
                        <p class="text-muted small mb-0">Hệ thống Quản lý Thư viện HUIT</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('login') }}" id="login-form">
                            @csrf

                            <div class="mb-3">
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

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label fw-semibold text-dark small mb-0">Mật khẩu</label>
                                    <div class="dropdown d-inline-block">
                                        <a href="{{ route('password.email_otp.request') }}" class="small text-primary text-decoration-none fw-semibold">
                                            <i class="bi bi-envelope-at me-1"></i> Quên mật khẩu? (Email OTP)
                                        </a>
                                    </div>
                                </div>
                                <div class="input-icon-group has-password-toggle">
                                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                                    <input id="password" 
                                           type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           placeholder="••••••••"
                                           required 
                                           autocomplete="current-password">
                                    <button type="button" class="btn-toggle-password" data-target="#password" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-secondary small" for="remember">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                                <a href="{{ route('password.google.request') }}" class="small text-muted text-decoration-none">
                                    <i class="bi bi-qr-code me-1"></i> Dùng Google Authenticator
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                            </button>

                            <div class="text-center small text-secondary">
                                Chưa có tài khoản độc giả? 
                                <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-none">Đăng ký ngay</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
