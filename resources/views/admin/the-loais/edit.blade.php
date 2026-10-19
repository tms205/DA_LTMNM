@extends('layouts.app')

@section('title', 'Chỉnh sửa thể loại: '.$theLoai->ten_the_loai)

@section('content')
<div class="container-fluid px-0" style="max-width: 850px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.the-loais.index') }}">Quản lý thể loại</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.the-loais.show', $theLoai) }}">{{ $theLoai->ten_the_loai }}</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header py-3 px-4">
            <div>
                <div class="section-label mb-1">
                    <i class="bi bi-pencil-square me-1"></i> Danh mục thể loại
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Chỉnh sửa: {{ $theLoai->ten_the_loai }}</h1>
            </div>
        </div>
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.the-loais.update', $theLoai) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.the-loais.form', ['submitLabel' => 'Cập nhật thể loại'])
            </form>
        </div>
    </div>
</div>
@endsection
