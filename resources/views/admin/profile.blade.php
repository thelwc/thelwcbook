@extends('admin.layouts.layout')

@section('header', 'Hồ sơ cá nhân')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 border-0 bg-success-subtle text-success fw-bold">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3 border-0">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            {{-- CỘT AVATAR --}}
                            <div class="col-md-4 text-center border-end mb-4 mb-md-0">
                                <div class="mb-4 position-relative d-inline-block">
                                    @php
                                        $avatarSrc = '';
                                        $userAvatar = Auth::user()->avatar;
                                        if ($userAvatar) {
                                            if (str_contains($userAvatar, 'http')) $avatarSrc = $userAvatar;
                                            elseif (file_exists(public_path('images/' . $userAvatar))) $avatarSrc = asset('images/' . $userAvatar);
                                            elseif (file_exists(public_path($userAvatar))) $avatarSrc = asset($userAvatar);
                                            else $avatarSrc = asset('storage/' . $userAvatar);
                                        } else {
                                            $avatarSrc = 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0D6EFD&color=fff&size=160';
                                        }
                                    @endphp

                                    <img src="{{ $avatarSrc }}" id="preview-img" class="rounded-circle shadow border border-4 border-white" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>

                                <div class="d-grid gap-2 col-10 mx-auto">
                                    <label class="btn btn-outline-primary rounded-pill fw-bold" for="avatar-input">
                                        <i class="fas fa-camera me-1"></i> Đổi ảnh mới
                                    </label>
                                    <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    
                                    @if(Auth::user()->avatar)
                                        <button type="button" class="btn btn-outline-danger rounded-pill fw-bold w-100 border-0 mt-2" onclick="if(confirm('Xóa ảnh đại diện?')) { document.getElementById('delete-avatar-form').submit(); }">
                                            <i class="fas fa-trash-alt me-1"></i> Xóa ảnh
                                        </button>
                                    @endif
                                    <small class="text-muted mt-2" style="font-size: 0.8rem">Max: 20MB</small>
                                </div>
                            </div>

                            {{-- CỘT THÔNG TIN --}}
                            <div class="col-md-8 ps-md-5">
                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-lg bg-light" value="{{ old('name', Auth::user()->name) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Email đăng nhập</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary bg-opacity-10 border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" class="form-control bg-secondary bg-opacity-10 border-start-0" value="{{ Auth::user()->email }}" readonly disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted fw-bold small text-uppercase">Số điện thoại</label>
                                        <input type="text" name="phone" class="form-control bg-light" value="{{ old('phone', Auth::user()->phone) }}" placeholder="09xxxx...">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted fw-bold small text-uppercase">Vai trò hệ thống</label>
                                        @php
                                            $roleNames = [0 => 'Admin (Quản trị viên)', 1 => 'Giám đốc', 2 => 'Quản lý', 3 => 'Nhân viên', 4 => 'Kiểm duyệt viên'];
                                        @endphp
                                        <input type="text" class="form-control bg-danger bg-opacity-10 text-danger fw-bold border-danger border-opacity-25" value="{{ $roleNames[Auth::user()->role] ?? 'Nhân sự' }}" readonly disabled>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Địa chỉ / Nơi công tác</label>
                                    <textarea name="address" class="form-control bg-light" rows="3">{{ old('address', Auth::user()->address) }}</textarea>
                                </div>

                                <div class="text-end border-top pt-4">
                                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-save me-2"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form ẩn để xóa ảnh --}}
<form id="delete-avatar-form" action="{{ route('profile.avatar.delete') }}" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            if (file.size > 20 * 1024 * 1024) {
                alert("⚠️ File quá lớn! Vui lòng chọn ảnh dưới 20MB.");
                input.value = ""; return;
            }
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById('preview-img').src = e.target.result; }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection