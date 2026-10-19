@extends('layouts.app')

@section('title', 'Thêm thể loại mới')

@section('content')
<div class="container-fluid px-0" style="max-width: 850px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.the-loais.index') }}">Quản lý thể loại</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header py-3 px-4">
            <div>
                <div class="section-label mb-1">
                    <i class="bi bi-tag-fill me-1"></i> Danh mục thể loại
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Thêm thể loại sách mới</h1>
            </div>
        </div>
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.the-loais.store') }}" method="POST">
                @csrf
                @include('admin.the-loais.form', ['submitLabel' => 'Lưu thể loại'])
            </form>
        </div>
    </div>
</div>
@endsection
