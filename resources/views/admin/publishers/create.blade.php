@extends('admin.layouts.layout')

@section('header', 'Thêm Nhà Xuất Bản')

@section('content')
{{-- Tiêu đề và nút quay lại - Đã tối ưu Mobile --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 w-100" style="max-width: 700px; margin: 0 auto;">
    <h4 class="fw-bold text-dark m-0">
        <span class="text-muted fw-light">Nhà xuất bản /</span> Thêm mới
    </h4>
    <a href="{{ route('publishers.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm align-self-start align-self-md-auto">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

{{-- Form nhập liệu --}}
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                
                <form action="{{ route('publishers.store') }}" method="POST">
                    @csrf
                    
                    {{-- 1. Input Tên NXB --}}
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold text-dark">
                            Tên nhà xuất bản <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-secondary">
                                <i class="fas fa-building"></i>
                            </span>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   class="form-control border-start-0 ps-0 py-2 @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}"
                                   required 
                                   placeholder="Ví dụ: Kim Đồng, Trẻ, Lao Động..."
                                   autofocus>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted small">Nhập tên đầy đủ và chính xác của NXB.</div>
                    </div>

                    {{-- 2. Input Địa chỉ (MỚI BỔ SUNG) --}}
                    <div class="mb-4">
                        <label for="address" class="form-label fw-bold text-dark">
                            Địa chỉ trụ sở
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-secondary">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" 
                                   name="address" 
                                   id="address"
                                   class="form-control border-start-0 ps-0 py-2 @error('address') is-invalid @enderror" 
                                   value="{{ old('address') }}"
                                   placeholder="Nhập địa chỉ (không bắt buộc)...">
                        </div>
                        @error('address')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('publishers.index') }}" class="btn btn-light border fw-bold text-secondary px-4 rounded-pill">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm rounded-pill">
                            <i class="fas fa-save me-1"></i> Lưu lại
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection