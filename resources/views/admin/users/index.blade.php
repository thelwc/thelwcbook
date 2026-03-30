@extends('admin.layouts.layout')

@section('content')

{{-- CSS Xử lý rê chuột mở Dropdown (CHỈ ÁP DỤNG CHO MÁY TÍNH) --}}
<style>
    @media (min-width: 992px) {
        .hover-dropdown:hover .dropdown-menu { 
            display: block; 
            margin-top: 0; 
            animation: fadeIn 0.2s ease-in; 
        }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

{{-- ========================================================== --}}
{{-- 🔥 KHU VỰC HIỂN THỊ THÔNG BÁO 🔥 --}}
{{-- ========================================================== --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fs-4 me-3"></i> 
            <div><strong>Thành công!</strong><br>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
         <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fs-4 me-3"></i> 
            <div><strong>Úi chà! Có lỗi xảy ra:</strong><br>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ========================================================== --}}
{{-- DÒNG 1: TIÊU ĐỀ TÍCH HỢP LỌC NHANH & CÁC NÚT CHỨC NĂNG --}}
{{-- ========================================================== --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
    
    {{-- TRÁI: TIÊU ĐỀ ĐỘNG --}}
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <h4 class="fw-bold text-dark mb-0 me-2"><i class="fas fa-user-shield text-primary me-2"></i> Quản Lý:</h4>
        
        {{-- Nút TẤT CẢ --}}
        <a href="{{ route('users.index') }}" class="btn {{ empty(request('role')) ? 'btn-dark' : 'btn-outline-secondary' }} fw-bold rounded-pill shadow-sm">
            Tất cả
        </a>

        @php
            $currentRole = request('role');
            $staffLabels = [
                '0' => '🛡️ Admin',
                '1' => '👔 Giám đốc',
                '2' => '💼 Quản lý',
                '3' => '🧑‍💼 Nhân viên',
                '4' => '📝 Kiểm duyệt',
                'staff' => '👨‍💼 Nhân sự'
            ];
            $activeLabel = array_key_exists($currentRole, $staffLabels) ? $staffLabels[$currentRole] : '👨‍💼 Nhân sự';
        @endphp

        {{-- Nút Nhân Sự (Đã fix lỗi Mobile click) --}}
        <div class="dropdown hover-dropdown">
            <button class="btn {{ in_array($currentRole, ['0','1','2','3','4','staff']) ? 'btn-primary' : 'btn-outline-primary' }} fw-bold rounded-pill shadow-sm dropdown-toggle" 
                    type="button" id="staffDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                {{ $activeLabel }}
            </button>
            
            <ul class="dropdown-menu shadow-lg border-0 rounded-4 mt-2" aria-labelledby="staffDropdown">
                <li><a class="dropdown-item fw-bold py-2 {{ $currentRole === '0' ? 'active bg-danger text-white' : 'text-danger' }}" href="{{ route('users.index', ['role' => 0]) }}">🛡️ Admin</a></li>
                <li><a class="dropdown-item fw-bold py-2 {{ $currentRole === '1' ? 'active bg-warning text-dark' : 'text-warning' }}" href="{{ route('users.index', ['role' => 1]) }}">👔 Giám đốc</a></li>
                <li><a class="dropdown-item fw-bold py-2 {{ $currentRole === '2' ? 'active bg-info text-white' : 'text-info' }}" href="{{ route('users.index', ['role' => 2]) }}">💼 Quản lý</a></li>
                <li><a class="dropdown-item fw-bold py-2 {{ $currentRole === '3' ? 'active bg-primary text-white' : 'text-primary' }}" href="{{ route('users.index', ['role' => 3]) }}">🧑‍💼 Nhân viên</a></li>
                <li><a class="dropdown-item fw-bold py-2 {{ $currentRole === '4' ? 'active bg-success text-white' : 'text-success' }}" href="{{ route('users.index', ['role' => 4]) }}">📝 Kiểm duyệt</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item fw-bold py-2 text-dark" href="{{ route('users.index', ['role' => 'staff']) }}">Xem tất cả nhân sự</a></li>
            </ul>
        </div>

        {{-- Nút Khách Hàng --}}
        <a href="{{ route('users.index', ['role' => 5]) }}" class="btn {{ $currentRole === '5' ? 'btn-success' : 'btn-outline-success' }} fw-bold rounded-pill shadow-sm">
            👥 Khách hàng
        </a>
    </div>
    
    {{-- PHẢI: NÚT EXPORT / IMPORT / THÊM MỚI (CHỈ ADMIN ROLE 0) --}}
    <div class="d-flex gap-2 flex-wrap">
        @if(Auth::user()->role == 0)
            <a href="{{ route('users.export') }}" class="btn btn-success text-white shadow-sm fw-bold rounded-pill px-3">
                <i class="fas fa-file-excel me-1"></i> Xuất Excel
            </a>

            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                @csrf
                <input type="file" name="file" id="userFileInput" style="display: none;" accept=".xlsx, .xls" onchange="this.form.submit()">
                <button type="button" class="btn btn-warning fw-bold text-dark shadow-sm rounded-pill px-3" onclick="document.getElementById('userFileInput').click()">
                    <i class="fas fa-file-import me-1"></i> Nhập Excel
                </button>
            </form>

            <a href="{{ route('users.create') }}" class="btn btn-dark fw-bold shadow-sm rounded-pill px-3">
                <i class="fas fa-plus me-1"></i> Thêm Mới
            </a>
        @endif
    </div>
</div>

{{-- ========================================================== --}}
{{-- DÒNG 2: THANH TÌM KIẾM & BỘ LỌC NÂNG CAO CHECKBOX --}}
{{-- ========================================================== --}}
<div class="card mb-4 border-0 shadow-sm rounded-4">
    <div class="card-body p-3">
        <form action="{{ route('users.index') }}" method="GET" id="filterForm">
            
            @if(request()->has('role') && !request()->has('roles'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            
            <div class="row g-3 align-items-center">
                {{-- Ô Tìm kiếm chính --}}
                <div class="col-12 col-md-5 col-lg-6">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                        <input type="text" name="keyword" class="form-control border-0 ps-2" placeholder="Tìm tên hoặc email..." value="{{ request('keyword') }}" style="font-size: 0.95rem;">
                    </div>
                </div>

                {{-- Các nút Hành động --}}
                <div class="col-12 col-md-7 col-lg-6 d-flex gap-2 justify-content-md-end flex-wrap">
                    <button class="btn btn-outline-primary fw-bold rounded-pill px-3 flex-grow-1 flex-md-grow-0" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilter">
                        <i class="fas fa-sliders-h me-1"></i> Lọc quyền
                    </button>
                    
                    <button type="submit" class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm flex-grow-1 flex-md-grow-0">
                        Tìm kiếm
                    </button>

                    <a href="{{ route('users.index') }}" class="btn btn-light border fw-bold rounded-pill px-3 text-danger flex-grow-1 flex-md-grow-0" title="Bỏ lọc">
                        <i class="fas fa-times me-1"></i> Bỏ lọc
                    </a>
                </div>
            </div>

            {{-- BẢNG CHECKBOX LỌC NÂNG CAO --}}
            <div class="collapse {{ request()->has('roles') ? 'show' : '' }} mt-3" id="advancedFilter">
                <div class="p-3 bg-light border rounded-4 shadow-sm">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.8rem;">
                            <i class="fas fa-user-tag me-1"></i> Check chọn vai trò:
                        </span>
                        
                        @php
                            $rolesArr = request('roles', []);
                            $allRoles = [
                                0 => ['Admin', 'text-danger'],
                                1 => ['Giám đốc', 'text-warning text-dark'],
                                2 => ['Quản lý', 'text-info text-dark'],
                                3 => ['Nhân viên', 'text-primary'],
                                4 => ['Kiểm duyệt', 'text-success'],
                                5 => ['Khách hàng', 'text-secondary']
                            ];
                        @endphp

                        <div class="d-flex flex-wrap gap-3">
                            @foreach($allRoles as $val => $roleInfo)
                            <div class="form-check custom-checkbox mb-0">
                                <input class="form-check-input border-secondary" type="checkbox" name="roles[]" value="{{ $val }}" id="role_{{ $val }}" {{ in_array($val, $rolesArr) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold {{ $roleInfo[1] }}" for="role_{{ $val }}" style="font-size: 0.9rem;">{{ $roleInfo[0] }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</div>

{{-- ========================================================== --}}
{{-- BẢNG DANH SÁCH (ĐÃ ÁP DỤNG BÍ KÍP RESPONSIVE CARD) --}}
{{-- ========================================================== --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        
        {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase small fw-bold border-bottom">
                    <tr>
                        <th class="py-3 ps-4" style="width: 80px;">ID</th>
                        <th class="py-3" style="width: 30%;">Họ Tên</th>
                        <th class="py-3" style="width: 30%;">Email</th>
                        <th class="py-3" style="width: 150px;">Chức vụ</th>
                        <th class="py-3 text-end pe-4" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 fw-bold text-muted">#{{ $user->id }}</td>
                        
                        {{-- Cột Họ Tên --}}
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle border me-3 shadow-sm flex-shrink-0" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-light border text-secondary d-flex justify-content-center align-items-center me-3 fw-bold shadow-sm flex-shrink-0" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 1.05rem;">{{ $user->name }}</div>
                                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> Tham gia: {{ $user->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </td>

                        <td class="text-dark">{{ $user->email }}</td>
                        
                        {{-- Cột Chức vụ --}}
                        <td>
                            @if($user->role == 0) <span class="badge bg-danger rounded-pill px-3 py-2">🛡️ Admin</span>
                            @elseif($user->role == 1) <span class="badge bg-warning text-dark rounded-pill px-3 py-2">👔 Giám đốc</span>
                            @elseif($user->role == 2) <span class="badge bg-info text-white rounded-pill px-3 py-2">💼 Quản lý</span>
                            @elseif($user->role == 3) <span class="badge bg-primary rounded-pill px-3 py-2">🧑‍💼 Nhân viên</span>
                            @elseif($user->role == 4) <span class="badge bg-success rounded-pill px-3 py-2">📝 Kiểm duyệt</span>
                            @else <span class="badge bg-secondary rounded-pill px-3 py-2">👥 Khách hàng</span>
                            @endif
                        </td>

                        {{-- Cột Hành động --}}
                        <td class="text-end pe-4">
                            @if(in_array(Auth::user()->role, [1, 2]))
                                <span class="badge bg-light text-secondary border"><i class="fas fa-eye me-1"></i> Chỉ xem</span>
                            @else
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($user->role != 0) 
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài khoản này?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light border text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" disabled title="Admin không thể bị xóa">
                                            <i class="fas fa-user-shield"></i>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-search fa-3x mb-3 d-block opacity-25"></i>
                            <h5 class="fw-bold">Không tìm thấy tài khoản nào!</h5>
                            <p class="mb-0">Thử tìm kiếm với từ khóa hoặc bộ lọc khác xem sao.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔥 2. GIAO DIỆN MOBILE (CARD DỌC CỰC XỊN) 🔥 --}}
        <div class="d-lg-none p-2 p-sm-3 bg-light">
            @forelse($users as $user)
                <div class="card mb-3 shadow-sm border-0 rounded-4 overflow-hidden position-relative bg-white">
                    <div class="card-body p-3">
                        
                        {{-- Phần đầu Card: Avatar + Tên + Quyền + Nút 3 chấm (nếu có) --}}
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom border-light pb-3">
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ asset($user->avatar) }}" class="rounded-circle border shadow-sm me-3 flex-shrink-0" style="width: 55px; height: 55px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-light border text-secondary d-flex justify-content-center align-items-center me-3 fw-bold shadow-sm flex-shrink-0" style="width: 55px; height: 55px; font-size: 1.2rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 text-wrap lh-sm">{{ $user->name }}</h6>
                                    <div>
                                        @if($user->role == 0) <span class="badge bg-danger rounded-pill px-2">🛡️ Admin</span>
                                        @elseif($user->role == 1) <span class="badge bg-warning text-dark rounded-pill px-2">👔 Giám đốc</span>
                                        @elseif($user->role == 2) <span class="badge bg-info text-white rounded-pill px-2">💼 Quản lý</span>
                                        @elseif($user->role == 3) <span class="badge bg-primary rounded-pill px-2">🧑‍💼 Nhân viên</span>
                                        @elseif($user->role == 4) <span class="badge bg-success rounded-pill px-2">📝 Kiểm duyệt</span>
                                        @else <span class="badge bg-secondary rounded-pill px-2">👥 Khách hàng</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <span class="badge bg-light text-secondary border fw-bold">#{{ $user->id }}</span>
                        </div>

                        {{-- Phần thông tin phụ: Email & Ngày Tham gia --}}
                        <div class="bg-light p-2 rounded-3 border text-dark mb-3 small lh-base">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-envelope text-muted me-2" style="width: 16px;"></i> 
                                <span class="text-break fw-medium">{{ $user->email }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="far fa-calendar-alt text-muted me-2" style="width: 16px;"></i> 
                                <span>Tham gia: {{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        {{-- Nút bấm hành động --}}
                        <div class="d-flex flex-wrap gap-2">
                            @if(in_array(Auth::user()->role, [1, 2]))
                                <div class="badge bg-light text-secondary border w-100 py-2 rounded-pill"><i class="fas fa-lock me-1"></i> Chỉ xem (Không có quyền chỉnh sửa)</div>
                            @else
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary fw-bold flex-grow-1 rounded-pill py-2">
                                    <i class="fas fa-edit me-1"></i> Sửa
                                </a>
                                
                                @if($user->role != 0) 
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Xóa tài khoản này?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold w-100 rounded-pill py-2">
                                            <i class="fas fa-trash me-1"></i> Xóa
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-light border text-muted fw-bold flex-grow-1 rounded-pill py-2" disabled title="Admin không thể bị xóa">
                                        <i class="fas fa-user-shield me-1"></i> Admin
                                    </button>
                                @endif
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                    <i class="fas fa-search fa-3x mb-3 d-block text-muted opacity-25"></i>
                    <h6 class="fw-bold text-dark">Không tìm thấy tài khoản nào!</h6>
                    <p class="small text-muted mb-0">Thử tìm kiếm với từ khóa khác xem sao.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

<div class="d-flex justify-content-center justify-content-lg-end">
    {{ $users->appends(request()->all())->links() }}
</div>
@endsection