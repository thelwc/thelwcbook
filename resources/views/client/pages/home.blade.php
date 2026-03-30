@extends('client.layouts.master')

@section('content')

{{-- CSS TÙY CHỈNH --}}
<style>
    /* ===== BANNER FULL ===== */
    .banner-full {
        position: relative;
    }

    .banner-full .carousel-item {
        position: relative;
        height: 420px;
    }

    /* ẢNH */
    .banner-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
    }

    /* OVERLAY */
    .banner-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.2));
    }

    /* CONTENT */
    .banner-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        align-items: center;
    }

    /* FIX CHỮ BỊ XUỐNG DÒNG */
    .banner-content h2 {
        font-size: clamp(1.8rem, 3vw, 2.8rem);
        line-height: 1.2;
        white-space: normal;
        word-break: keep-all;
        /* 🔥 QUAN TRỌNG */
        max-width: 600px;
        /* 🔥 KHÔNG CHO BÓ QUÁ SỚM */
    }

    .banner-content p {
        max-width: 520px;
    }

    .banner-content .col-md-6 {
        flex: 0 0 70%;
        max-width: 70%;
    }

    /* MOBILE */
    @media (max-width: 576px) {
        .banner-full .carousel-item {
            height: 300px;
        }

        .banner-content {
            align-items: flex-end;
            padding-bottom: 20px;
        }

        .banner-content h2 {
            font-size: 1.4rem !important;
            max-width: 100%;
        }

        .banner-content p {
            font-size: 0.9rem;
            max-width: 100%;
        }

        .banner-overlay {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.2));
        }
    }

    .card-img-custom {
        width: 100%;
        height: 200px;
        object-fit: contain;
        padding: 8px;
        margin: 0 auto;
        background: #fff;
    }

    .card {
        border: 1px solid #eee !important;
        transition: all 0.3s ease;
        border-radius: 14px !important;
        overflow: hidden;
    }

    .card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-3px);
        border-color: #ddd !important;
    }

    .book-title {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        height: 40px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        color: #333;
        word-break: break-word;
    }

    /* 🔥 FIX LỖI CAO THẤP: Khung chứa giá luôn cao cố định 45px 🔥 */
    .price-box {
        height: 45px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .price-text {
        font-size: 16px;
        color: #C92127;
        font-weight: 700;
        line-height: 1.2;
    }

    .price-old {
        font-size: 12px;
        color: #999;
        text-decoration: line-through;
        line-height: 1.2;
    }

    /* Chỉnh nút Slide cho đẹp */
    .swiper-button-next,
    .swiper-button-prev {
        color: #333;
        width: 30px;
        height: 30px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 14px;
        font-weight: bold;
    }

    .hover-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .hover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .hover-info:hover {
        color: #0dcaf0 !important;
    }

    /* =========================
       MOBILE OPTIMIZATION
    ========================== */
    @media (max-width: 575.98px) {

        html,
        body {
            overflow-x: hidden;
        }

        .container {
            padding-left: 12px;
            padding-right: 12px;
        }

        section {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .card {
            border-radius: 12px !important;
        }

        .card-img-custom {
            height: 165px !important;
            padding: 6px;
        }

        .book-title {
            font-size: 13px;
            line-height: 1.35;
            height: 36px;
        }

        .price-box {
            height: 40px;
        }

        .price-text {
            font-size: 15px;
        }

        .price-old {
            font-size: 11px;
        }

        .card-body {
            padding: 0.65rem !important;
        }

        /* Banner */
        #billboard .container {
            min-height: auto !important;
        }

        #billboard .carousel-item .container {
            padding-top: 18px;
            padding-bottom: 18px;
        }

        #billboard h2,
        #billboard .display-5 {
            font-size: 1.55rem !important;
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }

        #billboard p,
        #billboard .fs-5 {
            font-size: 0.95rem !important;
            line-height: 1.45;
        }

        #billboard img {
            max-height: 220px !important;
            margin-top: 8px;
        }

        #billboard .btn {
            width: 100%;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        /* Section header */
        .d-flex.flex-column.flex-md-row {
            gap: 0.75rem !important;
        }

        h3 {
            font-size: 1.05rem !important;
            line-height: 1.3;
        }

        /* Swiper cards */
        .swiper-slide {
            height: auto !important;
        }

        .swiper-wrapper {
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }

        .progress {
            height: 4px !important;
        }

        /* All books grid */
        #all-books .card-img-custom {
            height: 150px !important;
            object-fit: cover;
        }

        #all-books .card-body {
            padding: 0.55rem !important;
        }

        #all-books h6 {
            font-size: 0.88rem !important;
            min-height: 34px !important;
        }

        #all-books small {
            font-size: 10px !important;
        }

        /* News */
        #latest-news .card-img-top {
            height: 170px !important;
        }

        #latest-news h5 {
            font-size: 1rem !important;
            height: auto !important;
        }

        #latest-news p {
            font-size: 0.88rem !important;
            height: auto !important;
        }

        /* Buttons */
        .btn,
        .btn-sm {
            border-radius: 999px !important;
        }

        .carousel-indicators [data-bs-target] {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            padding: 0.75rem !important;
        }

        /* Section all-books */
        #all-books .row {
            --bs-gutter-x: 0.7rem;
            --bs-gutter-y: 0.7rem;
        }

        /* Posts cards */
        #latest-news .card-body {
            padding: 0.85rem !important;
        }
    }

    @media (min-width: 576px) and (max-width: 767.98px) {
        .card-img-custom {
            height: 180px;
        }

        #billboard h2,
        #billboard .display-5 {
            font-size: 2rem !important;
        }

        #billboard img {
            max-height: 260px !important;
        }

        #latest-news .card-img-top {
            height: 190px !important;
        }
    }

    @media (min-width: 768px) {
        .card-body {
            padding: 0.75rem !important;
        }
    }
</style>

{{-- BANNER SLIDER --}}
@if($banners->count() > 0)
<section id="billboard" class="banner-full">
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">

        <div class="carousel-indicators">
            @foreach($banners as $key => $banner)
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="{{ $key }}"
                class="{{ $key == 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>

        <div class="carousel-inner">
            @foreach($banners as $key => $banner)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">

                {{-- ẢNH FULL --}}
                <div class="banner-bg" style="background-image: url('{{ asset($banner->image) }}');"></div>

                {{-- OVERLAY --}}
                <div class="banner-overlay"></div>

                {{-- TEXT --}}
                <div class="banner-content container">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6 text-white text-center text-md-start">
                            <h2 class="fw-bold display-5 mb-2">
                                {{ $banner->title }}
                            </h2>

                            <p class="fs-5 mb-3">
                                {{ $banner->description }}
                            </p>

                            <a href="{{ url($banner->link) }}"
                                class="btn btn-light text-dark fw-bold rounded-pill px-4">
                                Xem ngay <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
        </button>

    </div>
</section>
@endif

{{-- 🔥 SECTION 1: DEAL SỐC (SWIPER) 🔥 --}}
@if(isset($saleBooks) && $saleBooks->count() > 0)
<section id="flash-sale" class="py-5" style="background-color: #fff0f0;">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 border-bottom border-danger pb-2 gap-3">
            <h3 class="fw-bold text-danger mb-0"><i class="fas fa-bolt me-2"></i> DEAL SỐC HÔM NAY</h3>
            <div class="d-flex gap-2 align-items-center align-self-end align-self-md-auto">
                <div class="swiper-button-prev btn-sale-prev position-static m-0 d-none d-md-flex"></div>
                <div class="swiper-button-next btn-sale-next position-static m-0 d-none d-md-flex"></div>
                <a href="{{ route('flash.sale') }}" class="btn btn-outline-danger btn-sm rounded-pill fw-bold ms-md-2">Xem tất cả</a>
            </div>
        </div>

        <div class="swiper flashSaleSwiper">
            <div class="swiper-wrapper py-2">
                @foreach($saleBooks as $book)
                @if($book->sale_price > 0 && $book->sale_price < $book->price)
                    <div class="swiper-slide h-auto">
                        <div class="card h-100 shadow-sm position-relative bg-white border-0 hover-card">

                            {{-- LOGIC PHÂN LOẠI TAGS --}}
                            @php
                            $isSale = $book->sale_price > 0 && $book->sale_price < $book->price;
                                $percent = $isSale ? round((($book->price - $book->sale_price)/$book->price)*100) : 0;
                                $isNew = $book->created_at && $book->created_at > now()->subDays(7);
                                $isEbook = $book->ebook_price > 0;

                                // LOGIC TÍNH SAO ĐÁNH GIÁ
                                $avgRating = $book->reviews_avg_rating ?? ($book->reviews ? $book->reviews->avg('rating') : 0);
                                $reviewCount = $book->reviews_count ?? ($book->reviews ? $book->reviews->count() : 0);
                                @endphp

                                {{-- BỘ TAGS XẾP DỌC (Trái) --}}
                                <div class="position-absolute top-0 start-0 m-2 z-1 d-flex flex-column gap-1 align-items-start">
                                    @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                                    @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                                    @if($isEbook) <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-tablet-alt me-1"></i>Ebook</span> @endif
                                </div>

                                <a href="{{ route('book.detail', $book->id) }}">
                                    <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" loading="lazy" class="card-img-custom rounded-top">
                                </a>

                                <div class="card-body p-2 d-flex flex-column">
                                    <h6 class="mb-1" style="font-size: 14px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <a href="{{ route('book.detail', $book->id) }}" class="book-title text-decoration-none text-dark fw-bold">{{ $book->title }}</a>
                                    </h6>
                                    <small class="text-muted mb-1 text-truncate d-block" style="font-size: 12px;">{{ $book->author ?? 'Đang cập nhật' }}</small>

                                    {{-- HIỂN THỊ SAO ĐÁNH GIÁ --}}
                                    <div class="mb-2" style="min-height: 18px;">
                                        @if($reviewCount > 0)
                                        <div class="text-warning d-flex align-items-center" style="font-size: 11px;">
                                            <div class="me-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <=floor($avgRating)) <i class="fas fa-star"></i>
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
                                        <div class="price-box d-flex flex-column justify-content-end mb-2" style="min-height: 38px;">
                                            <span class="text-danger fw-bold" style="font-size: 16px; line-height: 1.2;">{{ number_format($book->sale_price) }}đ</span>
                                            <span class="text-muted text-decoration-line-through" style="font-size: 12px; line-height: 1.2;">{{ number_format($book->price) }}đ</span>
                                        </div>

                                        {{-- Progress Bar Lượt Bán --}}
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-danger" style="width: {{ rand(50, 90) }}%"></div>
                                        </div>
                                        <small class="text-danger fw-bold d-block mt-1" style="font-size: 0.75rem;">
                                            🔥 Đã bán: {{ number_format($book->total_sold ?? 0) }}
                                        </small>
                                    </div>
                                </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- 🔥 SECTION 2: SÁCH BÁN CHẠY 🔥 --}}
@if(isset($bestSellingBooks) && $bestSellingBooks->count() > 0)
<section id="best-sellers" class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 border-bottom border-warning pb-2 gap-3">
            <h3 class="fw-bold text-dark mb-0"><i class="fas fa-crown text-warning me-2"></i> BÁN CHẠY NHẤT</h3>
            <div class="d-flex gap-2 align-items-center align-self-end align-self-md-auto">
                <div class="swiper-button-prev btn-best-prev position-static m-0 d-none d-md-flex"></div>
                <div class="swiper-button-next btn-best-next position-static m-0 d-none d-md-flex"></div>
                <a href="{{ route('best.sellers') }}" class="btn btn-outline-dark btn-sm rounded-pill fw-bold">Xem BXH</a>
            </div>
        </div>

        <div class="swiper bestSellerSwiper">
            <div class="swiper-wrapper py-2">
                @foreach($bestSellingBooks as $index => $book)
                <div class="swiper-slide h-auto">
                    <div class="card h-100 border-0 shadow-sm position-relative hover-card">

                        @php
                        $isSale = $book->sale_price > 0 && $book->sale_price < $book->price;
                            $percent = $isSale ? round((($book->price - $book->sale_price)/$book->price)*100) : 0;
                            $isNew = $book->created_at && $book->created_at > now()->subDays(7);
                            $isEbook = $book->ebook_price > 0;

                            $avgRating = $book->reviews_avg_rating ?? ($book->reviews ? $book->reviews->avg('rating') : 0);
                            $reviewCount = $book->reviews_count ?? ($book->reviews ? $book->reviews->count() : 0);
                            @endphp

                            {{-- Huy hiệu TOP nằm góc TRÁI --}}
                            <div class="position-absolute top-0 start-0 m-1 z-3">
                                @if($index == 0) <span class="badge rounded-pill shadow bg-warning text-dark">TOP 1</span>
                                @elseif($index == 1) <span class="badge rounded-pill shadow bg-secondary">TOP 2</span>
                                @elseif($index == 2) <span class="badge rounded-pill shadow" style="background: #CD7F32;">TOP 3</span>
                                @else <span class="badge rounded-pill bg-dark">#{{ $index+1 }}</span> @endif
                            </div>

                            {{-- BỘ TAGS XẾP DỌC NẰM GÓC PHẢI --}}
                            <div class="position-absolute top-0 end-0 m-2 z-1 d-flex flex-column gap-1 align-items-end">
                                @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                                @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                                @if($isEbook) <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-tablet-alt me-1"></i>Ebook</span> @endif
                            </div>

                            <a href="{{ route('book.detail', $book->id) }}">
                                <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" loading="lazy" class="card-img-custom rounded-top">
                            </a>

                            <div class="card-body p-2 d-flex flex-column">
                                <h6 class="mb-1" style="font-size: 14px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <a href="{{ route('book.detail', $book->id) }}" class="text-dark text-decoration-none fw-bold">{{ $book->title }}</a>
                                </h6>
                                <small class="text-muted mb-1 text-truncate d-block" style="font-size: 12px;">{{ $book->author ?? 'Đang cập nhật' }}</small>

                                {{-- HIỂN THỊ SAO ĐÁNH GIÁ --}}
                                <div class="mb-2" style="min-height: 18px;">
                                    @if($reviewCount > 0)
                                    <div class="text-warning d-flex align-items-center" style="font-size: 11px;">
                                        <div class="me-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <=floor($avgRating)) <i class="fas fa-star"></i>
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
                                    <div class="price-box d-flex flex-column justify-content-end" style="min-height: 38px;">
                                        @if($isSale)
                                        <span class="text-danger fw-bold" style="font-size: 15px; line-height: 1.2;">{{ number_format($book->sale_price) }}đ</span>
                                        <span class="text-muted text-decoration-line-through" style="font-size: 12px; line-height: 1.2;">{{ number_format($book->price) }}đ</span>
                                        @else
                                        <span class="text-dark fw-bold" style="font-size: 15px; line-height: 1.2;">{{ number_format($book->price) }}đ</span>
                                        @endif
                                    </div>

                                    <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 11px;">Đã bán: <b class="text-dark">{{ number_format($book->total_sold ?? 0) }}</b></small>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- 🔥 SECTION 3: TỦ SÁCH ĐIỆN TỬ (EBOOK) 🔥 --}}
