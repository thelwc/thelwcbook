@extends('client.layouts.master')

@section('title', 'Kết quả tìm kiếm: ' . $keyword)

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
        border-color: #0d6efd !important; /* Màu xanh Primary báo hiệu tìm kiếm */
    }
</style>

<div class="container py-4 py-md-5">
    
    {{-- Tiêu đề kết quả --}}
    <div class="mb-4 pb-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fas fa-search me-2 text-primary"></i>
                Kết quả tìm kiếm cho: "<span class="text-primary">{{ $keyword }}</span>"
            </h3>
            <p class="text-muted mb-0">Tìm thấy <strong>{{ $books->total() }}</strong> kết quả phù hợp.</p>
        </div>
        
        {{-- Nút quay lại shop --}}
        <a href="{{ route('shop') }}" class="btn btn-light border rounded-pill shadow-sm fw-bold px-4">
            <i class="fas fa-store me-1"></i> Xem tất cả sách
        </a>
    </div>

    @if($books->count() > 0)
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

                    {{-- Ảnh Sách --}}
                    <a href="{{ route('book.detail', $book->id) }}" class="overflow-hidden rounded-top-4">
                        <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" 
                             class="card-img-custom" loading="lazy" alt="{{ $book->title }}">
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

                        {{-- NẾU TÌM BẰNG TÊN NXB THÌ HIGHLIGHT NXB ĐÓ LÊN --}}
                        @if($book->publisher && stripos($book->publisher->name, $keyword) !== false)
                            <div class="mb-1">
                                <span class="badge bg-info bg-opacity-10 text-info border border-info" style="font-size: 10px;">
                                    <i class="fas fa-building me-1"></i>{{ $book->publisher->name }}
                                </span>
                            </div>
                        @endif

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
                            <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 11px;">Đã bán: <b class="text-dark">{{ number_format($book->total_sold ?? 0) }}</b></small>
                            </div>
                            
                            {{-- Nút Xem Ngay --}}
                            <a href="{{ route('book.detail', $book->id) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold py-1 mt-2" style="font-size: 0.85rem;">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Phân trang (Giữ lại tất cả các tham số trên URL) --}}
        <div class="mt-5 d-flex justify-content-center">
            {{ $books->appends(request()->query())->links() }}
        </div>
    @else
        {{-- Khi không tìm thấy kết quả --}}
        <div class="text-center py-5 my-md-5 bg-white rounded-4 border shadow-sm mx-auto" style="max-width: 600px;">
            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="120" class="mb-4 opacity-50">
            <h4 class="fw-bold text-dark">Oops! Không tìm thấy cuốn sách nào.</h4>
            <p class="text-muted px-4">Chúng tôi không tìm thấy kết quả nào phù hợp với từ khóa "<span class="text-danger fw-bold">{{ $keyword }}</span>". Hãy thử tìm với tên tác giả hoặc từ khóa ngắn hơn nhé!</p>
            <a href="{{ route('shop') }}" class="btn btn-primary rounded-pill fw-bold px-4 mt-2 shadow-sm">
                <i class="fas fa-store me-1"></i> Khám phá kho sách
            </a>
        </div>
    @endif
</div>

@endsection