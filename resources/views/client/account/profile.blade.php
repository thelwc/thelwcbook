@extends('client.layouts.master')

@section('title', 'Hồ sơ cá nhân - Thelwc Books')

@section('styles')
    <style>
        /* CSS CHUẨN THIẾT KẾ THELWC BOOKS */
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; }
        .card-custom { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        
        /* Typography */
        .text-brand-dark { color: #212529; }
        .text-brand-muted { color: #6c757d; }
        .text-brand-highlight { color: #0d6efd; }
        
        /* Form & Input */
        .form-label { font-weight: 800; font-size: 0.8rem; text-transform: uppercase; color: #6c757d; letter-spacing: 0.5px; margin-bottom: 8px; }
        .form-control { border-radius: 10px; padding: 12px 16px; border: 1px solid #dee2e6; font-size: 0.95rem; color: #212529; transition: all 0.2s ease-in-out; }
        .form-control:focus { border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
        .bg-readonly { background-color: #f8f9fa; color: #6c757d; opacity: 1; border-color: #e9ecef; cursor: not-allowed; }

        /* Buttons */
        .btn-brand-dark { background-color: #212529; color: #ffffff; border: none; padding: 12px 28px; border-radius: 999px; font-weight: 700; transition: 0.3s; }
        .btn-brand-dark:hover { background-color: #c5a992; color: #ffffff; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(197, 169, 146, 0.4); }
        
        .btn-outline-dark-brand { border: 2px solid #212529; color: #212529; font-weight: 700; border-radius: 999px; padding: 8px 20px; transition: 0.3s; }
        .btn-outline-dark-brand:hover { background-color: #212529; color: #ffffff; }

        /* Links */
        .hover-link { transition: color 0.2s ease; }
        .hover-link:hover { color: #c5a992 !important; }

        /* Stats Box */
        .stat-box { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 15px; text-align: center; transition: 0.3s; }
        .stat-box:hover { border-color: #c5a992; box-shadow: 0 5px 15px rgba(197, 169, 146, 0.1); transform: translateY(-3px); }
    </style>
@endsection

@section('content')
    <div class="container py-4 py-md-5">
        
        {{-- Header & Nút Trở về --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <h3 class="fw-bold mb-0 text-brand-dark">Hồ sơ cá nhân</h3>
            <a href="{{ route('home') }}" class="btn btn-light border text-brand-muted fw-bold rounded-pill px-4 shadow-sm align-self-start align-self-sm-auto hover-link">
                <i class="fas fa-arrow-left me-2"></i> Trở về Trang chủ
            </a>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-lg-10">
                <div class="card card-custom bg-white">
                    <div class="card-body p-3 p-md-5">
                        
                        <h4 class="fw-bold mb-4 text-brand-dark pb-3 border-bottom border-light">
                            <i class="fas fa-user-circle text-brand-highlight me-2"></i> Thông tin tài khoản
                        </h4>

                        {{-- Thông báo --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 border-0 bg-success bg-opacity-10 text-success fw-bold px-4">
                                <i class="fas fa-check-circle me-2 fs-5 align-middle"></i> {{ session('success') }}
                                <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 border-0 px-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-exclamation-triangle fs-5 me-2 text-danger"></i>
                                    <strong class="text-danger">Đã có lỗi xảy ra!</strong>
                                </div>
                                <ul class="mb-0 ps-4 text-danger small">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- FORM CHÍNH --}}
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row g-4 g-md-5">
                                {{-- ================= CỘT TRÁI: AVATAR & THỐNG KÊ ================= --}}
                                <div class="col-md-4 border-end-md border-light">
                                    <div class="text-center mb-4">
                                        <div class="position-relative d-inline-block mb-3">
                                            @php
                                                $avatarSrc = '';
                                                $userAvatar = $user->avatar;

                                                if ($userAvatar) {
                                                    if (str_contains($userAvatar, 'http')) $avatarSrc = $userAvatar;
                                                    elseif (file_exists(public_path('images/' . $userAvatar))) $avatarSrc = asset('images/' . $userAvatar);
                                                    elseif (file_exists(public_path($userAvatar))) $avatarSrc = asset($userAvatar);
                                                    else $avatarSrc = asset('storage/' . $userAvatar);
                                                } else {
                                                    $avatarSrc = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D6EFD&color=fff&size=160';
                                                }
                                            @endphp
                                            <img src="{{ $avatarSrc }}" id="preview-img" class="rounded-circle shadow-sm border border-4 border-white" style="width: 150px; height: 150px; object-fit: cover;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D6EFD&color=fff&size=160';">
                                        </div>

                                        <div class="d-grid gap-2 col-10 col-md-12 mx-auto">
                                            <label class="btn btn-outline-dark-brand d-flex align-items-center justify-content-center m-0" for="avatar-input" style="cursor: pointer;">
                                                <i class="fas fa-camera me-2"></i> Đổi ảnh mới
                                            </label>
                                            <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*" onchange="previewImage(this)">
                                            
                                            @if($user->avatar)
                                                <button type="button" class="btn btn-light text-danger fw-bold rounded-pill w-100 mt-2" onclick="if(confirm('Bạn có chắc muốn xóa ảnh đại diện này không?')) { document.getElementById('delete-avatar-form').submit(); }">
                                                    <i class="fas fa-trash-alt me-2"></i> Xóa ảnh
                                                </button>
                                            @endif
                                            <small class="text-brand-muted mt-2" style="font-size: 0.75rem"><i class="fas fa-info-circle me-1"></i> Định dạng: JPG, PNG. Max: 20MB</small>
                                        </div>
                                    </div>

                                    {{-- 🔥 KHỐI THỐNG KÊ MỚI THÊM 🔥 --}}
                                    <div class="row g-2 mt-2 px-2 px-md-0">
                                        <div class="col-6">
                                            <a href="{{ route('favorites.index') }}" class="text-decoration-none">
                                                <div class="stat-box">
                                                    <i class="fas fa-heart text-danger fs-4 mb-2"></i>
                                                    <div class="fs-5 fw-bold text-dark">{{ $user->favorites()->count() ?? 0 }}</div>
                                                    <div class="small text-muted">Yêu thích</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('client.account.history') }}" class="text-decoration-none">
                                                <div class="stat-box">
                                                    <i class="fas fa-shopping-bag text-primary fs-4 mb-2"></i>
                                                    <div class="fs-5 fw-bold text-dark">{{ $user->orders()->count() ?? 0 }}</div>
                                                    <div class="small text-muted">Đơn hàng</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= CỘT PHẢI: THÔNG TIN CHI TIẾT ================= --}}
                                <div class="col-md-8 ps-md-4">
                                    
                                    {{-- 1. Email (Đưa lên đầu, Khóa mờ) --}}
                                    <div class="mb-4">
                                        <label class="form-label">Tài khoản (Email)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-readonly border-end-0"><i class="fas fa-envelope text-brand-muted"></i></span>
                                            <input type="email" class="form-control bg-readonly border-start-0 fw-bold" value="{{ $user->email }}" readonly tabindex="-1">
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        {{-- 2. Họ tên --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" placeholder="Nhập họ và tên đầy đủ..." required>
                                        </div>
                                        
                                        {{-- 3. Số điện thoại --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Số điện thoại</label>
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="Ví dụ: 0912345678...">
                                        </div>
                                    </div>

                                    {{-- 4. Địa chỉ --}}
                                    <div class="mb-4">
                                        <label class="form-label">Địa chỉ nhận hàng mặc định</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-brand-muted"></i></span>
                                            <textarea name="address" class="form-control border-start-0 ps-0" rows="3" placeholder="Nhập số nhà, tên đường, phường/xã, quận/huyện...">{{ old('address', $user->address) }}</textarea>
                                        </div>
                                        <div class="form-text text-brand-muted mt-1" style="font-size: 0.8rem;"><i class="fas fa-info-circle me-1"></i> Địa chỉ này sẽ được tự động điền khi bạn thanh toán.</div>
                                    </div>

                                    {{-- Nút Submit --}}
                                    <div class="d-flex justify-content-end pt-3 border-top border-light mt-4">
                                        <button type="submit" class="btn btn-brand-dark w-100 w-sm-auto">
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

    {{-- FORM ẨN ĐỂ XỬ LÝ XÓA ẢNH --}}
    <form id="delete-avatar-form" action="{{ route('profile.avatar.delete') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

@endsection

@section('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var maxSize = 20 * 1024 * 1024; // 20MB

                if (file.size > maxSize) {
                    alert("⚠️ Kích thước ảnh quá lớn! Vui lòng chọn ảnh dưới 20MB.");
                    input.value = ""; 
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection