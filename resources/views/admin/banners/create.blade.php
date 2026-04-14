@extends('admin.layouts.layout') {{-- Hoặc layout admin của cậu --}}

@section('content')
<div class="card shadow border-0">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">Thêm Banner Mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề lớn</label>
                <input type="text" name="title" class="form-control" placeholder="Ví dụ: Thelwc Book Collection" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mô tả nhỏ</label>
                {{-- Đã thêm required --}}
                <input type="text" name="description" class="form-control" placeholder="Ví dụ: Khám phá thế giới tri thức...">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Hình ảnh (Nên chọn ảnh ngang, >1200px)</label>
                    <input type="file" name="image" class="form-control" required accept="image/*">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Đường dẫn khi bấm vào (Link)</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-link me-1"></i> Tên_Miền/</span>
                        {{-- Đã thêm required --}}
                        <input type="text" name="link" class="form-control" placeholder="VD: minigame hoặc danh-muc/sach-moi" required>
                    </div>
                    <small class="text-danger fst-italic" style="font-size: 12px;">* Chỉ nhập phần đuôi (VD: minigame). Không nhập http://127.0.0.1...</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Thứ tự hiển thị</label>
                    {{-- Đã thêm required --}}
                    <input type="number" name="order" class="form-control" value="0" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="active">Hiện</option>
                        <option value="hidden">Ẩn</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Lưu Banner</button>
            <a href="{{ route('banners.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</div>
@endsection