@if(isset($ebooks) && $ebooks->count() > 0)
<section id="ebooks" class="py-5" style="background-color: #f0f8ff;">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 border-bottom border-primary pb-2 gap-3">
            <h3 class="fw-bold text-primary mb-0"><i class="fas fa-tablet-alt me-2"></i> KHO SÁCH EBOOK</h3>
            <div class="d-flex gap-2 align-items-center align-self-end align-self-md-auto">
                <a href="{{ route('ebooks') }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold me-md-2">Xem tất cả</a>
                <div class="swiper-button-prev btn-ebook-prev position-static m-0 d-none d-md-flex"></div>
                <div class="swiper-button-next btn-ebook-next position-static m-0 d-none d-md-flex"></div>
            </div>
        </div>

        <div class="swiper ebookSwiper">
            <div class="swiper-wrapper py-2">
                @foreach($ebooks as $book)
                <div class="swiper-slide h-auto">
                    <div class="card h-100 shadow-sm position-relative border-0 hover-card">

                        @php
                        $isSale = $book->sale_price > 0 && $book->sale_price < $book->price;
                            $percent = $isSale ? round((($book->price - $book->sale_price)/$book->price)*100) : 0;
                            $isNew = $book->created_at && $book->created_at > now()->subDays(7);

                            $avgRating = $book->reviews_avg_rating ?? ($book->reviews ? $book->reviews->avg('rating') : 0);
                            $reviewCount = $book->reviews_count ?? ($book->reviews ? $book->reviews->count() : 0);
                            @endphp

                            {{-- BỘ TAGS XẾP DỌC (Trái) --}}
                            <div class="position-absolute top-0 start-0 m-2 z-1 d-flex flex-column gap-1 align-items-start">
                                <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-file-pdf me-1"></i>Ebook</span>
                                @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                                @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                            </div>

                            <a href="{{ route('book.detail', $book->id) }}">
                                <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" loading="lazy" class="card-img-custom rounded-top">
                            </a>

                            <div class="card-body p-2 d-flex flex-column">
                                <h6 class="mb-1" style="font-size: 14px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <a href="{{ route('book.detail', $book->id) }}" class="text-dark text-decoration-none fw-bold">{{ $book->title }}</a>
                                </h6>
                                <small class="text-muted mb-1 text-truncate d-block" style="font-size: 12px;">{{ $book->author ?? 'Đang cập nhật' }}</small>

                                {{-- HIỂN THỊ SAO ĐÁNH GIÁ --}}
                                <div class="mb-2" style="min-height: 18px;">
                                    @if($reviewCount > 0)
                                    <div class="text-warning d-flex align-items-center" style="font-size: 11px;">
                                        <div class="me-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <=floor($avgRating)) <i class="fas fa-star"></i>
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
                                    <div class="price-box d-flex flex-column justify-content-end" style="min-height: 38px;">
                                        <span class="text-primary fw-bold" style="font-size: 15px; line-height: 1.2;">{{ number_format($book->ebook_price) }}đ</span>
                                        @if($book->price > 0)
                                        <span class="text-muted text-decoration-line-through" style="font-size: 12px; line-height: 1.2;">Gốc: {{ number_format($book->price) }}đ</span>
                                        @endif
                                    </div>

                                    <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 11px;">Đã bán: <b class="text-primary">{{ number_format($book->ebook_sold ?? 0) }}</b></small>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- 🔥 SECTION 4: TOÀN BỘ GIAN HÀNG 🔥 --}}
