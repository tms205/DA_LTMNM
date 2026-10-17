@extends('layouts.app')

@section('title', 'Lấy lại mật khẩu qua Email OTP')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem; background: linear-gradient(135deg, #2563eb, #1e40af);">
                            <i class="bi bi-envelope-check-fill"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Khôi phục mật khẩu qua Email</h1>
                        <p class="text-muted small mb-0">Hệ thống gửi mã OTP 6 số trực tiếp đến hòm thư của bạn</p>
                    </div>

                    <!-- Method Switcher Pills -->
                    <div class="px-4 pt-3 pb-3 bg-white border-bottom">
                        <ul class="nav nav-segment nav-fill">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('password.email_otp.request') }}">
                                    <i class="bi bi-envelope-fill me-1"></i> Gửi mã qua Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('password.google.request') }}">
                                    <i class="bi bi-qr-code me-1"></i> Google Authenticator
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <p class="small text-secondary mb-4">
                            Nhập địa chỉ email đăng ký tài khoản thư viện của bạn. Hệ thống sẽ gửi một mã xác thực <strong>OTP gồm 6 chữ số</strong> về hòm thư đến để bạn khôi phục mật khẩu.
                        </p>

                        <form method="POST" action="{{ route('password.email_otp.request.post') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold text-dark small">Địa chỉ Email của bạn</label>
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

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                <span>Gửi mã OTP về Email</span>
                                <i class="bi bi-send-fill ms-1"></i>
                            </button>

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
