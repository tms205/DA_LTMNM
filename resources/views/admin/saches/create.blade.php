@extends('layouts.app')

@section('title', 'Thêm sách mới')

@section('content')
<div class="container-fluid px-0" style="max-width: 950px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.saches.index') }}">Quản lý sách</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header py-3 px-4">
            <div>
                <div class="section-label mb-1">
                    <i class="bi bi-book-half me-1"></i> Kho sách
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Thêm sách mới vào thư viện</h1>
            </div>
        </div>
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.saches.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.saches.form', ['submitLabel' => 'Lưu sách mới'])
            </form>
        </div>
    </div>
</div>
@endsection