@if(isset($allBooks) && $allBooks->count() > 0)
<section id="all-books" class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold text-dark text-uppercase" style="letter-spacing: 2px;">
                <i class="fas fa-store me-2 text-primary"></i> Gian Hàng Thelwc
            </h3>
            <p class="text-muted">Khám phá kho tàng tri thức phong phú</p>
            <div style="width: 50px; height: 3px; background: #0d6efd; margin: 0 auto; border-radius: 10px;"></div>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
            @foreach($allBooks as $book)
            <div class="col d-flex">
                <div class="card w-100 shadow-sm border-0 position-relative hover-card">

                    @php
                    $isSale = $book->sale_price > 0 && $book->sale_price < $book->price;
                        $percent = $isSale ? round((($book->price - $book->sale_price)/$book->price)*100) : 0;
                        $isNew = $book->created_at && $book->created_at > now()->subDays(7);
                        $isEbook = $book->ebook_price > 0;

                        $avgRating = $book->reviews_avg_rating ?? ($book->reviews ? $book->reviews->avg('rating') : 0);
                        $reviewCount = $book->reviews_count ?? ($book->reviews ? $book->reviews->count() : 0);
                        @endphp

                        {{-- BỘ TAGS XẾP DỌC (Trái) --}}
                        <div class="position-absolute top-0 start-0 m-2 z-1 d-flex flex-column gap-1 align-items-start">
                            @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                            @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                            @if($isEbook) <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-tablet-alt me-1"></i>Ebook</span> @endif
                        </div>

                        <a href="{{ route('book.detail', $book->id) }}">
                            <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}"
                                class="card-img-custom rounded-top" loading="lazy" alt="{{ $book->title }}">
                        </a>

                        <div class="card-body p-2 d-flex flex-column">
                            <h6 class="mb-1" style="font-size: 14px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <a href="{{ route('book.detail', $book->id) }}" class="text-dark text-decoration-none fw-bold" title="{{ $book->title }}">
                                    {{ $book->title }}
                                </a>
                            </h6>
                            <small class="text-muted mb-1 text-truncate d-block" style="font-size: 12px;">{{ $book->author ?? 'Đang cập nhật' }}</small>

                            {{-- HIỂN THỊ SAO ĐÁNH GIÁ --}}
                            <div class="mb-2" style="min-height: 18px;">
                                @if($reviewCount > 0)
                                <div class="text-warning d-flex align-items-center" style="font-size: 11px;">
                                    <div class="me-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <=floor($avgRating)) <i class="fas fa-star"></i>
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
                                <div class="price-box d-flex flex-column justify-content-end" style="min-height: 38px;">
                                    @if($isSale)
                                    <span class="text-danger fw-bold" style="font-size: 15px; line-height: 1.2;">{{ number_format($book->sale_price) }}đ</span>
                                    <span class="text-muted text-decoration-line-through" style="font-size: 12px; line-height: 1.2;">{{ number_format($book->price) }}đ</span>
                                    @else
                                    <span class="text-dark fw-bold" style="font-size: 15px; line-height: 1.2;">{{ number_format($book->price) }}đ</span>
                                    @endif
                                </div>

                                <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                    <small class="text-muted" style="font-size: 11px;">Đã bán: <b class="text-dark">{{ number_format($book->total_sold ?? 0) }}</b></small>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('shop') }}" class="btn btn-outline-dark rounded-pill px-5 py-2 fw-bold">
                Xem toàn bộ sách <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- 🔥 SECTION: TIN TỨC MỚI NHẤT 🔥 --}}
