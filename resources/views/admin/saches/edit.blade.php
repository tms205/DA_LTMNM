@extends('layouts.app')

@section('title', 'Chỉnh sửa: '.$sach->ten_sach)

@section('content')
<div class="container-fluid px-0" style="max-width: 950px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.saches.index') }}">Quản lý sách</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.saches.show', $sach) }}">{{ Str::limit($sach->ten_sach, 30) }}</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header py-3 px-4">
            <div>
                <div class="section-label mb-1">
                    <i class="bi bi-pencil-square me-1"></i> Cập nhật thông tin
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Chỉnh sửa sách: {{ $sach->ten_sach }}</h1>
            </div>
        </div>
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.saches.update', $sach) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.saches.form', ['submitLabel' => 'Cập nhật thay đổi'])
            </form>
        </div>
    </div>
</div>
@endsection
