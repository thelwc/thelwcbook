@extends('client.layouts.master')

@section('title')
    🏆 Bảng Xếp Hạng Sách Bán Chạy - Thelwc Books
@endsection

@section('content')

<style>
    /* CSS Giao diện Card đồng bộ */
    .card-img-custom {
        width: 100%; 
        aspect-ratio: 2 / 3; /* Tỉ lệ bìa sách chuẩn */
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
        border-color: #ffc107 !important; 
    }

    /* Header riêng cho trang Best Seller (Màu Vàng Gold) */
    .best-header {
        background: linear-gradient(45deg, #ffc107, #ffdb4d);
        color: #333; 
        padding: 40px 0;
        margin-bottom: 30px;
        border-radius: 0 0 50% 50% / 20px;
    }
    
    /* Huy hiệu Top */
    .badge-top { position: absolute; top: 0; left: 0; margin: 8px; z-index: 3; box-shadow: 0 3px 8px rgba(0,0,0,0.15); }
    .top-1 { background: #FFD700; color: #000; font-weight: 800; border: 2px solid #fff; font-size: 0.85rem;} 
    .top-2 { background: #C0C0C0; color: #000; font-weight: 800; border: 2px solid #fff; font-size: 0.75rem;} 
    .top-3 { background: #CD7F32; color: #fff; font-weight: 800; border: 2px solid #fff; font-size: 0.75rem;} 
</style>

{{-- HEADER TRANG --}}
<div class="best-header text-center shadow-sm">
    <h1 class="fw-bold animate__animated animate__fadeInDown"><i class="fas fa-crown me-2"></i> BXH BÁN CHẠY NHẤT</h1>
    <p class="fs-5 opacity-75 fw-bold mb-0">Những cuốn sách được độc giả săn đón nhiều nhất</p>
</div>

<div class="container pb-5">
    @if($books->isEmpty())
        <div class="text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="100" class="mb-3 opacity-50">
            <h3>Chưa có dữ liệu bán hàng!</h3>
            <p class="text-muted">Cửa hàng đang chờ những đơn hàng đầu tiên.</p>
            <a href="{{ route('shop') }}" class="btn btn-dark rounded-pill px-4 mt-2">Mua sắm ngay</a>
        </div>
    @else
        {{-- LƯỚI SẢN PHẨM: 2 Cột (Mobile) - 4 Cột (Tablet) - 5 Cột (Desktop) --}}
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
            @foreach($books as $key => $book)
            <div class="col d-flex">
                <div class="card w-100 h-100 border-0 shadow-sm position-relative hover-card rounded-4">
                    
                    {{-- 🔥 LOGIC PHÂN LOẠI TAGS & ĐÁNH GIÁ 🔥 --}}
                    @php 
                        // Tính số thứ tự thật sự kể cả khi sang trang 2, trang 3
                        $rank = ($books->currentPage() - 1) * $books->perPage() + $key + 1;
                        
                        $isSale = $book->sale_price > 0 && $book->sale_price < $book->price;
                        $percent = $isSale ? round((($book->price - $book->sale_price)/$book->price)*100) : 0;
                        $isNew = $book->created_at && $book->created_at > now()->subDays(7);
                        $isEbook = $book->ebook_price > 0;
                        
                        // Tính Sao đánh giá
                        $avgRating = $book->reviews_avg_rating ?? ($book->reviews ? $book->reviews->avg('rating') : 0);
                        $reviewCount = $book->reviews_count ?? ($book->reviews ? $book->reviews->count() : 0);
                    @endphp

                    {{-- HUY HIỆU TOP RANKING (GÓC TRÁI) --}}
                    @if($rank == 1) 
                        <span class="badge rounded-pill badge-top top-1 px-3 py-2"><i class="fas fa-trophy text-danger"></i> TOP 1</span>
                    @elseif($rank == 2) 
                        <span class="badge rounded-pill badge-top top-2 px-3 py-2">TOP 2</span>
                    @elseif($rank == 3) 
                        <span class="badge rounded-pill badge-top top-3 px-3 py-2">TOP 3</span>
                    @else 
                        <span class="position-absolute top-0 start-0 badge bg-dark m-2 shadow z-3 px-2 py-1">#{{ $rank }}</span>
                    @endif

                    {{-- BỘ TAGS XẾP DỌC (GÓC PHẢI ĐỂ NÉ CÁI TOP RANKING RA) --}}
                    <div class="position-absolute top-0 end-0 m-2 z-1 d-flex flex-column gap-1 align-items-end">
                        @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                        @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                        @if($isEbook) <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-tablet-alt me-1"></i>Ebook</span> @endif
                    </div>

                    {{-- Ảnh Sách --}}
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
                                    <i class="fas fa-fire text-danger me-1"></i>Đã bán: <b class="text-dark">{{ number_format($book->total_sold ?? 0) }}</b>
                                </small>
                            </div>
                            
                            {{-- Nút Xem Ngay --}}
                            <a href="{{ route('book.detail', $book->id) }}" class="btn btn-warning btn-sm w-100 rounded-pill fw-bold py-1 mt-2" style="font-size: 0.85rem;">Xem Ngay</a>
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