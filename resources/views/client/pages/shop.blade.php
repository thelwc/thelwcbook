@extends('client.layouts.master')

@section('title', 'Cửa hàng sách - Thelwc Books')

@section('content')

<style>
    /* CSS Đồng bộ giao diện Card hiện đại */
    .card-img-custom {
        width: 100%; 
        aspect-ratio: 2 / 3; /* Tỉ lệ vàng bìa sách */
        object-fit: cover;
        border-radius: 12px 12px 0 0;
        background-color: #f8f9fa;
    }
    .hover-card { 
        border: 1px solid #eee !important; 
        transition: all 0.3s ease; 
    }
    .hover-card:hover { 
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; 
        transform: translateY(-5px); 
        border-color: #6f42c1 !important; /* Màu tím chủ đạo cho Shop */
    }
    
    /* Header riêng cho trang Shop */
    .shop-header {
        background: linear-gradient(45deg, #6f42c1, #a885d8); /* Màu tím sang trọng */
        color: white;
        padding: 40px 0;
        margin-bottom: 30px;
        border-radius: 0 0 50% 50% / 20px;
    }
</style>

{{-- HEADER TRANG --}}
<div class="shop-header text-center shadow-sm">
    <h1 class="fw-bold animate__animated animate__fadeInDown"><i class="fas fa-book-open me-2"></i> KHO SÁCH KHỔNG LỒ</h1>
    <p class="fs-5 opacity-75 mb-0">Khám phá thế giới tri thức vô tận cùng Thelwc Books</p>
</div>

<div class="container pb-5">
    
    {{-- Bộ lọc & Sắp xếp --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3 bg-white p-3 rounded-4 shadow-sm border">
        <span class="text-muted fw-bold"><i class="fas fa-filter text-primary me-1"></i> Hiển thị <strong>{{ $books->total() }}</strong> đầu sách</span>
        <div class="d-flex gap-2 align-items-center">
            <span class="text-muted small d-none d-md-inline">Sắp xếp:</span>
            <select class="form-select form-select-sm rounded-pill border-secondary fw-bold" style="width: 180px; cursor: pointer;" onchange="location = this.value;">
                <option value="{{ route('shop') }}" {{ !request('sort') ? 'selected' : '' }}>🌟 Mặc định</option>
                <option value="{{ route('shop', ['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>📈 Giá thấp đến cao</option>
                <option value="{{ route('shop', ['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>📉 Giá cao đến thấp</option>
            </select>
        </div>
    </div>

    @if($books->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted opacity-25 mb-3"></i>
            <h4 class="fw-bold text-secondary">Không tìm thấy sách nào!</h4>
            <p class="text-muted">Thử thay đổi bộ lọc hoặc tìm kiếm từ khóa khác xem sao nhé.</p>
        </div>
    @else
        {{-- LƯỚI SẢN PHẨM: 2 Cột (Mobile) - 4 Cột (Tablet) - 5 Cột (Desktop) --}}
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
            @foreach($books as $book)
            <div class="col d-flex">
                <div class="card w-100 h-100 border-0 shadow-sm position-relative hover-card rounded-4">
                    
                    {{-- 🔥 LOGIC PHÂN LOẠI TAGS & ĐÁNH GIÁ 🔥 --}}
                    @php 
                        $isSale = $book->sale_price > 0 && $book->sale_price < $book->price;
                        $percent = $isSale ? round((($book->price - $book->sale_price)/$book->price)*100) : 0;
                        $isNew = $book->created_at && $book->created_at > now()->subDays(7);
                        $isEbook = $book->ebook_price > 0;
                        
                        // Tính Sao đánh giá
                        $avgRating = $book->reviews_avg_rating ?? ($book->reviews ? $book->reviews->avg('rating') : 0);
                        $reviewCount = $book->reviews_count ?? ($book->reviews ? $book->reviews->count() : 0);
                    @endphp

                    {{-- BỘ TAGS XẾP DỌC (Góc trái) --}}
                    <div class="position-absolute top-0 start-0 m-2 z-1 d-flex flex-column gap-1 align-items-start">
                        @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                        @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                        @if($isEbook) <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-tablet-alt me-1"></i>Ebook</span> @endif
                    </div>

                    {{-- Ảnh --}}
                    <a href="{{ route('book.detail', $book->id) }}" class="overflow-hidden rounded-top-4">
                        <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" class="card-img-custom" alt="{{ $book->title }}" loading="lazy">
                    </a>
                    
                    <div class="card-body p-2 p-md-3 d-flex flex-column">
                        {{-- Tên sách --}}
                        <h6 class="mb-1" style="font-size: 14px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                            <a href="{{ route('book.detail', $book->id) }}" class="text-dark text-decoration-none fw-bold" title="{{ $book->title }}">
                                {{ $book->title }}
                            </a>
                        </h6>
                        
                        {{-- Tác giả & Thể loại --}}
                        <div class="mb-2">
                            <small class="text-muted d-block text-truncate mb-1" style="font-size: 12px;"><i class="fas fa-pen-nib text-xs me-1"></i> {{ $book->author ?? 'Đang cập nhật' }}</small>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 10px;">{{ $book->category->name ?? 'Chưa phân loại' }}</span>
                        </div>

                        {{-- HIỂN THỊ SAO ĐÁNH GIÁ --}}
                        <div class="mb-2" style="min-height: 18px;">
                            @if($reviewCount > 0)
                                <div class="text-warning d-flex align-items-center" style="font-size: 11px;">
                                    <div class="me-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($avgRating)) <i class="fas fa-star"></i>
                                            @elseif($i == ceil($avgRating) && $avgRating - floor($avgRating) > 0) <i class="fas fa-star-half-alt"></i>
                                            @else <i class="far fa-star text-muted opacity-25"></i> @endif
                                        @endfor
                                    </div>
                                    <span class="text-dark fw-bold" style="font-size: 10px;">({{ round($avgRating, 1) }})</span>
                                </div>
                            @else
                                <span class="text-muted fst-italic" style="font-size: 11px;">Chưa có đánh giá</span>
                            @endif
                        </div>
                        
                        <div class="mt-auto">
                            {{-- Cụm Giá tiền --}}
                            <div class="d-flex flex-column justify-content-end mb-2" style="min-height: 38px;">
                                @if($isSale)
                                    <span class="text-danger fw-bold" style="font-size: 16px; line-height: 1.2;">{{ number_format($book->sale_price) }}đ</span>
                                    <span class="text-muted text-decoration-line-through" style="font-size: 12px; line-height: 1.2;">{{ number_format($book->price) }}đ</span>
                                @else
                                    <span class="text-dark fw-bold" style="font-size: 16px; line-height: 1.2;">{{ number_format($book->price) }}đ</span>
                                @endif
                            </div>
                            
                            {{-- Lượt bán --}}
                            <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center" style="min-height: 25px;">
                                <small class="text-secondary fw-bold" style="font-size: 11px;">
                                    <i class="fas fa-check-circle text-success me-1"></i>Đã bán: <b class="text-dark">{{ number_format($book->total_sold ?? 0) }}</b>
                                </small>
                            </div>
                            
                            {{-- Nút Xem Ngay --}}
                            <a href="{{ route('book.detail', $book->id) }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-bold py-1 mt-2" style="font-size: 0.85rem;">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Phân trang --}}
        <div class="mt-5 d-flex justify-content-center">
            {{-- Dùng appends() để giữ nguyên các query string (sort, category...) khi qua trang mới --}}
            {{ $books->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection