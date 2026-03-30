@extends('client.layouts.master')

@section('title', 'Kho Sách Điện Tử (Ebook) - Thelwc Books')

@section('styles')
<style>
    /* 1. Banner Gradient Xanh */
    .ebook-banner {
        background: linear-gradient(135deg, #0061ff 0%, #60efff 100%);
        color: white;
        padding: 40px 0;
        border-radius: 0 0 30px 30px;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0, 97, 255, 0.3);
    }

    /* 2. Hiệu ứng Card */
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #f0f0f0;
    }
    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        border-color: #b3d7ff;
    }

    /* 3. Ảnh Sách Responsive (Dùng aspect-ratio thay vì height cố định để ko bị méo) */
    .card-img-custom {
        width: 100%;
        aspect-ratio: 2 / 3; /* Tỉ lệ vàng của bìa sách */
        object-fit: cover; 
        border-radius: 12px 12px 0 0;
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')

{{-- HEADER EBOOK --}}
<div class="ebook-banner text-center position-relative overflow-hidden">
    <div class="container position-relative z-1">
        <h1 class="fw-bold display-5 mb-2">
            <i class="fas fa-tablet-alt me-2"></i> KHO EBOOK SỐ
        </h1>
        <p class="fs-5 opacity-90 mb-0">Đọc mọi lúc, mọi nơi - Kho tàng tri thức trong tầm tay</p>
        
        {{-- Form tìm kiếm --}}
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <form action="{{ route('ebooks') }}" method="GET">
                    <div class="input-group shadow-sm">
                        <input type="text" name="keyword" class="form-control border-0 rounded-start-pill py-3 px-4" 
                               placeholder="Tìm tên sách ebook..." value="{{ request('keyword') }}">
                        <button class="btn btn-light text-primary fw-bold border-0 rounded-end-pill px-4 hover-lift" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Họa tiết nền --}}
    <i class="fas fa-wifi position-absolute text-white opacity-10" style="font-size: 12rem; top: -30px; left: -30px; transform: rotate(-15deg);"></i>
    <i class="fas fa-cloud-download-alt position-absolute text-white opacity-10" style="font-size: 12rem; bottom: -30px; right: -30px; transform: rotate(10deg);"></i>
</div>

<div class="container pb-5">
    
    {{-- Bộ lọc danh mục --}}
    <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
        <a href="{{ route('ebooks') }}" class="btn btn-sm rounded-pill {{ !request('category_id') ? 'btn-primary' : 'btn-outline-secondary' }} px-3">
            Tất cả
        </a>
        @foreach($categories as $cate)
            <a href="{{ route('ebooks', ['category_id' => $cate->id]) }}" 
               class="btn btn-sm rounded-pill {{ request('category_id') == $cate->id ? 'btn-primary' : 'btn-outline-secondary' }} px-3">
                {{ $cate->name }}
            </a>
        @endforeach
    </div>

    @if($books->isEmpty())
        <div class="text-center py-5">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/searching-not-found-2130355-1800920.png" width="150" class="mb-3 opacity-75">
            <h4 class="fw-bold text-secondary">Không tìm thấy Ebook nào!</h4>
            <p class="text-muted">Thử tìm từ khóa khác xem sao nhé.</p>
        </div>
    @else
        {{-- 🔥 ĐÃ ĐỔI GRID: 2 Cột (Mobile) - 4 Cột (Tablet) - 5 Cột (Desktop) 🔥 --}}
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
            @foreach($books as $book)
            <div class="col d-flex">
                <div class="card w-100 h-100 border-0 shadow-sm hover-card rounded-4 position-relative">
                    
                    {{-- 🔥 LOGIC TAGS (Mới, Sale, Ebook) 🔥 --}}
                    @php
                        $isSale = $book->sale_price > 0 && $book->sale_price < $book->price;
                        $percent = $isSale ? round((($book->price - $book->sale_price)/$book->price)*100) : 0;
                        $isNew = $book->created_at && $book->created_at > now()->subDays(7);
                        
                        // Logic tính Sao (Chống lỗi nếu DB chưa có)
                        $avgRating = $book->reviews_avg_rating ?? ($book->reviews ? $book->reviews->avg('rating') : 0);
                        $reviewCount = $book->reviews_count ?? ($book->reviews ? $book->reviews->count() : 0);
                    @endphp

                    {{-- BỘ TAGS XẾP DỌC (Góc trái) --}}
                    <div class="position-absolute top-0 start-0 m-2 z-1 d-flex flex-column gap-1 align-items-start">
                        <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-file-pdf me-1"></i>Ebook</span>
                        @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                        @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                    </div>

                    {{-- Ảnh --}}
                    <a href="{{ route('book.detail', $book->id) }}" class="overflow-hidden rounded-top-4">
                        <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" 
                             class="card-img-custom" alt="{{ $book->title }}" loading="lazy">
                    </a>
                    
                    <div class="card-body p-2 p-md-3 d-flex flex-column">
                        {{-- Tên sách --}}
                        <h6 class="mb-1" style="font-size: 14px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <a href="{{ route('book.detail', $book->id) }}" class="text-decoration-none text-dark fw-bold" title="{{ $book->title }}">
                                {{ $book->title }}
                            </a>
                        </h6>
                        <small class="text-muted mb-2 text-truncate d-block"><i class="fas fa-pen-nib text-xs me-1"></i> {{ $book->author ?? 'Đang cập nhật' }}</small>
                        
                        {{-- 🔥 HIỂN THỊ SAO ĐÁNH GIÁ 🔥 --}}
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
                                <span class="text-primary fw-bold" style="font-size: 16px; line-height: 1.2;">{{ number_format($book->ebook_price) }}đ</span>
                                @if($book->price > 0)
                                    <span class="text-muted text-decoration-line-through" style="font-size: 12px; line-height: 1.2;">Gốc: {{ number_format($book->price) }}đ</span>
                                @endif
                            </div>
                            
                            {{-- Lượt mua --}}
                            <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center" style="min-height: 25px;">
                                @if($book->ebook_sold > 0)
                                    <small class="text-success fw-bold" style="font-size: 11px;">
                                        <i class="fas fa-download me-1"></i>Đã bán: {{ $book->ebook_sold }}
                                    </small>
                                @endif
                            </div>
                            
                            {{-- Nút Xem Ngay --}}
                            <a href="{{ route('book.detail', $book->id) }}" class="btn btn-outline-primary w-100 rounded-pill fw-bold btn-sm py-1 mt-2" style="font-size: 0.85rem;">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Phân trang --}}
        <div class="mt-5 d-flex justify-content-center">
            {{ $books->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection