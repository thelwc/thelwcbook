@extends('client.layouts.master')

@section('title', 'Chi tiết đơn hàng #' . $order->id . ' - Thelwc Books')

@section('styles')
    <style>
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); background: #fff; overflow: hidden; }
        .card-header-custom { background: #fff; border-bottom: 1px solid #f0f0f0; padding: 20px 25px; }
        .text-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #888; font-weight: 700; margin-bottom: 5px; }
        .text-value { font-weight: 600; color: #333; font-size: 1rem; }
        .product-img { width: 65px; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        
        /* Status Badge Colors */
        .status-badge { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; }
        .status-pending { background: #fff8e1; color: #b7791f; }
        .status-confirmed { background: #ebf8ff; color: #2b6cb0; }
        .status-shipping { background: #e6fffa; color: #2c7a7b; }
        .status-completed { background: #f0fff4; color: #276749; }
        .status-cancelled { background: #fff5f5; color: #c53030; }
    </style>
@endsection

@section('content')
<div class="container py-4 py-md-5 bg-light" style="min-height: 100vh;">
    
    {{-- Breadcrumb & Back Button --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
        <div>
            <a href="{{ route('client.account.history') }}" class="text-decoration-none text-muted fw-bold hover-primary">
                <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
            </a>
        </div>
        <div class="text-muted small bg-white px-3 py-1 rounded-pill shadow-sm border">
            Mã đơn hàng: <span class="fw-bold text-dark">#{{ $order->id }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
    @endif

    <div class="row g-4">
        {{-- CỘT TRÁI: THÔNG TIN TRẠNG THÁI & SẢN PHẨM --}}
        <div class="col-lg-8">
            
            {{-- 1. TRẠNG THÁI ĐƠN HÀNG --}}
            <div class="card card-custom mb-4">
                <div class="card-body p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="text-label">Trạng thái hiện tại</div>
                        @if($order->status == 'pending' || $order->status == 0)
                            <span class="status-badge status-pending"><i class="fas fa-clock me-2"></i> Chờ xử lý</span>
                        @elseif($order->status == 'confirmed' || $order->status == 1)
                            <span class="status-badge status-confirmed"><i class="fas fa-check me-2"></i> Đã xác nhận</span>
                        @elseif($order->status == 'shipping')
                            <span class="status-badge status-shipping"><i class="fas fa-shipping-fast me-2"></i> Đang vận chuyển</span>
                        @elseif($order->status == 'completed')
                            <span class="status-badge status-completed"><i class="fas fa-star me-2"></i> Hoàn thành</span>
                        @elseif($order->status == 'cancelled' || $order->status == 2)
                            <span class="status-badge status-cancelled"><i class="fas fa-times-circle me-2"></i> Đã hủy</span>
                        @endif
                    </div>
                    
                    {{-- Nút Hủy (Chỉ hiện khi đơn Chờ xử lý) --}}
                    @if($order->status == 'pending' || $order->status == 0)
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?');">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3">
                            <i class="fas fa-times me-1"></i> Hủy đơn hàng
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- 2. SẢN PHẨM ĐÃ ĐẶT (🔥 ĐÃ ĐẬP BỎ TABLE - CHUYỂN SANG FLEXBOX 🔥) --}}
            <div class="card card-custom mb-4">
                <div class="card-header-custom fw-bold fs-5"><i class="fas fa-box me-2 text-warning"></i> Sản phẩm đã đặt</div>
                
                <div class="card-body p-3 p-md-4">
                    @foreach($order->details as $detail)
                        @php
                            // Logic kiểm tra loại sách
                            $isEbook = false;
                            if(isset($detail->type) && $detail->type == 'ebook') {
                                $isEbook = true;
                            } elseif($detail->book && $detail->price == $detail->book->ebook_price) {
                                $isEbook = true;
                            }

                            // Logic kiểm tra giá khuyến mãi
                            $originalPrice = $detail->price;
                            if($detail->book) {
                                $originalPrice = $isEbook ? ($detail->book->ebook_price ?? $detail->price) : ($detail->book->price ?? $detail->price);
                            }
                            $isDiscounted = $originalPrice > $detail->price;
                        @endphp

                        <div class="d-flex mb-3 pb-3 border-bottom">
                            
                            {{-- 1. ẢNH SẢN PHẨM --}}
                            <div class="me-3 flex-shrink-0">
                                @if($detail->book)
                                    <a href="{{ route('book.detail', $detail->book_id) }}">
                                        <img src="{{ asset(str_contains($detail->book->image ?? '', 'uploads') ? ($detail->book->image ?? '') : 'uploads/' . ($detail->book->image ?? '')) }}" 
                                             class="product-img bg-light shadow-sm" alt="img" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    </a>
                                @else
                                    <img src="https://via.placeholder.com/60x85?text=Deleted" class="product-img bg-light shadow-sm" alt="Sản phẩm đã xóa">
                                @endif
                            </div>
                            
                            {{-- 2. THÔNG TIN (Dàn theo cột dọc flex-column) --}}
                            <div class="flex-grow-1 d-flex flex-column justify-content-between">
                                
                                {{-- Tiêu đề & Badge (Nằm trên) --}}
                                <div>
                                    <div class="fw-bold text-dark mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                        @if($detail->book)
                                            <a href="{{ route('book.detail', $detail->book_id) }}" class="text-dark text-decoration-none">
                                                {{ $detail->book->title }}
                                            </a>
                                        @else
                                            <span class="text-muted text-decoration-line-through">Sản phẩm đã bị xóa</span>
                                        @endif
                                    </div>
                                    
                                    @if($isEbook)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary" style="font-size: 0.65rem;">
                                            <i class="fas fa-tablet-alt me-1"></i> Ebook
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary" style="font-size: 0.65rem;">
                                            <i class="fas fa-book me-1"></i> Sách giấy
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Giá x Số lượng & Tổng tiền (Nằm dưới, ép ra 2 bên bằng justify-content-between) --}}
                                <div class="d-flex justify-content-between align-items-end mt-2">
                                    <div class="small">
                                        @if($isDiscounted)
                                            <span class="text-muted text-decoration-line-through me-1" style="font-size: 0.75rem;">{{ number_format($originalPrice) }}đ</span>
                                        @endif
                                        <span class="{{ $isDiscounted ? 'text-danger' : 'text-dark' }} fw-bold">{{ number_format($detail->price) }}đ</span> 
                                        <span class="text-dark fw-bold ms-1" style="font-size: 0.85rem;">x{{ $detail->quantity }}</span>
                                    </div>
                                    
                                    {{-- Tổng tiền của món này (Bự và đỏ) --}}
                                    <div class="fw-bold text-danger fs-5">
                                        {{ number_format($detail->price * $detail->quantity) }} ₫
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- 3. TỔNG KẾT ĐƠN HÀNG CHI TIẾT --}}
                <div class="card-footer bg-light p-3 p-md-4 border-0">
                    <div class="row">
                        <div class="col-md-8 col-lg-7 offset-md-4 offset-lg-5">
                            @php
                                $tongGiaGoc = 0;
                                $tongTienMua = 0;
                                foreach($order->details as $d) {
                                    $isEbookItem = false;
                                    if(isset($d->type) && $d->type == 'ebook') $isEbookItem = true;
                                    elseif($d->book && $d->price == $d->book->ebook_price) $isEbookItem = true;
                                    
                                    $origPrice = $d->price;
                                    if($d->book) {
                                        $origPrice = $isEbookItem ? ($d->book->ebook_price ?? $d->price) : ($d->book->price ?? $d->price);
                                    }
                                    $tongGiaGoc += $origPrice * $d->quantity;
                                    $tongTienMua += $d->price * $d->quantity;
                                }
                                $giamGiaSanPham = $tongGiaGoc - $tongTienMua;
                            @endphp

                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Tổng giá trị sản phẩm:</span>
                                <span>{{ number_format($tongGiaGoc) }} ₫</span>
                            </div>

                            @if($giamGiaSanPham > 0)
                            <div class="d-flex justify-content-between mb-2 small text-success">
                                <span><i class="fas fa-tags me-1"></i> Khuyến mãi sản phẩm:</span>
                                <span class="fw-bold">-{{ number_format($giamGiaSanPham) }} ₫</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between mb-2 small text-dark fw-bold border-bottom pb-2 border-secondary border-opacity-25">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($tongTienMua) }} ₫</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Phí vận chuyển:</span>
                                <span>
                                    @if($order->shipping_fee > 0)
                                        {{ number_format($order->shipping_fee) }} ₫
                                    @else
                                        <span class="text-primary fw-bold">0 ₫</span>
                                    @endif
                                </span>
                            </div>

                            @if($order->discount > 0)
                            <div class="d-flex justify-content-between mb-2 small text-success">
                                <span><i class="fas fa-ticket-alt me-1"></i> Voucher giảm giá:</span>
                                <span class="fw-bold">-{{ number_format($order->discount) }} ₫</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between mb-0 pt-3 border-top border-secondary border-opacity-25 align-items-center">
                                <span class="fw-bold text-dark text-uppercase">Thành tiền:</span>
                                <span class="fs-3 fw-bold text-danger">{{ number_format($order->total_price) }} ₫</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- CỘT PHẢI: GHI CHÚ, ĐỊA CHỈ, THANH TOÁN --}}
        <div class="col-lg-4">
            
            {{-- GHI CHÚ NỔI BẬT --}}
            @if($order->note)
            <div class="alert alert-warning shadow-sm border-warning d-flex align-items-start mb-4 rounded-4" role="alert">
                <i class="fas fa-sticky-note fs-4 me-3 mt-1 text-warning"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1 text-dark">Ghi chú của bạn:</h6>
                    <p class="mb-0 fst-italic small text-dark">"{{ $order->note }}"</p>
                </div>
            </div>
            @endif

            {{-- THÔNG TIN GIAO HÀNG --}}
            <div class="card card-custom mb-4">
                <div class="card-header-custom fw-bold"><i class="fas fa-map-marker-alt me-2 text-danger"></i> Địa chỉ nhận hàng</div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="text-label">Người nhận</div>
                        <div class="text-value">{{ $order->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-label">Điện thoại</div>
                        <div class="text-value">{{ $order->phone }}</div>
                    </div>
                    <div class="mb-0">
                        <div class="text-label">Địa chỉ</div>
                        <div class="text-value small text-secondary lh-base">{{ $order->address }}</div>
                    </div>
                </div>
            </div>

            {{-- THÔNG TIN THANH TOÁN --}}
            <div class="card card-custom mb-4">
                <div class="card-header-custom fw-bold"><i class="fas fa-credit-card me-2 text-primary"></i> Thanh toán</div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="text-label">Phương thức</div>
                        <div class="text-value">
                            @if($order->payment_method == 'COD' || $order->payment_method == 'cod')
                                <i class="fas fa-money-bill-wave text-success me-1"></i> Thanh toán khi nhận hàng
                            @else
                                <i class="fas fa-university text-primary me-1"></i> Chuyển khoản ngân hàng
                            @endif
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="text-label">Ngày đặt hàng</div>
                        <div class="text-value small">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection