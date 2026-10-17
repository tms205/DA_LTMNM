@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân & Bảo mật')

@section('content')
<section class="py-4 py-lg-5">
    <div class="container" style="max-width: 950px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb py-2 px-3 bg-white border rounded-3 shadow-sm small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none"><i class="bi bi-house-door me-1"></i> Tổng quan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hồ sơ cá nhân</li>
            </ol>
        </nav>

        <!-- User Profile Summary Header -->
        <div class="card border shadow-sm mb-4 overflow-hidden">
            <div class="card-body p-4 p-md-5" style="background: linear-gradient(135deg, #0f2744 0%, #1e3a8a 100%);">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4 text-center text-md-start">
                    <div class="user-avatar" style="width: 4.5rem; height: 4.5rem; font-size: 2rem; background: linear-gradient(135deg, #f59e0b, #d97706); border: 3px solid rgba(255,255,255,0.4);">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>
                    <div class="text-white flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                            <h1 class="h3 fw-bold mb-0 text-white">{{ $user->name }}</h1>
                            <span class="badge {{ $user->isAdmin() ? 'bg-warning text-dark' : 'bg-info text-dark' }} rounded-pill px-3 py-1 fw-semibold small">
                                <i class="bi {{ $user->isAdmin() ? 'bi-shield-fill-check' : 'bi-person-check-fill' }} me-1"></i>
                                {{ $user->isAdmin() ? 'Thủ thư quản trị' : 'Độc giả thư viện' }}
                            </span>
                        </div>
                        <div class="text-white-50 small mb-2">
                            <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                            @if ($user->so_dien_thoai)
                                <span class="mx-2">·</span>
                                <i class="bi bi-telephone me-1"></i> {{ $user->so_dien_thoai }}
                            @endif
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                            @if ($user->hasGoogle2Fa())
                                <span class="badge bg-success bg-opacity-25 text-light border border-success border-opacity-50 small">
                                    <i class="bi bi-shield-check text-success me-1"></i> Đã bật bảo mật Google 2FA (OTP)
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-25 text-white-50 border border-secondary border-opacity-50 small">
                                    <i class="bi bi-shield-exclamation text-warning me-1"></i> Chưa bật Google 2FA
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $activeTab = request('tab', 'info');
        @endphp

        <!-- Main Tabbed Settings Card -->
        <div class="card border shadow-sm">
            <div class="card-header bg-white p-0 border-bottom">
                <ul class="nav nav-tabs border-0 px-3 pt-2" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-dark fw-semibold py-3 px-3 {{ $activeTab === 'info' ? 'active' : '' }}" 
                                id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button" role="tab">
                            <i class="bi bi-person-vcard me-1 text-primary"></i> Thông tin cá nhân
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-dark fw-semibold py-3 px-3 {{ $activeTab === 'password' ? 'active' : '' }}" 
                                id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab">
                            <i class="bi bi-key me-1 text-warning"></i> Đổi mật khẩu
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-dark fw-semibold py-3 px-3 {{ $activeTab === '2fa' ? 'active' : '' }}" 
                                id="twofa-tab" data-bs-toggle="tab" data-bs-target="#twofa-tab-pane" type="button" role="tab">
                            <i class="bi bi-shield-check me-1 text-success"></i> Google Authenticator (OTP)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="tab-content" id="profileTabsContent">
                    <!-- Tab 1: Personal Info -->
                    <div class="tab-pane fade {{ $activeTab === 'info' ? 'show active' : '' }}" id="info-tab-pane" role="tabpanel" tabindex="0">
                        <div class="mb-4">
                            <h2 class="h5 fw-bold text-dark mb-1">Cập nhật thông tin tài khoản</h2>
                            <p class="text-muted small mb-0">Quản lý số điện thoại, ngày sinh và địa chỉ liên lạc để nhận thông báo mượn trả.</p>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold text-dark small">Họ và tên <span class="text-danger">*</span></label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-person"></i></span>
                                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold text-dark small">Địa chỉ Email</label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                        <input type="email" id="email" value="{{ $user->email }}" class="form-control bg-light" disabled>
                                    </div>
                                    <div class="form-text small text-muted">Email là định danh tài khoản và không thể chỉnh sửa.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="so_dien_thoai" class="form-label fw-semibold text-dark small">Số điện thoại liên hệ</label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}" class="form-control @error('so_dien_thoai') is-invalid @enderror" placeholder="VD: 0901234567">
                                    </div>
                                    <div class="form-text small text-muted">Định dạng di động 10 số (bắt đầu bằng 03, 05, 07, 08 hoặc 09).</div>
                                    @error('so_dien_thoai')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="ngay_sinh" class="form-label fw-semibold text-dark small">Ngày tháng năm sinh</label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" id="ngay_sinh" name="ngay_sinh" value="{{ old('ngay_sinh', $user->ngay_sinh?->format('Y-m-d')) }}" class="form-control @error('ngay_sinh') is-invalid @enderror" max="{{ today()->format('Y-m-d') }}">
                                    </div>
                                    @error('ngay_sinh')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="dia_chi" class="form-label fw-semibold text-dark small">Địa chỉ thường trú / Nơi ở hiện tại</label>
                                    <div class="input-icon-group">
                                        <span class="input-icon"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" id="dia_chi" name="dia_chi" value="{{ old('dia_chi', $user->dia_chi) }}" class="form-control @error('dia_chi') is-invalid @enderror" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố...">
                                    </div>
                                    @error('dia_chi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="bi bi-check2 me-1"></i> Lưu thông tin cá nhân
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab 2: Change Password -->
                    <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="password-tab-pane" role="tabpanel" tabindex="0">
                        <div class="mb-4">
                            <h2 class="h5 fw-bold text-dark mb-1">Thay đổi mật khẩu đăng nhập</h2>
                            <p class="text-muted small mb-0">Đảm bảo tài khoản sử dụng mật khẩu mạnh tối thiểu 8 ký tự.</p>
                        </div>

                        <form action="{{ route('profile.updatePassword') }}" method="POST" style="max-width: 550px;">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold text-dark small">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <div class="input-icon-group has-password-toggle">
                                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                                    <button type="button" class="btn-toggle-password" data-target="#current_password" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-dark small">Mật khẩu mới <span class="text-danger">*</span></label>
                                <div class="input-icon-group has-password-toggle">
                                    <span class="input-icon"><i class="bi bi-key"></i></span>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Tối thiểu 8 ký tự" required autocomplete="new-password">
                                    <button type="button" class="btn-toggle-password" data-target="#password" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold text-dark small">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <div class="input-icon-group has-password-toggle">
                                    <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới" required autocomplete="new-password">
                                    <button type="button" class="btn-toggle-password" data-target="#password_confirmation" title="Hiện/ẩn mật khẩu" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning text-dark fw-bold px-4 shadow-sm">
                                <i class="bi bi-shield-check me-1"></i> Cập nhật mật khẩu mới
                            </button>
                        </form>
                    </div>

                    <!-- Tab 3: Google Authenticator (OTP) -->
                    <div class="tab-pane fade {{ $activeTab === '2fa' ? 'show active' : '' }}" id="twofa-tab-pane" role="tabpanel" tabindex="0">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h2 class="h5 fw-bold text-dark mb-1">Xác thực Google Authenticator (Mã OTP)</h2>
                                <p class="text-muted small mb-0">Sử dụng mã OTP 6 số sinh ra theo thời gian (chuẩn RFC 6238 TOTP) để bảo vệ tài khoản và khôi phục mật khẩu khi bị quên.</p>
                            </div>
                            <div>
                                @if ($user->hasGoogle2Fa())
                                    <span class="badge badge-subtle-success rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i> Đang kích hoạt
                                    </span>
                                @else
                                    <span class="badge badge-subtle-neutral rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-dash-circle"></i> Chưa liên kết
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($user->hasGoogle2Fa())
                            <div class="alert alert-success border-success-subtle mb-4">
                                <div class="fw-bold mb-1"><i class="bi bi-shield-check me-1"></i> Google Authenticator đã được liên kết</div>
                                <div class="small">Bạn chỉ cần mở ứng dụng và nhập mã 6 số hiện tại khi khôi phục mật khẩu. Không cần quét lại QR.</div>
                            </div>

                            <form action="{{ route('profile.toggleGoogle2fa') }}" method="POST" style="max-width: 520px;">
                                @csrf
                                <input type="hidden" name="action" value="disable">

                                <div class="mb-3">
                                    <label for="disable_current_password" class="form-label fw-semibold text-dark small">Mật khẩu hiện tại</label>
                                    <input type="password" id="disable_current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="disable_otp_code" class="form-label fw-semibold text-dark small">Mã OTP hiện tại</label>
                                    <input type="text" id="disable_otp_code" name="otp_code" class="form-control font-monospace @error('otp_code') is-invalid @enderror" maxlength="6" inputmode="numeric" autocomplete="one-time-code" required>
                                    @error('otp_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-outline-danger" data-confirm="Bạn chắc chắn muốn tắt bảo mật Google Authenticator?">
                                    <i class="bi bi-shield-x me-1"></i> Hủy kích hoạt 2FA
                                </button>
                            </form>
                        @else
                            <div class="row g-4 align-items-center">
                                <div class="col-md-5 text-center">
                                    <div class="p-3 bg-light rounded-3 border d-inline-block shadow-sm mb-3">
                                        <img src="{{ $qrCodeUrl }}" alt="Mã QR Google Authenticator" class="img-fluid rounded" style="max-width: 200px;">
                                    </div>
                                    <div class="small text-muted mb-1">Khóa thiết lập thủ công:</div>
                                    <div class="p-2 bg-white rounded border font-monospace fw-bold text-primary small select-all d-inline-block">
                                        {{ $secret }}
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <h3 class="h6 fw-bold text-dark mb-3"><i class="bi bi-phone me-1 text-primary"></i> Liên kết một lần</h3>
                                    <ol class="small text-secondary ps-3 mb-4 d-flex flex-column gap-2" style="line-height: 1.6;">
                                        <li>Mở Google Authenticator, Authy hoặc Microsoft Authenticator.</li>
                                        <li>Quét QR bên cạnh hoặc nhập khóa thiết lập thủ công.</li>
                                        <li>Nhập mã 6 số đang hiển thị để hoàn tất. QR sẽ giữ nguyên nếu trang tải lại.</li>
                                    </ol>

                                    <form action="{{ route('profile.toggleGoogle2fa') }}" method="POST">
                                        @csrf

                                        <div class="mb-3" style="max-width: 320px;">
                                            <label for="otp_code" class="form-label fw-semibold text-dark small">Mã OTP 6 số</label>
                                            <input type="text" id="otp_code" name="otp_code" class="form-control font-monospace fs-5 text-center letter-spacing-2 @error('otp_code') is-invalid @enderror" maxlength="6" inputmode="numeric" autocomplete="one-time-code" required>
                                            @error('otp_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-success shadow-sm">
                                            <i class="bi bi-shield-check me-1"></i> Xác nhận kích hoạt 2FA
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
