@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid px-0 px-md-3 py-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3 px-2 px-md-0">
        <h4 class="mb-0 text-dark fw-bold d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted text-decoration-none me-3" style="transition: 0.2s;">
                <i class="fas fa-arrow-left fs-5 hover-scale"></i>
            </a>
            Chi Tiết Doanh Thu
        </h4>
    </div>

    {{-- ========================================== --}}
    {{-- BỘ LỌC VÀ NÚT XUẤT EXCEL --}}
    {{-- ========================================== --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="row align-items-center">
                {{-- Form Lọc Ngày --}}
                <div class="col-lg-8">
                    <form action="{{ route('admin.revenue.details') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 gap-md-3">
                        <div class="input-group input-group-sm flex-nowrap" style="max-width: 180px;">
                            <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                        </div>
                        <span class="text-muted fw-bold text-xs d-none d-sm-inline">ĐẾN</span>
                        <div class="input-group input-group-sm flex-nowrap" style="max-width: 180px;">
                            <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary mb-0 px-3 px-md-4 flex-grow-1 flex-sm-grow-0">
                            <i class="fas fa-filter me-1"></i> Lọc
                        </button>
                    </form>
                </div>

                {{-- Nút Xuất Excel --}}
                <div class="col-lg-4 text-start text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('admin.revenue.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                       class="btn btn-sm btn-success mb-0 shadow-sm w-100 w-lg-auto">
                        <i class="fas fa-file-excel me-1"></i> Xuất Báo Cáo
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- THẺ TỔNG DOANH THU KỲ LỌC --}}
    {{-- ========================================== --}}
    <div class="row mb-4 px-2 px-md-0">
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-primary hover-scale">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-white opacity-8">Tổng doanh thu kỳ lọc</p>
                                <h3 class="font-weight-bolder mb-0 mt-1 text-white">
                                    {{ number_format($totalFilteredRevenue) }} ₫
                                </h3>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-white shadow text-center rounded-circle d-flex align-items-center justify-content-center ms-auto">
                                <i class="fas fa-wallet text-lg text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- DANH SÁCH ĐƠN HÀNG HOÀN THÀNH --}}
    {{-- ========================================== --}}
    <div class="card shadow-sm border-0 rounded-4 bg-transparent bg-lg-white overflow-hidden">
        <div class="card-header bg-white border-bottom border-light py-3 d-none d-lg-block">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-check-circle text-success me-2"></i>Đơn hàng giao dịch thành công</h6>
        </div>
        
        <div class="card-body p-0">
            
            {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="bg-light text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">
                        <tr>
                            <th class="ps-4 py-3">Mã Đơn</th>
                            <th class="py-3">Khách Hàng</th>
                            <th class="py-3 text-center">Thanh Toán</th>
                            <th class="py-3 text-end">Tổng Tiền</th>
                            <th class="py-3 text-center">Ngày Hoàn Thành</th>
                            <th class="py-3 text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="border-bottom border-light">
                            <td class="ps-4">
                                <span class="text-primary fw-bold">#{{ $order->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold text-sm">{{ $order->name }}</span>
                                    <span class="text-muted text-xs"><i class="fas fa-phone-alt me-1"></i>{{ $order->phone }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if(strtolower($order->payment_method) == 'cod')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1"><i class="fas fa-money-bill-wave me-1"></i> COD</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border px-2 py-1"><i class="fas fa-university me-1"></i> Chuyển khoản</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ number_format($order->total_price) }} ₫
                            </td>
                            <td class="text-center text-sm text-muted">
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-end pe-4">
                                {{-- NÚT XEM CHI TIẾT ĐƠN HÀNG (DESKTOP) --}}
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Xem chi tiết đơn hàng">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x opacity-25 mb-3"></i>
                                <h6 class="fw-bold">Không có đơn hàng nào trong khoảng thời gian này.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 🔥 2. GIAO DIỆN MOBILE (CARD VIEWS) 🔥 --}}
            <div class="d-lg-none p-2 p-sm-3">
                @forelse($orders as $order)
                    <div class="card mb-3 shadow-sm border-0 rounded-4 overflow-hidden" style="background: #fff;">
                        <div class="card-body p-3">
                            {{-- Header Card Mobile --}}
                            <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-2 mb-2">
                                <span class="text-primary fw-bold fs-6">Đơn #{{ $order->id }}</span>
                                <span class="text-muted text-xs"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            {{-- Content Card Mobile --}}
                            <div class="row g-0 align-items-center">
                                <div class="col-8 pe-2">
                                    <div class="fw-bold text-dark text-sm mb-1">{{ $order->name }}</div>
                                    <div class="text-muted text-xs mb-2"><i class="fas fa-phone-alt me-1"></i>{{ $order->phone }}</div>
                                    
                                    @if(strtolower($order->payment_method) == 'cod')
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 10px;"><i class="fas fa-money-bill-wave me-1"></i> COD</span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border px-2 py-1" style="font-size: 10px;"><i class="fas fa-university me-1"></i> Chuyển khoản</span>
                                    @endif
                                </div>
                                
                                <div class="col-4 ps-2 text-end d-flex flex-column justify-content-between h-100 border-start border-light">
                                    <div class="fw-bold text-danger text-sm mb-2">{{ number_format($order->total_price) }} ₫</div>
                                    
                                    {{-- NÚT XEM CHI TIẾT ĐƠN HÀNG (MOBILE) --}}
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-2 py-1 mt-auto" style="font-size: 11px;">
                                        <i class="fas fa-eye me-1"></i> Xem
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                        <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                        <h6 class="fw-bold text-muted">Không có đơn hàng nào</h6>
                    </div>
                @endforelse
            </div>

        </div>
        
        {{-- Phân trang (Áp dụng chung cho cả 2 giao diện) --}}
        <div class="card-footer bg-white py-3 border-0 rounded-bottom-4">
            <div class="d-flex justify-content-center justify-content-md-end">
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary { background: linear-gradient(310deg, #7928ca, #ff0080); }
    .icon-shape { width: 48px; height: 48px; border-radius: 0.75rem; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-3px); }
</style>
@endsection