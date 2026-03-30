@extends('admin.layouts.layout')

@section('content')
{{-- HEADER --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center">
        <i class="fas fa-image text-primary me-2"></i> Quản lý Banner
    </h4>
    
    {{-- PHÂN QUYỀN: QUẢN LÝ (2) & KIỂM DUYỆT (4) ĐƯỢC THÊM MỚI --}}
    @if(in_array(Auth::user()->role, [2, 4]))
        <a href="{{ route('banners.create') }}" class="btn btn-primary shadow-sm fw-bold rounded-pill px-4">
            <i class="fas fa-plus me-1"></i> Thêm mới
        </a>
    @endif
</div>

{{-- KHU VỰC THÔNG BÁO --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- KHU VỰC HIỂN THỊ DỮ LIỆU --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-transparent bg-lg-white">
    <div class="card-body p-0">
        
        {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light text-secondary text-uppercase small fw-bold border-bottom">
                    <tr>
                        <th class="py-3 ps-4" style="width: 150px;">Hình ảnh</th>
                        <th class="py-3" style="width: 35%;">Tiêu đề & Mô tả</th>
                        <th class="py-3 text-center" style="width: 100px;">Thứ tự</th>
                        <th class="py-3 text-center" style="width: 150px;">Trạng thái</th>
                        <th class="py-3 text-end pe-4" style="width: 150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr class="border-bottom border-light">
                        {{-- 1. Ảnh --}}
                        <td class="ps-4 py-3">
                            <img src="{{ asset($banner->image) }}" class="rounded-3 border shadow-sm" style="height: 60px; width: 120px; object-fit: cover;">
                        </td>
                        
                        {{-- 2. Tiêu đề --}}
                        <td>
                            <div class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $banner->title ?? '---' }}</div>
                            <small class="text-muted text-wrap" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                {{ $banner->description ?? 'Không có mô tả' }}
                            </small>
                        </td>
                        
                        {{-- 3. Thứ tự --}}
                        <td class="text-center fw-bold text-primary fs-5">{{ $banner->order }}</td>
                        
                        {{-- 4. Trạng thái --}}
                        <td class="text-center">
                            @php $isActive = ($banner->status == 'active' || $banner->status == 1); @endphp
                            @if(in_array(Auth::user()->role, [2, 4]))
                                <form action="{{ route('banners.toggle', $banner->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill fw-bold px-3 shadow-sm w-100 {{ $isActive ? 'btn-success' : 'btn-outline-secondary' }}" title="Đổi trạng thái">
                                        @if($isActive) <i class="fas fa-eye me-1"></i> Đang hiện
                                        @else <i class="fas fa-eye-slash me-1"></i> Đang ẩn @endif
                                    </button>
                                </form>
                            @else
                                <span class="badge rounded-pill px-3 py-2 w-100 {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                                    {!! $isActive ? '<i class="fas fa-eye me-1"></i> Đang hiện' : '<i class="fas fa-eye-slash me-1"></i> Đang ẩn' !!}
                                </span>
                            @endif
                        </td>

                        {{-- 5. Hành động --}}
                        <td class="text-end pe-4">
                            @if(in_array(Auth::user()->role, [2, 4]))
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-warning text-dark rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa banner này?')">
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
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-images fa-3x opacity-25 mb-3"></i>
                            <h6 class="fw-bold">Chưa có banner nào</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔥 2. GIAO DIỆN MOBILE (CARD) 🔥 --}}
        <div class="d-lg-none p-2 p-sm-3">
            @forelse($banners as $banner)
                <div class="card mb-3 shadow-sm border-0 rounded-4 overflow-hidden">
                    
                    {{-- Hình ảnh Banner bự chà bá --}}
                    <img src="{{ asset($banner->image) }}" class="card-img-top border-bottom" style="height: 160px; object-fit: cover;" alt="Banner">
                    
                    <div class="card-body p-3">
                        {{-- Tiêu đề & Cắt gọn mô tả --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold text-dark text-wrap mb-0" style="line-height: 1.4; font-size: 1.05rem;">{{ $banner->title ?? 'Không có tiêu đề' }}</h6>
                            <span class="badge bg-light text-primary border fw-bold flex-shrink-0 ms-2 px-2 py-1 shadow-sm" title="Thứ tự hiển thị">Thứ tự: {{ $banner->order }}</span>
                        </div>
                        <div class="text-muted small mb-3 lh-base" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $banner->description ?? 'Không có mô tả chi tiết.' }}
                        </div>

                        {{-- Nút Trạng thái & Hành động (Flexbox dàn ngang) --}}
                        <div class="d-flex flex-wrap gap-2 pt-3 border-top border-light">
                            @php $isActive = ($banner->status == 'active' || $banner->status == 1); @endphp
                            
                            {{-- Nút Ẩn/Hiện --}}
                            @if(in_array(Auth::user()->role, [2, 4]))
                                <form action="{{ route('banners.toggle', $banner->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill fw-bold w-100 shadow-sm py-2 {{ $isActive ? 'btn-success' : 'btn-outline-secondary' }}">
                                        @if($isActive) <i class="fas fa-eye me-1"></i> Đang hiện
                                        @else <i class="fas fa-eye-slash me-1"></i> Đang ẩn @endif
                                    </button>
                                </form>
                            @else
                                <div class="flex-grow-1">
                                    <span class="badge rounded-pill px-3 py-2 w-100 d-block {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                                        {!! $isActive ? '<i class="fas fa-eye me-1"></i> Đang hiện' : '<i class="fas fa-eye-slash me-1"></i> Đang ẩn' !!}
                                    </span>
                                </div>
                            @endif

                            {{-- Nút Sửa & Xóa --}}
                            @if(in_array(Auth::user()->role, [2, 4]))
                                <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill fw-bold px-4 py-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa banner này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-4 py-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                    <i class="fas fa-images fa-3x text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-bold text-muted">Chưa có banner nào</h6>
                </div>
            @endforelse
        </div>

    </div>
</div>

@endsection