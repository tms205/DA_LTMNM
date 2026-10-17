@extends('layouts.app')

@section('title', 'Thêm thể loại mới')

@section('content')
<section class="py-4 py-lg-5">
    <div class="container" style="max-width: 800px;">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.the-loais.index') }}">Quản lý thể loại</a></li>
                <li class="breadcrumb-item active">Thêm mới</li>
            </ol>
        </nav>

        <div class="card border shadow-sm">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="section-label mb-1">
                    <i class="bi bi-tag-fill me-1"></i> Danh mục thể loại
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Thêm thể loại sách mới</h1>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.the-loais.store') }}" method="POST">
                    @csrf
                    @include('admin.the-loais.form', ['submitLabel' => 'Lưu thể loại'])
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