@if(isset($latestPosts) && $latestPosts->count() > 0)
<section id="latest-news" class="py-5 bg-white">
    <div class="container">
        {{-- ĐÃ TỐI ƯU MOBILE --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 border-bottom pb-2 gap-3">
            <h3 class="fw-bold text-dark mb-0"><i class="fas fa-newspaper me-2 text-info"></i> TIN TỨC MỚI NHẤT</h3>
            <a href="{{ route('posts.index') }}" class="btn btn-outline-info btn-sm rounded-pill fw-bold align-self-end align-self-md-auto">
                Xem tất cả <i class="fas fa-angle-double-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($latestPosts as $post)
            <div class="col-md-4 d-flex">
                <div class="card w-100 border-0 shadow-sm hover-card">
                    <a href="{{ route('posts.show', $post->id) }}">
                        <img src="{{ asset($post->thumbnail ? $post->thumbnail : 'https://via.placeholder.com/600x400') }}"
                            class="card-img-top" style="height: 200px; object-fit: cover;" loading="lazy" alt="{{ $post->title }}">
                    </a>

                    <div class="card-body p-3 d-flex flex-column">
                        <div class="text-muted small mb-2">
                            <i class="far fa-calendar-alt me-1"></i> {{ $post->created_at->format('d/m/Y') }}
                        </div>

                        <h5 class="fw-bold mb-2" style="height: 50px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            <a href="{{ route('posts.show', $post->id) }}" class="text-dark text-decoration-none hover-info">
                                {{ $post->title }}
                            </a>
                        </h5>

                        <p class="text-secondary small mb-3" style="height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                            {{ Str::limit($post->short_description ?? 'Khám phá những tin tức và chia sẻ mới nhất từ cộng đồng yêu sách Thelwc Books...', 120) }}
                        </p>

                        <div class="mt-auto">
                            <a href="{{ route('posts.show', $post->id) }}" class="text-info fw-bold small text-decoration-none">
                                Đọc thêm <i class="fas fa-chevron-right ms-1" style="font-size: 0.7rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Cấu hình chung cho các Slider
        const commonConfig = {
            slidesPerView: 1.15,
            spaceBetween: 12,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                768: {
                    slidesPerView: 4,
                    spaceBetween: 20
                },
                1024: {
                    slidesPerView: 6,
                    spaceBetween: 20
                },
            }
        };

        // 1. Slider Deal Sốc
        new Swiper(".flashSaleSwiper", {
            ...commonConfig,
            navigation: {
                nextEl: ".btn-sale-next",
                prevEl: ".btn-sale-prev"
            },
        });

        // 2. Slider Sách Bán Chạy
        new Swiper(".bestSellerSwiper", {
            ...commonConfig,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false
            },
            navigation: {
                nextEl: ".btn-best-next",
                prevEl: ".btn-best-prev"
            },
        });

        // 3. Slider EBOOK
        new Swiper(".ebookSwiper", {
            ...commonConfig,
            autoplay: {
                delay: 4500,
                disableOnInteraction: false
            },
            navigation: {
                nextEl: ".btn-ebook-next",
                prevEl: ".btn-ebook-prev"
            },
        });

        // 4. Slider Sách Mới
        new Swiper(".newArrivalsSwiper", {
            ...commonConfig,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false
            },
            navigation: {
                nextEl: ".btn-new-next",
                prevEl: ".btn-new-prev"
            },
        });
    });
</script>
@endsection