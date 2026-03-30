@extends('client.layouts.master') 

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-uppercase border-bottom pb-2 border-primary d-inline-block">
                {{ $category->name }}
            </h2>
        </div>
    </div>

    {{-- Danh sách sách --}}
    @if($books->count() > 0)
        {{-- 🔥 SỬA GRID: row-cols-md-5 để hiện 5 cuốn/hàng giống trang chủ --}}
        <div class="row row-cols-2 row-cols-md-5 g-3">
            @foreach($books as $book)
            <div class="col">
                <div class="card h-100 shadow-sm border-0 position-relative product-card">
                    
                    {{-- Badge Giảm giá --}}
                    @if($book->sale_price > 0 && $book->sale_price < $book->price)
                        <span class="position-absolute top-0 start-0 badge bg-danger m-2 shadow" style="z-index: 2;">
                            -{{ round((($book->price - $book->sale_price)/$book->price)*100) }}%
                        </span>
                    @endif

                    <a href="{{ route('books.detail', $book->id) }}" class="overflow-hidden rounded-top">
                        {{-- 🔥 CODE ẢNH CHUẨN TỪ TRANG CHỦ --}}
                        <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" 
                             class="card-img-top" 
                             alt="{{ $book->title }}" 
                             style="height: 280px; object-fit: cover; width: 100%;">
                    </a>

                    <div class="card-body p-3 d-flex flex-column">
                        {{-- Tên sách --}}
                        <h6 class="card-title text-truncate fw-bold mb-1">
                            <a href="{{ route('books.detail', $book->id) }}" class="text-decoration-none text-dark" title="{{ $book->title }}">
                                {{ $book->title }}
                            </a>
                        </h6>

                        {{-- Tác giả (Thêm mới cho giống trang chủ) --}}
                        <p class="text-secondary small mb-2 text-truncate">{{ $book->author }}</p>

                        <div class="mt-auto">
                            {{-- Giá tiền --}}
                            @if($book->sale_price > 0 && $book->sale_price < $book->price)
                                <span class="text-danger fw-bold fs-6">{{ number_format($book->sale_price) }}đ</span>
                                <span class="text-muted text-decoration-line-through small ms-1">{{ number_format($book->price) }}đ</span>
                            @else
                                <span class="text-danger fw-bold fs-6">{{ number_format($book->price) }}đ</span>
                            @endif

                            {{-- Nút mua (Đổi sang btn-dark cho giống section Sách Mới) --}}
                            <a href="{{ route('books.detail', $book->id) }}" class="btn btn-dark btn-sm w-100 rounded-pill mt-2 fw-bold">
                                <i class="fas fa-shopping-cart me-1"></i> Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Phân trang --}}
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $books->links() }}
            </div>
        </div>

    @else
        {{-- Màn hình trống --}}
        <div class="row">
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="120" class="mb-3 opacity-50">
                <h5 class="text-muted">Chưa có sách nào trong danh mục này!</h5>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3">Quay lại trang chủ</a>
            </div>
        </div>
    @endif
</div>

<style>
    /* Hiệu ứng hover nhẹ nhàng */
    .product-card { transition: all 0.3s ease; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    
    /* Chỉnh lại ảnh cho chuẩn tỉ lệ dọc */
    .card-img-top {
        transition: transform 0.3s ease;
    }
    .product-card:hover .card-img-top {
        transform: scale(1.05);
    }
</style>
@endsection