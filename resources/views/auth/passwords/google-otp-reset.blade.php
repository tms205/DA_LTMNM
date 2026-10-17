@extends('layouts.app')

@section('title', 'Tạo mật khẩu mới')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem; background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Tạo mật khẩu mới</h1>
                        <p class="text-muted small mb-0">Bước 3: Hoàn tất đặt lại mật khẩu</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="alert alert-success d-flex align-items-center gap-2 small rounded-3 mb-4">
                            <i class="bi bi-shield-check fs-5"></i>
                            <div>Mã OTP Google Authenticator đã được xác minh thành công cho tài khoản <strong>{{ $user->email }}</strong>.</div>
                        </div>

                        <form method="POST" action="{{ route('password.google.reset.post') }}">
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
                                <label for="password-confirm" class="form-label fw-semibold text-dark small">Nhập lại mật khẩu mới</label>
                                <div class="input-icon-group has-password-toggle">
                                    <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                    <input id="password-confirm" 
                                           type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           placeholder="Xác nhận lại mật khẩu"
                                           required 
                                           autocomplete="new-password">
                                    <button type="button" class="btn-toggle-password" data-target="#password-confirm" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                <i class="bi bi-check2-circle me-1"></i> Lưu mật khẩu &amp; Đăng nhập
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
