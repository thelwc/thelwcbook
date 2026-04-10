@extends('client.layouts.master') 

@section('content')
<style>
    /* CSS Giao diện Card hiện đại */
    .card-img-custom {
        width: 100%; 
        aspect-ratio: 2 / 3; /* Tỉ lệ vàng cho bìa sách */
        object-fit: cover;
        border-radius: 12px 12px 0 0;
        background-color: #f8f9fa;
        transition: transform 0.4s ease;
    }
    .hover-card { 
        border: 1px solid #eee !important; 
        transition: all 0.3s ease; 
    }
    .hover-card:hover { 
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; 
        transform: translateY(-5px); 
        border-color: #212529 !important; 
    }
    .hover-card:hover .card-img-custom {
        transform: scale(1.05);
    }
</style>

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
        {{-- LƯỚI SẢN PHẨM: 2 Cột (Mobile) - 4 Cột (Tablet) - 5 Cột (Desktop) --}}
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
            @foreach($books as $book)
            
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

            <div class="col d-flex">
                <div class="card w-100 h-100 border-0 shadow-sm position-relative hover-card rounded-4">
                    
                    {{-- BỘ TAGS XẾP DỌC (Góc trái) --}}
                    <div class="position-absolute top-0 start-0 m-2 z-1 d-flex flex-column gap-1 align-items-start">
                        @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                        @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                        @if($isEbook) <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-tablet-alt me-1"></i>Ebook</span> @endif
                    </div>

                    {{-- Ảnh --}}
                    <a href="{{ route('books.detail', $book->id) }}" class="overflow-hidden rounded-top-4">
                        <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" class="card-img-custom" alt="{{ $book->title }}" loading="lazy">
                    </a>
                    
                    <div class="card-body p-2 p-md-3 d-flex flex-column">
                        {{-- Tên sách --}}
                        <h6 class="mb-1" style="font-size: 14px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                            <a href="{{ route('books.detail', $book->id) }}" class="text-dark text-decoration-none fw-bold" title="{{ $book->title }}">
                                {{ $book->title }}
                            </a>
                        </h6>
                        
                        {{-- Tác giả & Thể loại --}}
                        <div class="mb-2">
                            <small class="text-muted d-block text-truncate mb-1" style="font-size: 12px;"><i class="fas fa-pen-nib text-xs me-1"></i> {{ $book->author ?? 'Đang cập nhật' }}</small>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 10px;">{{ $category->name }}</span>
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
                                    <span class="text-danger fw-bold" style="font-size: 16px; line-height: 1.2;">{{ number_format($book->price) }}đ</span>
                                    <span class="text-white" style="font-size: 12px; line-height: 1.2; user-select: none;">-</span> {{-- Spacer để không bị nhảy layout --}}
                                @endif
                            </div>
                            
                            
                            <div class="d-flex justify-content-between align-items-center mt-1 mb-2">
                                <small class="text-muted" style="font-size: 0.7rem;">Đã bán: {{ number_format($book->total_sold ?? 0) }}</small>
                            </div>
                            
                            {{-- Nút Mua Ngay --}}
                            <a href="{{ route('books.detail', $book->id) }}" class="btn btn-dark btn-sm w-100 rounded-pill fw-bold py-1" style="font-size: 0.85rem;">
                                <i class="fas fa-shopping-cart me-1"></i> Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Phân trang --}}
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $books->appends(request()->query())->links() }}
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
@endsection