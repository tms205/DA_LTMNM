@extends('layouts.app')

@section('title', 'Lập phiếu mượn mới')

@section('content')
<section class="py-4 py-lg-5">
    <div class="container" style="max-width: 900px;">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.phieu-muons.index') }}">Phiếu mượn – trả</a></li>
                <li class="breadcrumb-item active">Lập phiếu mới</li>
            </ol>
        </nav>

        <div class="card border shadow-sm">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="section-label mb-1">
                    <i class="bi bi-journal-plus me-1"></i> Nghiệp vụ thư viện
                </div>
                <h1 class="h4 fw-bold text-dark mb-0">Lập phiếu mượn sách mới</h1>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.phieu-muons.store') }}" method="POST">
                    @csrf
                    @include('admin.phieu-muons.form', ['submitLabel' => 'Xác nhận tạo phiếu'])
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
