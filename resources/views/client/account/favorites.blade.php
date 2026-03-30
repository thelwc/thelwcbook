@extends('client.layouts.master')

@section('title', 'Sách yêu thích - Thelwc Books')

@section('styles')
    <style>
        /* Ép chia 5 cột trên màn hình máy tính (lg) */
        @media (min-width: 992px) {
            .col-5-custom { width: 20%; flex: 0 0 20%; }
        }
        
        .card-book { 
            border: none; border-radius: 12px; background: #fff; 
            transition: transform 0.3s, box-shadow 0.3s; 
            display: flex; flex-direction: column; height: 100%;
        }
        .card-book:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.08); 
        }

        /* Tỷ lệ ảnh đứng 9:16 */
        .image-container {
            position: relative;
            width: 100%;
            padding-top: 177.77%; /* Tỷ lệ 16/9 = 1.77 */
            overflow: hidden;
            border-radius: 12px 12px 0 0;
        }
        .image-container img {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover;
        }

        .btn-remove { 
            position: absolute; top: 8px; right: 8px; z-index: 10;
            background: rgba(255, 255, 255, 0.8); border: none; 
            width: 30px; height: 30px; border-radius: 50%; 
            color: #dc3545; transition: 0.2s; 
            display: flex; align-items: center; justify-content: center; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
        }
        .btn-remove:hover { background: #dc3545; color: #fff; }

        .badge-sale {
            position: absolute; top: 8px; left: 8px; z-index: 10;
            background: #ff4757; color: #fff; font-size: 0.7rem;
            padding: 3px 8px; border-radius: 4px; fw-bold;
        }

        .card-body { padding: 12px; display: flex; flex-direction: column; flex-grow: 1; }
        .book-title { font-size: 0.9rem; line-height: 1.2rem; height: 2.4rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 5px; }
        .price-current { color: #ff4757; font-weight: 800; font-size: 1rem; }
        .price-old { text-decoration: line-through; color: #999; font-size: 0.8rem; margin-left: 5px; }
        .sold-count { font-size: 0.75rem; color: #666; margin-top: 5px; }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0 text-dark"><i class="fas fa-heart text-danger me-2"></i> YÊU THÍCH</h4>
            <a href="{{ route('home') }}" class="btn btn-outline-dark rounded-pill px-3 btn-sm">
                Tiếp tục mua sắm
            </a>
        </div>

        @if($favorites->count() > 0)
            <div class="row g-3"> {{-- g-3 để khoảng cách giữa các cuốn sách vừa phải --}}
                @foreach($favorites as $fav)
                    @if($fav->book)
                        <div class="col-6 col-md-4 col-lg-5-custom col-5-custom mb-3">
                            <div class="card card-book shadow-sm">
                                
                                {{-- Nút Xóa nhanh --}}
                                <form action="{{ route('favorites.toggle', $fav->book->id) }}" method="POST">
                                    @csrf
                                    <button class="btn-remove" title="Bỏ thích" onclick="return confirm('Xóa khỏi danh sách yêu thích?')">
                                        <i class="fas fa-times" style="font-size: 12px;"></i>
                                    </button>
                                </form>

                                {{-- Badge giảm giá --}}
                                @if($fav->book->sale_price && $fav->book->sale_price < $fav->book->price)
                                    @php
                                        $percent = round((($fav->book->price - $fav->book->sale_price) / $fav->book->price) * 100);
                                    @endphp
                                    <div class="badge-sale">-{{ $percent }}%</div>
                                @endif

                                {{-- Ảnh sách tỷ lệ 9:16 --}}
                                <a href="{{ route('book.detail', $fav->book->id) }}">
                                    <div class="image-container">
                                        <img src="{{ asset(str_contains($fav->book->image, 'uploads') ? $fav->book->image : 'uploads/' . $fav->book->image) }}">
                                    </div>
                                </a>

                                <div class="card-body">
                                    <a href="{{ route('book.detail', $fav->book->id) }}" class="text-dark text-decoration-none book-title fw-bold">
                                        {{ $fav->book->title }}
                                    </a>
                                    
                                    <div class="mt-2">
                                        @if($fav->book->sale_price && $fav->book->sale_price < $fav->book->price)
                                            <div class="price-current">{{ number_format($fav->book->sale_price) }}đ</div>
                                            <span class="price-old">{{ number_format($fav->book->price) }}đ</span>
                                        @else
                                            <div class="price-current" style="color: #333;">{{ number_format($fav->book->price) }}đ</div>
                                        @endif
                                    </div>

                                    {{-- Giả lập số lượng bán hoặc lấy từ DB nếu cậu có cột sold --}}
                                    <div class="sold-count">
                                        <i class="fas fa-shopping-cart me-1" style="font-size: 0.7rem;"></i> Đã bán {{ $fav->book->sold_count ?? rand(10, 100) }}
                                    </div>

                                    <div class="mt-auto pt-2">
                                        <a href="{{ route('book.detail', $fav->book->id) }}" class="btn btn-outline-dark w-100 btn-sm rounded-pill py-1" style="font-size: 0.75rem;">
                                            Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                <img src="https://cdn-icons-png.flaticon.com/512/1077/1077035.png" width="80" class="mb-3 opacity-25">
                <h5 class="text-muted fw-bold">Chưa có cuốn sách nào!</h5>
                <a href="{{ route('home') }}" class="btn btn-dark rounded-pill mt-3 px-4">Khám phá ngay</a>
            </div>
        @endif
    </div>
@endsection