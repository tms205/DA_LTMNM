@extends('layouts.app')

@section('title', 'Lập phiếu mượn mới')

@section('content')
<div class="container-fluid px-0" style="max-width: 950px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb py-2 px-3 bg-white border rounded-2 shadow-sm small">
            <li class="breadcrumb-item"><a href="{{ route('admin.phieu-muons.index') }}">Phiếu mượn – trả</a></li>
            <li class="breadcrumb-item active">Lập phiếu mới</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header py-3 px-4">
            <div>
                <div class="section-label mb-1">
                    <i class="bi bi-journal-plus me-1"></i> Nghiệp vụ thư viện
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Lập phiếu mượn sách mới</h1>
            </div>
        </div>
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.phieu-muons.store') }}" method="POST">
                @csrf
                @include('admin.phieu-muons.form', ['submitLabel' => 'Xác nhận tạo phiếu'])
            </form>
        </div>
    </div>
</div>
@endsection
