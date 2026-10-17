@extends('layouts.app')

@section('title', 'Chỉnh sửa tác giả: '.$tacGia->ten_tac_gia)

@section('content')
<section class="py-4 py-lg-5">
    <div class="container" style="max-width: 800px;">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.tac-gias.index') }}">Quản lý tác giả</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tac-gias.show', $tacGia) }}">{{ $tacGia->ten_tac_gia }}</a></li>
                <li class="breadcrumb-item active">Chỉnh sửa</li>
            </ol>
        </nav>

        <div class="card border shadow-sm">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="section-label mb-1">
                    <i class="bi bi-pencil-square me-1"></i> Hồ sơ tác giả
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Chỉnh sửa: {{ $tacGia->ten_tac_gia }}</h1>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.tac-gias.update', $tacGia) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.tac-gias.form', ['submitLabel' => 'Cập nhật tác giả'])
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
