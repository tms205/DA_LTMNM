@extends('layouts.app')

@section('title', 'Thêm tác giả mới')

@section('content')
<div class="container-fluid px-0" style="max-width: 850px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.tac-gias.index') }}">Quản lý tác giả</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header py-3 px-4">
            <div>
                <div class="section-label mb-1">
                    <i class="bi bi-person-plus me-1"></i> Hồ sơ tác giả
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Thêm tác giả mới</h1>
            </div>
        </div>
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.tac-gias.store') }}" method="POST">
                @csrf
                @include('admin.tac-gias.form', ['submitLabel' => 'Lưu tác giả'])
            </form>
        </div>
    </div>
</div>
@endsection
