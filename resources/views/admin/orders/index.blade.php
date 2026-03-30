@extends('admin.layouts.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h3 class="fw-bold text-dark m-0">📦 Quản Lý Đơn Hàng</h3>
    
    <form action="{{ route('orders.index') }}" method="GET" class="d-flex shadow-sm w-100" style="max-width: 400px;">
        @foreach(request()->except(['keyword', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <div class="input-group">
            <input type="text" name="keyword" class="form-control border-0" placeholder="Nhập mã đơn, tên, SĐT..." value="{{ request('keyword') }}">
            <button class="btn btn-primary px-3" type="submit"><i class="fas fa-search"></i></button>
            @if(request('keyword'))
                <a href="{{ route('orders.index') }}" class="btn btn-light border-0 text-danger"><i class="fas fa-times"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- BỘ LỌC --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="fw-bold text-dark me-2" style="min-width: 100px;"><i class="fas fa-filter text-muted me-1"></i> Trạng thái:</span>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'all', 'page' => 1]) }}" class="btn btn-sm {{ !request('status') || request('status') == 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">Tất cả</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => 1]) }}" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">⏳ Chờ xử lý</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'confirmed', 'page' => 1]) }}" class="btn btn-sm {{ request('status') == 'confirmed' ? 'btn-primary' : 'btn-outline-primary' }}">👮 Đã xác nhận</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'shipping', 'page' => 1]) }}" class="btn btn-sm {{ request('status') == 'shipping' ? 'btn-info text-white' : 'btn-outline-info text-dark' }}">🚚 Đang giao</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'completed', 'page' => 1]) }}" class="btn btn-sm {{ request('status') == 'completed' ? 'btn-success' : 'btn-outline-success' }}">✅ Hoàn thành</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'bom_hang', 'page' => 1]) }}" class="btn btn-sm {{ request('status') == 'bom_hang' ? 'btn-dark' : 'btn-outline-dark' }}">💣 Bom hàng</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled', 'page' => 1]) }}" class="btn btn-sm {{ request('status') == 'cancelled' ? 'btn-danger' : 'btn-outline-danger' }}">❌ Đã hủy</a>
        </div>
        <hr class="my-3 text-muted opacity-25">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="fw-bold text-dark me-2" style="min-width: 100px;"><i class="fas fa-wallet text-muted me-1"></i> Thanh toán:</span>
            <a href="{{ request()->fullUrlWithQuery(['payment_method' => 'all', 'page' => 1]) }}" class="btn btn-sm {{ !request('payment_method') || request('payment_method') == 'all' ? 'btn-secondary' : 'btn-outline-secondary' }}">Tất cả</a>
            <a href="{{ request()->fullUrlWithQuery(['payment_method' => 'cod', 'page' => 1]) }}" class="btn btn-sm {{ request('payment_method') == 'cod' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}" title="Chỉ hiện đơn COD">💵 Tiền mặt (COD)</a>
            <a href="{{ request()->fullUrlWithQuery(['payment_method' => 'banking', 'page' => 1]) }}" class="btn btn-sm {{ request('payment_method') == 'banking' ? 'btn-info text-white fw-bold' : 'btn-outline-secondary' }}" title="Chỉ hiện đơn đã chuyển khoản/VNPAY">🏦 Chuyển khoản</a>
        </div>
    </div>
</div>

<div class="card shadow border-0 mb-4 bg-transparent bg-md-white">
    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center rounded-top border-bottom-md-1 border-bottom-0">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng</h6>
        @if(request('payment_method') == 'cod') <span class="badge bg-warning text-dark"><i class="fas fa-filter me-1"></i> Lọc: Đơn COD</span>
        @elseif(request('payment_method') == 'banking') <span class="badge bg-info text-white"><i class="fas fa-filter me-1"></i> Lọc: Chuyển khoản</span>
        @endif
    </div>
    
    <div class="card-body p-0 p-md-3">
        
        {{-- 🔥 1. GIAO DIỆN MÁY TÍNH (TABLE) 🔥 --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-bordered table-hover align-middle mb-0" width="100%" cellspacing="0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3">Mã Đơn</th>
                        <th class="py-3">Khách hàng</th>
                        <th class="py-3 text-end">Tổng tiền</th>
                        <th class="py-3 text-center">Thanh toán</th>
                        <th class="py-3">Ngày đặt</th>
                        <th class="py-3 text-center">Trạng thái</th>
                        <th class="py-3 text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="fw-bold text-primary">#{{ $order->id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $order->name }}</div>
                            <small class="text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $order->phone }}</small>
                        </td>
                        <td class="text-danger fw-bold text-end">{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                        <td class="text-center">
                            @php $pm = strtolower($order->payment_method); @endphp
                            @if($pm == 'cod') <span class="badge bg-light text-dark border border-secondary">💵 COD</span>
                            @elseif($pm == 'vnpay' || $pm == 'bank_transfer') <span class="badge bg-info text-white border border-info">🏦 Banking</span>
                            @else <span class="badge bg-secondary text-white">{{ $order->payment_method }}</span> @endif
                        </td>
                        <td>
                            <div class="text-dark">{{ $order->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                        </td>
                        <td class="text-center">
                            @if($order->status == 'pending') <span class="badge bg-warning text-dark border border-warning">⏳ Chờ xử lý</span>
                            @elseif($order->status == 'confirmed') <span class="badge bg-primary border border-primary">👮 Đã xác nhận</span>
                            @elseif($order->status == 'shipping') <span class="badge bg-info text-dark border border-info">🚚 Đang giao</span>
                            @elseif($order->status == 'completed') <span class="badge bg-success border border-success">✅ Hoàn thành</span>
                            @elseif($order->status == 'bom_hang') <span class="badge bg-dark border border-dark">💣 Bom hàng</span>
                            @elseif($order->status == 'cancelled') <span class="badge bg-danger border border-danger">❌ Đã hủy</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-info btn-sm rounded-circle" title="Xem chi tiết" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- 🔥 2. GIAO DIỆN ĐIỆN THOẠI (CARD) 🔥 --}}
        <div class="d-md-none p-2 p-sm-3">
            @foreach($orders as $order)
                <div class="card mb-3 shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom pt-3 pb-2 d-flex justify-content-between align-items-center rounded-top-4">
                        <span class="fw-bold text-primary fs-5">#{{ $order->id }}</span>
                        <div>
                            @if($order->status == 'pending') <span class="badge bg-warning text-dark px-2 py-1">⏳ Chờ xử lý</span>
                            @elseif($order->status == 'confirmed') <span class="badge bg-primary px-2 py-1">👮 Xác nhận</span>
                            @elseif($order->status == 'shipping') <span class="badge bg-info text-dark px-2 py-1">🚚 Đang giao</span>
                            @elseif($order->status == 'completed') <span class="badge bg-success px-2 py-1">✅ Hoàn thành</span>
                            @elseif($order->status == 'bom_hang') <span class="badge bg-dark px-2 py-1">💣 Bom hàng</span>
                            @elseif($order->status == 'cancelled') <span class="badge bg-danger px-2 py-1">❌ Đã hủy</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body py-3">
                        <div class="mb-2">
                            <div class="fw-bold text-dark"><i class="fas fa-user text-muted me-2" style="width: 16px;"></i>{{ $order->name }}</div>
                            <div class="small text-muted mt-1"><i class="fas fa-phone-alt text-muted me-2" style="width: 16px;"></i>{{ $order->phone }}</div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-muted small"><i class="far fa-clock me-1"></i>{{ $order->created_at->format('H:i d/m/Y') }}</div>
                            <div>
                                @php $pm = strtolower($order->payment_method); @endphp
                                @if($pm == 'cod') <span class="badge bg-light text-dark border">💵 COD</span>
                                @elseif($pm == 'vnpay' || $pm == 'bank_transfer') <span class="badge bg-info text-white border">🏦 Banking</span>
                                @else <span class="badge bg-secondary text-white">{{ $order->payment_method }}</span> @endif
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div>
                                <span class="text-muted small d-block">Tổng tiền</span>
                                <span class="text-danger fw-bold fs-5">{{ number_format($order->total_price, 0, ',', '.') }} đ</span>
                            </div>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-info btn-sm rounded-pill fw-bold px-4 py-2">
                                <i class="fas fa-eye me-1"></i> Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Phân trang chung --}}
        <div class="d-flex justify-content-center justify-content-md-end mt-3 p-3">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        
    </div>
</div>
@endsection