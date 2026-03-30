@extends('client.layouts.master')

@section('title', 'Lịch sử đơn hàng - Thelwc Books')

@section('styles')
    <style>
        body { background-color: #f8f9fa; font-family: 'Nunito', sans-serif; }
        
        /* Card & Table */
        .card-history {
            border: none; border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            background: #fff; overflow: hidden;
        }
        .table-history thead th {
            background-color: #ffffff; color: #6c757d;
            font-weight: 700; font-size: 0.8rem; text-transform: uppercase;
            padding: 16px 20px; border-bottom: 2px solid #f0f0f0;
            letter-spacing: 0.5px;
        }
        .table-history tbody td {
            padding: 20px; vertical-align: middle;
            border-bottom: 1px solid #f8f9fa;
        }
        .table-history tr:last-child td { border-bottom: none; }
        
        /* Badges Status */
        .badge-soft { padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.3px; }
        .badge-soft-warning { background-color: #fff8e1; color: #b7791f; border: 1px solid #fef0cd; }
        .badge-soft-info { background-color: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; }
        .badge-soft-primary { background-color: #e6fffa; color: #2c7a7b; border: 1px solid #b2f5ea; }
        .badge-soft-success { background-color: #f0fff4; color: #276749; border: 1px solid #c6f6d5; }
        .badge-soft-danger { background-color: #fff5f5; color: #c53030; border: 1px solid #fed7d7; }

        /* Card Mobile Riêng Biệt */
        .mobile-order-card {
            background: #fff; border-radius: 12px; padding: 16px; margin-bottom: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #f0f0f0;
        }
        .mobile-order-card img { width: 50px; height: 75px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
        
        .hover-lift { transition: transform 0.2s, background-color 0.2s; }
        .hover-lift:hover { transform: translateX(5px); background-color: #f8f9fa; }
    </style>
@endsection

@section('content')
<div class="container py-4 py-md-5" style="min-height: 80vh;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Header (Đã tối ưu Mobile flex-column) --}}
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold m-0 text-dark mb-1">
                        <i class="fas fa-history text-primary me-2"></i> Lịch sử đơn hàng
                    </h3>
                    <p class="text-muted small m-0">Quản lý và theo dõi quá trình vận chuyển</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-dark rounded-pill fw-bold px-4 shadow-sm align-self-start align-self-sm-auto">
                    <i class="fas fa-shopping-basket me-2"></i> Mua sắm tiếp
                </a>
            </div>

            {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
            <div class="card card-history d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-history mb-0 table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 15%">Mã đơn / Ngày</th>
                                <th style="width: 35%">Sản phẩm</th>
                                <th style="width: 20%">Thanh toán</th>
                                <th style="width: 15%" class="text-center">Trạng thái</th>
                                <th style="width: 15%" class="text-end">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                {{-- 1. MÃ ĐƠN & NGÀY --}}
                                <td>
                                    <div class="fw-bold text-primary fs-6">#{{ $order->id }}</div>
                                    <div class="small text-muted mt-1"><i class="far fa-clock me-1"></i> {{ $order->created_at->format('d/m/Y') }}</div>
                                </td>

                                {{-- 2. TÓM TẮT SẢN PHẨM --}}
                                <td>
                                    @php
                                        $firstDetail = $order->details->first();
                                        $remainCount = $order->details->count() - 1;
                                    @endphp

                                    @if($firstDetail && $firstDetail->book)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset(str_contains($firstDetail->book->image, 'uploads') ? $firstDetail->book->image : 'uploads/'.$firstDetail->book->image) }}" 
                                                 alt="img" class="rounded border me-3 shadow-sm" style="width: 45px; height: 65px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                                    {{ $firstDetail->book->title }}
                                                </div>
                                                @if($remainCount > 0)
                                                    <small class="text-muted fst-italic">... và {{ $remainCount }} sản phẩm khác</small>
                                                @else
                                                    <small class="text-muted">x{{ $firstDetail->quantity }} cuốn</small>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Không có thông tin sản phẩm</span>
                                    @endif
                                </td>

                                {{-- 3. TỔNG TIỀN & PHƯƠNG THỨC --}}
                                <td>
                                    <div class="fw-bold text-danger fs-6">{{ number_format($order->total_price) }} ₫</div>
                                    <div class="small text-muted mt-1 fw-bold">
                                        @if(strtolower($order->payment_method) == 'cod')
                                            <span class="text-success"><i class="fas fa-money-bill-wave me-1"></i> COD</span>
                                        @else
                                            <span class="text-primary"><i class="fas fa-credit-card me-1"></i> Chuyển khoản</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 4. TRẠNG THÁI --}}
                                <td class="text-center">
                                    @if($order->status == 'pending' || $order->status == 0)
                                        <span class="badge-soft badge-soft-warning"><i class="fas fa-hourglass-half me-1"></i> Chờ xử lý</span>
                                    @elseif($order->status == 'confirmed' || $order->status == 1)
                                        <span class="badge-soft badge-soft-info"><i class="fas fa-user-check me-1"></i> Đã xác nhận</span>
                                    @elseif($order->status == 'shipping')
                                        <span class="badge-soft badge-soft-primary"><i class="fas fa-truck me-1"></i> Đang giao</span>
                                    @elseif($order->status == 'completed' || $order->status == 3)
                                        <span class="badge-soft badge-soft-success"><i class="fas fa-check-circle me-1"></i> Hoàn thành</span>
                                    @else
                                        <span class="badge-soft badge-soft-danger"><i class="fas fa-times-circle me-1"></i> Đã hủy</span>
                                    @endif
                                </td>

                                {{-- 5. NÚT CHI TIẾT --}}
                                <td class="text-end">
                                    <a href="{{ route('client.account.history.detail', $order->id) }}" class="btn btn-light text-primary btn-sm rounded-circle shadow-sm hover-lift" title="Xem chi tiết" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted opacity-25 mb-3"><i class="fas fa-box-open fa-4x"></i></div>
                                    <h5 class="fw-bold text-dark">Chưa có đơn hàng nào</h5>
                                    <p class="small text-muted mb-4">Hãy dạo một vòng và chọn cuốn sách ưng ý nhé!</p>
                                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 fw-bold">Khám phá ngay</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 2. GIAO DIỆN MOBILE (CARD DỌC THAY THẾ TABLE) 🔥 --}}
            <div class="d-md-none">
                @forelse($orders as $order)
                    <div class="mobile-order-card">
                        
                        {{-- Header Card: ID & Trạng Thái --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <span class="fw-bold text-primary">#{{ $order->id }}</span>
                                <span class="text-muted small ms-2"><i class="far fa-clock me-1"></i>{{ $order->created_at->format('d/m') }}</span>
                            </div>
                            <div>
                                @if($order->status == 'pending' || $order->status == 0)
                                    <span class="badge-soft badge-soft-warning px-2 py-1" style="font-size: 0.65rem;">Chờ xử lý</span>
                                @elseif($order->status == 'confirmed' || $order->status == 1)
                                    <span class="badge-soft badge-soft-info px-2 py-1" style="font-size: 0.65rem;">Đã xác nhận</span>
                                @elseif($order->status == 'shipping')
                                    <span class="badge-soft badge-soft-primary px-2 py-1" style="font-size: 0.65rem;">Đang giao</span>
                                @elseif($order->status == 'completed' || $order->status == 3)
                                    <span class="badge-soft badge-soft-success px-2 py-1" style="font-size: 0.65rem;">Hoàn thành</span>
                                @else
                                    <span class="badge-soft badge-soft-danger px-2 py-1" style="font-size: 0.65rem;">Đã hủy</span>
                                @endif
                            </div>
                        </div>

                        {{-- Body Card: Hình ảnh & Thông tin sách --}}
                        <a href="{{ route('client.account.history.detail', $order->id) }}" class="text-decoration-none">
                            @php
                                $firstDetail = $order->details->first();
                                $remainCount = $order->details->count() - 1;
                            @endphp

                            @if($firstDetail && $firstDetail->book)
                                <div class="d-flex mb-3">
                                    <img src="{{ asset(str_contains($firstDetail->book->image, 'uploads') ? $firstDetail->book->image : 'uploads/'.$firstDetail->book->image) }}" alt="img" class="me-3">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="mb-1 small fw-bold text-dark" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $firstDetail->book->title }}
                                        </div>
                                        @if($remainCount > 0)
                                            <div class="small text-muted fst-italic">... và {{ $remainCount }} sản phẩm khác</div>
                                        @else
                                            <div class="small text-muted">x{{ $firstDetail->quantity }} cuốn</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </a>

                        {{-- Footer Card: Tổng tiền & Nút bấm --}}
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div>
                                <span class="text-muted small d-block">Thành tiền</span>
                                <span class="fw-bold text-danger fs-6">{{ number_format($order->total_price) }} ₫</span>
                            </div>
                            <a href="{{ route('client.account.history.detail', $order->id) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3">
                                Chi tiết
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                        <div class="text-muted opacity-25 mb-3"><i class="fas fa-box-open fa-3x"></i></div>
                        <h6 class="fw-bold text-dark">Chưa có đơn hàng nào</h6>
                        <a href="{{ route('home') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2 fw-bold">Khám phá ngay</a>
                    </div>
                @endforelse
            </div>

            {{-- Phân trang --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $orders->links() }}
            </div>

        </div>
    </div>
</div>
@endsection