@extends('layouts.app')

@section('title', 'Thiết lập mật khẩu mới - Quản Lý Thư Viện')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem; background: linear-gradient(135deg, #10b981, #047857);">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Thiết lập mật khẩu mới</h1>
                        <p class="text-muted small mb-0">Xác thực OTP thành công cho tài khoản <strong>{{ $user->email }}</strong></p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('password.email_otp.reset.post') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-dark small">Mật khẩu mới</label>
                                <div class="input-icon-group has-password-toggle">
                                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                                    <input id="password" 
                                           type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           placeholder="Tối thiểu 8 ký tự"
                                           required 
                                           autocomplete="new-password"
                                           autofocus>
                                    <button type="button" class="btn-toggle-password" data-target="#password" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password-confirm" class="form-label fw-semibold text-dark small">Xác nhận mật khẩu mới</label>
                                <div class="input-icon-group has-password-toggle">
                                    <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                    <input id="password-confirm" 
                                           type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           placeholder="Nhập lại mật khẩu mới"
                                           required 
                                           autocomplete="new-password">
                                    <button type="button" class="btn-toggle-password" data-target="#password-confirm" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                <span>Lưu mật khẩu mới & Đăng nhập</span>
                                <i class="bi bi-box-arrow-in-right ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
