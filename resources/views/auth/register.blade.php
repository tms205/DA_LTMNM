@extends('layouts.app')

@section('title', 'Đăng ký tài khoản độc giả')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Đăng ký tài khoản độc giả</h1>
                        <p class="text-muted small mb-0">Tham gia hệ thống thư viện để mượn và đọc sách miễn phí</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Section 1: Thông tin cơ bản -->
                            <div class="text-uppercase text-muted fw-bold small mb-3 letter-spacing-1">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Thông tin cá nhân
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold text-dark small">
                                        Họ và tên độc giả <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-person"></i></span>
                                        <input id="name" 
                                               type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               name="name" 
                                               value="{{ old('name') }}" 
                                               placeholder="Nguyễn Văn A"
                                               required 
                                               autocomplete="name" 
                                               autofocus>
                                    </div>
                                    @error('name')
                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold text-dark small">
                                        Địa chỉ Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                        <input id="email" 
                                               type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               placeholder="docgia@gmail.com"
                                               required 
                                               autocomplete="email">
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="so_dien_thoai" class="form-label fw-semibold text-dark small">
                                        Số điện thoại liên hệ
                                    </label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-telephone"></i></span>
                                        <input id="so_dien_thoai" 
                                               type="text" 
                                               class="form-control @error('so_dien_thoai') is-invalid @enderror" 
                                               name="so_dien_thoai" 
                                               value="{{ old('so_dien_thoai') }}" 
                                               placeholder="0912345678"
                                               autocomplete="tel">
                                    </div>
                                    @error('so_dien_thoai')
                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="ngay_sinh" class="form-label fw-semibold text-dark small">
                                        Ngày tháng năm sinh
                                    </label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-calendar-date"></i></span>
                                        <input id="ngay_sinh" 
                                               type="date" 
                                               class="form-control @error('ngay_sinh') is-invalid @enderror" 
                                               name="ngay_sinh" 
                                               value="{{ old('ngay_sinh') }}" 
                                               max="{{ date('Y-m-d') }}">
                                    </div>
                                    @error('ngay_sinh')
                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="dia_chi" class="form-label fw-semibold text-dark small">
                                    Địa chỉ thường trú / Nơi ở
                                </label>
                                <div class="input-icon-group">
                                    <span class="input-icon"><i class="bi bi-geo-alt"></i></span>
                                    <input id="dia_chi" 
                                           type="text" 
                                           class="form-control @error('dia_chi') is-invalid @enderror" 
                                           name="dia_chi" 
                                           value="{{ old('dia_chi') }}" 
                                           placeholder="Số nhà, tên đường, quận/huyện, tỉnh/thành phố">
                                </div>
                                @error('dia_chi')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <!-- Section 2: Thiết lập bảo mật -->
                            <div class="text-uppercase text-muted fw-bold small mb-3 letter-spacing-1 pt-2 border-top">
                                <i class="bi bi-shield-lock me-1 text-primary"></i> Thiết lập mật khẩu
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold text-dark small">
                                        Mật khẩu bảo mật <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-group has-password-toggle">
                                        <span class="input-icon"><i class="bi bi-lock"></i></span>
                                        <input id="password" 
                                               type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               name="password" 
                                               placeholder="Tối thiểu 8 ký tự"
                                               required 
                                               autocomplete="new-password">
                                        <button type="button" class="btn-toggle-password" data-target="#password" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password-confirm" class="form-label fw-semibold text-dark small">
                                        Xác nhận mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-group has-password-toggle">
                                        <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                        <input id="password-confirm" 
                                               type="password" 
                                               class="form-control" 
                                               name="password_confirmation" 
                                               placeholder="Nhập lại mật khẩu"
                                               required 
                                               autocomplete="new-password">
                                        <button type="button" class="btn-toggle-password" data-target="#password-confirm" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                <span>Tiếp tục: Xác thực tài khoản (OTP / QR)</span>
                                <i class="bi bi-arrow-right ms-1"></i>
                            </button>

                            <div class="text-center small text-secondary">
                                Đã có tài khoản trước đó? 
                                <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none">Đăng nhập ngay</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
