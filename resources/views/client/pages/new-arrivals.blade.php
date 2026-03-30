@extends('client.layouts.master')

@section('title')
    📚 Sách Mới Phát Hành (7 ngày qua) - Thelwc Books
@endsection

@section('content')

<style>
    /* CSS Giao diện Fahasa nhỏ gọn */
    .card-img-custom {
        width: 100%; height: 200px; object-fit: contain;
        padding: 8px; margin: 0 auto;
    }
    .card { border: 1px solid #eee !important; transition: all 0.3s ease; }
    .card:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important; transform: translateY(-3px); border-color: #0dcaf0 !important; }
    
    .book-title {
        font-size: 14px; font-weight: 600; line-height: 1.4; height: 40px;
        overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; color: #333;
    }
    .price-text { font-size: 16px; color: #C92127; font-weight: 700; }
    
    /* Header riêng cho trang New (Màu Xanh Mát Mẻ) */
    .new-header {
        background: linear-gradient(45deg, #0dcaf0, #0d6efd);
        color: white;
        padding: 40px 0;
        margin-bottom: 30px;
        border-radius: 0 0 50% 50% / 20px;
    }
</style>

{{-- HEADER TRANG --}}
<div class="new-header text-center">
    <h1 class="fw-bold animate__animated animate__fadeInDown">✨ SÁCH MỚI VỀ ✨</h1>
    <p class="fs-5 opacity-75">Cập nhật những đầu sách nóng hổi nhất trong 7 ngày qua</p>
</div>

<div class="container pb-5">
    {{-- Nếu không có sách nào mới trong 7 ngày --}}
    @if($books->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3 opacity-50"></i>
            <h3>Tuần này chưa có sách mới!</h3>
            <p class="text-muted">Đội ngũ Thelwc đang nhập hàng, bạn quay lại sau nhé.</p>
            <a href="{{ route('shop') }}" class="btn btn-outline-primary rounded-pill">Xem tất cả sách</a>
        </div>
    @else
        {{-- LƯỚI SẢN PHẨM --}}
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
            @foreach($books as $book)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm position-relative">
                    {{-- Badge NEW màu xanh --}}
                    <span class="position-absolute top-0 end-0 badge bg-info text-dark m-2 shadow fw-bold" style="font-size: 10px;">
                        NEW <i class="fas fa-star text-white ms-1"></i>
                    </span>

                    <a href="{{ route('book.detail', $book->id) }}">
                        <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" class="card-img-custom rounded-top">
                    </a>
                    
                    <div class="card-body p-2 d-flex flex-column">
                        <h6 class="mb-1"><a href="{{ route('book.detail', $book->id) }}" class="book-title text-decoration-none">{{ $book->title }}</a></h6>
                        <p class="text-muted small mb-1 text-truncate">{{ $book->author }}</p>
                        
                        <div class="mt-auto">
                            @if($book->sale_price && $book->sale_price < $book->price)
                                <div class="d-flex flex-column">
                                    <span class="price-text">{{ number_format($book->sale_price) }}đ</span>
                                    <small class="text-decoration-line-through text-muted" style="font-size: 11px">{{ number_format($book->price) }}đ</small>
                                </div>
                            @else
                                <span class="price-text">{{ number_format($book->price) }}đ</span>
                            @endif
                            
                            <a href="{{ route('book.detail', $book->id) }}" class="btn btn-primary btn-sm w-100 rounded-pill mt-2 fw-bold" style="font-size: 12px">
                                Xem Ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Phân trang --}}
        <div class="mt-5 d-flex justify-content-center">
            {{ $books->links() }}
        </div>
    @endif
</div>

@endsection