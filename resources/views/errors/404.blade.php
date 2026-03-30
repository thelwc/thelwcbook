@extends('client.layouts.master')

@section('title')
    404 - Không tìm thấy trang
@endsection

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center text-center py-5" style="min-height: 60vh;">
    
    {{-- 1. Icon hoặc Số 404 to đùng --}}
    <div class="mb-4">
        <h1 class="fw-bold text-secondary" style="font-size: 8rem; opacity: 0.2;">404</h1>
        <div class="position-absolute start-50 translate-middle-x" style="margin-top: -80px;">
            <i class="fas fa-book-dead fa-5x text-warning"></i>
        </div>
    </div>

    {{-- 2. Lời nhắn dễ thương --}}
    <h2 class="fw-bold text-dark mb-3">Úi! Lạc đường rồi?</h2>
    <p class="text-secondary fs-5 mb-4" style="max-width: 500px;">
        Xin lỗi, cuốn sách hoặc trang bạn đang tìm kiếm có vẻ như đã bị thất lạc trong thư viện hoặc không tồn tại.
    </p>

    {{-- 3. Các nút điều hướng --}}
    <div class="d-flex gap-3">
        <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
            <i class="fas fa-home me-2"></i> Về trang chủ
        </a>
        <a href="{{ route('shop') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold">
            <i class="fas fa-search me-2"></i> Tìm sách khác
        </a>
    </div>

</div>
@endsection