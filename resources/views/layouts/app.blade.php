<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Hệ thống Quản lý Thư viện số hiện đại - Tra cứu tài liệu, theo dõi phiếu mượn trả và kiểm soát tồn kho.">
    <title>@yield('title', 'Thư viện') | {{ config('app.name', 'Quản Lý Thư Viện') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="skip-link">Chuyển đến nội dung chính</a>

    @auth
        <!-- ========================================== -->
        <!-- GENTELELLA AUTHENTICATED WORKSPACE SHELL   -->
        <!-- ========================================== -->
        <div class="app-shell">
            <!-- Sidebar -->
            <aside class="app-sidebar" aria-label="Thanh điều hướng chính">
                <!-- Sidebar Header / Brand -->
                <div class="sidebar-header">
                    <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <span class="sidebar-brand-mark" aria-hidden="true">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </span>
                        <div>
                            <span class="sidebar-brand-text d-block">Thư viện HUIT</span>
                            <span class="sidebar-brand-sub">Quản lý Thư viện</span>
                        </div>
                    </a>
                </div>

                <!-- Sidebar User Profile Info -->
                <div class="sidebar-profile">
                    <span class="sidebar-avatar" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <div class="sidebar-profile-info">
                        <div class="sidebar-user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                        <span class="badge {{ Auth::user()->isAdmin() ? 'badge-subtle-warning text-dark' : 'badge-subtle-primary' }} rounded-pill" style="font-size: 0.7rem;">
                            <i class="bi {{ Auth::user()->isAdmin() ? 'bi-shield-fill-check' : 'bi-person-check-fill' }} me-1"></i>
                            {{ Auth::user()->isAdmin() ? 'Thủ thư' : 'Độc giả' }}
                        </span>
                    </div>
                </div>

                <!-- Sidebar Nav Menu -->
                <div class="sidebar-nav">
                    <!-- General Group -->
                    <div class="sidebar-group">
                        <div class="sidebar-heading">Bảng điều khiển</div>
                        <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 sidebar-icon"></i>
                            <span class="sidebar-text">Tổng quan</span>
                        </a>
                    </div>

                    @if (Auth::user()->isAdmin())
                        <!-- Admin Operations -->
                        <div class="sidebar-group">
                            <div class="sidebar-heading">Nghiệp vụ lưu thông</div>
                            <a href="{{ route('admin.phieu-muons.index') }}" class="sidebar-link {{ request()->routeIs('admin.phieu-muons.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-left-right sidebar-icon"></i>
                                <span class="sidebar-text">Phiếu mượn – trả</span>
                            </a>
                            <a href="{{ route('admin.loan-reminders.index') }}" class="sidebar-link {{ request()->routeIs('admin.loan-reminders.*') ? 'active' : '' }}">
                                <i class="bi bi-envelope-paper-fill sidebar-icon"></i>
                                <span class="sidebar-text">Thông báo hạn trả</span>
                            </a>
                            <a href="{{ route('admin.phieu-phats.index') }}" class="sidebar-link {{ request()->routeIs('admin.phieu-phats.*') ? 'active' : '' }}">
                                <i class="bi bi-cash-coin sidebar-icon"></i>
                                <span class="sidebar-text">Quản lý phiếu phạt</span>
                            </a>
                        </div>

                        <!-- Admin Catalog Management -->
                        <div class="sidebar-group">
                            <div class="sidebar-heading">Quản trị danh mục</div>
                            <a href="{{ route('admin.saches.index') }}" class="sidebar-link {{ request()->routeIs('admin.saches.*') ? 'active' : '' }}">
                                <i class="bi bi-book sidebar-icon"></i>
                                <span class="sidebar-text">Quản lý sách</span>
                            </a>
                            <a href="{{ route('admin.tac-gias.index') }}" class="sidebar-link {{ request()->routeIs('admin.tac-gias.*') ? 'active' : '' }}">
                                <i class="bi bi-people sidebar-icon"></i>
                                <span class="sidebar-text">Quản lý tác giả</span>
                            </a>
                            <a href="{{ route('admin.the-loais.index') }}" class="sidebar-link {{ request()->routeIs('admin.the-loais.*') ? 'active' : '' }}">
                                <i class="bi bi-tags sidebar-icon"></i>
                                <span class="sidebar-text">Quản lý thể loại</span>
                            </a>
                        </div>

                        <!-- Admin Readers & Users -->
                        <div class="sidebar-group">
                            <div class="sidebar-heading">Người dùng &amp; Hệ thống</div>
                            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="bi bi-person-badge-fill sidebar-icon"></i>
                                <span class="sidebar-text">Quản lý độc giả</span>
                            </a>
                            <a href="{{ route('catalog.index') }}" class="sidebar-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}">
                                <i class="bi bi-grid-fill sidebar-icon"></i>
                                <span class="sidebar-text">Kho sách công khai</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                <i class="bi bi-person-gear sidebar-icon"></i>
                                <span class="sidebar-text">Hồ sơ cá nhân</span>
                            </a>
                        </div>
                    @else
                        <!-- Reader Navigation -->
                        <div class="sidebar-group">
                            <div class="sidebar-heading">Mượn trả &amp; Sách</div>
                            <a href="{{ route('user.phieu-muons.index') }}" class="sidebar-link {{ request()->routeIs('user.phieu-muons.*') ? 'active' : '' }}">
                                <i class="bi bi-clock-history sidebar-icon"></i>
                                <span class="sidebar-text">Lịch sử mượn</span>
                            </a>
                            <a href="{{ route('user.phieu-phats.index') }}" class="sidebar-link {{ request()->routeIs('user.phieu-phats.*') ? 'active' : '' }}">
                                <i class="bi bi-cash-coin sidebar-icon"></i>
                                <span class="sidebar-text">Phiếu phạt của tôi</span>
                            </a>
                            <a href="{{ route('catalog.index') }}" class="sidebar-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}">
                                <i class="bi bi-grid-fill sidebar-icon"></i>
                                <span class="sidebar-text">Tra cứu kho sách</span>
                            </a>
                        </div>

                        <div class="sidebar-group">
                            <div class="sidebar-heading">Tài khoản</div>
                            <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                <i class="bi bi-person-gear sidebar-icon"></i>
                                <span class="sidebar-text">Hồ sơ &amp; Bảo mật</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Sidebar Footer Quick Logout -->
                <div class="p-3 border-top border-secondary border-opacity-10 mt-auto">
                    <a href="{{ route('logout') }}" 
                       class="sidebar-link text-danger" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right sidebar-icon"></i>
                        <span class="sidebar-text">Đăng xuất</span>
                    </a>
                </div>
            </aside>

            <!-- Main Application Area -->
            <div class="app-main-wrapper">
                <!-- Topbar -->
                <header class="app-topbar">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" 
                                id="btn-sidebar-toggle" 
                                class="topbar-toggle" 
                                aria-label="Thu gọn hoặc mở rộng thanh điều hướng" 
                                aria-expanded="false" 
                                aria-pressed="false">
                            <i class="bi bi-list"></i>
                        </button>

                        <div class="topbar-context d-none d-md-flex">
                            <span class="fw-semibold text-dark">
                                @if (Auth::user()->isAdmin())
                                    <i class="bi bi-shield-lock-fill text-primary me-1"></i> Không gian Quản trị
                                @else
                                    <i class="bi bi-person-circle text-primary me-1"></i> Cổng thông tin Độc giả
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex" title="Mở danh mục tra cứu sách">
                            <i class="bi bi-search me-1"></i> Tra cứu sách
                        </a>

                        <!-- User Profile Dropdown -->
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none py-1 px-2 rounded-2" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="user-avatar" style="width: 2rem; height: 2rem; font-size: 0.8rem;">
                                    {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <span class="fw-semibold text-dark small d-none d-md-inline">{{ Auth::user()->name }}</span>
                                <i class="bi bi-chevron-down small text-muted"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2 rounded-3 mt-1" style="min-width: 220px;">
                                <li class="px-3 py-2 border-bottom mb-1 bg-light">
                                    <div class="fw-bold text-dark text-truncate">{{ Auth::user()->name }}</div>
                                    <div class="small text-muted text-truncate mb-1">{{ Auth::user()->email }}</div>
                                    <span class="badge {{ Auth::user()->isAdmin() ? 'badge-subtle-warning text-dark' : 'badge-subtle-primary' }} rounded-pill">
                                        {{ Auth::user()->isAdmin() ? 'Thủ thư' : 'Độc giả' }}
                                    </span>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person-gear text-primary"></i> Hồ sơ cá nhân
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('catalog.index') }}">
                                        <i class="bi bi-book text-info"></i> Kho sách thư viện
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>

                <!-- Main Content -->
                <main id="main-content" class="app-main-content">
                    @foreach (['success', 'error', 'info'] as $messageType)
                        @if (session($messageType))
                            @php
                                $alertClass = match($messageType) {
                                    'success' => 'alert-success border-success-subtle',
                                    'error' => 'alert-danger border-danger-subtle',
                                    default => 'alert-info border-info-subtle',
                                };
                                $alertIcon = match($messageType) {
                                    'success' => 'bi-check-circle-fill text-success',
                                    'error' => 'bi-exclamation-triangle-fill text-danger',
                                    default => 'bi-info-circle-fill text-info',
                                };
                            @endphp
                            <div class="alert {{ $alertClass }} alert-dismissible fade show shadow-sm d-flex align-items-center gap-3 rounded-2 mb-4" role="alert">
                                <i class="bi {{ $alertIcon }} fs-5 flex-shrink-0"></i>
                                <div class="flex-grow-1">{{ session($messageType) }}</div>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Đóng"></button>
                            </div>
                        @endif
                    @endforeach

                    @yield('content')
                </main>

                <!-- Admin Compact Footer -->
                <footer class="app-admin-footer">
                    <span>&copy; {{ date('Y') }} <strong>Thư viện HUIT</strong> &middot; Hệ thống Quản lý Thư viện trực tuyến</span>
                    <span class="d-none d-md-inline text-muted">Gentelella UI &middot; Laravel 12 &middot; PHP 8.3 &middot; Chuẩn WCAG &amp; Responsive</span>
                </footer>
            </div>

            <!-- Mobile Backdrop -->
            <div id="sidebar-backdrop" class="sidebar-backdrop" tabindex="-1" aria-hidden="true"></div>
        </div>
    @else
        <!-- ========================================== -->
        <!-- PUBLIC GUEST NAVBAR & LAYOUT               -->
        <!-- ========================================== -->
        <div class="d-flex flex-column min-vh-100">
            <nav class="navbar navbar-expand-lg navbar-dark guest-navbar sticky-top">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('catalog.index') }}">
                        <span class="sidebar-brand-mark" aria-hidden="true" style="width: 2rem; height: 2rem; font-size: 0.95rem;">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </span>
                        <div>
                            <span class="navbar-brand-text d-block lh-1">Thư viện HUIT</span>
                            <span class="sidebar-brand-sub" style="font-size: 0.65rem;">Quản lý Thư viện</span>
                        </div>
                    </a>

                    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbar" aria-controls="guestNavbar" aria-expanded="false" aria-label="Mở menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="guestNavbar">
                        <ul class="navbar-nav me-auto ms-lg-4">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" href="{{ route('catalog.index') }}">
                                    <i class="bi bi-grid-fill me-1"></i> Kho sách thư viện
                                </a>
                            </li>
                        </ul>

                        <ul class="navbar-nav ms-auto align-items-lg-center gap-2 mt-3 mt-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill text-white fw-semibold" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus-fill me-1"></i> Đăng ký độc giả
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main id="main-content" class="flex-grow-1">
                <div class="container pt-3">
                    @foreach (['success', 'error', 'info'] as $messageType)
                        @if (session($messageType))
                            @php
                                $alertClass = match($messageType) {
                                    'success' => 'alert-success border-success-subtle',
                                    'error' => 'alert-danger border-danger-subtle',
                                    default => 'alert-info border-info-subtle',
                                };
                                $alertIcon = match($messageType) {
                                    'success' => 'bi-check-circle-fill text-success',
                                    'error' => 'bi-exclamation-triangle-fill text-danger',
                                    default => 'bi-info-circle-fill text-info',
                                };
                            @endphp
                            <div class="alert {{ $alertClass }} alert-dismissible fade show shadow-sm d-flex align-items-center gap-3 rounded-2 mb-4" role="alert">
                                <i class="bi {{ $alertIcon }} fs-5 flex-shrink-0"></i>
                                <div class="flex-grow-1">{{ session($messageType) }}</div>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Đóng"></button>
                            </div>
                        @endif
                    @endforeach
                </div>
                @yield('content')
            </main>

            <footer class="guest-footer">
                <div class="container">
                    <div class="row g-4 justify-content-between mb-4">
                        <div class="col-lg-5">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="sidebar-brand-mark" style="width: 2rem; height: 2rem; font-size: 0.95rem;">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                </span>
                                <span class="h5 fw-bold text-white mb-0">Thư viện HUIT</span>
                            </div>
                            <p class="small text-secondary mb-3">
                                Hệ thống Quản lý Thư viện trực tuyến phục vụ tra cứu tài liệu học thuật, số hóa quy trình mượn trả và kiểm soát tồn kho sách theo thời gian thực.
                            </p>
                        </div>
                        <div class="col-6 col-lg-3">
                            <h6 class="text-white fw-bold text-uppercase small mb-3">Liên kết nhanh</h6>
                            <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                                <li><a href="{{ route('catalog.index') }}"><i class="bi bi-chevron-right me-1 small"></i> Kho sách thư viện</a></li>
                                <li><a href="{{ route('login') }}"><i class="bi bi-chevron-right me-1 small"></i> Đăng nhập độc giả</a></li>
                                <li><a href="{{ route('register') }}"><i class="bi bi-chevron-right me-1 small"></i> Đăng ký tài khoản</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-lg-3">
                            <h6 class="text-white fw-bold text-uppercase small mb-3">Thông tin đồ án</h6>
                            <p class="small text-secondary mb-1"><strong class="text-light">Học phần:</strong> Lập trình mã nguồn mở</p>
                            <p class="small text-secondary mb-1"><strong class="text-light">Đề tài:</strong> Quản lý Thư viện</p>
                            <p class="small text-secondary mb-0"><strong class="text-light">Năm học:</strong> {{ date('Y') }}</p>
                        </div>
                    </div>
                    <div class="pt-3 border-top border-secondary border-opacity-25 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-secondary">
                        <span>&copy; {{ date('Y') }} Thư viện HUIT. Đồ án Lập trình mã nguồn mở.</span>
                        <span>Thiết kế Gentelella UX &middot; Chuẩn Responsive &amp; Accessibility.</span>
                    </div>
                </div>
            </footer>
        </div>
    @endauth

    <!-- Hidden logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    @stack('scripts')
</body>
</html>
