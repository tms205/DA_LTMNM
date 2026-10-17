@extends('layouts.app')

@section('title', 'Chỉnh sửa phiếu #'.$phieuMuon->id)

@section('content')
<section class="py-4 py-lg-5">
    <div class="container" style="max-width: 900px;">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.phieu-muons.index') }}">Phiếu mượn – trả</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.phieu-muons.show', $phieuMuon) }}">Phiếu #{{ $phieuMuon->id }}</a></li>
                <li class="breadcrumb-item active">Chỉnh sửa</li>
            </ol>
        </nav>

        <div class="card border shadow-sm">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="section-label mb-1">
                    <i class="bi bi-pencil-square me-1"></i> Cập nhật phiếu mượn
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Chỉnh sửa phiếu mượn #{{ $phieuMuon->id }}</h1>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.phieu-muons.update', $phieuMuon) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.phieu-muons.form', ['submitLabel' => 'Lưu cập nhật'])
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
