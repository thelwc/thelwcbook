@extends('admin.layouts.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark">Chỉnh sửa Banner</h4>
    <a href="{{ route('banners.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="card shadow border-0 rounded-4">
    <div class="card-body p-4">
        {{-- Form cập nhật (dùng route update và method PUT) --}}
        <form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Bắt buộc để Laravel hiểu là cập nhật --}}
            
            <div class="row">
                {{-- CỘT TRÁI: ẢNH HIỆN TẠI --}}
                <div class="col-md-4 mb-4 text-center">
                    <label class="form-label fw-bold d-block text-start">Hình ảnh hiện tại</label>
                    <div class="border rounded p-2 bg-light d-inline-block shadow-sm">
                        @if($banner->image)
                            <img src="{{ asset($banner->image) }}" class="img-fluid rounded" style="max-height: 200px;">
                        @else
                            <span class="text-muted">Chưa có ảnh</span>
                        @endif
                    </div>
                    
                    <div class="mt-3 text-start">
                        <label class="form-label fw-bold text-primary">Thay đổi ảnh mới (Nếu cần)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted fst-italic">* Để trống nếu không muốn đổi ảnh.</small>
                    </div>
                </div>

                {{-- CỘT PHẢI: THÔNG TIN --}}
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề lớn</label>
                        <input type="text" name="title" class="form-control" value="{{ $banner->title }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả nhỏ</label>
                        <input type="text" name="description" class="form-control" value="{{ $banner->description }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Đường dẫn khi bấm vào (Link)</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-link me-1"></i> Tên_Miền/</span>
                            <input type="text" name="link" class="form-control" value="{{ $banner->link }}">
                        </div>
                        <small class="text-danger fst-italic" style="font-size: 12px;">* Chỉ nhập phần đuôi (VD: minigame). Không nhập http://127.0.0.1...</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Thứ tự hiển thị</label>
                            <input type="number" name="order" class="form-control" value="{{ $banner->order }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $banner->status == 'active' || $banner->status == 1 ? 'selected' : '' }}>Hiện</option>
                                <option value="hidden" {{ $banner->status == 'hidden' || $banner->status == 0 ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-warning fw-bold text-white shadow-sm">
                            <i class="fas fa-save me-2"></i> Cập nhật Banner
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection