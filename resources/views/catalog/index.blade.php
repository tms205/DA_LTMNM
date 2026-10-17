@extends('layouts.app')

@section('title', 'Kho sách thư viện')

@section('content')
<!-- Hero Header Section -->
<section class="hero-section">
    <div class="hero-pattern"></div>
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <div class="hero-eyebrow mb-3">
                    <i class="bi bi-stars" aria-hidden="true"></i>
                    <span>Hệ thống Quản lý Thư viện Số</span>
                </div>
                <h1 class="display-6 fw-bold mb-3 text-white lh-sm">
                    Khám phá kho tri thức &amp; tài liệu học thuật
                </h1>
                <p class="lead text-white-50 mb-4" style="max-width: 650px;">
                    Tra cứu hàng ngàn đầu sách giáo trình, tài liệu tham khảo và văn học theo tên sách, tác giả hoặc nhà xuất bản với dữ liệu tồn kho theo thời gian thực.
                </p>
                
                <div class="d-flex flex-wrap gap-2">
                    <div class="hero-stats-badge">
                        <i class="bi bi-book text-warning"></i>
                        <span>Kho sách phong phú</span>
                    </div>
                    <div class="hero-stats-badge">
                        <i class="bi bi-tags text-info"></i>
                        <span>{{ count($theLoais) }} thể loại chuyên ngành</span>
                    </div>
                    <div class="hero-stats-badge">
                        <i class="bi bi-shield-check text-success"></i>
                        <span>Kiểm soát mượn trả tự động</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating Search Panel -->
<div class="container">
    <div class="search-panel p-3 p-md-4">
        <form id="catalog-search" action="{{ route('catalog.index', [], false) }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6 col-lg-7">
                <label for="q" class="form-label fw-semibold text-dark small mb-1">
                    <i class="bi bi-search me-1 text-primary"></i> Tìm kiếm tài liệu
                </label>
                <div class="input-icon-group">
                    <span class="input-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search" 
                           class="form-control" 
                           id="q" 
                           name="q" 
                           value="{{ $keyword }}" 
                           placeholder="Nhập tên sách, tác giả, nhà xuất bản..." 
                           autocomplete="off">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <label for="the_loai_id" class="form-label fw-semibold text-dark small mb-1">
                    <i class="bi bi-filter me-1 text-primary"></i> Phân loại thể loại
                </label>
                <select class="form-select" id="the_loai_id" name="the_loai_id">
                    <option value="">Tất cả thể loại</option>
                    @foreach ($theLoais as $theLoai)
                        <option value="{{ $theLoai->id }}" @selected($theLoaiId === $theLoai->id)>{{ $theLoai->ten_the_loai }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Book Grid Section -->
    <section class="py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4 pb-2 border-bottom">
            <div>
                <div class="section-label">
                    <i class="bi bi-collection me-1"></i> Danh mục tra cứu
                </div>
                <h2 class="page-heading h4 mb-0">Sách hiện có trong thư viện</h2>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-subtle-primary rounded-pill py-2 px-3 fw-semibold">
                    <i class="bi bi-journal-check me-1"></i>
                    <strong id="catalog-total">{{ $saches->total() }}</strong> kết quả
                </span>
            </div>
        </div>

        <div id="catalog-results" aria-live="polite">
            @include('catalog.partials.book-grid', ['saches' => $saches])
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('catalog-search');
    const results = document.getElementById('catalog-results');
    const total = document.getElementById('catalog-total');
    const keyword = document.getElementById('q');
    const category = document.getElementById('the_loai_id');
    let timer;
    let activeRequest;

    const loadResults = async (url, updateHistory = true) => {
        activeRequest?.abort();
        const request = new AbortController();
        activeRequest = request;
        results.classList.add('ajax-loading');

        try {
            const requestUrl = new URL(url, window.location.origin);
            const response = await fetch(requestUrl.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: request.signal,
            });

            if (!response.ok) {
                throw new Error('Không thể tải dữ liệu.');
            }

            const data = await response.json();
            results.innerHTML = data.html;
            total.textContent = data.total;
            if (updateHistory) {
                window.history.replaceState({}, '', requestUrl.toString());
            }
        } catch (error) {
            if (error.name === 'AbortError') return;

            results.innerHTML = '<div class="alert alert-danger d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i> Không thể tải kết quả. Vui lòng thử lại.</div>';
        } finally {
            if (activeRequest === request) {
                activeRequest = undefined;
                results.classList.remove('ajax-loading');
            }
        }
    };

    const submitSearch = () => {
        window.clearTimeout(timer);
        const actionAttr = form.getAttribute('action') || window.location.pathname;
        const url = new URL(actionAttr, window.location.origin);
        const data = new FormData(form);

        url.searchParams.delete('page');

        for (const [key, value] of data.entries()) {
            const raw = typeof value === 'string' ? value : '';
            const normalized = typeof raw.normalize === 'function' ? raw.normalize('NFC') : raw;
            const trimmed = normalized.trim();
            if (trimmed !== '') {
                url.searchParams.set(key, trimmed);
            } else {
                url.searchParams.delete(key);
            }
        }

        loadResults(url.toString());
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        submitSearch();
    });

    keyword.addEventListener('input', () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(submitSearch, 300);
    });

    keyword.addEventListener('search', submitSearch);

    category.addEventListener('change', submitSearch);

    results.addEventListener('click', (event) => {
        const link = event.target.closest('.pagination a');
        if (!link) return;
        event.preventDefault();
        loadResults(link.href);
        results.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    window.addEventListener('popstate', () => {
        const currentParams = new URLSearchParams(window.location.search);
        keyword.value = currentParams.get('q') || '';
        category.value = currentParams.get('the_loai_id') || '';
        loadResults(window.location.href, false);
    });
});
</script>
@endpush
