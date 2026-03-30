@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">
    
    {{-- Nút quay lại --}}
    <div class="mb-3">
        <a href="{{ route('users.index') }}" class="text-decoration-none text-muted fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                
                {{-- HEADER: PROFILE USER --}}
                <div class="card-header bg-white border-bottom p-4 text-center">
                    <div class="position-relative d-inline-block">
                        @if($user->avatar)
                            {{-- Ảnh Avatar: Viền màu xanh (primary) thay vì trắng --}}
                            <img src="{{ asset($user->avatar) }}" alt="Avatar" 
                                class="rounded-circle border border-3 border-primary shadow-sm" 
                                style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            {{-- Chữ cái đầu: Nền xanh, chữ trắng cho nổi --}}
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-1 shadow-sm"
                                style="width: 100px; height: 100px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    {{-- Tên người dùng: Màu đen đậm --}}
                    <h4 class="mt-3 fw-bold text-dark">{{ $user->name }}</h4>
                    
                    {{-- Email: Màu xám ghi và có icon xanh --}}
                    <p class="mb-0 text-muted">
                        <i class="fas fa-envelope text-primary me-1"></i> {{ $user->email }}
                    </p>

                    {{-- Thêm cái Badge hiển thị role hiện tại cho xịn --}}
                    <div class="mt-2">
                        @if($user->role == 0) <span class="badge bg-danger">Quản trị viên</span>
                        @elseif($user->role == 1) <span class="badge bg-warning text-dark">Giám đốc</span>
                        @elseif($user->role == 2) <span class="badge bg-info text-white">Quản lý</span>
                        @elseif($user->role == 3) <span class="badge bg-primary">Nhân viên</span>
                        @elseif($user->role == 4) <span class="badge bg-success">Kiểm duyệt</span>
                        @else <span class="badge bg-secondary">Khách hàng</span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4 bg-white">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- CHỨC VỤ / QUYỀN HẠN --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">
                                <i class="fas fa-user-tag text-primary me-1"></i> Phân quyền hệ thống:
                            </label>
                            <select name="role" id="roleSelect" class="form-select form-select-lg border-secondary bg-light">
                                {{-- Nhóm Quản trị --}}
                                <optgroup label="Ban lãnh đạo">
                                    <option value="0" {{ $user->role == 0 ? 'selected' : '' }}>🛡️ Quản trị viên (Admin - Full quyền)</option>
                                    <option value="1" {{ $user->role == 1 ? 'selected' : '' }}>👔 Giám đốc (Xem báo cáo)</option>
                                </optgroup>
                                
                                {{-- Nhóm Vận hành --}}
                                <optgroup label="Vận hành cửa hàng">
                                    <option value="2" {{ $user->role == 2 ? 'selected' : '' }}>💼 Quản lý (Manager)</option>
                                    <option value="3" {{ $user->role == 3 ? 'selected' : '' }}>🧑‍💼 Nhân viên (Staff)</option>
                                    <option value="4" {{ $user->role == 4 ? 'selected' : '' }}>📝 Kiểm duyệt viên (Content)</option>
                                </optgroup>

                                {{-- Nhóm Khác --}}
                                <optgroup label="Khác">
                                    <option value="5" {{ $user->role == 5 ? 'selected' : '' }}>👥 Khách hàng (Member)</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Cập Nhật Thông Tin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection