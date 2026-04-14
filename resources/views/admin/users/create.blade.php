@extends('admin.layouts.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">
        <i class="fas fa-user-plus text-primary me-2"></i> Thêm Tài Khoản Mới
    </h4>
    <a href="{{ route('users.index') }}" class="btn btn-light border fw-bold rounded-pill shadow-sm text-secondary hover-dark">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- CỘT TRÁI: THÔNG TIN CƠ BẢN --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-0"><i class="fas fa-info-circle me-1"></i> Thông tin cơ bản</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Họ và Tên <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                {{-- Thêm value="{{ old('name') }}" để giữ lại chữ khi bị lỗi --}}
                                <input type="text" name="name" class="form-control border-start-0 ps-0" placeholder="Nhập họ và tên..." value="{{ old('name') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Email <span class="text-danger">*</span></label>
                            {{-- Nếu có lỗi ở trường email thì thêm viền đỏ (is-invalid) --}}
                            <div class="input-group shadow-sm rounded-3 overflow-hidden @error('email') border border-danger @enderror">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="nguyenvana@gmail.com" value="{{ old('email') }}" required>
                            </div>
                            {{-- Bắt và in câu thông báo lỗi màu đỏ ra màn hình --}}
                            @error('email')
                                <div class="text-danger mt-1 small fw-bold">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Nhập mật khẩu..." required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Chức vụ (Quyền) <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-shield text-muted"></i></span>
                                <select name="role" class="form-select border-start-0 ps-0 fw-bold text-dark" required>
                                    <option value="5" class="text-secondary">👥 Khách hàng</option>
                                    <option value="4" class="text-success">📝 Kiểm duyệt</option>
                                    <option value="3" class="text-primary">🧑‍💼 Nhân viên</option>
                                    <option value="2" class="text-info">💼 Quản lý</option>
                                    <option value="1" class="text-warning">👔 Giám đốc</option>
                                    <option value="0" class="text-danger">🛡️ Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: ẢNH ĐẠI DIỆN & SUBMIT --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-0"><i class="fas fa-camera-retro me-1"></i> Ảnh đại diện</h6>
                </div>
                <div class="card-body p-4 text-center">
                    {{-- Khung Preview Ảnh có nút bấm --}}
                    <div class="position-relative d-inline-block mb-3">
                        <div id="imagePreview" class="rounded-circle bg-light border d-flex align-items-center justify-content-center overflow-hidden shadow-sm mx-auto" style="width: 140px; height: 140px;">
                            <i class="fas fa-image fa-3x text-muted opacity-25" id="cameraIcon"></i>
                            <img id="previewImg" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        </div>
                        {{-- Nút bấm đè lên khung ảnh --}}
                        <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 35px; height: 35px; cursor: pointer; transform: translate(0%, -10%); border: 3px solid white; transition: 0.2s;" onmouseover="this.style.transform='translate(0%, -10%) scale(1.1)'" onmouseout="this.style.transform='translate(0%, -10%) scale(1)'">
                            <i class="fas fa-pencil-alt fs-6"></i>
                        </label>
                    </div>
                    
                    <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                    <p class="small text-muted mb-0">Hỗ trợ định dạng JPG, PNG.<br>Kích thước khuyên dùng: Vuông.</p>
                </div>
            </div>

            {{-- Nút Submit bự chà bá --}}
            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-3 shadow-sm d-flex align-items-center justify-content-center gap-2" style="font-size: 1.05rem; transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-check-circle"></i> Xác nhận tạo tài khoản
            </button>
        </div>
    </div>
</form>

{{-- Script xử lý xem trước ảnh khi vừa upload --}}
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('previewImg').style.display = 'block';
                document.getElementById('cameraIcon').style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection