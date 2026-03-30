@extends('client.layouts.master')

@section('title', 'Thanh toán - Thelwc Book')

@section('styles')
<style>
    /* --- CSS RIÊNG CHO TRANG THANH TOÁN --- */
    .card-custom {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .text-muted-custom {
        color: #6c757d;
    }

    .btn-dark-brand {
        background-color: #212529;
        border: 1px solid #212529;
        color: #ffffff;
        font-weight: 700;
        padding: 14px;
        border-radius: 999px;
        transition: all .3s ease;
    }

    .btn-dark-brand:hover {
        background-color: #c5a992;
        border-color: #c5a992;
        color: #ffffff;
    }

    .form-label {
        font-weight: 700;
        margin-bottom: .25rem;
        font-size: 0.9rem;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 14px;
        font-size: .95rem;
        border: 1px solid #dee2e6;
    }

    .form-control:focus {
        border-color: #c5a992;
        box-shadow: 0 0 0 0.2rem rgba(197, 169, 146, 0.25);
    }

    .checkout-item-img {
        width: 50px;
        height: 70px;
        object-fit: cover;
        border-radius: 4px;
    }

    .alert-danger {
        background-color: #ffe5e5;
        color: #b02a37;
        border: none;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')

<div class="container py-5">

    {{-- Nút quay lại --}}
    <div class="mb-4">
        <a href="{{ route('cart.index') }}" class="fw-bold small text-decoration-none text-secondary hover-text-dark">
            <i class="fas fa-arrow-left me-2"></i>QUAY LẠI GIỎ HÀNG
        </a>
    </div>

    <div class="row g-4">
        {{-- CỘT TRÁI: FORM NHẬP LIỆU --}}
        <div class="col-lg-7">
            <div class="card-custom p-4 h-100">
                <h4 class="fw-bold mb-4">Thông tin giao hàng</h4>

                @if(session('error'))
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('place.order') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="name" class="form-control" placeholder="Nhập họ tên"
                                value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại"
                                value="{{ Auth::check() ? Auth::user()->phone : '' }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email (Để nhận thông báo đơn hàng)</label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập địa chỉ email của bạn"
                            value="{{ Auth::check() ? Auth::user()->email : '' }}" required>
                    </div>

                    {{-- 🔥 LOGIC ĐỊA CHỈ: Nếu toàn Ebook thì ẩn ô nhập địa chỉ hoặc readonly --}}
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ nhận hàng</label>
                        @if($needsShipping)
                        <textarea name="address" rows="2" class="form-control" placeholder="Số nhà, tên đường, phường/xã..." required>{{ Auth::check() ? Auth::user()->address : '' }}</textarea>
                        @else
                        <input type="text" name="address" class="form-control bg-light" value="Gửi qua Email (Không cần địa chỉ)" readonly>
                        <div class="form-text text-success"><i class="fas fa-check-circle me-1"></i> Đơn hàng Ebook sẽ được gửi qua email của bạn.</div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted-custom">Ghi chú đơn hàng (Tuỳ chọn)</label>
                        <textarea name="note" rows="2" class="form-control" placeholder="Ví dụ: Gọi trước khi giao..."></textarea>
                    </div>

                    <h5 class="fw-bold mb-3">Phương thức thanh toán</h5>
                    <div class="border rounded p-3 mb-4 bg-white">

                        {{-- 🔥 LOGIC KIỂM TRA EBOOK 🔥 --}}
                        @php
                        $hasEbookItem = false;
                        if(session('cart')) {
                        foreach(session('cart') as $item) {
                        if(isset($item['type']) && $item['type'] == 'ebook') {
                        $hasEbookItem = true;
                        break;
                        }
                        }
                        }
                        @endphp

                        {{-- Lựa chọn 1: Thanh toán khi nhận hàng (COD) --}}
                        @if(!$hasEbookItem)
                        {{-- Nếu KHÔNG có Ebook -> Cho chọn COD --}}
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label fw-bold cursor-pointer" for="cod">
                                <i class="fas fa-money-bill-wave text-success me-2"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                            <div class="text-muted small ms-4">
                                Thanh toán bằng tiền mặt cho shipper khi nhận hàng.
                            </div>
                        </div>
                        <hr class="my-2 border-secondary opacity-25">

                        @else
                        {{-- Nếu CÓ Ebook -> Ẩn COD và hiện thông báo --}}
                        <div class="alert alert-warning small mb-3 border-warning">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            Đơn hàng có chứa <strong>Ebook (Sách điện tử)</strong> nên không hỗ trợ thanh toán khi nhận hàng (COD). Vui lòng chuyển khoản để nhận sách ngay.
                        </div>
                        @endif

                        {{-- Lựa chọn 2: Chuyển khoản VietQR --}}
                        <div class="form-check mt-3">
                            {{-- Nếu có Ebook thì auto check cái này --}}
                            <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer" {{ $hasEbookItem ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold cursor-pointer" for="bank_transfer">
                                <i class="fas fa-qrcode text-primary me-2"></i> Chuyển khoản ngân hàng (VietQR)
                            </label>
                            <div class="text-muted small ms-4">
                                Hệ thống sẽ tạo mã QR tự động. Bạn chỉ cần mở app ngân hàng và quét.
                                <span class="badge bg-primary ms-1" style="font-size: 0.7rem">Khuyên dùng</span>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-dark-brand w-100 text-uppercase">
                        Xác nhận đặt hàng
                    </button>
                </form>
            </div>
        </div>

        {{-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG --}}
        <div class="col-lg-5">
            <div class="card-custom p-4 bg-white sticky-top" style="top: 100px; z-index: 1;">
                <h5 class="fw-bold mb-4">Đơn hàng của bạn <span class="badge bg-dark rounded-pill">{{ count(session('cart', [])) }}</span></h5>

                <div class="mb-4 pe-2" style="max-height: 380px; overflow-y: auto; overflow-x: hidden;">
                    @foreach(session('cart', []) as $item)
                    <div class="d-flex mb-3 pb-3 border-bottom border-light">

                        {{-- 1. ẢNH SẢN PHẨM --}}
                        <div class="me-3 flex-shrink-0">
                            <img src="{{ asset(str_contains($item['image'], 'uploads') ? $item['image'] : 'uploads/' . $item['image']) }}"
                                class="checkout-item-img rounded shadow-sm border"
                                style="width: 60px; height: 85px; object-fit: cover;">
                        </div>

                        {{-- 2. THÔNG TIN (Tên, Phân loại, Giá, Số lượng) --}}
                        <div class="flex-grow-1 d-flex flex-column justify-content-between">

                            {{-- Dòng trên: Tên sách và Phân loại --}}
                            <div>
                                <h6 class="mb-1 small fw-bold text-dark" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"> {{ $item['name'] }}
                                </h6>

                                @if(isset($item['type']) && $item['type'] == 'ebook')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary mt-1" style="font-size: 0.6rem;">
                                    <i class="fas fa-tablet-alt me-1"></i> EBOOK
                                </span>
                                @endif
                            </div>

                            {{-- Dòng dưới: Giá x Số lượng + Tổng cộng (Nằm ngang dàn đều) --}}
                            <div class="d-flex justify-content-between align-items-end mt-2">
                                <div class="text-muted small">
                                    {{ number_format($item['price']) }} đ
                                    <span class="fw-bold text-dark ms-1">x{{ $item['quantity'] }}</span>
                                </div>
                                <div class="fw-bold text-danger">
                                    {{ number_format($item['price'] * $item['quantity']) }} đ
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="my-4">

                {{-- HIỂN THỊ TỔNG TIỀN ĐÃ DÙNG DB (Đã thêm logic hiển thị Giá gốc & Khuyến mãi) --}}
                @php
                // Tính toán tổng giá gốc ngay tại View (giống hệt trang Cart)
                $tongGiaGoc = 0;
                if(session('cart')) {
                foreach(session('cart') as $id => $item) {
                $bookInDb = \App\Models\Book::find($item['id']);
                $originalPrice = (isset($item['type']) && $item['type'] == 'ebook')
                ? ($bookInDb->ebook_price ?? $item['price'])
                : ($bookInDb->price ?? $item['price']);
                $tongGiaGoc += $originalPrice * $item['quantity'];
                }
                }
                // Trừ ra để biết sản phẩm đã giảm bao nhiêu
                $giamGiaSanPham = $tongGiaGoc - $tongTienHang;
                @endphp

                <div class="card p-4 rounded-3 shadow-sm bg-white border">

                    {{-- 1. Tổng giá gốc --}}
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-secondary">Tổng giá trị sản phẩm</span>
                        <span>{{ number_format($tongGiaGoc) }} đ</span>
                    </div>

                    {{-- 2. Giảm giá từ bản thân sản phẩm --}}
                    @if($giamGiaSanPham > 0)
                    <div class="d-flex justify-content-between mb-2 small text-success">
                        <span><i class="fas fa-tags me-1"></i> Khuyến mãi sản phẩm</span>
                        <span class="fw-bold">-{{ number_format($giamGiaSanPham) }} đ</span>
                    </div>
                    @endif

                    {{-- 3. Tạm tính (Đã gạch dưới phân cách) --}}
                    <div class="d-flex justify-content-between mb-3 pb-2 border-bottom small">
                        <span class="text-dark fw-bold">Tạm tính</span>
                        <span class="fw-bold">{{ number_format($tongTienHang) }} đ</span>
                    </div>

                    {{-- 4. Phí vận chuyển --}}
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        @if(!$needsShipping)
                        <span class="text-primary fw-bold">Gửi qua Email (0đ)</span>
                        @elseif($tienShip == 0)
                        <span class="text-success fw-bold">Miễn phí</span>
                        @else
                        <span class="fw-bold">{{ number_format($tienShip) }} ₫</span>
                        @endif
                    </div>

                    {{-- Logic Marketing: Khuyến khích Freeship --}}
                    @if($needsShipping && $tienShip > 0)
                    <div class="alert alert-info small mt-2 py-2">
                        <i class="fas fa-truck-loading me-1"></i> Mua thêm <b>{{ number_format($mocFreeship - $tongTienHang) }}đ</b> để được Freeship!
                    </div>
                    @endif

                    {{-- 5. Giảm giá từ Voucher --}}
                    @if(session()->has('coupon'))
                    <div class="d-flex justify-content-between mt-2 mb-2 small text-success">
                        <span><i class="fas fa-ticket-alt me-1"></i> Voucher ({{ session('coupon')['code'] }})</span>
                        <span class="fw-bold">-{{ number_format($discount) }} đ</span>
                    </div>
                    @endif

                    <hr class="my-3">

                    {{-- 6. Tổng cộng cuối cùng --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-6 fw-bold text-dark">THÀNH TIỀN</span>
                        <span class="fs-4 fw-bold text-danger">{{ number_format($tongThanhToan) }} đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection