@extends('layouts.app')

@section('title', 'Xác thực mã Google Authenticator')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6">
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom text-center">
                        <div class="brand-mark mb-2 mx-auto" style="width: 2.75rem; height: 2.75rem; font-size: 1.25rem; background: linear-gradient(135deg, #059669, #10b981);">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-1">Xác thực mã bảo mật OTP</h1>
                        <p class="text-muted small mb-0">Bước 2: Mở ứng dụng trên điện thoại để lấy mã 6 số</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="alert alert-light border d-flex align-items-center gap-3 rounded-3 mb-4">
                            <i class="bi bi-person-check-fill fs-4 text-primary"></i>
                            <div class="small">
                                Tài khoản đang khôi phục: <strong class="text-dark">{{ $user->email }}</strong>
                            </div>
                        </div>

                        <!-- Instruction Box -->
                        <div class="p-3 bg-light rounded-3 border mb-4 small text-secondary">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-phone text-primary fs-5 mt-n1"></i>
                                <div>
                                    Vui lòng mở ứng dụng <strong>Google Authenticator</strong> (hoặc Authy / Microsoft Authenticator) trên điện thoại của bạn, tìm mã 6 chữ số tương ứng với tài khoản thư viện này và nhập vào bên dưới.
                                </div>
                            </div>
                        </div>

                        <!-- OTP Form -->
                        <form method="POST" action="{{ route('password.google.verify.post') }}">
                            @csrf

                            <div class="mb-4 text-center">
                                <label for="otp_code" class="form-label fw-semibold text-dark mb-2">
                                    Nhập mã 6 chữ số từ ứng dụng
                                </label>
                                <div style="max-width: 260px; margin-inline: auto;">
                                    <input type="text" 
                                           id="otp_code" 
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
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                                <i class="bi bi-check2-circle me-1"></i> Xác minh mã OTP
                            </button>

                            <div class="text-center small mb-3">
                                <a href="{{ route('password.google.request') }}" class="text-muted text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i> Nhập lại địa chỉ Email khác
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
