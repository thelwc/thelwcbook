@extends('client.layouts.master')

@section('title')
    Tủ sách của tôi - Thelwc Books
@endsection

@section('content')
<div class="container py-5">
    
    {{-- HEADER: TIÊU ĐỀ --}}
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-book-reader me-2 text-primary"></i>Tủ sách của tôi</h2>
            <p class="text-muted small mb-0">Các cuốn sách bạn đã sở hữu (Đã thanh toán)</p>
        </div>
        <div>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-store me-1"></i> Mua thêm sách
            </a>
        </div>
    </div>

    {{-- LIST SÁCH --}}
    <div class="row g-4">
        @forelse($books as $book)
            <div class="col-6 col-md-3 col-lg-2">
                <div class="card h-100 border-0 shadow-sm book-card position-relative">
                    
                    {{-- ẢNH BÌA --}}
                    <div class="position-relative overflow-hidden rounded-3">
                        <a href="{{ route('book.read', $book->id) }}">
                            @if($book->image)
                                <img src="{{ asset(str_contains($book->image, 'uploads') ? $book->image : 'uploads/' . $book->image) }}" 
                                     class="card-img-top object-fit-cover" 
                                     style="height: 240px; width: 100%; transition: transform 0.3s;"
                                     alt="{{ $book->title }}">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 240px;">
                                    <i class="fas fa-book fa-3x"></i>
                                </div>
                            @endif
                        </a>
                        
                        {{-- Overlay nút đọc khi hover (tùy chọn) --}}
                        <div class="book-overlay d-none d-md-flex">
                            <a href="{{ route('book.read', $book->id) }}" class="btn btn-light rounded-circle shadow text-primary">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                    </div>

                    {{-- THÔNG TIN --}}
                    <div class="card-body px-0 py-3 text-center d-flex flex-column">
                        <h6 class="card-title fw-bold text-truncate mb-1" title="{{ $book->title }}">
                            <a href="{{ route('book.read', $book->id) }}" class="text-decoration-none text-dark">
                                {{ $book->title }}
                            </a>
                        </h6>
                        <div class="text-muted small mb-3">{{ $book->author }}</div>
                        
                        {{-- NÚT ĐỌC NGAY --}}
                        <div class="mt-auto">
                            <a href="{{ route('book.read', $book->id) }}" class="btn btn-primary w-100 rounded-pill fw-bold btn-sm">
                                <i class="fas fa-book-open me-1"></i> Đọc ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- TRẠNG THÁI TRỐNG --}}
            <div class="col-12">
                <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                    <i class="fas fa-box-open fa-4x text-muted mb-3 opacity-50"></i>
                    <h5 class="fw-bold text-secondary">Tủ sách trống trơn!</h5>
                    <p class="text-muted mb-4">Bạn chưa mua cuốn sách điện tử (Ebook) nào cả.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary px-4 rounded-pill">
                        <i class="fas fa-shopping-cart me-2"></i> Dạo nhà sách ngay
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    /* Hiệu ứng hover nhẹ cho ảnh bìa */
    .book-card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    /* Overlay nút Play ở giữa ảnh */
    .book-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.2);
        align-items: center; justify-content: center;
        opacity: 0; transition: 0.3s;
    }
    .book-card:hover .book-overlay {
        opacity: 1;
    }
</style>
@endsection