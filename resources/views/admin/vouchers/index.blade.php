@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid px-0 px-md-3">
    
    {{-- HEADER & NÚT THÊM MỚI --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
        <h3 class="fw-bold mb-0 text-dark"><i class="fas fa-ticket-alt text-primary me-2"></i> Quản lý Mã giảm giá</h3>
        
        {{-- PHÂN QUYỀN: QUẢN LÝ (2) & NHÂN VIÊN (3) ĐƯỢC THÊM MỚI --}}
        @if(in_array(Auth::user()->role, [2, 3]))
            <a href="{{ route('vouchers.create') }}" class="btn btn-primary shadow-sm rounded-pill fw-bold px-4">
                <i class="fas fa-plus me-1"></i> Tạo mã mới
            </a>
        @endif
    </div>

    {{-- KHU VỰC THÔNG BÁO --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- CARD BAO BỌC NỘI DUNG --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-transparent bg-lg-white">
        <div class="card-header bg-white py-3 border-bottom border-light rounded-top-4 d-none d-lg-block">
            <h6 class="m-0 fw-bold text-primary">Danh sách Voucher hệ thống</h6>
        </div>
        
        <div class="card-body p-0 p-lg-0">
            
            {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Mã Code</th>
                            <th class="py-3">Giảm giá</th>
                            <th class="py-3 text-center">Số lượng</th>
                            <th class="py-3 text-end">Đơn tối thiểu</th>
                            <th class="py-3 text-center">Hạn sử dụng</th>
                            <th class="py-3 text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                        <tr class="border-bottom border-light">
                            <td class="ps-4 fw-bold text-primary fs-6">{{ $voucher->code }}</td>
                            
                            <td>
                                @if($voucher->type == 'percent')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 fs-6">{{ intval($voucher->value) }}%</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 fs-6">{{ number_format($voucher->value) }}đ</span>
                                @endif
                            </td>
                            
                            <td class="text-center">
                                <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $voucher->quantity }}</span>
                            </td>
                            
                            <td class="text-end fw-bold text-dark">{{ number_format($voucher->min_order_amount) }} ₫</td>
                            
                            <td class="text-center">
                                <div class="small text-muted mb-1"><i class="far fa-calendar-alt me-1"></i> BĐ: {{ date('d/m/Y', strtotime($voucher->start_date)) }}</div>
                                <div class="small text-danger fw-bold"><i class="fas fa-hourglass-end me-1"></i> KT: {{ date('d/m/Y', strtotime($voucher->end_date)) }}</div>
                            </td>
                            
                            <td class="text-end pe-4">
                                @if(in_array(Auth::user()->role, [2, 3, 4]))
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-outline-warning text-dark rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('vouchers.destroy', $voucher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge bg-light text-secondary border"><i class="fas fa-lock me-1"></i> Chỉ xem</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-ticket-alt fa-3x opacity-25 mb-3"></i>
                                <h6 class="fw-bold">Chưa có mã giảm giá nào</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 🔥 2. GIAO DIỆN MOBILE (CARD TICKET) 🔥 --}}
            <div class="d-lg-none p-2 p-sm-3">
                @forelse($vouchers as $voucher)
                    <div class="card mb-3 shadow-sm border-0 rounded-4 overflow-hidden position-relative" style="background: #fff;">
                        
                        {{-- Đường viền đứt nét tạo cảm giác vé (Ticket) --}}
                        <div class="position-absolute" style="left: -10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; background: #f8f9fc; border-radius: 50%; box-shadow: inset -2px 0 5px rgba(0,0,0,0.05);"></div>
                        <div class="position-absolute" style="right: -10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; background: #f8f9fc; border-radius: 50%; box-shadow: inset 2px 0 5px rgba(0,0,0,0.05);"></div>
                        
                        <div class="card-body p-3">
                            <div class="row g-0 align-items-center">
                                
                                {{-- Phần thông tin trái (Giảm giá & Tên Mã) --}}
                                <div class="col-8 border-end border-dashed pe-3 position-relative" style="border-right-style: dashed !important; border-color: #dee2e6 !important;">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0 me-2">
                                            @if($voucher->type == 'percent')
                                                <div class="bg-info bg-opacity-10 text-info rounded d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 50px; height: 50px;">%</div>
                                            @else
                                                <div class="bg-success bg-opacity-10 text-success rounded d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 50px; height: 50px;">$</div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="small text-muted mb-1">Mã code:</div>
                                            <h5 class="fw-bold text-primary mb-0">{{ $voucher->code }}</h5>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="text-muted small">Mức giảm:</span>
                                        @if($voucher->type == 'percent')
                                            <span class="fw-bold text-info fs-6">{{ intval($voucher->value) }}%</span>
                                        @else
                                            <span class="fw-bold text-success fs-6">{{ number_format($voucher->value) }}đ</span>
                                        @endif
                                    </div>

                                    <div class="small text-muted lh-base">
                                        Đơn tối thiểu: <strong class="text-dark">{{ number_format($voucher->min_order_amount) }}₫</strong><br>
                                        HSD: <strong class="text-danger">{{ date('d/m/Y', strtotime($voucher->end_date)) }}</strong>
                                    </div>
                                </div>

                                {{-- Phần bên phải (Số lượng & Nút bấm) --}}
                                <div class="col-4 ps-3 text-center d-flex flex-column justify-content-center align-items-center h-100">
                                    <div class="mb-3">
                                        <div class="small text-muted mb-1">Số lượng</div>
                                        <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6">{{ $voucher->quantity }}</span>
                                    </div>

                                    @if(in_array(Auth::user()->role, [2, 3, 4]))
                                        <div class="d-grid gap-2 w-100 mt-auto">
                                            <a href="{{ route('vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <form action="{{ route('vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Xóa mã giảm giá này?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold rounded-pill w-100">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-secondary border w-100 py-2"><i class="fas fa-lock"></i> Xem</span>
                                    @endif
                                </div>
                                
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                        <i class="fas fa-ticket-alt fa-3x text-muted opacity-25 mb-3"></i>
                        <h6 class="fw-bold text-muted">Chưa có mã giảm giá nào</h6>
                    </div>
                @endforelse
            </div>

        </div>
        
        {{-- PHÂN TRANG --}}
        <div class="card-footer bg-white py-3 border-0 rounded-bottom-4">
            <div class="d-flex justify-content-center justify-content-md-end">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>

</div>
@endsection