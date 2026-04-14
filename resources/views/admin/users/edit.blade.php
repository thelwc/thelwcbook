@extends('admin.layouts.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">
        <i class="fas fa-user-edit text-primary me-2"></i> Cập Nhật Phân Quyền
    </h4>
    <a href="{{ route('users.index') }}" class="btn btn-light border fw-bold rounded-pill shadow-sm text-secondary hover-dark">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="row g-4">
    {{-- CỘT TRÁI: THÔNG TIN TÀI KHOẢN (CHỈ XEM) --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 text-center">
                {{-- Ảnh Avatar đồng bộ --}}
                <div class="position-relative d-inline-block mb-3">
                    @if($user->avatar)
                        <img src="{{ asset($user->avatar) }}" class="rounded-circle border shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-light border text-secondary d-flex justify-content-center align-items-center fw-bold shadow-sm mx-auto" style="width: 120px; height: 120px; font-size: 2.5rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                
                {{-- Tên & Email --}}
                <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-3"><i class="fas fa-envelope me-1"></i> {{ $user->email }}</p>
                
                {{-- Badge Vai trò y hệt trang Index --}}
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block mb-2">Vai trò hiện tại:</span>
                    @if($user->role == 0) <span class="badge bg-danger rounded-pill px-3 py-2">🛡️ Admin</span>
                    @elseif($user->role == 1) <span class="badge bg-warning text-dark rounded-pill px-3 py-2">👔 Giám đốc</span>
                    @elseif($user->role == 2) <span class="badge bg-info text-white rounded-pill px-3 py-2">💼 Quản lý</span>
                    @elseif($user->role == 3) <span class="badge bg-primary rounded-pill px-3 py-2">🧑‍💼 Nhân viên</span>
                    @elseif($user->role == 4) <span class="badge bg-success rounded-pill px-3 py-2">📝 Kiểm duyệt</span>
                    @else <span class="badge bg-secondary rounded-pill px-3 py-2">👥 Khách hàng</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- CỘT PHẢI: FORM CẬP NHẬT QUYỀN --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold text-uppercase text-muted mb-0"><i class="fas fa-sliders-h me-1"></i> Điều chỉnh phân quyền</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Chức vụ (Quyền) mới <span class="text-danger">*</span></label>
                        {{-- Dropdown dùng Input Group giống trang Create --}}
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-shield text-muted"></i></span>
                            <select name="role" class="form-select border-start-0 ps-0 fw-bold text-dark" required>
                                <optgroup label="Ban lãnh đạo">
                                    <option value="0" class="text-danger" {{ $user->role == 0 ? 'selected' : '' }}>🛡️ Quản trị viên (Admin)</option>
                                    <option value="1" class="text-warning" {{ $user->role == 1 ? 'selected' : '' }}>👔 Giám đốc</option>
                                </optgroup>
                                <optgroup label="Vận hành hệ thống">
                                    <option value="2" class="text-info" {{ $user->role == 2 ? 'selected' : '' }}>💼 Quản lý (Manager)</option>
                                    <option value="3" class="text-primary" {{ $user->role == 3 ? 'selected' : '' }}>🧑‍💼 Nhân viên (Staff)</option>
                                    <option value="4" class="text-success" {{ $user->role == 4 ? 'selected' : '' }}>📝 Kiểm duyệt viên (Content)</option>
                                </optgroup>
                                <optgroup label="Khác">
                                    <option value="5" class="text-secondary" {{ $user->role == 5 ? 'selected' : '' }}>👥 Khách hàng (Member)</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    {{-- Nút Submit bự và có hiệu ứng hover y chang form Create --}}
                    <div class="mt-5 pt-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-3 shadow-sm d-flex align-items-center justify-content-center gap-2" style="font-size: 1.05rem; transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <i class="fas fa-save"></i> Lưu Thay Đổi Phân Quyền
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection