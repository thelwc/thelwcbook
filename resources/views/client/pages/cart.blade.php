@extends('client.layouts.master')

@section('title', 'Giỏ hàng - Thelwc Books')

@section('styles')
    <style>
        /* --- CSS SỐ LƯỢNG --- */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        .quantity-grp {
            width: 110px; position: relative; display: flex;
            border: 1px solid #dee2e6; background-color: #fff; border-radius: 6px;
            overflow: hidden;
        }
        .quantity-grp .form-control {
            text-align: center; border: none; font-weight: bold;
            width: 40px; height: 32px; padding: 0; box-shadow: none; font-size: 0.9rem;
        }
        .quantity-grp .btn-qty {
            border: none; background: #fff; color: #212529;
            width: 35px; height: 32px; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s; cursor: pointer;
        }
        .quantity-grp .btn-qty:hover { background: #212529; color: #fff; }

        /* --- CSS Card Giỏ hàng --- */
        .card-custom { background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .cart-item-img { width: 75px; height: 110px; object-fit: cover; border-radius: 6px; border: 1px solid #eee;}
        .btn-dark-brand { background-color: #212529; border: 1px solid #212529; color: #ffffff; font-weight: 700; transition: all 0.3s; }
        .btn-dark-brand:hover { background-color: #c5a992; border-color: #c5a992; color: #ffffff; }

        /* --- Mobile Cart Item --- */
        .mobile-cart-item {
            border: 1px solid #f0f0f0; border-radius: 12px; padding: 15px; margin-bottom: 15px;
            background: #fff; position: relative;
        }

        /* --- CSS VOUCHER TICKET --- */
        .coupon-ticket {
            border: 1px dashed #c5a992; background-color: #fffaf5; border-radius: 8px; padding: 15px;
            margin-bottom: 15px; position: relative; transition: 0.2s; display: flex; justify-content: space-between; align-items: center;
        }
        .coupon-ticket:hover { background-color: #fff; border-color: #212529; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .coupon-info { display: flex; align-items: center; }
        .coupon-icon { 
            width: 50px; height: 50px; background-color: #ffeebc; color: #d68f00;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 1.2rem; margin-right: 15px;
        }
        .coupon-code-text { font-weight: 800; color: #dc3545; font-size: 1rem; margin-bottom: 2px; }
        .coupon-desc { font-size: 0.8rem; color: #6c757d; margin-bottom: 0; }
    </style>
@endsection

@section('content')

    <div class="container py-4 py-md-5">
        {{-- Tiêu đề & Nút Quay lại --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <h3 class="fw-bold mb-0 text-dark"><i class="fas fa-shopping-cart me-2 text-primary"></i>Giỏ hàng của bạn</h3>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill fw-bold px-4 shadow-sm align-self-start align-self-sm-auto">
                <i class="fas fa-arrow-left me-1"></i> Tiếp tục mua sắm
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('cart') && count(session('cart')) > 0)
            <div class="row g-4">
                {{-- CỘT TRÁI: DANH SÁCH SẢN PHẨM --}}
                <div class="col-lg-8">
                    
                    {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
                    <div class="card-custom p-4 d-none d-md-block">
                        <div class="table-responsive overflow-hidden">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="border-bottom border-2">
                                    <tr class="text-muted small text-uppercase fw-bold">
                                        <th style="width:45%" class="ps-3 pb-3">Sản phẩm</th>
                                        <th style="width:15%" class="pb-3">Đơn giá</th>
                                        <th style="width:20%" class="text-center pb-3">Số lượng</th>
                                        <th style="width:15%" class="text-end pe-3 pb-3">Thành tiền</th>
                                        <th style="width:5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(session('cart') as $id => $details)
                                        <tr class="border-bottom">
                                            {{-- Sản phẩm --}}
                                            <td class="ps-3 py-3">
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ route('book.detail', $id) }}" class="flex-shrink-0">
                                                        <img src="{{ asset(str_contains($details['image'], 'uploads') ? $details['image'] : 'uploads/' . $details['image']) }}" class="cart-item-img me-3 shadow-sm">
                                                    </a>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        @if(isset($details['type']) && $details['type'] == 'ebook')
                                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary small mb-1" style="font-size: 0.65rem;">
                                                                <i class="fas fa-tablet-alt me-1"></i> EBOOK
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary small mb-1" style="font-size: 0.65rem;">
                                                                <i class="fas fa-book me-1"></i> SÁCH GIẤY
                                                            </span>
                                                        @endif
                                                        <a href="{{ route('book.detail', $id) }}" class="fw-bold d-block text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                                            {{ $details['name'] }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Đơn giá --}}
                                            <td class="align-middle">
                                                @php
                                                    $bookInDb = \App\Models\Book::find($details['id']);
                                                    $originalPrice = (isset($details['type']) && $details['type'] == 'ebook') 
                                                                        ? ($bookInDb->ebook_price ?? $details['price']) 
                                                                        : ($bookInDb->price ?? $details['price']);
                                                    $isDiscounted = $originalPrice > $details['price'];
                                                    $discountPercent = $isDiscounted ? round((($originalPrice - $details['price']) / $originalPrice) * 100) : 0;
                                                @endphp

                                                @if($isDiscounted)
                                                    <div class="fw-bold text-danger">{{ number_format($details['price']) }} đ</div>
                                                    <div class="text-muted text-decoration-line-through small">{{ number_format($originalPrice) }} đ</div>
                                                @else
                                                    <div class="fw-bold text-dark">{{ number_format($details['price']) }} đ</div>
                                                @endif
                                            </td>
                                            
                                            {{-- Số lượng --}}
                                            <td>
                                                <form action="{{ route('update.cart') }}" method="POST" class="qty-form d-flex justify-content-center">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    <div class="quantity-grp shadow-sm">
                                                        <button type="button" class="btn-qty btn-minus"><i class="fas fa-minus small"></i></button>
                                                        <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control input-qty" min="1" max="{{ $details['max_quantity'] ?? 99 }}">                                                        
                                                        <button type="button" class="btn-qty btn-plus"><i class="fas fa-plus small"></i></button>
                                                    </div>
                                                </form>
                                            </td>

                                            {{-- Thành tiền --}}
                                            <td class="text-end fw-bold text-danger pe-3 fs-6">{{ number_format($details['price'] * $details['quantity']) }} đ</td>
                                            
                                            {{-- Nút xóa --}}
                                            <td class="text-end">
                                                <form action="{{ route('remove.from.cart') }}" method="POST" onsubmit="return confirm('Xóa sản phẩm này khỏi giỏ hàng?');">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    <button class="btn btn-light text-danger rounded-circle p-2 shadow-sm"><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 🔥 2. GIAO DIỆN MOBILE (CARD DỌC THAY THẾ TABLE) 🔥 --}}
                    <div class="d-md-none">
                        @foreach(session('cart') as $id => $details)
                            <div class="mobile-cart-item shadow-sm">
                                
                                {{-- Nút xóa (Góc phải trên cùng) --}}
                                <form action="{{ route('remove.from.cart') }}" method="POST" class="position-absolute top-0 end-0 m-2" onsubmit="return confirm('Xóa sản phẩm này?');">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button class="btn btn-sm text-danger p-1"><i class="fas fa-times fs-5"></i></button>
                                </form>

                                <div class="d-flex mb-3">
                                    {{-- Ảnh sách --}}
                                    <a href="{{ route('book.detail', $id) }}" class="flex-shrink-0 me-3">
                                        <img src="{{ asset(str_contains($details['image'], 'uploads') ? $details['image'] : 'uploads/' . $details['image']) }}" class="cart-item-img shadow-sm border">
                                    </a>
                                    
                                    {{-- Thông tin --}}
                                    <div class="flex-grow-1 overflow-hidden pe-4">
                                        @if(isset($details['type']) && $details['type'] == 'ebook')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary small mb-1" style="font-size: 0.6rem;"><i class="fas fa-tablet-alt me-1"></i> EBOOK</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary small mb-1" style="font-size: 0.6rem;"><i class="fas fa-book me-1"></i> SÁCH GIẤY</span>
                                        @endif
                                        
                                        <a href="{{ route('book.detail', $id) }}" class="fw-bold d-block text-dark text-decoration-none mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.3; font-size: 0.95rem;">
                                            {{ $details['name'] }}
                                        </a>

                                        @php
                                            $bookInDb = \App\Models\Book::find($details['id']);
                                            $originalPrice = (isset($details['type']) && $details['type'] == 'ebook') ? ($bookInDb->ebook_price ?? $details['price']) : ($bookInDb->price ?? $details['price']);
                                            $isDiscounted = $originalPrice > $details['price'];
                                        @endphp
                                        
                                        <div class="d-flex align-items-end gap-2 mt-1">
                                            <span class="fw-bold text-danger">{{ number_format($details['price']) }}đ</span>
                                            @if($isDiscounted)
                                                <span class="text-muted text-decoration-line-through" style="font-size: 0.75rem;">{{ number_format($originalPrice) }}đ</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Khối điều chỉnh số lượng & Tổng tiền nằm ngang --}}
                                <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                                    <form action="{{ route('update.cart') }}" method="POST" class="qty-form m-0">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <div class="quantity-grp shadow-sm" style="width: 100px;">
                                            <button type="button" class="btn-qty btn-minus" style="height: 30px;"><i class="fas fa-minus small"></i></button>
                                            <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control input-qty" min="1" max="{{ $details['max_quantity'] ?? 99 }}" style="height: 30px; font-size: 0.85rem;">                                                        
                                            <button type="button" class="btn-qty btn-plus" style="height: 30px;"><i class="fas fa-plus small"></i></button>
                                        </div>
                                    </form>
                                    <div class="text-end">
                                        <span class="text-muted small d-block" style="font-size: 0.7rem;">Thành tiền:</span>
                                        <span class="fw-bold text-danger fs-6">{{ number_format($details['price'] * $details['quantity']) }} ₫</span>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- CỘT PHẢI: TỔNG TIỀN & THANH TOÁN --}}
                <div class="col-lg-4">
                    <div class="card-custom p-4 sticky-top" style="top: 100px; z-index: 1;">
                        <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-receipt text-primary me-2"></i>Cộng giỏ hàng</h5>
                        
                        @php
                            $tongGiaGoc = 0;
                            $coEbook = false; // 🔥 Thêm biến radar quét Ebook

                            if(session('cart')) {
                                foreach(session('cart') as $id => $details) {
                                    // Kiểm tra xem món hàng này có phải ebook không
                                    if(isset($details['type']) && $details['type'] == 'ebook') {
                                        $coEbook = true; 
                                    }

                                    $bookInDb = \App\Models\Book::find($details['id']);
                                    $originalPrice = (isset($details['type']) && $details['type'] == 'ebook') 
                                                        ? ($bookInDb->ebook_price ?? $details['price']) 
                                                        : ($bookInDb->price ?? $details['price']);
                                    $tongGiaGoc += $originalPrice * $details['quantity'];
                                }
                            }
                            $giamGiaSanPham = $tongGiaGoc - $tongTienHang; 
                            $giamGiaVoucher = session()->has('coupon') ? session('coupon')['discount'] : 0;
                            $finalTotal = $tongTienHang + $tienShip - $giamGiaVoucher;
                            if($finalTotal < 0) $finalTotal = 0;
                        @endphp

                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Tổng giá trị sản phẩm:</span>
                            <span>{{ number_format($tongGiaGoc) }} đ</span>
                        </div>

                        @if($giamGiaSanPham > 0)
                        <div class="d-flex justify-content-between mb-2 small text-success">
                            <span><i class="fas fa-tags me-1"></i> Giảm giá sản phẩm:</span>
                            <span class="fw-bold">-{{ number_format($giamGiaSanPham) }} đ</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-light fw-bold text-dark">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($tongTienHang) }} đ</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Phí vận chuyển:</span>
                            @if(!$needsShipping)
                                <span class="text-primary fw-bold">Gửi Email (0đ)</span>
                            @elseif($tienShip == 0)
                                <span class="text-success fw-bold">Miễn phí</span>
                            @else
                                <span class="fw-bold text-dark">{{ number_format($tienShip) }} đ</span>
                            @endif
                        </div>

                        @if($needsShipping && $tienShip > 0)
                            <div class="alert alert-info small mt-2 py-2 text-center rounded-3 border-0 shadow-sm">
                                <i class="fas fa-truck-loading me-1"></i> Mua thêm <b>{{ number_format($mocFreeship - $tongTienHang) }}đ</b> để Freeship!
                            </div>
                        @endif

                        @if(session()->has('coupon'))
                            <div class="d-flex justify-content-between mt-3 mb-1 small text-success">
                                <span><i class="fas fa-ticket-alt me-1"></i> Voucher (<b>{{ session('coupon')['code'] }}</b>):</span>
                                <span class="fw-bold">-{{ number_format($giamGiaVoucher) }} đ</span>
                            </div>
                            <div class="text-end mb-3">
                                <a href="{{ route('cart.coupon.remove') }}" class="small text-danger text-decoration-none fw-bold" style="font-size: 0.75rem;"><i class="fas fa-times me-1"></i>Gỡ bỏ mã</a>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon.apply') }}" method="POST" class="mt-3 mb-3" id="coupon-form">
                                @csrf
                                <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                    <input type="text" name="code" id="coupon-input" class="form-control border-0 bg-light px-3" placeholder="Nhập mã Voucher" required>
                                    <button class="btn btn-dark fw-bold px-3" type="submit">Áp dụng</button>
                                </div>
                                <div class="mt-2 text-end">
                                    <a href="#" class="small text-primary text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#voucherModal" style="font-size: 0.8rem;">
                                        <i class="fas fa-tags me-1"></i> Chọn Voucher có sẵn
                                    </a>
                                </div>
                            </form>
                        @endif

                        <div class="d-flex justify-content-between border-top pt-3 mb-4 mt-2 align-items-center">
                            <span class="fw-bold text-dark text-uppercase">Tổng cộng:</span>
                            <span class="fs-3 fw-bold text-danger">{{ number_format($finalTotal) }} đ</span>
                        </div>

                        {{-- 🔥 RÀNG BUỘC EBOOK Ở ĐÂY 🔥 --}}
                        @if($coEbook && !Auth::check())
                            <div class="alert alert-warning small text-center rounded-3 border-0 shadow-sm mb-3">
                                <i class="fas fa-exclamation-triangle me-1 text-warning"></i> Giỏ hàng chứa <b>Ebook</b>. Bắt buộc phải <a href="{{ route('login') }}" class="fw-bold text-dark">Đăng nhập</a> để hệ thống lưu sách vào thư viện của cậu sau khi mua!
                            </div>
                            <a href="{{ route('login') }}" class="btn btn-danger w-100 py-3 rounded-pill fw-bold text-uppercase mb-2 shadow-sm d-flex align-items-center justify-content-center">
                                <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập để thanh toán
                            </a>
                        @else
                            {{-- Bình thường: Đã đăng nhập HOẶC Khách vãng lai chỉ mua sách giấy --}}
                            <a href="{{ route('checkout') }}" class="btn btn-dark-brand w-100 py-3 rounded-pill fw-bold text-uppercase mb-2 shadow-sm d-flex align-items-center justify-content-center">
                                Thanh toán ngay <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        @endif
                        
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png" width="150" class="opacity-50 mb-3">
                <h4 class="fw-bold text-dark">Giỏ hàng trống!</h4>
                <p class="text-muted mb-4 small">Hãy lấp đầy giỏ hàng bằng những cuốn sách tuyệt vời.</p>
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">Khám phá ngay</a>
            </div>
        @endif
    </div>

    {{-- MODAL VOUCHER (Giữ nguyên code cũ của cậu, vì đã hoạt động tốt) --}}
    <div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-ticket-alt me-2 text-warning"></i>Kho Voucher</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    @if(isset($vouchers) && $vouchers->count() > 0)
                        @foreach($vouchers as $voucher)
                            <div class="coupon-ticket">
                                <div class="coupon-info">
                                    <div class="coupon-icon">%</div>
                                    <div>
                                        <div class="coupon-code-text">{{ $voucher->code }}</div>
                                        <div class="small fw-bold text-dark mb-1">
                                            Giảm {{ $voucher->type == 'percent' ? $voucher->value . '%' : number_format($voucher->value) . 'đ' }}
                                        </div>
                                        <div class="coupon-desc">
                                            Đơn tối thiểu: {{ number_format($voucher->min_order_amount ?? 0) }}đ <br>
                                            <span class="text-danger fw-bold" style="font-size: 0.75rem"><i class="far fa-clock me-1"></i>HSD: {{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-dark fw-bold rounded-pill px-3 shadow-sm" onclick="applyVoucher('{{ $voucher->code }}')">
                                    Dùng ngay
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted fw-bold">Hiện chưa có mã giảm giá nào khả dụng.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- ĐOẠN SCRIPT XỬ LÝ SỐ LƯỢNG TỚ GIỮ NGUYÊN HOÀN TOÀN CỦA CẬU VÌ NÓ ĐANG CHẠY RẤT NGON RỒI NHÉ --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        function updateCartQuantity(inputElement) {
            const form = inputElement.closest('.qty-form');
            const id = form.querySelector('input[name="id"]').value;
            const quantity = inputElement.value;

            fetch("{{ route('update.cart') }}", {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest" 
                },
                body: JSON.stringify({ id: id, quantity: quantity })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(result => {
                if (result.status === 422) {
                    alert(result.body.message); 
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            })
            .catch(error => { console.error('Lỗi:', error); alert('Không thể cập nhật, thử lại!'); });
        }

        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.closest('.quantity-grp').querySelector('.input-qty');
                let value = parseInt(input.value);
                let min = parseInt(input.getAttribute('min')) || 1;
                if (value > min) { input.value = value - 1; updateCartQuantity(input); }
            });
        });

        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.closest('.quantity-grp').querySelector('.input-qty');
                let value = parseInt(input.value);
                let max = parseInt(input.getAttribute('max')) || 99; 
                if (value < max) { input.value = value + 1; updateCartQuantity(input); } 
                else { alert('Kho chỉ còn tối đa ' + max + ' cuốn!'); }
            });
        });

        document.querySelectorAll('.input-qty').forEach(input => {
            input.addEventListener('change', function() {
                let max = parseInt(this.getAttribute('max')) || 99;
                if (this.value >= 1 && this.value <= max) { updateCartQuantity(this); } 
                else { alert('Số lượng không hợp lệ!'); this.value = max; updateCartQuantity(this); }
            });
        });
    });

    function applyVoucher(code) {
        document.getElementById('coupon-input').value = code;
        var myModalEl = document.getElementById('voucherModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if (modal) { modal.hide(); }
    }
    </script>
@endsection