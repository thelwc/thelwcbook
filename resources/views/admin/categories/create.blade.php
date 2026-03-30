@extends('admin.layouts.layout')

@section('header', 'Thêm Danh Mục')

@section('content')
{{-- Tiêu đề và nút quay lại --}}
<div class="d-flex justify-content-between align-items-center mb-4 w-100" style="max-width: 600px; margin: 0 auto;">
    <h4 class="fw-bold text-dark m-0">
        <span class="text-muted fw-light">Danh mục /</span> Thêm mới
    </h4>
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

{{-- Form nhập liệu --}}
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    
                    {{-- Input Tên danh mục --}}
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold text-dark">
                            Tên danh mục <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-secondary">
                                <i class="fas fa-tag"></i>
                            </span>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   class="form-control border-start-0 ps-0 py-2" 
                                   required 
                                   placeholder="Ví dụ: Tiểu thuyết, Kinh tế..."
                                   autofocus>
                        </div>
                        <div class="form-text text-muted">Tên danh mục nên ngắn gọn và rõ nghĩa.</div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('categories.index') }}" class="btn btn-light border fw-bold text-secondary">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Lưu lại
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection