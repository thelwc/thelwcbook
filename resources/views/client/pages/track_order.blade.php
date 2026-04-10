@extends('client.layouts.master')

@section('title', 'Tra cứu đơn hàng - Thelwc Books')

@section('styles')
<style>
    .track-card { background: #fff; border-radius: 12px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 30px; }
    .status-timeline { display: flex; justify-content: space-between; position: relative; margin: 40px 0; }
    .status-timeline::before { content: ""; position: absolute; top: 15px; left: 0; width: 100%; height: 3px; background: #eee; z-index: 1; }
    .status-step { position: relative; z-index: 2; text-align: center; width: 25%; }
    .status-icon { width: 35px; height: 35px; background: #eee; color: #aaa; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; font-size: 14px; border: 3px solid #fff; }
    .status-text { font-size: 13px; font-weight: bold; color: #aaa; }
    
    /* Trạng thái Active */
    .status-step.active .status-icon { background: #28a745; color: #fff; }
    .status-step.active .status-text { color: #28a745; }
    .status-step.cancel .status-icon { background: #dc3545; color: #fff; }
    .status-step.cancel .status-text { color: #dc3545; }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4 text-center"><i class="fas fa-search me-2 text-primary"></i>Tra cứu trạng thái đơn hàng</h3>

            {{-- FORM NHẬP LIỆU --}}
            <div class="track-card mb-4">
                @if(session('error'))
                    <div class="alert alert-danger rounded-3"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}</div>
                @endif
                <form action="{{ route('orders.track.post') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="fw-bold small text-muted mb-1">Mã đơn hàng (#ID)</label>
                            <input type="text" name="order_id" class="form-control" placeholder="Ví dụ: 123" required value="{{ request('order_id') }}">
                        </div>
                        <div class="col-md-5">
                            <label class="fw-bold small text-muted mb-1">Email hoặc Số điện thoại lúc đặt</label>
                            <input type="text" name="contact_info" class="form-control" placeholder="Ví dụ: 0987654321" required value="{{ request('contact_info') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">Tra cứu</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- KẾT QUẢ HIỂN THỊ (Chỉ hiện khi tìm thấy $order) --}}
            @if(isset($order))
            <div class="track-card border-top border-4 border-dark">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Chi tiết đơn hàng #{{ $order->id }}</h5>
                    <span class="text-muted small"><i class="far fa-clock me-1"></i> Đặt lúc: {{ $order->created_at->format('H:i d/m/Y') }}</span>
                </div>

                {{-- THANH TIẾN TRÌNH TRẠNG THÁI --}}
                @php
                    $statusNum = 0;
                    if(in_array($order->status, ['pending', '0'])) $statusNum = 1;
                    if(in_array($order->status, ['confirmed', '1'])) $statusNum = 2;
                    if(in_array($order->status, ['shipping'])) $statusNum = 3;
                    if(in_array($order->status, ['completed', '2'])) $statusNum = 4;
                    $isCancelled = in_array($order->status, ['cancelled', '3', 'bom_hang', '4']);
                @endphp

                <div class="status-timeline">
                    <div class="status-step {{ $statusNum >= 1 || $isCancelled ? 'active' : '' }}">
                        <div class="status-icon"><i class="fas fa-file-invoice"></i></div>
                        <div class="status-text">Chờ xác nhận</div>
                    </div>
                    @if($isCancelled)
                        <div class="status-step cancel">
                            <div class="status-icon"><i class="fas fa-times"></i></div>
                            <div class="status-text">Đã hủy / Thất bại</div>
                        </div>
                    @else
                        <div class="status-step {{ $statusNum >= 2 ? 'active' : '' }}">
                            <div class="status-icon"><i class="fas fa-box-open"></i></div>
                            <div class="status-text">Đã duyệt</div>
                        </div>
                        <div class="status-step {{ $statusNum >= 3 ? 'active' : '' }}">
                            <div class="status-icon"><i class="fas fa-truck"></i></div>
                            <div class="status-text">Đang giao</div>
                        </div>
                        <div class="status-step {{ $statusNum == 4 ? 'active' : '' }}">
                            <div class="status-icon"><i class="fas fa-check"></i></div>
                            <div class="status-text">Thành công</div>
                        </div>
                    @endif
                </div>

                {{-- DANH SÁCH SÁCH --}}
                <div class="bg-light p-3 rounded-3 mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Sản phẩm đã mua</h6>
                    @foreach($order->details as $detail)
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset($detail->book->image ?? 'images/default-book.png') }}" width="50" class="rounded shadow-sm me-3 border">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ $detail->book->title ?? 'Sách đã bị xóa' }}</p>
                            <small class="text-muted">SL: x{{ $detail->quantity }} | 
                                {{ $detail->type == 'ebook' ? '(Bản Ebook)' : '(Sách giấy)' }}
                            </small>
                        </div>
                        <div class="fw-bold text-danger">{{ number_format($detail->price * $detail->quantity) }}đ</div>
                    </div>
                    @endforeach
                </div>

                {{-- TỔNG TIỀN INFO --}}
                <div class="row text-end small">
                    <div class="col-8 text-muted">Tạm tính:</div><div class="col-4 fw-bold">{{ number_format($order->total_price - $order->shipping_fee + $order->discount) }}đ</div>
                    <div class="col-8 text-muted">Phí ship:</div><div class="col-4 fw-bold">{{ number_format($order->shipping_fee) }}đ</div>
                    @if($order->discount > 0)
                        <div class="col-8 text-muted">Voucher giảm:</div><div class="col-4 fw-bold text-success">-{{ number_format($order->discount) }}đ</div>
                    @endif
                    <div class="col-8 fw-bold fs-6 mt-2">Tổng cộng:</div><div class="col-4 fw-bold fs-5 text-danger mt-2">{{ number_format($order->total_price) }}đ</div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection