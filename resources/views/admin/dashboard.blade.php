@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    
    {{-- =========================================================== --}}
    {{-- HÀNG 1: KPI CHIẾN LƯỢC (TÀI CHÍNH & TỒN KHO) --}}
    {{-- =========================================================== --}}
    <div class="row g-4 mb-4">
        {{-- 1. DOANH THU TỔNG (Quan trọng nhất) --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow rounded-4 h-100 hover-scale">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted small">Tổng Doanh Thu</p>
                                <h4 class="font-weight-bolder mb-0 mt-2 text-primary">
                                    {{ number_format($totalRevenue) }} ₫
                                </h4>
                                <small class="text-success text-xs font-weight-bolder">+5% so với tháng trước</small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-gradient-primary shadow text-center rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-coins text-lg text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TỔNG ĐƠN HÀNG --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow rounded-4 h-100 hover-scale">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted small">Đơn hàng</p>
                                <h4 class="font-weight-bolder mb-0 mt-2">{{ $totalOrders }}</h4>
                                <a href="{{ route('orders.index') }}" class="text-primary text-xs font-weight-bold mt-2 d-block text-decoration-none">
                                    Xem quản lý đơn <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-gradient-info shadow text-center rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-shopping-cart text-lg text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. KHÁCH HÀNG (USER) --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow rounded-4 h-100 hover-scale">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted small">Khách hàng</p>
                                <h4 class="font-weight-bolder mb-0 mt-2">{{ $totalUsers }}</h4>
                                
                                {{-- 🔥 NÚT XEM KHÁCH HÀNG (Truyền thêm tham số role=5) 🔥 --}}
                               <a href="{{ route('users.index', ['role' => 5]) }}" 
                                class="text-success text-xs fw-bold mt-2 d-inline-flex align-items-center text-nowrap text-decoration-none">
                                    Xem danh sách khách <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-gradient-success shadow text-center rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-users text-lg text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. CẢNH BÁO TỒN KHO (Chiến lược) --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow rounded-4 h-100 hover-scale">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-danger small">Sắp hết hàng</p>
                                <h4 class="font-weight-bolder mb-0 mt-2 text-danger">{{ $lowStockBooks->count() }}</h4>
                                <span class="text-muted text-xs">Sách dưới 10 cuốn</span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-gradient-danger shadow text-center rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-exclamation-triangle text-lg text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- HÀNG 2: BIỂU ĐỒ (VISUALIZATION) --}}
    {{-- =========================================================== --}}
    <div class="row mt-4 g-4">
        {{-- Biểu đồ Doanh thu --}}
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line me-2 text-primary"></i>Xu hướng doanh thu (7 ngày)</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ Trạng thái --}}
        <div class="col-lg-4">
            <div class="card shadow border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-pie me-2 text-info"></i>Tỷ lệ đơn hàng</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="width: 100%; max-width: 280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- HÀNG 3: BÁO CÁO CHIẾN LƯỢC SẢN PHẨM (MỚI) --}}
    {{-- =========================================================== --}}
    <div class="row mt-4 g-4">
        
        {{-- 1. TOP SÁCH BÁN CHẠY (GÀ ĐẺ TRỨNG VÀNG) --}}
<div class="col-lg-7 mb-4 mb-lg-0">
    <div class="card shadow-sm border-0 rounded-4 h-100 bg-white">
        <div class="card-header bg-white border-bottom border-light py-3 d-flex justify-content-between align-items-center rounded-top-4">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-trophy text-warning me-2 fs-5"></i>Top Sách Bán Chạy</h6>
            <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold shadow-sm">
                <i class="fas fa-boxes me-1"></i> Kho hàng
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="bg-light text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">
                        <tr>
                            <th class="ps-4 py-3 text-center" style="width: 10%;">Top</th>
                            <th class="py-3" style="width: 45%;">Thông tin Sách</th>
                            <th class="text-center py-3" style="width: 20%;">Đã bán</th>
                            <th class="text-end pe-4 py-3" style="width: 25%;">Đánh giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topBooks as $key => $book)
                        
                        {{-- 🔥 LOGIC TÍNH SAO ĐÁNH GIÁ (ĐÃ FIX LỖI STDCLASS) 🔥 --}}
                        @php
                            // Gán mặc định là 0
                            $avgRating = $book->reviews_avg_rating ?? 0;
                            $reviewCount = $book->reviews_count ?? 0;
                            
                            // Kiểm tra an toàn: Nếu tồn tại property 'reviews' thì mới tính toán
                            if (isset($book->reviews) && $book->reviews) {
                                $avgRating = $avgRating ?: $book->reviews->avg('rating');
                                $reviewCount = $reviewCount ?: $book->reviews->count();
                            }
                        @endphp
                        
                        <tr class="border-bottom border-light">
                            
                            {{-- Cột Thứ hạng (Huy hiệu) --}}
                            <td class="ps-4 text-center">
                                @if($key == 0) 
                                    <span class="badge bg-warning text-dark rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">1</span>
                                @elseif($key == 1) 
                                    <span class="badge bg-secondary text-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">2</span>
                                @elseif($key == 2) 
                                    <span class="badge text-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="background: #CD7F32; width: 32px; height: 32px; font-size: 14px;">3</span>
                                @else 
                                    <span class="badge bg-light text-secondary border rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">{{ $key + 1 }}</span>
                                @endif
                            </td>

                            {{-- Cột Ảnh & Thông tin Sách --}}
                            <td>
                                <div class="d-flex align-items-center py-2">
                                    {{-- ĐÃ FIX LỖI ĐƯỜNG DẪN ẢNH --}}
                                    <img src="{{ asset($book->image ? (str_contains($book->image, 'uploads') ? $book->image : 'uploads/'.$book->image) : 'https://via.placeholder.com/300x450') }}" 
                                         class="rounded-3 shadow-sm border me-3 flex-shrink-0" width="45" height="65" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark text-wrap mb-1" style="font-size: 0.95rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-width: 250px;">
                                            {{ $book->title }}
                                        </div>
                                        <small class="text-muted"><i class="fas fa-pen-nib text-xs me-1"></i>{{ $book->author ?? 'Đang cập nhật' }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Cột Đã bán --}}
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem;">
                                    <i class="fas fa-fire me-1 text-danger"></i> {{ number_format($book->total_sold ?? 0) }}
                                </span>
                            </td>

                            {{-- Cột Đánh giá Sao (ĐÃ FIX) --}}
                            <td class="text-end pe-4">
                                @if($reviewCount > 0)
                                    <div class="d-flex flex-column align-items-end">
                                        <div class="text-warning mb-1" style="font-size: 13px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($avgRating)) <i class="fas fa-star"></i>
                                                @elseif($i == ceil($avgRating) && $avgRating - floor($avgRating) > 0) <i class="fas fa-star-half-alt"></i>
                                                @else <i class="far fa-star text-muted opacity-25"></i> @endif
                                            @endfor
                                        </div>
                                        <span class="text-dark fw-bold" style="font-size: 11px;">{{ round($avgRating, 1) }} / 5.0</span>
                                    </div>
                                @else
                                    <span class="badge bg-light text-secondary border px-2 py-1">Chưa có</span>
                                @endif
                            </td>
                            
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x opacity-25 mb-3"></i>
                                <h6 class="fw-bold">Chưa có sách nào được bán ra.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        </div>

        {{-- 2. CẢNH BÁO KHO (SẮP HẾT HÀNG) --}}
        <div class="col-lg-5">
            <div class="card shadow border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-bell me-2"></i>Cần nhập hàng gấp (<10)</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($lowStockBooks as $book)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset($book->image) }}" class="rounded border me-3" width="35" height="50" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark text-sm">{{ Str::limit($book->title, 30) }}</div>
                                    <small class="text-danger fw-bold">Chỉ còn: {{ $book->quantity }}</small>
                                </div>
                            </div>
                            <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-light text-danger border-danger">Nhập</a>
                        </li>
                        @endforeach
                        
                        @if($lowStockBooks->isEmpty())
                            <li class="list-group-item text-center py-5 text-muted">
                                <i class="fas fa-check-circle text-success fs-1 mb-2"></i><br>
                                Kho hàng đang ổn định!
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- HÀNG 4: ĐƠN HÀNG VỪA ĐẶT (OPERATIONAL) --}}
    {{-- =========================================================== --}}
    {{-- (Giữ lại bảng đơn hàng cũ của cậu ở đây nhưng rút gọn lại nếu cần) --}}
</div>

{{-- SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    };

    // 1. BIỂU ĐỒ DOANH THU (NÂNG CẤP)
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    let gradient = ctxRevenue.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(203, 12, 159, 0.5)'); 
    gradient.addColorStop(1, 'rgba(203, 12, 159, 0.0)'); 

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!}, 
            datasets: [{
                label: 'Doanh thu',
                data: {!! json_encode($data) !!},
                borderColor: '#cb0c9f',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#cb0c9f',
                pointRadius: 4,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Để chỉnh height trong html
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { borderDash: [2, 4], color: '#f0f2f5' },
                    ticks: { callback: function(value) { return formatCurrency(value); }, font: { size: 11 } }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. BIỂU ĐỒ TRẠNG THÁI (NÂNG CẤP)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    const statusData = {!! json_encode($statusCounts) !!};
    const totalOrders = statusData.reduce((a, b) => a + b, 0);

    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Chờ xử lý', 'Đã xác nhận', 'Thành công', 'Đã hủy'],
            datasets: [{
                data: statusData,
                backgroundColor: ['#ffc107', '#17c1e8', '#82d616', '#ea0606'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw || 0;
                            let percentage = totalOrders > 0 ? Math.round((value / totalOrders) * 100) : 0;
                            return ` ${context.label}: ${value} đơn (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '75%',
        }
    });
</script>

<style>
    /* Gradient & Icon Colors */
    .bg-gradient-primary { background: linear-gradient(310deg, #7928ca, #ff0080); }
    .bg-gradient-success { background: linear-gradient(310deg, #17ad37, #98ec2d); }
    .bg-gradient-info { background: linear-gradient(310deg, #2152ff, #21d4fd); }
    .bg-gradient-warning { background: linear-gradient(310deg, #f53939, #fbcf33); }
    .bg-gradient-danger { background: linear-gradient(310deg, #ea0606, #ff667c); }
    
    .icon-shape { width: 48px; height: 48px; border-radius: 0.75rem; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-5px); }
</style>
@endsection