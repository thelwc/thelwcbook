@extends('client.layouts.master')
@section('title', 'Trạng thái đơn hàng')

@section('content')
<div class="container py-4 py-md-5 text-center">
    {{-- 🔥 ĐÃ SỬA: Giảm padding trên mobile (p-3 p-md-5) để không bị ép chữ 🔥 --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-5 mx-auto" style="max-width: 600px;">
        
        {{-- 🟢 TRƯỜNG HỢP 1: THANH TOÁN QR (CHỜ THANH TOÁN) --}}
        @if($order->payment_method == 'bank_transfer')
            <div class="mb-3 mb-md-4">
                {{-- Icon Đồng hồ chờ --}}
                <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
            </div>
            <h2 class="fw-bold text-warning fs-3 fs-md-2">Đơn hàng đang chờ thanh toán</h2>
            <p class="text-muted small md-normal">Đơn hàng #{{ $order->id }} đã được tạo. Vui lòng quét mã bên dưới.</p>

            {{-- 🔥 ĐÃ SỬA: Giảm padding (p-3 p-md-4) 🔥 --}}
            <div class="mt-3 mt-md-4 p-3 p-md-4 bg-light rounded-4 border border-warning border-opacity-50">
                <h5 class="fw-bold text-primary mb-3 fs-6 fs-md-5">
                    <i class="fas fa-qrcode me-2"></i>QUÉT MÃ ĐỂ THANH TOÁN
                </h5>
                
                @php
                    $bankId = 'VCB';              
                    $accountNo = '9964617664';    
                    $accountName = 'LE THE LUC';  
                    $content = "TT " . $order->id; // Nội dung ngắn gọn
                    $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$order->total_price}&addInfo={$content}&accountName={$accountName}";
                @endphp

                {{-- 🔥 ĐÃ SỬA: Thêm mx-auto d-block để ép mã QR nằm chính giữa 100% 🔥 --}}
                <img src="{{ $qrUrl }}" class="img-fluid mx-auto d-block rounded-3 shadow-sm mb-4 border" style="max-width: 250px; width: 100%;">
                
                {{-- 🔥 BẢNG THÔNG TIN (ĐÃ TỐI ƯU LẠI FLEXBOX CHỐNG CẮT CHỮ) 🔥 --}}
                <div class="text-start bg-white p-3 rounded-4 border border-secondary border-opacity-10 mb-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 gap-2">
                        <span class="text-muted small flex-shrink-0">Ngân hàng:</span>
                        <span class="fw-bold text-dark text-end text-break">Vietcombank</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 gap-2">
                        <span class="text-muted small flex-shrink-0">Chủ TK:</span>
                        <span class="fw-bold text-dark text-uppercase text-end text-break">{{ $accountName }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 gap-2">
                        <span class="text-muted small flex-shrink-0">Số TK:</span>
                        <span class="text-primary fs-5 fw-bold text-end">{{ $accountNo }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 gap-2">
                        <span class="text-muted small flex-shrink-0">Số tiền:</span>
                        <span class="text-danger fs-5 fw-bold text-end">{{ number_format($order->total_price) }} đ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1 gap-2">
                        <span class="text-muted small flex-shrink-0">Nội dung:</span>
                        <span class="fw-bold text-primary bg-light px-2 py-1 rounded text-end text-break">{{ $content }}</span>
                    </div>
                </div>

                {{-- 🔥 CẶP NÚT: HỦY & ĐÃ CHUYỂN 🔥 --}}
                <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3 justify-content-center mt-4 mx-auto" style="max-width: 400px;">
                    {{-- Nút Hủy --}}
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?');" class="w-100 m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-bold py-2 py-sm-2 d-flex align-items-center justify-content-center h-100">
                            <i class="fas fa-times me-2"></i> Hủy đơn
                        </button>
                    </form>

                    {{-- Nút Đã chuyển khoản --}}
                    <a href="{{ route('client.account.history.detail', $order->id) }}" class="btn btn-success w-100 rounded-pill fw-bold py-2 py-sm-2 shadow-sm d-flex align-items-center justify-content-center h-100">
                        <i class="fas fa-check me-2"></i> Đã chuyển khoản
                    </a>
                </div>
                
                <p class="text-muted small mt-4 fst-italic mb-0" style="font-size: 0.75rem;">
                    * Lưu ý: Bấm "Đã chuyển khoản" sau khi bạn thực hiện giao dịch thành công trên App ngân hàng.
                </p>
            </div>

        {{-- 🟢 TRƯỜNG HỢP 2: THANH TOÁN COD (THÀNH CÔNG LUÔN) --}}
        @else
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
            </div>
            <h2 class="fw-bold text-success fs-3 fs-md-2">Đặt hàng thành công!</h2>
            <div class="alert alert-light border rounded-3 d-inline-block px-3 px-md-4 py-2 mt-2">
                Mã đơn hàng: <strong class="text-dark">#{{ $order->id }}</strong>
            </div>

            <div class="mt-3 mt-md-4">
                <p class="text-muted mb-1 small md-normal">Vui lòng chuẩn bị tiền mặt khi nhận hàng:</p>
                
                {{-- Hiển thị Tổng tiền to rõ --}}
                <h2 class="text-danger fw-bold mb-3 fs-2">{{ number_format($order->total_price) }} đ</h2>

                {{-- Bảng kê chi tiết nhỏ (Để khách check lại) --}}
                <div class="bg-light p-3 rounded-4 d-inline-block text-start border border-secondary border-opacity-10 mx-auto" style="width: 100%; max-width: 320px;">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Tiền hàng:</span>
                        <span class="fw-bold text-dark">{{ number_format($order->total_price + $order->discount - $order->shipping_fee) }} đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="fw-bold">
                            @if($order->shipping_fee > 0)
                                + {{ number_format($order->shipping_fee) }} đ
                            @else
                                <span class="text-success">Miễn phí</span>
                            @endif
                        </span>
                    </div>
                    @if($order->discount > 0)
                    <div class="d-flex justify-content-between mb-2 small text-success">
                        <span><i class="fas fa-ticket-alt me-1"></i> Voucher:</span>
                        <span class="fw-bold">- {{ number_format($order->discount) }} đ</span>
                    </div>
                    @endif
                    <div class="border-top mt-2 pt-2 d-flex justify-content-between fw-bold">
                        <span class="text-dark">Tổng thu:</span>
                        <span class="text-danger fs-5">{{ number_format($order->total_price) }} đ</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('client.account.history.detail', $order->id) }}" class="btn btn-dark w-100 rounded-pill fw-bold mx-auto py-2" style="max-width: 320px;">
                        Xem chi tiết đơn hàng
                    </a>
                </div>
            </div>
        @endif
        
        <div class="mt-4">
            <a href="{{ route('home') }}" class="text-decoration-none text-secondary small fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Quay lại trang chủ
            </a>
        </div>
    </div>
</div>
@endsection