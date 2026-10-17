<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Hệ thống quản lý thư viện hiện đại, tra cứu sách và theo dõi mượn trả thông minh.">
    <title>@yield('title', 'Thư viện') | {{ config('app.name', 'Quản Lý Thư Viện') }}</title>
    
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1 d-flex flex-column">
        <!-- Main Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('catalog.index') }}">
                    <span class="brand-mark" aria-hidden="true">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </span>
                    <div>
                        <span class="navbar-brand-text d-block lh-1">Thư viện HUIT</span>
                        <span class="navbar-brand-sub">Quản lý Thư viện</span>
                    </div>
                </a>
                
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Mở menu điều hướng">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto ms-lg-4 gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" href="{{ route('catalog.index') }}">
                                <i class="bi bi-grid-fill me-1 opacity-75"></i> Kho sách
                            </a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                    <i class="bi bi-speedometer2 me-1 opacity-75"></i> Tổng quan
                                </a>
                            </li>
                            @if (Auth::user()->isAdmin())
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-shield-lock-fill me-1 opacity-75"></i> Quản trị
                                    </a>
                                    <ul class="dropdown-menu shadow border-0 py-2 rounded-3">
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.users.index') }}">
                                                <i class="bi bi-person-badge-fill text-primary"></i> Quản lý độc giả
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.saches.index') }}">
                                                <i class="bi bi-book text-primary"></i> Quản lý sách
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.tac-gias.index') }}">
                                                <i class="bi bi-people text-info"></i> Quản lý tác giả
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.the-loais.index') }}">
                                                <i class="bi bi-tags text-warning"></i> Quản lý thể loại
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-2"></li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.phieu-muons.index') }}">
                                                <i class="bi bi-arrow-left-right text-success"></i> Phiếu mượn – trả
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.loan-reminders.index') }}">
                                                <i class="bi bi-envelope-paper-fill text-primary"></i> Thông báo hạn trả
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.phieu-phats.index') }}">
                                                <i class="bi bi-cash-coin text-danger"></i> Quản lý phiếu phạt
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user.phieu-muons.*') ? 'active' : '' }}" href="{{ route('user.phieu-muons.index') }}">
                                        <i class="bi bi-clock-history me-1 opacity-75"></i> Lịch sử mượn
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user.phieu-phats.*') ? 'active' : '' }}" href="{{ route('user.phieu-phats.index') }}">
                                        <i class="bi bi-cash-coin me-1 opacity-75"></i> Phiếu phạt
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-lg-center gap-2 mt-3 mt-lg-0">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-light btn-sm px-3 shadow-sm rounded-pill fw-semibold text-primary" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus-fill me-1"></i> Đăng ký
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                                    <span class="fw-semibold text-white">{{ Auth::user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2 rounded-3">
                                    <li class="px-3 py-2 border-bottom mb-1 bg-light rounded-top">
                                        <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                        <div class="small text-muted mb-1">{{ Auth::user()->email }}</div>
                                        <span class="badge {{ Auth::user()->isAdmin() ? 'badge-subtle-warning text-dark' : 'badge-subtle-primary' }} rounded-pill">
                                            <i class="bi {{ Auth::user()->isAdmin() ? 'bi-shield-fill-check' : 'bi-person-check-fill' }} me-1"></i>
                                            {{ Auth::user()->isAdmin() ? 'Thủ thư (Admin)' : 'Độc giả (User)' }}
                                        </span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                            <i class="bi bi-person-gear text-primary"></i> Hồ sơ cá nhân
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                        </a>
                                    </li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-grow-1">
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
                        <div class="alert {{ $alertClass }} alert-dismissible fade show shadow-sm d-flex align-items-center gap-3 rounded-3 mb-4" role="alert">
                            <i class="bi {{ $alertIcon }} fs-5 flex-shrink-0"></i>
                            <div class="flex-grow-1">{{ session($messageType) }}</div>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Đóng"></button>
                        </div>
                    @endif
                @endforeach
            </div>
            @yield('content')
        </main>

        <!-- Academic Modern Footer -->
        <footer class="app-footer">
            <div class="container">
                <div class="row g-4 justify-content-between mb-4">
                    <div class="col-lg-5">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="brand-mark" style="width: 2rem; height: 2rem; font-size: 0.95rem;">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </span>
                            <span class="h5 fw-bold text-white mb-0">Thư viện HUIT</span>
                        </div>
                        <p class="small text-secondary mb-3">
                            Hệ thống Quản lý Thư viện trực tuyến phục vụ tra cứu tài liệu học thuật, số hóa quy trình mượn trả và kiểm soát tồn kho sách theo thời gian thực.
                        </p>
                        <div class="d-flex gap-2">
                            <span class="badge bg-secondary bg-opacity-25 text-light fw-normal border border-secondary border-opacity-25">Laravel 13</span>
                            <span class="badge bg-secondary bg-opacity-25 text-light fw-normal border border-secondary border-opacity-25">PHP 8.3</span>
                            <span class="badge bg-secondary bg-opacity-25 text-light fw-normal border border-secondary border-opacity-25">Bootstrap 5</span>
                            <span class="badge bg-secondary bg-opacity-25 text-light fw-normal border border-secondary border-opacity-25">MySQL</span>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <h6 class="text-white fw-bold text-uppercase small mb-3">Liên kết nhanh</h6>
                        <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                            <li><a href="{{ route('catalog.index') }}" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Kho sách thư viện</a></li>
                            @auth
                                <li><a href="{{ route('home') }}" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Bảng điều khiển</a></li>
                                @if (Auth::user()->isAdmin())
                                    <li><a href="{{ route('admin.phieu-muons.index') }}" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Phiếu mượn - trả</a></li>
                                @else
                                    <li><a href="{{ route('user.phieu-muons.index') }}" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Lịch sử mượn cá nhân</a></li>
                                @endif
                            @else
                                <li><a href="{{ route('login') }}" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Đăng nhập độc giả</a></li>
                                <li><a href="{{ route('register') }}" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Đăng ký tài khoản</a></li>
                            @endauth
                        </ul>
                    </div>

                    <div class="col-6 col-lg-3">
                        <h6 class="text-white fw-bold text-uppercase small mb-3">Thông tin đồ án</h6>
                        <p class="small text-secondary mb-1">
                            <strong class="text-light">Học phần:</strong> Lập trình mã nguồn mở
                        </p>
                        <p class="small text-secondary mb-1">
                            <strong class="text-light">Đề tài:</strong> Xây dựng website Quản lý Thư viện
                        </p>
                        <p class="small text-secondary mb-0">
                            <strong class="text-light">Năm học:</strong> 2026
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-top border-secondary border-opacity-25 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-secondary">
                    <span>&copy; {{ date('Y') }} Thư viện HUIT. Đồ án Lập trình mã nguồn mở.</span>
                    <span>Thiết kế hiện đại, chuẩn Accessibility &amp; Responsive.</span>
                </div>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
