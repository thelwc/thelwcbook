@extends('client.layouts.master')

@section('title')
    {{ $book->title }} - Thelwc Books
@endsection

@section('content')

    {{-- 1. THÔNG BÁO FLASH MESSAGE --}}
    @if(session('success'))
        <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-4">
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- 2. KHỐI THÔNG TIN SẢN PHẨM --}}
    <section class="py-5">
        <div class="container">
            <div class="row gx-lg-5 gy-4 align-items-start">

                {{-- CỘT TRÁI: ẢNH SẢN PHẨM --}}
                <div class="col-lg-5 col-md-5 mb-4 mb-md-0">
                    <div class="product-image-container position-relative bg-white p-3 rounded-3 shadow-sm text-center border">
                        {{-- Badge Giảm giá --}}
                        @if($book->sale_price && $book->sale_price < $book->price)
                            <span class="position-absolute top-0 start-0 m-3 badge rounded-pill bg-danger fs-6 shadow z-3">
                                -{{ round((($book->price - $book->sale_price) / $book->price) * 100) }}%
                            </span>
                        @endif

                        @if($book->image)
                            <img src="{{ asset(str_contains($book->image, 'uploads') ? $book->image : 'uploads/' . $book->image) }}"
                                 class="w-100 rounded-2"
                                 style="object-fit: contain; max-height: 500px;"
                                 alt="{{ $book->title }}">
                        @else
                            <img src="https://via.placeholder.com/400x600?text=No+Image" class="w-100 rounded-2" alt="{{ $book->title }}">
                        @endif
                    </div>
                </div>

                {{-- CỘT PHẢI: THÔNG TIN CHI TIẾT --}}
                <div class="col-lg-7 col-md-7">
                    <div class="ps-lg-3">
                        {{-- Danh mục & Nguồn gốc --}}
                        <div class="mb-2 mobile-badges">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                {{ $book->category->name ?? 'Chưa phân loại' }}
                            </span>
                            @if($book->is_foreign)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-bold ms-2">
                                    <i class="fas fa-globe me-1"></i> Sách nước ngoài
                                </span>
                            @endif
                        </div>

                        <h1 class="display-6 fw-bold text-dark mb-2 section-title-mobile">{{ $book->title }}</h1>

                        {{-- Tác giả, Rating, Đã bán --}}
                        <div class="d-flex flex-wrap align-items-start align-items-sm-center gap-2 mb-4 text-secondary small mobile-small-text">
                            <span class="me-3 fs-6">
                                Tác giả:
                                @if($book->author)
                                    <a href="{{ route('search', ['keyword' => $book->author]) }}" class="text-dark text-decoration-none fw-bold doc-thu-hover">
                                        {{ $book->author }}
                                    </a>
                                @else
                                    <strong class="text-dark">Đang cập nhật</strong>
                                @endif
                            </span>

                            <span class="border-start ps-3 me-3">
                                @if(isset($avgRating) && $avgRating > 0)
                                    <span class="text-warning fw-bold fs-6">{{ number_format($avgRating, 1) }}</span>
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <span class="text-muted small fst-italic">Chưa có đánh giá</span>
                                @endif
                            </span>

                            @php
                                $totalSold = $book->total_sold ?? 0;
                                $ebookSold = $book->ebook_sold ?? 0;
                                $physicalSold = max(0, $totalSold - $ebookSold);
                            @endphp

                            <span class="text-dark fw-bold pe-sm-3">
                                🔥 Tổng đã bán: <strong class="text-danger fs-6">{{ number_format($totalSold) }}</strong>
                            </span>

                            @if($physicalSold > 0)
                                <span class="border-start ps-sm-3 pe-sm-3 text-success">
                                    <i class="fas fa-book me-1"></i> Giấy: <strong>{{ number_format($physicalSold) }}</strong>
                                </span>
                            @endif

                            @if($ebookSold > 0)
                                <span class="border-start ps-sm-3 text-primary">
                                    <i class="fas fa-tablet-alt me-1"></i> Ebook: <strong>{{ number_format($ebookSold) }}</strong>
                                </span>
                            @endif
                        </div>

                        {{-- BẢNG THÔNG SỐ CHI TIẾT SÁCH --}}
                        <div class="mt-4 mb-4 border rounded-3 p-3 bg-light info-grid-mobile">

                            {{-- PHẦN 1: THÔNG SỐ HIỂN THỊ CỐ ĐỊNH --}}
                            <div class="row g-3" style="font-size: 0.95rem;">
                                <div class="col-sm-6">
                                    <span class="text-muted"><i class="fas fa-tags me-1 opacity-50"></i> Thể loại:</span>
                                    <span class="fw-bold d-block mt-1">
                                        @if($book->category)
                                            <a href="{{ route('search', ['keyword' => $book->category->name]) }}" class="text-primary text-decoration-none">{{ $book->category->name }}</a>
                                        @else
                                            <span class="text-dark">---</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="col-sm-6">
                                    <span class="text-muted"><i class="fas fa-building me-1 opacity-50"></i> Nhà xuất bản:</span>
                                    <span class="fw-bold d-block mt-1">
                                        @if($book->publisher)
                                            <a href="{{ route('search', ['keyword' => $book->publisher->name]) }}" class="text-primary text-decoration-none">{{ $book->publisher->name }}</a>
                                        @else
                                            <span class="text-dark">---</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="col-sm-6">
                                    <span class="text-muted"><i class="fas fa-book-open me-1 opacity-50"></i> Hình thức:</span>
                                    <span class="fw-bold d-block mt-1">
                                        @if($book->cover_type)
                                            <a href="{{ route('search', ['keyword' => $book->cover_type]) }}" class="text-primary text-decoration-none">{{ $book->cover_type }}</a>
                                        @else
                                            <span class="text-dark">---</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="col-sm-6">
                                    <span class="text-muted"><i class="far fa-calendar-alt me-1 opacity-50"></i> Ngày XB:</span>
                                    <span class="fw-bold d-block mt-1">
                                        @if($book->published_date)
                                            <a href="{{ route('search', ['keyword' => date('Y', strtotime($book->published_date))]) }}" class="text-primary text-decoration-none">
                                                {{ date('d/m/Y', strtotime($book->published_date)) }}
                                            </a>
                                        @else
                                            <span class="text-dark">---</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- PHẦN 2: THÔNG SỐ BỊ GIẤU --}}
                            <div class="collapse mt-3 pt-3 border-top" id="moreDetails">
                                <div class="row g-3" style="font-size: 0.95rem;">

                                    <div class="col-sm-6">
                                        <span class="text-muted"><i class="fas fa-ruler-combined me-1 opacity-50"></i> Kích thước:</span>
                                        <span class="fw-bold text-dark d-block mt-1">{{ $book->dimensions ?? '---' }}</span>
                                    </div>

                                    <div class="col-sm-6">
                                        <span class="text-muted"><i class="fas fa-file-alt me-1 opacity-50"></i> Số trang:</span>
                                        <span class="fw-bold text-dark d-block mt-1">{{ $book->page_count ?? '---' }} trang</span>
                                    </div>

                                    <div class="col-sm-6">
                                        <span class="text-muted"><i class="fas fa-globe-asia me-1 opacity-50"></i> Nguồn gốc:</span>
                                        <span class="fw-bold text-dark d-block mt-1">{{ $book->is_foreign ? 'Sách Nước Ngoài (Dịch)' : 'Sách Trong Nước' }}</span>
                                    </div>

                                    @if($book->is_foreign)
                                        <div class="col-sm-6">
                                            <span class="text-muted"><i class="fas fa-language me-1 opacity-50"></i> Dịch giả:</span>
                                            <span class="fw-bold text-dark d-block mt-1">{{ $book->translator ?? 'Đang cập nhật' }}</span>
                                        </div>
                                    @endif

                                    @if($book->ebook_price > 0 || $book->preview_pages > 0)
                                        <div class="col-12"><hr class="border-secondary opacity-25 my-1"></div>

                                        @if(($book->file_preview || $book->book_content) && $book->preview_pages)
                                            <div class="col-sm-6">
                                                <span class="text-muted"><i class="fas fa-book-reader me-1 opacity-50"></i> Đọc thử:</span>
                                                <span class="fw-bold text-dark d-block mt-1">{{ $book->preview_pages }} trang</span>
                                            </div>
                                        @endif

                                        @if($book->ebook_price > 0)
                                            <div class="col-sm-6">
                                                <span class="text-muted"><i class="fas fa-file-archive me-1 opacity-50"></i> Size Ebook:</span>
                                                <span class="fw-bold text-primary d-block mt-1">{{ $book->file_size ? $book->file_size : 'Đang cập nhật' }}</span>
                                            </div>

                                            <div class="col-sm-6">
                                                <span class="text-muted"><i class="fas fa-font me-1 opacity-50"></i> Font Ebook:</span>
                                                <span class="fw-bold text-dark d-block mt-1">{{ $book->font_family ? $book->font_family : 'Chuẩn hệ thống' }}</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- NÚT BẤM XEM THÊM --}}
                            <div class="text-center mt-3">
                                <a class="text-decoration-none fw-bold text-primary" data-bs-toggle="collapse" href="#moreDetails" role="button" aria-expanded="false" aria-controls="moreDetails" onclick="toggleText(this)">
                                    Xem thêm chi tiết <i class="fas fa-chevron-down ms-1"></i>
                                </a>
                            </div>
                        </div>

                        {{-- LỰA CHỌN PHIÊN BẢN --}}
                        <div class="border rounded-3 p-3 mb-4">
                            <label class="fw-bold mb-2 text-dark">Chọn định dạng:</label>

                            @if($book->ebook_price > 0 && $book->file_ebook)
                                <div class="d-flex flex-column flex-sm-row gap-3">
                                    <div class="form-check custom-radio-box flex-fill p-0 m-0">
                                        <input class="form-check-input d-none" type="radio" name="product_variant" id="variant_physical" checked
                                               onchange="switchProductType('physical')">
                                        <label class="form-check-label border rounded-3 p-2 w-100 text-center cursor-pointer bg-white" for="variant_physical">
                                            <div class="fw-bold text-dark"><i class="fas fa-book me-1"></i> Sách giấy</div>
                                            <div class="text-danger fw-bold">{{ number_format($book->sale_price ?: $book->price) }}đ</div>
                                        </label>
                                    </div>
                                    <div class="form-check custom-radio-box flex-fill p-0 m-0">
                                        <input class="form-check-input d-none" type="radio" name="product_variant" id="variant_ebook"
                                               onchange="switchProductType('ebook')">
                                        <label class="form-check-label border rounded-3 p-2 w-100 text-center cursor-pointer bg-white" for="variant_ebook">
                                            <div class="fw-bold text-primary"><i class="fas fa-tablet-alt me-1"></i> Ebook</div>
                                            <div class="text-primary fw-bold">{{ number_format($book->ebook_price) }}đ</div>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex flex-column flex-sm-row gap-3 align-items-stretch">
                                    <div class="border border-primary bg-primary bg-opacity-10 rounded-3 p-2 flex-fill text-center">
                                        <div class="fw-bold text-dark"><i class="fas fa-book me-1"></i> Sách giấy</div>
                                        <div class="text-danger fw-bold">{{ number_format($book->sale_price ?: $book->price) }}đ</div>
                                    </div>
                                    <div class="border rounded-3 p-2 flex-fill text-center bg-light text-muted" style="cursor: not-allowed; opacity: 0.7;">
                                        <div class="fw-bold"><i class="fas fa-tablet-alt me-1"></i> Ebook</div>
                                        <div class="small">Chưa có bản Ebook</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- GIÁ TIỀN CHÍNH --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-end gap-2">
                                <h2 class="fw-bold text-danger mb-0" id="display_price">
                                    {{ number_format($book->sale_price ?: $book->price) }} ₫
                                </h2>
                                <span class="text-decoration-line-through text-muted fs-5 mb-1" id="display_old_price"
                                      style="{{ ($book->sale_price && $book->sale_price < $book->price) ? '' : 'display: none;' }}">
                                    {{ number_format($book->price) }} ₫
                                </span>
                            </div>
                        </div>

                        
                                {{-- 🔥 FORM MUA HÀNG (ÉP CHIỀU CAO CHUẨN 42PX, CHỐNG VỠ TRÊN MOBILE) 🔥 --}}
                        <form id="add-to-cart-form" action="{{ route('add.to.cart', $book->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" id="input_book_type" value="physical">

                            <div class="row g-2 mb-4 align-items-center">
                                
                                {{-- 1. Ô NHẬP SỐ LƯỢNG --}}
                                <div class="col-6 col-md-auto" id="quantity-input-area">
                                    <div class="input-group shadow-sm rounded-pill overflow-hidden border border-secondary border-opacity-25" style="height: 42px; width: 130px;">
                                        <button class="btn btn-light border-0 px-2 d-flex align-items-center justify-content-center" type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" name="quantity" class="form-control text-center fw-bold border-0 p-0 h-100" value="1" min="1" max="{{ $book->quantity }}">
                                        <button class="btn btn-light border-0 px-2 d-flex align-items-center justify-content-center" type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                    <div class="text-center mt-2 d-none d-md-block">
                                        <span class="badge bg-light text-dark border">Còn sẵn: <strong class="text-danger">{{ $book->quantity }}</strong> cuốn</span>
                                    </div>
                                </div>

                                {{-- Nút Tim (Chỉ hiện góc phải trên Mobile, mượn đất của Số lượng) --}}
                                <div class="col-6 d-md-none text-end">
                                    @auth
                                        @php $isFavorite = \App\Models\Favorite::where('user_id', Auth::id())->where('book_id', $book->id)->exists(); @endphp
                                        <button type="button" onclick="document.getElementById('fav-form').submit();" class="btn btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="{{ $isFavorite ? 'fas' : 'far' }} fa-heart fs-6"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="far fa-heart fs-6"></i>
                                        </a>
                                    @endauth
                                </div>

                                {{-- Badge "Còn sẵn" cho Mobile --}}
                                <div class="col-12 d-md-none mb-2">
                                    <span class="badge bg-light text-dark border w-100">Còn sẵn: <strong class="text-danger">{{ $book->quantity }}</strong> cuốn</span>
                                </div>

                                {{-- 2. CỤM NÚT MUA HÀNG & ĐỌC THỬ (Dàn hàng ngang/dọc tuỳ màn hình) --}}
                                <div class="col-12 col-md d-flex gap-2 flex-wrap flex-sm-nowrap">
                                    @if($book->quantity > 0 || $book->file_ebook)
                                        <button type="submit" name="action" value="add" class="btn btn-outline-dark w-100 rounded-pill shadow-sm fw-bold text-nowrap d-flex align-items-center justify-content-center" style="height: 42px; font-size: 0.9rem;">
                                            <i class="fas fa-cart-plus me-1"></i> Thêm giỏ
                                        </button>
                                        <button type="submit" name="action" value="buy" id="btn-buy-text" class="btn btn-danger w-100 rounded-pill shadow-sm fw-bold text-nowrap d-flex align-items-center justify-content-center" style="height: 42px; font-size: 0.9rem;">
                                            <i class="fas fa-bolt me-1"></i> Mua ngay
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-secondary w-100 rounded-pill d-flex align-items-center justify-content-center" style="height: 42px; font-size: 0.9rem;" disabled>
                                            Hết hàng
                                        </button>
                                    @endif

                                    @php $hasPreview = !empty($book->file_preview) || !empty($book->book_content); @endphp
                                    <a href="{{ $hasPreview ? route('book.read', ['id' => $book->id, 'mode' => 'preview']) : 'javascript:void(0);' }}"
                                       class="btn btn-outline-info w-100 rounded-pill px-3 fw-bold text-nowrap d-flex align-items-center justify-content-center {{ !$hasPreview ? 'disabled opacity-50' : '' }}"
                                       style="height: 42px; font-size: 0.9rem;"
                                       @if(!$hasPreview) tabindex="-1" aria-disabled="true" style="pointer-events: none;" @endif>
                                        <i class="fas fa-book-reader me-1"></i> Đọc thử
                                    </a> 
                                </div>

                                {{-- 3. NÚT TRÁI TIM (Chỉ hiện trên PC) --}}
                                <div class="col-auto d-none d-md-block">
                                    @auth
                                        <button type="button" onclick="document.getElementById('fav-form').submit();" class="btn btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="{{ $isFavorite ? 'fas' : 'far' }} fa-heart fs-6"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="far fa-heart fs-6"></i>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </form>
                        
                        {{-- Form ẩn của nút Yêu thích (CHỈ ĐỂ LẠI 1 CÁI DUY NHẤT) --}}
                        @auth 
                            <form id="fav-form" action="{{ route('favorites.toggle', $book->id) }}" method="POST" class="d-none">
                                @csrf
                            </form> 
                        @endauth

                        {{-- Cam kết --}}
                        <div class="border-top pt-4 mt-2">
                            <div class="row g-2 text-secondary small mobile-small-text">
                                <div class="col-6 col-md-6"><i class="fas fa-shield-alt text-success me-2"></i> Chính hãng 100%</div>
                                <div class="col-6 col-md-6"><i class="fas fa-shipping-fast text-primary me-2"></i> Giao nhanh 2h</div>
                                <div class="col-6 col-md-6"><i class="fas fa-undo text-warning me-2"></i> Đổi trả 30 ngày</div>
                                <div class="col-6 col-md-6"><i class="fas fa-headset text-dark me-2"></i> Hỗ trợ 24/7</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. MÔ TẢ NỘI DUNG --}}
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold text-dark m-0"><i class="fas fa-align-left me-2"></i> Giới thiệu nội dung</h5>
                        </div>
                        <div class="card-body p-4 text-dark" style="line-height: 1.8; font-size: 1.05rem;">
                            {!! nl2br(e($book->description ?? 'Đang cập nhật nội dung...')) !!}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- 4. PHẦN ĐÁNH GIÁ --}}
    <section class="bg-light py-5 border-top">
        <div class="container">
            <h4 class="fw-bold mb-4 text-dark">
                <i class="fas fa-comments text-primary me-2"></i> Khách Hàng Đánh Giá
            </h4>

            @php
                $totalReviews = $book->reviews->count();
                $rate5 = $totalReviews > 0 ? ($book->reviews->where('rating', 5)->count() / $totalReviews) * 100 : 0;
                $rate4 = $totalReviews > 0 ? ($book->reviews->where('rating', 4)->count() / $totalReviews) * 100 : 0;
                $rate3 = $totalReviews > 0 ? ($book->reviews->where('rating', 3)->count() / $totalReviews) * 100 : 0;
                $rate2 = $totalReviews > 0 ? ($book->reviews->where('rating', 2)->count() / $totalReviews) * 100 : 0;
                $rate1 = $totalReviews > 0 ? ($book->reviews->where('rating', 1)->count() / $totalReviews) * 100 : 0;
            @endphp

            {{-- KHỐI THỐNG KÊ TỔNG QUAN --}}
            <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden review-stats-card">
                <div class="row g-0">
                    <div class="col-md-4 bg-white d-flex flex-column justify-content-center align-items-center p-4 border-end">
                        <h1 class="fw-bold mb-0" style="font-size: 4rem; color: #ff9800;">{{ number_format($avgRating ?? 0, 1) }}</h1>
                        <div class="text-warning fs-5 mb-2">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star {{ $i <= round($avgRating ?? 0) ? '' : 'text-secondary opacity-25' }}"></i>
                            @endfor
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            Cơ sở từ {{ $totalReviews }} đánh giá
                        </span>
                    </div>

                    <div class="col-md-8 bg-white p-4">
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center">
                                <div class="text-muted fw-bold" style="width: 40px;">5 <i class="fas fa-star text-warning small"></i></div>
                                <div class="progress flex-grow-1 mx-3" style="height: 10px;">
                                    <div class="progress-bar" style="width: {{ $rate5 }}%; background-color: #ff9800;"></div>
                                </div>
                                <div class="text-muted small" style="width: 40px; text-align: right;">{{ round($rate5) }}%</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="text-muted fw-bold" style="width: 40px;">4 <i class="fas fa-star text-warning small"></i></div>
                                <div class="progress flex-grow-1 mx-3" style="height: 10px;">
                                    <div class="progress-bar" style="width: {{ $rate4 }}%; background-color: #ffb300;"></div>
                                </div>
                                <div class="text-muted small" style="width: 40px; text-align: right;">{{ round($rate4) }}%</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="text-muted fw-bold" style="width: 40px;">3 <i class="fas fa-star text-warning small"></i></div>
                                <div class="progress flex-grow-1 mx-3" style="height: 10px;">
                                    <div class="progress-bar" style="width: {{ $rate3 }}%; background-color: #ffc107;"></div>
                                </div>
                                <div class="text-muted small" style="width: 40px; text-align: right;">{{ round($rate3) }}%</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="text-muted fw-bold" style="width: 40px;">2 <i class="fas fa-star text-warning small"></i></div>
                                <div class="progress flex-grow-1 mx-3" style="height: 10px;">
                                    <div class="progress-bar" style="width: {{ $rate2 }}%; background-color: #ffd54f;"></div>
                                </div>
                                <div class="text-muted small" style="width: 40px; text-align: right;">{{ round($rate2) }}%</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="text-muted fw-bold" style="width: 40px;">1 <i class="fas fa-star text-warning small"></i></div>
                                <div class="progress flex-grow-1 mx-3" style="height: 10px;">
                                    <div class="progress-bar" style="width: {{ $rate1 }}%; background-color: #ffe082;"></div>
                                </div>
                                <div class="text-muted small" style="width: 40px; text-align: right;">{{ round($rate1) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KHU VỰC VIẾT ĐÁNH GIÁ --}}
            @auth
                @if(isset($canReview) && $canReview)
                    @php
                        $myReview = \App\Models\Review::where('user_id', Auth::id())->where('book_id', $book->id)->first();
                    @endphp

                    <div class="card shadow-sm border border-primary border-opacity-25 mb-5 rounded-4" style="background: linear-gradient(to right, #ffffff, #f8fbff);">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="fas fa-pen-nib me-2"></i> Trải nghiệm của bạn thế nào?
                            </h5>

                            @if($myReview)
                                <div class="alert alert-warning small py-2 mb-3 border-0 shadow-sm">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Bạn đã đánh giá sản phẩm này rồi. Nếu bạn tiếp tục gửi, <strong>đánh giá cũ sẽ bị ghi đè</strong>.
                                </div>
                            @endif

                            <form action="{{ route('review.store') }}" method="POST"
                                @if($myReview)
                                    onsubmit="return confirm('⚠️ CẢNH BÁO: Bạn đã có đánh giá cho cuốn sách này!\n\nNếu bạn tiếp tục gửi, hệ thống sẽ XÓA bình luận cũ và thay bằng bình luận mới này.\n\nBạn có chắc chắn muốn tiếp tục không?');"
                                @endif
                            >
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">

                                <style>
                                    .rating-stars { display: flex; flex-direction: row-reverse; justify-content: flex-end; }
                                    .rating-stars input { display: none; }
                                    .rating-stars label { color: #d3d3d3; font-size: 1.8rem; padding: 0 0.15rem; cursor: pointer; transition: color 0.2s ease-in-out; }
                                    .rating-stars input:checked ~ label, .rating-stars label:hover, .rating-stars label:hover ~ label { color: #ffc107 !important; }
                                </style>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">Chất lượng sách (Chọn sao)</label>
                                    <br>
                                    <div class="bg-white border rounded-3 p-2 d-inline-block shadow-sm">
                                        <div class="rating-stars">
                                            <input type="radio" name="rating" value="5" id="rating5" checked><label for="rating5" class="fas fa-star"></label>
                                            <input type="radio" name="rating" value="4" id="rating4"><label for="rating4" class="fas fa-star"></label>
                                            <input type="radio" name="rating" value="3" id="rating3"><label for="rating3" class="fas fa-star"></label>
                                            <input type="radio" name="rating" value="2" id="rating2"><label for="rating2" class="fas fa-star"></label>
                                            <input type="radio" name="rating" value="1" id="rating1"><label for="rating1" class="fas fa-star"></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">Chi tiết đánh giá</label>
                                    <textarea name="comment" class="form-control rounded-3" rows="3" placeholder="Chia sẻ thêm về chất lượng giấy, nội dung sách, tốc độ giao hàng..." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                                    {{ $myReview ? 'Viết Đánh Giá Mới' : 'Gửi Đánh Giá Ngay' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary border-0 shadow-sm rounded-4 mb-5 d-flex align-items-center p-4">
                        <i class="fas fa-shopping-cart text-secondary me-3 fa-2x"></i>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Chỉ khách hàng đã mua mới được đánh giá</h6>
                            <span class="text-muted small">Bạn cần hoàn tất đơn mua cuốn sách này để mở khóa chức năng bình luận nhé.</span>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-5 d-flex align-items-center p-4">
                    <i class="fas fa-user-circle text-warning me-3 fa-2x"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Bạn chưa đăng nhập</h6>
                        <span class="text-muted small">Vui lòng <a href="{{ route('login') }}" class="text-primary text-decoration-underline fw-bold">đăng nhập</a> để tham gia thảo luận và đánh giá sản phẩm.</span>
                    </div>
                </div>
            @endauth

            {{-- DANH SÁCH BÌNH LUẬN --}}
            <h5 class="fw-bold mb-4">Lọc theo đánh giá mới nhất</h5>
            <div class="row">
                <div class="col-12">
                    @php
                        $sortedReviews = $book->reviews->sortByDesc(function($review) {
                            $isMine = (Auth::check() && $review->user_id == Auth::id()) ? 1 : 0;
                            return $isMine . '_' . $review->created_at->timestamp;
                        });
                    @endphp

                    @forelse($sortedReviews as $review)
                        @if($review->status == 'hidden')
                            <div class="card mb-4 border border-danger border-opacity-50 shadow-sm rounded-4 bg-light" style="opacity: 0.85;">
                                <div class="card-body p-4">
                                    <div class="alert alert-danger py-2 px-3 small fw-bold mb-3 border-0 shadow-sm d-flex align-items-center">
                                        <i class="fas fa-eye-slash fa-lg me-2"></i>
                                        <span>Bình luận của bạn đã bị Quản trị viên ẩn đi và không còn hiển thị với những người dùng khác.</span>
                                    </div>

                                    <div class="d-flex opacity-75 grayscale">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold bg-secondary" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                {{ substr($review->user->name ?? 'U', 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fw-bold text-dark mb-1">{{ $review->user->name ?? 'Bạn' }}</h6>
                                            <div class="mb-2">
                                                @for($i=1; $i<=5; $i++)
                                                    <i class="fas fa-star text-secondary"></i>
                                                @endfor
                                            </div>
                                            <p class="mb-0 text-dark fst-italic" style="line-height: 1.6; word-break: break-word;"><del>{{ $review->comment }}</del></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card mb-4 border-0 shadow-sm rounded-4 review-item-mobile {{ (Auth::check() && $review->user_id == Auth::id()) ? 'border border-primary border-opacity-50' : '' }}">
                                <div class="card-body p-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                 style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 1.2rem;">
                                                {{ substr($review->user->name ?? 'U', 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0 d-inline-block">{{ $review->user->name ?? 'Người dùng ẩn danh' }}</h6>

                                                    @if(Auth::check() && $review->user_id == Auth::id())
                                                        <span class="badge bg-primary ms-2 px-2 py-1 rounded-pill" style="font-size: 0.7rem;">Đánh giá của bạn</span>
                                                    @endif

                                                    <span class="badge bg-success bg-opacity-10 text-success ms-1 px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="fas fa-check-circle me-1"></i>Đã mua hàng
                                                    </span>
                                                </div>
                                                <span class="text-muted small"><i class="far fa-clock me-1"></i>{{ $review->created_at->format('d/m/Y') }}</span>
                                            </div>

                                            <div class="mb-2">
                                                @for($i=1; $i<=5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-secondary opacity-25' }}" style="font-size: 0.9rem;"></i>
                                                @endfor
                                                @php
                                                    $ratingLabels = [1 => 'Tệ', 2 => 'Hơi tệ', 3 => 'Ổn', 4 => 'Tốt', 5 => 'Rất tốt'];
                                                    $ratingColors = [1 => 'text-danger', 2 => 'text-danger', 3 => 'text-warning', 4 => 'text-success', 5 => 'text-success'];
                                                @endphp
                                                <span class="{{ $ratingColors[$review->rating] ?? 'text-muted' }} small fw-bold ms-2">
                                                    {{ $ratingLabels[$review->rating] ?? '' }}
                                                </span>
                                            </div>

                                            <p class="mb-0 text-dark" style="line-height: 1.6; word-break: break-word;">{{ $review->comment }}</p>

                                            @if($review->admin_reply)
                                                <div class="mt-3 p-3 bg-light rounded-3 border-start border-4 border-primary position-relative">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-2 shadow-sm" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                                            <i class="fas fa-user-shield"></i>
                                                        </div>
                                                        <h6 class="fw-bold text-primary mb-0" style="font-size: 0.9rem;">Phản hồi từ Thelwc Books</h6>
                                                    </div>
                                                    <p class="mb-0 text-secondary" style="font-size: 0.9rem; line-height: 1.5; word-break: break-word;">
                                                        {{ $review->admin_reply }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if(Auth::check() && in_array(Auth::user()->role, [0, 1, 2, 3, 4]))
                                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="mt-3 text-end" onsubmit="return confirm('Xóa bình luận vi phạm này?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 text-xs">
                                                        <i class="fas fa-trash-alt me-1"></i> Gỡ bỏ
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-5 border-0 shadow-sm rounded-4 bg-white">
                            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" alt="No Reviews" width="100" class="mb-3 opacity-50">
                            <h6 class="fw-bold text-dark">Chưa có đánh giá nào!</h6>
                            <p class="text-muted small">Hãy trở thành người đầu tiên đánh giá cuốn sách này.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- 5. SÁCH LIÊN QUAN --}}
    @if(isset($relatedBooks) && $relatedBooks->count() > 0)
        <section class="bg-white py-5 border-top">
            <div class="container">
                {{-- CSS bổ sung để Card không bị cắt bóng (shadow) khi nằm trong Owl Carousel --}}
            <style>
                .owl-carousel .item {
                    padding: 10px 5px 20px 5px; /* Chừa không gian cho bóng đổ khi hover */
                }
                .hover-card { 
                    border: 1px solid #eee !important; 
                    transition: all 0.3s ease; 
                }
                .hover-card:hover { 
                    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; 
                    transform: translateY(-5px); 
                    border-color: #0d6efd !important; 
                }
                .card-img-custom {
                    width: 100%; 
                    aspect-ratio: 2 / 3; /* Tỉ lệ vàng bìa sách */
                    object-fit: cover;
                    border-radius: 12px 12px 0 0;
                    background-color: #f8f9fa;
                }
            </style>

            <div class="mb-5 pb-4">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
                    <h3 class="fw-bold text-dark mb-0">
                        <i class="fas fa-heart text-danger me-2"></i> Có thể bạn quan tâm
                    </h3>
                </div>
                
                <div class="owl-carousel owl-theme">
                    @foreach($relatedBooks as $related)
                        <div class="item">
                            <div class="card h-100 border-0 shadow-sm position-relative hover-card rounded-4 bg-white">
                                
                                {{-- 🔥 LOGIC PHÂN LOẠI TAGS & ĐÁNH GIÁ 🔥 --}}
                                @php 
                                    $isSale = $related->sale_price > 0 && $related->sale_price < $related->price;
                                    $percent = $isSale ? round((($related->price - $related->sale_price)/$related->price)*100) : 0;
                                    $isNew = $related->created_at && $related->created_at > now()->subDays(7);
                                    $isEbook = $related->ebook_price > 0;
                                    
                                    // Tính Sao đánh giá
                                    $avgRating = $related->reviews_avg_rating ?? ($related->reviews ? $related->reviews->avg('rating') : 0);
                                    $reviewCount = $related->reviews_count ?? ($related->reviews ? $related->reviews->count() : 0);
                                @endphp

                                {{-- BỘ TAGS XẾP DỌC (Góc trái) --}}
                                <div class="position-absolute top-0 start-0 m-2 z-1 d-flex flex-column gap-1 align-items-start">
                                    @if($isSale) <span class="badge bg-danger shadow-sm px-2 py-1">-{{ $percent }}%</span> @endif
                                    @if($isNew) <span class="badge bg-success shadow-sm px-2 py-1">Mới</span> @endif
                                    @if($isEbook) <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fas fa-tablet-alt me-1"></i>Ebook</span> @endif
                                </div>

                                {{-- Ảnh --}}
                                <a href="{{ route('book.detail', $related->id) }}" class="overflow-hidden rounded-top-4">
                                    <img src="{{ asset($related->image ? (str_contains($related->image, 'uploads') ? $related->image : 'uploads/'.$related->image) : 'https://via.placeholder.com/300x450') }}" class="card-img-custom" alt="{{ $related->title }}" loading="lazy">
                                </a>
                                
                                <div class="card-body p-2 p-md-3 d-flex flex-column">
                                    {{-- Tên sách --}}
                                    <h6 class="mb-1" style="font-size: 14px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                        <a href="{{ route('book.detail', $related->id) }}" class="text-dark text-decoration-none fw-bold" title="{{ $related->title }}">
                                            {{ $related->title }}
                                        </a>
                                    </h6>
                                    
                                    {{-- Tác giả & Thể loại --}}
                                    <div class="mb-2">
                                        <small class="text-muted d-block text-truncate mb-1" style="font-size: 12px;"><i class="fas fa-pen-nib text-xs me-1"></i> {{ $related->author ?? 'Đang cập nhật' }}</small>
                                        <span class="badge bg-light text-secondary border fw-normal" style="font-size: 10px;">{{ $related->category->name ?? 'Chưa phân loại' }}</span>
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
                                                <span class="text-danger fw-bold" style="font-size: 16px; line-height: 1.2;">{{ number_format($related->sale_price) }}đ</span>
                                                <span class="text-muted text-decoration-line-through" style="font-size: 12px; line-height: 1.2;">{{ number_format($related->price) }}đ</span>
                                            @else
                                                <span class="text-dark fw-bold" style="font-size: 16px; line-height: 1.2;">{{ number_format($related->price) }}đ</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Lượt bán --}}
                                        <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                            <small class="text-muted" style="font-size: 11px;">Đã bán: <b class="text-dark">{{ number_format($related->total_sold ?? 0) }}</b></small>
                                        </div>
                                        
                                        {{-- Nút Xem Ngay --}}
                                        <a href="{{ route('book.detail', $related->id) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold py-1 mt-2" style="font-size: 0.85rem;">
                                            Xem chi tiết
                                        </a>
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

@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <style>
        .custom-radio-box input:checked + label {
            border-color: #0d6efd !important;
            background-color: #f0f7ff;
            color: #0d6efd;
            box-shadow: 0 0 0 1px #0d6efd;
        }

        .cursor-pointer { cursor: pointer; }

        .product-card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
            transition: all .3s;
        }

        .rating-css div { color: #ffc107; font-size: 20px; font-weight: 800; }
        .rating-css input { display: none; }
        .rating-css input + label {
            font-size: 24px;
            text-shadow: 1px 1px 0 #ffe066;
            cursor: pointer;
            color: #ccc;
            transition: all 0.2s;
        }
        .rating-css input:checked + label ~ label { color: #ccc; }
        .rating-css label:active { transform: scale(0.8); }
        .star-icon { direction: rtl; display: inline-flex; }
        .star-icon label:hover, .star-icon label:hover ~ label, .star-icon input:checked + label { color: #ffc107; }

        .btn-action-lite {
            font-size: 0.95rem;
            padding: 8px 16px;
        }

        .heart-btn {
            width: 44px;
            height: 44px;
        }

        @media (max-width: 767.98px) {
            .product-image-container {
                padding: 12px !important;
                border-radius: 18px !important;
            }

            .product-image-container img {
                max-height: 320px !important;
            }

            .display-6 {
                font-size: 1.5rem !important;
                line-height: 1.25;
            }

            #display_price {
                font-size: 1.7rem !important;
            }

            #display_old_price {
                font-size: 1rem !important;
            }

            .mobile-stack {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .mobile-stack > * {
                width: 100% !important;
            }

            .mobile-full {
                width: 100% !important;
            }

            .mobile-center {
                text-align: center !important;
            }

            .mobile-gap-2 > * + * {
                margin-top: .5rem;
            }

            .mobile-nowrap-fix {
                white-space: normal !important;
            }

            .mobile-small-text {
                font-size: .9rem !important;
            }

            .mobile-badges {
                display: flex;
                flex-wrap: wrap;
                gap: .5rem;
            }

            .mobile-badges .badge {
                margin-left: 0 !important;
            }

            .review-stats-card .col-md-4,
            .review-stats-card .col-md-8 {
                border-right: 0 !important;
            }

            .review-stats-card .col-md-4 {
                border-bottom: 1px solid #eee;
            }

            .owl-carousel .item {
                padding: 0 6px;
            }

            .owl-carousel .card-img-top {
                height: 220px !important;
            }

            .btn-action-lite {
                font-size: 0.9rem;
                padding: 7px 12px;
            }
        }

        @media (max-width: 575.98px) {
            .section-title-mobile {
                font-size: 1.2rem !important;
            }

            .quantity-box-mobile {
                width: 100% !important;
                max-width: 100% !important;
            }

            .quantity-box-mobile .input-group {
                max-width: 170px;
                margin: 0 auto;
            }

            .buy-actions-mobile {
                flex-direction: column !important;
            }

            .buy-actions-mobile .btn {
                width: 100% !important;
            }

            .buy-actions-mobile .btn.rounded-circle {
                width: 52px !important;
                height: 52px !important;
                align-self: center;
            }

            .info-grid-mobile .col-sm-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .rating-stars label {
                font-size: 1.5rem !important;
            }

            .review-item-mobile .d-flex {
                flex-direction: column !important;
            }

            .review-item-mobile .ms-3 {
                margin-left: 0 !important;
                margin-top: .75rem !important;
            }

            .btn-action-lite {
                font-size: 0.82rem;
                padding: 6px 10px;
            }

            #quantity-input-area {
                width: 100px !important;
            }

            .heart-btn {
                width: 52px !important;
                height: 52px !important;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <script>
        const physicalPrice = {{ ($book->sale_price && $book->sale_price < $book->price) ? $book->sale_price : $book->price }};
        const physicalOldPrice = {{ $book->price }};
        const hasSale = {{ ($book->sale_price && $book->sale_price < $book->price) ? 'true' : 'false' }};
        const ebookPrice = {{ $book->ebook_price ?? 0 }};

        function switchProductType(type) {
            const displayPrice = document.getElementById('display_price');
            const displayOldPrice = document.getElementById('display_old_price');
            const inputType = document.getElementById('input_book_type');
            const quantityArea = document.getElementById('quantity-input-area');
            const buyBtnText = document.getElementById('btn-buy-text');

            inputType.value = type;

            if (type === 'ebook') {
                displayPrice.innerText = new Intl.NumberFormat('vi-VN').format(ebookPrice) + ' ₫';
                displayPrice.classList.remove('text-danger');
                displayPrice.classList.add('text-primary');
                if(displayOldPrice) displayOldPrice.style.display = 'none';

                quantityArea.style.display = 'none';
                if(buyBtnText) buyBtnText.innerText = "Mua & Tải ngay";
            } else {
                displayPrice.innerText = new Intl.NumberFormat('vi-VN').format(physicalPrice) + ' ₫';
                displayPrice.classList.remove('text-primary');
                displayPrice.classList.add('text-danger');
                if(displayOldPrice && hasSale) displayOldPrice.style.display = 'inline';

                quantityArea.style.display = 'block';
                if(buyBtnText) buyBtnText.innerText = "Thêm vào giỏ";
            }
        }

        function toggleText(btn) {
            if (btn.getAttribute('aria-expanded') === 'true') {
                btn.innerHTML = 'Thu gọn <i class="fas fa-chevron-up ms-1"></i>';
            } else {
                btn.innerHTML = 'Xem thêm chi tiết <i class="fas fa-chevron-down ms-1"></i>';
            }
        }

        $(document).ready(function(){
            $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 0,
                nav: false,
                dots: true,
                autoplay: true,
                autoplayTimeout: 2000,
                smartSpeed: 1000,
                responsive:{
                    0:{items:2},
                    600:{items:3},
                    1000:{items:5}
                }
            });
        });
    </script>
@endsection