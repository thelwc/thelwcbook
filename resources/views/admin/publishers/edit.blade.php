@extends('admin.layouts.layout')

@section('header', 'Cập nhật Nhà Xuất Bản')

@section('content')
{{-- Tiêu đề và nút quay lại --}}
<div class="d-flex justify-content-between align-items-center mb-4 w-100" style="max-width: 600px; margin: 0 auto;">
    <h4 class="fw-bold text-dark m-0">
        <span class="text-muted fw-light">Nhà xuất bản /</span> Cập nhật
    </h4>
    <a href="{{ route('publishers.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

{{-- Form nhập liệu --}}
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                
                {{-- Hiển thị tên NXB đang sửa --}}
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold text-primary m-0">
                        <i class="fas fa-edit me-2"></i> {{ $publisher->name }}
                    </h5>
                </div>

                <form action="{{ route('publishers.update', $publisher->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    {{-- Input Tên NXB --}}
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
                                   class="form-control border-start-0 ps-0 py-2" 
                                   value="{{ old('name', $publisher->name) }}"
                                   required 
                                   placeholder="Nhập tên nhà xuất bản..."
                                   autofocus>
                        </div>
                    </div>

                    {{-- Input Địa chỉ (Thêm mới so với code cũ của cậu để đầy đủ hơn) --}}
                    <div class="mb-4">
                        <label for="address" class="form-label fw-bold text-dark">
                            Địa chỉ
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-secondary">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" 
                                   name="address" 
                                   id="address"
                                   class="form-control border-start-0 ps-0 py-2" 
                                   value="{{ old('address', $publisher->address) }}"
                                   placeholder="Nhập địa chỉ trụ sở (nếu có)...">
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('publishers.index') }}" class="btn btn-light border fw-bold text-secondary">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection