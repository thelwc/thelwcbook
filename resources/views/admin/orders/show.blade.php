@extends('admin.layouts.layout')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold m-0 text-dark">Chi tiết đơn hàng #{{ $order->id }}</h4>
            <span class="text-muted small"><i class="far fa-clock me-1"></i> Ngày đặt: {{ $order->created_at->format('H:i - d/m/Y') }}</span>
        </div>
    </div>

    <a href="{{ route('admin.orders.print', $order->id) }}" class="btn btn-dark shadow-sm fw-bold d-flex align-items-center px-4 rounded-pill">
        <i class="fas fa-print me-2"></i> In Hóa Đơn
    </a>
</div>

<div class="row g-4">
    {{-- CỘT TRÁI: THÔNG TIN KHÁCH HÀNG & TRẠNG THÁI --}}
    <div class="col-lg-4">

        {{-- 1. THẺ THÔNG TIN KHÁCH HÀNG --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h6 class="fw-bold m-0 text-primary"><i class="fas fa-user-circle me-2"></i>Thông tin khách hàng</h6>
            </div>
            <div class="card-body">
                {{-- Thông tin cá nhân --}}
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 overflow-hidden position-relative border shadow-sm" style="width: 50px; height: 50px;">
                        @php
                        $avatarSrc = null;
                        if ($order->user && $order->user->avatar) {
                        $userAvatar = $order->user->avatar;
                        if (str_contains($userAvatar, 'http')) $avatarSrc = $userAvatar;
                        elseif (file_exists(public_path('images/' . $userAvatar))) $avatarSrc = asset('images/' . $userAvatar);
                        elseif (file_exists(public_path('storage/' . $userAvatar))) $avatarSrc = asset('storage/' . $userAvatar);
                        else $avatarSrc = asset($userAvatar);
                        }
                        @endphp

                        @if($avatarSrc)
                        <img src="{{ $avatarSrc }}" alt="Avatar" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="fas fa-user text-secondary fs-4 position-absolute" style="display: none;"></i>
                        @else
                        <i class="fas fa-user text-secondary fs-4"></i>
                        @endif
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">{{ $order->name }}</div>
                        <div class="small mt-1">
                            @if($order->user_id)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Thành viên</span>
                            @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Khách vãng lai</span>
                            @endif
                        </div>
                    </div>
                </div>

                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 d-flex justify-content-between border-light align-items-center">
                        <span class="text-muted"><i class="fas fa-phone-alt me-2 opacity-50"></i> Số điện thoại:</span>
                        <span class="fw-bold text-dark"><a href="tel:{{ $order->phone }}" class="text-decoration-none text-dark">{{ $order->phone }}</a></span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between border-light align-items-center">
                        <span class="text-muted"><i class="fas fa-envelope me-2 opacity-50"></i> Email:</span>
                        <span class="fw-bold text-dark">{{ $order->email ?? $order->user->email ?? '---' }}</span>
                    </li>
                    <li class="list-group-item px-0 border-light">
                        <span class="text-muted d-block mb-2"><i class="fas fa-map-marker-alt me-2 opacity-50"></i> Địa chỉ giao hàng:</span>
                        <div class="p-2 bg-light rounded-3 border text-dark lh-base">
                            {{ $order->address }}
                        </div>
                    </li>
                </ul>

                <hr class="border-light my-3">

                {{-- Thông tin thanh toán & Ghi chú --}}
                <h6 class="fw-bold text-primary mb-3 small text-uppercase"><i class="fas fa-wallet me-2"></i>Thanh toán & Ghi chú</h6>

                <div class="mb-4">
                    <div class="text-muted small mb-2 fw-bold text-uppercase">Phương thức thanh toán:</div>
                    @php
                    $method = strtolower($order->payment_method);
                    $paymentConfig = [
                    'cod' => ['label' => 'Thanh toán khi nhận hàng (COD)', 'icon' => 'fa-hand-holding-usd', 'class' => 'bg-warning text-warning border-warning', 'desc' => 'Thu tiền mặt khi giao hàng'],
                    'vnpay' => ['label' => 'Thanh toán qua VNPAY', 'icon' => 'fa-qrcode', 'class' => 'bg-primary text-primary border-primary', 'desc' => 'Đã thanh toán điện tử'],
                    'momo' => ['label' => 'Ví điện tử MoMo', 'icon' => 'fa-wallet', 'class' => 'bg-danger text-danger border-danger', 'desc' => 'Đã thanh toán qua MoMo'],
                    'bank_transfer' => ['label' => 'Chuyển khoản ngân hàng', 'icon' => 'fa-university', 'class' => 'bg-info text-info border-info', 'desc' => 'Đã chuyển khoản']
                    ];
                    $current = $paymentConfig[$method] ?? ['label' => $order->payment_method, 'icon' => 'fa-credit-card', 'class' => 'bg-secondary text-secondary border-secondary', 'desc' => 'Thanh toán điện tử'];
                    @endphp

                    <div class="d-flex align-items-center p-3 rounded-4 border {{ $current['class'] }} bg-opacity-10" style="background-color: rgba(var(--bs-{{ explode('-', $current['class'])[1] }}-rgb), 0.05) !important;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 {{ $current['class'] }} bg-opacity-25" style="width: 45px; height: 45px; flex-shrink: 0;">
                            <i class="fas {{ $current['icon'] }} fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 {{ explode(' ', $current['class'])[1] }} text-capitalize" style="font-size: 0.95rem;">{{ $current['label'] }}</h6>
                            <small class="text-muted" style="font-size: 0.8rem;">{{ $current['desc'] }}</small>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="text-muted small mb-2 fw-bold text-uppercase">Ghi chú của khách:</div>
                    <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-4 text-dark small fst-italic lh-base">
                        "{{ $order->note ?? 'Không có ghi chú' }}"
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. THẺ CẬP NHẬT TRẠNG THÁI --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h6 class="fw-bold m-0 text-primary"><i class="fas fa-cog me-2"></i>Xử lý đơn hàng</h6>
            </div>
            <div class="card-body p-4">
                {{-- Trạng thái hiện tại --}}
                <div class="mb-4 text-center border-bottom pb-4">
                    <div class="text-muted small mb-2 fw-bold text-uppercase">Trạng thái hiện tại:</div>
                    @if($order->status == 'pending' || $order->status == 0)
                    <h5 class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 shadow-sm border">⏳ Đang chờ xử lý</h5>
                    @elseif($order->status == 'confirmed' || $order->status == 1)
                    <h5 class="badge bg-info text-white px-4 py-2 rounded-pill fs-6 shadow-sm">👮 Đã xác nhận</h5>
                    @elseif($order->status == 'shipping')
                    <h5 class="badge bg-primary text-white px-4 py-2 rounded-pill fs-6 shadow-sm">🚚 Đang giao hàng</h5>
                    @elseif($order->status == 'completed' || $order->status == 2 || $order->status == 3)
                    <h5 class="badge bg-success text-white px-4 py-2 rounded-pill fs-6 shadow-sm">✅ Đã hoàn thành</h5>
                    @elseif($order->status == 'cancelled' || $order->status == 4)
                    <h5 class="badge bg-danger text-white px-4 py-2 rounded-pill fs-6 shadow-sm">❌ Đã hủy</h5>
                    @elseif($order->status == 'bom_hang')
                    <h5 class="badge bg-dark text-white px-4 py-2 rounded-pill fs-6 shadow-sm">💣 Khách Bom Hàng</h5>
                    @endif
                    <div class="text-muted small mt-2"><i class="fas fa-history me-1 opacity-50"></i> Cập nhật lần cuối: {{ $order->updated_at->diffForHumans() }}</div>
                </div>

                {{-- KIỂM TRA LOẠI ĐƠN HÀNG (SÁCH GIẤY / EBOOK / HỖN HỢP) --}}
                @php
                $hasPhysicalBook = false;
                $hasEbook = false;

                foreach($order->details as $d) {
                $isEbookItem = false;
                if(isset($d->type) && $d->type == 'ebook') $isEbookItem = true;
                elseif($d->book && $d->price == $d->book->ebook_price) $isEbookItem = true;

                if ($isEbookItem) $hasEbook = true;
                else $hasPhysicalBook = true;
                }
                @endphp

                {{-- FORM XỬ LÝ --}}
                @if(in_array(Auth::user()->role, [2, 3]))
                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                    @csrf

                    {{-- TRẠNG THÁI: CHỜ XỬ LÝ --}}
                    @if($order->status == 'pending' || $order->status == 0)

                    {{-- TRƯỜNG HỢP 1: TOÀN EBOOK --}}
                    @if(!$hasPhysicalBook && $hasEbook)
                    <button type="submit" name="status" value="completed" class="btn btn-success w-100 fw-bold mb-2 shadow-sm py-3 rounded-pill">
                        <i class="fas fa-bolt me-2"></i> Kích hoạt Ebook & Hoàn thành
                    </button>
                    <div class="text-center text-muted small fst-italic mt-2"><i class="fas fa-info-circle me-1"></i>Khách hàng sẽ xem được sách ngay lập tức.</div>

                    {{-- TRƯỜNG HỢP 2: ĐƠN HỖN HỢP --}}
                    @elseif($hasPhysicalBook && $hasEbook)
                    <button type="submit" name="status" value="confirmed" class="btn btn-primary w-100 fw-bold mb-2 shadow-sm py-3 rounded-pill">
                        <i class="fas fa-layer-group me-2"></i> Kích hoạt Ebook & Soạn sách giấy
                    </button>
                    <div class="text-center text-muted small fst-italic mt-2">
                        <i class="fas fa-info-circle me-1"></i> Ebook sẽ được mở khóa ngay. Đơn hàng chuyển sang "Đã xác nhận" để kho soạn sách giấy.
                    </div>

                    {{-- TRƯỜNG HỢP 3: CHỈ CÓ SÁCH GIẤY --}}
                    @else
                    <button type="submit" name="status" value="confirmed" class="btn btn-primary w-100 fw-bold mb-2 shadow-sm py-3 rounded-pill">
                        <i class="fas fa-check-circle me-2"></i> Xác nhận & Soạn hàng
                    </button>
                    <div class="text-center text-muted small fst-italic mt-2"><i class="fas fa-info-circle me-1"></i>Hệ thống sẽ tự động trừ kho tổng.</div>
                    @endif

                    {{-- TRẠNG THÁI: ĐÃ XÁC NHẬN --}}
                    @elseif($order->status == 'confirmed' || $order->status == 1)
                    <div class="d-grid gap-3">
                        <button type="submit" name="status" value="shipping" class="btn btn-info text-white fw-bold shadow-sm py-3 rounded-pill">
                            <i class="fas fa-truck me-2"></i> Bắt đầu giao hàng
                        </button>
                        <button type="submit" name="status" value="pending" class="btn btn-outline-secondary rounded-pill py-2" onclick="return confirm('Bạn muốn hoàn tác về trạng thái Chờ xử lý?')">
                            <i class="fas fa-undo me-1"></i> Quay lại chờ xử lý
                        </button>
                    </div>

                    {{-- TRẠNG THÁI: ĐANG GIAO --}}
                    @elseif($order->status == 'shipping')
                    <div class="d-grid gap-3 mb-3">
                        <button type="submit" name="status" value="completed" class="btn btn-success fw-bold shadow-sm py-3 rounded-pill">
                            <i class="fas fa-check-double me-2"></i> Xác nhận giao thành công
                        </button>
                        <button type="submit" name="status" value="confirmed" class="btn btn-outline-warning text-dark rounded-pill py-2" onclick="return confirm('Đơn hàng chưa được giao? Bạn muốn quay lại trạng thái Đã xác nhận?')">
                            <i class="fas fa-undo me-1"></i> Giao chậm / Quay lại
                        </button>
                    </div>
                </form>

                {{-- NÚT BÁO BOM HÀNG --}}
                <form action="{{ route('admin.orders.bom_hang', $order->id) }}" method="POST" class="d-grid gap-2 mt-3 pt-3 border-top border-light">
                    @csrf
                    <button type="submit" class="btn btn-danger fw-bold shadow-sm py-3 rounded-pill" onclick="return confirm('XÁC NHẬN KHÁCH BOM HÀNG? \n\nHệ thống sẽ tự động:\n1. Hoàn lại số lượng sách vào kho\n2. Trừ đi số lượt đã bán\n3. Trả lại Voucher cho hệ thống (nếu có)\n\nLưu ý: Thao tác này không thể hoàn tác!');">
                        <i class="fas fa-bomb me-2"></i> Khách Bom Hàng / Từ chối nhận
                    </button>
                </form>

                <form style="display:none;">

                    {{-- TRẠNG THÁI: ĐÃ HỦY / BOM HÀNG --}}
                    @elseif($order->status == 'cancelled' || $order->status == 'bom_hang' || $order->status == 4)
                    <div class="alert alert-light border text-center mb-0 text-muted rounded-4 py-3">
                        <i class="fas fa-lock me-2"></i> Đơn hàng đã đóng
                    </div>
                    {{-- TRẠNG THÁI: HOÀN THÀNH --}}
                    @else
                    <div class="alert alert-success bg-opacity-10 border border-success text-center mb-0 text-success fw-bold rounded-4 py-3">
                        <i class="fas fa-check-circle me-2"></i> Đơn hàng đã hoàn tất
                    </div>
                    @endif
                </form>

                {{-- Nút Hủy Đơn --}}
                @if(!in_array($order->status, ['completed', 'cancelled', 'bom_hang', 2, 3, 4]))
                <div class="mt-4 pt-4 border-top border-light">
                    <form action="{{ route('orders.outOfStock', $order->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Hành động này sẽ hủy đơn hàng. Bạn có chắc chắn không?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2">
                            <i class="fas fa-times me-2"></i> Hủy đơn / Báo hết hàng
                        </button>
                    </form>
                </div>
                @endif

                @else
                <div class="alert alert-secondary text-center small m-0 border-0 bg-light text-muted rounded-4 p-4">
                    <i class="fas fa-eye fa-2x mb-3 text-secondary opacity-50"></i>
                    <br>Bạn đang ở chế độ <strong>Chỉ xem</strong>. <br> Không có quyền thay đổi trạng thái đơn hàng.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- CỘT PHẢI: DANH SÁCH SẢN PHẨM --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 h-100 bg-transparent bg-lg-white">
            <div class="card-header bg-white py-3 border-bottom border-light rounded-top-4">
                <h6 class="fw-bold m-0 text-dark"><i class="fas fa-box-open me-2 text-warning"></i>Chi tiết sản phẩm</h6>
            </div>
            <div class="card-body p-0">

                {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase text-secondary small fw-bold">
                            <tr>
                                <th class="ps-4 py-3" style="width: 45%;">Sản phẩm</th>
                                <th class="py-3 text-center">Đơn giá</th>
                                <th class="py-3 text-center">SL</th>
                                <th class="py-3 text-end pe-4">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->details as $detail)
                            <tr class="border-bottom border-light">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            @if($detail->book)
                                            <a href="{{ route('admin.books.show', $detail->book_id) }}" title="Xem chi tiết sách">
                                                @if($detail->book->image)
                                                <img src="{{ asset(str_contains($detail->book->image, 'uploads') ? $detail->book->image : 'uploads/' . $detail->book->image) }}"
                                                    class="rounded-3 border shadow-sm" style="width: 50px; height: 75px; object-fit: cover; transition: 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                                @else
                                                <div class="bg-light rounded-3 border d-flex align-items-center justify-content-center text-muted small shadow-sm" style="width: 50px; height: 75px;">No Img</div>
                                                @endif
                                            </a>
                                            @else
                                            <div class="bg-light rounded-3 border d-flex align-items-center justify-content-center text-muted small" style="width: 50px; height: 75px;">Đã xóa</div>
                                            @endif
                                        </div>

                                        <div class="overflow-hidden pe-2">
                                            @if($detail->book)
                                            <a href="{{ route('admin.books.show', $detail->book_id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block mb-1 text-wrap" style="line-height: 1.4; font-size: 0.95rem;">
                                                {{ $detail->book->title }}
                                            </a>
                                            @else
                                            <div class="fw-bold text-muted text-decoration-line-through mb-1">Sản phẩm đã bị xóa</div>
                                            @endif

                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <small class="text-muted opacity-75">ID: #{{ $detail->book_id }}</small>

                                                @php
                                                $isEbook = false;
                                                if(isset($detail->type) && $detail->type == 'ebook') $isEbook = true;
                                                elseif($detail->book && $detail->price == $detail->book->ebook_price) $isEbook = true;
                                                @endphp

                                                @if($isEbook)
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary" style="font-size: 0.65rem;"><i class="fas fa-tablet-alt me-1"></i> EBOOK</span>
                                                @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary" style="font-size: 0.65rem;"><i class="fas fa-book me-1"></i> SÁCH GIẤY</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center align-middle fw-bold text-dark">{{ number_format($detail->price) }} ₫</td>
                                <td class="text-center fw-bold align-middle text-secondary">x{{ $detail->quantity }}</td>
                                <td class="text-end pe-4 fw-bold align-middle text-primary fs-6">{{ number_format($detail->price * $detail->quantity) }} ₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 🔥 2. GIAO DIỆN MOBILE (CARD DỌC) 🔥 --}}
                <div class="d-lg-none p-2 p-sm-3">
                    @foreach($order->details as $detail)
                    <div class="card mb-3 shadow-sm border-0 rounded-4">
                        <div class="card-body p-3">
                            <div class="d-flex mb-3 pb-3 border-bottom border-light">
                                {{-- Ảnh --}}
                                <div class="flex-shrink-0 me-3">
                                    @if($detail->book)
                                    <img src="{{ asset(str_contains($detail->book->image ?? '', 'uploads') ? $detail->book->image : 'uploads/' . $detail->book->image) }}" class="rounded-3 border shadow-sm" style="width: 60px; height: 90px; object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded-3 border d-flex align-items-center justify-content-center text-muted small" style="width: 60px; height: 90px;">Xóa</div>
                                    @endif
                                </div>

                                {{-- Tên sách và Phân loại --}}
                                <div class="flex-grow-1 overflow-hidden">
                                    @if($detail->book)
                                    <h6 class="fw-bold text-dark text-wrap mb-2" style="line-height: 1.4;">{{ $detail->book->title }}</h6>
                                    @else
                                    <h6 class="fw-bold text-muted text-decoration-line-through mb-2">Sản phẩm đã bị xóa</h6>
                                    @endif

                                    @php
                                    $isEbook = false;
                                    if(isset($detail->type) && $detail->type == 'ebook') $isEbook = true;
                                    elseif($detail->book && $detail->price == $detail->book->ebook_price) $isEbook = true;
                                    @endphp
                                    @if($isEbook)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary small"><i class="fas fa-tablet-alt me-1"></i> EBOOK</span>
                                    @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary small"><i class="fas fa-book me-1"></i> SÁCH GIẤY</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Khối tính tiền --}}
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="small">
                                    <span class="text-dark fw-bold">{{ number_format($detail->price) }}đ</span>
                                    <span class="text-muted ms-1">x{{ $detail->quantity }}</span>
                                </div>
                                <div class="fw-bold text-primary fs-6">{{ number_format($detail->price * $detail->quantity) }} ₫</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- TỔNG KẾT TIỀN (Phần dưới cùng của Card - Chung cho cả Mobile và PC) --}}
                <div class="bg-light p-4 rounded-bottom-4 border-top">
                    <div class="row">
                        <div class="col-md-7 col-lg-6 offset-md-5 offset-lg-6">
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                <span class="text-muted fw-bold">Tổng tiền hàng:</span>
                                <span class="text-dark fw-bold">{{ number_format($order->total_price + $order->discount - $order->shipping_fee) }} ₫</span>
                            </div>

                            @if($order->discount > 0)
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-secondary border-opacity-10 text-success">
                                <span><i class="fas fa-ticket-alt me-1"></i> Voucher <span class="badge bg-success ms-1">{{ $order->coupon_code }}</span>:</span>
                                <span class="fw-bold">-{{ number_format($order->discount) }} ₫</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-secondary border-opacity-25">
                                <span class="text-muted fw-bold">Phí vận chuyển:</span>
                                <span>
                                    @if($order->shipping_fee > 0)
                                    <span class="text-dark fw-bold">{{ number_format($order->shipping_fee) }} ₫</span>
                                    @else
                                    <span class="badge bg-success rounded-pill px-3 py-1">Miễn phí</span>
                                    @endif
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-5 fw-bold text-dark text-uppercase">THÀNH TIỀN:</span>
                                <span class="fs-3 fw-bold text-danger">{{ number_format($order->total_price) }} ₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection