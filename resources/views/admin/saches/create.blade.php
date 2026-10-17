@extends('layouts.app')

@section('title', 'Thêm sách mới')

@section('content')
<section class="py-4 py-lg-5">
    <div class="container" style="max-width: 900px;">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.saches.index') }}">Quản lý sách</a></li>
                <li class="breadcrumb-item active">Thêm mới</li>
            </ol>
        </nav>

        <div class="card border shadow-sm">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="section-label mb-1">
                    <i class="bi bi-book-half me-1"></i> Kho sách
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Thêm sách mới vào thư viện</h1>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.saches.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.saches.form', ['submitLabel' => 'Lưu sách mới'])
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
