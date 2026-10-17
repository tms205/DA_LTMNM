@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem;">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Đặt lại mật khẩu</h1>
                        <p class="text-muted small mb-0">Tạo mật khẩu đăng nhập mới cho tài khoản</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-dark small">Địa chỉ Email</label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                    <input id="email" 
                                           type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ $email ?? old('email') }}" 
                                           required 
                                           autocomplete="email" 
                                           autofocus>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-dark small">Mật khẩu mới</label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                                    <input id="password" 
                                           type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           placeholder="Tối thiểu 8 ký tự"
                                           required 
                                           autocomplete="new-password">
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password-confirm" class="form-label fw-semibold text-dark small">Xác nhận mật khẩu mới</label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><i class="bi bi-shield-check"></i></span>
                                    <input id="password-confirm" 
                                           type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           placeholder="Nhập lại mật khẩu mới"
                                           required 
                                           autocomplete="new-password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                <i class="bi bi-check2-circle me-1"></i> Lưu mật khẩu mới
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
