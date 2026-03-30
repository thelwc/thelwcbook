<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin TheLWC - Quản trị hệ thống</title>
    
    {{-- 1. BOOTSTRAP 5 & FONT AWESOME --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f5f7fa; overflow-x: hidden; color: #67748e; }
        
        /* SIDEBAR CHUẨN */
        .sidebar {
            width: 260px; height: 100vh; position: fixed; top: 0; left: 0;
            background-color: #ffffff; box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
            z-index: 1050; overflow-y: auto; border-right: 1px solid rgba(0,0,0,0.05); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-brand {
            padding: 25px 20px; font-size: 1.2rem; font-weight: 800; color: #344767;
            text-decoration: none; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f0f2f5;
        }
        .sidebar-menu { list-style: none; padding: 20px 15px; margin: 0; }
        .sidebar-menu li { margin-bottom: 5px; }
        .sidebar-menu a {
            display: flex; align-items: center; padding: 12px 18px; color: #67748e;
            text-decoration: none; border-radius: 8px; transition: 0.2s; font-weight: 600; font-size: 0.95rem;
        }
        .sidebar-menu a:hover { background-color: #f8f9fa; color: #344767; }
        .sidebar-menu a.active {
            background: linear-gradient(310deg, #2152ff, #21d4fd); color: #fff !important;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .sidebar-menu a.active i { color: #fff !important; }
        .sidebar-menu i { width: 30px; font-size: 1.1rem; text-align: center; margin-right: 5px; }
        .menu-header {
            font-size: 0.7rem; text-transform: uppercase; color: #8898aa;
            margin: 20px 20px 10px; font-weight: 700; letter-spacing: 0.5px;
        }
        
        .main-content { margin-left: 260px; padding: 25px; transition: all 0.3s; }
        
        .top-navbar {
            background: #fff; border-radius: 16px; padding: 15px 25px;
            box-shadow: 0 2px 12px 0 rgba(0,0,0,0.03); margin-bottom: 30px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .btn-primary { background: linear-gradient(310deg, #2152ff, #21d4fd); border: 0; }
        
        /* 🔥 BÍ KÍP MOBILE: RESPONSIVE SIDEBAR & OVERLAY 🔥 */
        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 1040;
            opacity: 0; visibility: hidden; transition: all 0.3s;
        }
        .sidebar-overlay.show { opacity: 1; visibility: visible; }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); } /* Giấu sidebar sang trái */
            .sidebar.show { transform: translateX(0); } /* Bấm nút thì thò ra */
            .main-content { margin-left: 0; padding: 15px; }
            .top-navbar { padding: 15px; }
        }
    </style>
</head>
<body>

    {{-- Lớp màng đen tàng hình (Bấm vào đây để đóng menu trên Mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <nav class="sidebar" id="mainSidebar">
        <a href="{{ Auth::user()->role == 0 ? route('users.index') : route('dashboard') }}" class="sidebar-brand">
            <i class="fas fa-rocket text-primary" style="font-size: 1.5rem;"></i> 
            <span class="ms-2">THELWC ADMIN</span>
        </a>
        
        <ul class="sidebar-menu">
            {{-- 1. DASHBOARD & TỔNG QUAN --}}
            @if(in_array(Auth::user()->role, [1]))
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large text-primary"></i> Tổng quan
                </a>
            </li>
            @endif

            {{-- 5. QUẢN TRỊ VIÊN --}}
            @if(in_array(Auth::user()->role, [0, 1, 2]))
            <li class="menu-header">Quản trị viên</li>
            <li>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog text-dark"></i> Nhân sự & User
                </a>
            </li>
            @endif

            {{-- 2. QUẢN LÝ SẢN PHẨM & ĐƠN HÀNG --}}
            @if(in_array(Auth::user()->role, [0, 1, 2, 3, 4]))
                <li class="menu-header">Bán hàng</li>
                <li>
                    <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart text-danger"></i> Đơn hàng
                    </a>
                </li>
                <li>
                    <a href="{{ route('books.index') }}" class="{{ request()->routeIs('books.*') ? 'active' : '' }}">
                        <i class="fas fa-book text-primary"></i> Quản lý Sách
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags text-success"></i> Thể loại
                    </a>
                </li>
                <li>
                    <a href="{{ route('publishers.index') }}" class="{{ request()->routeIs('publishers.*') ? 'active' : '' }}">
                        <i class="fas fa-building text-warning"></i> Nhà xuất bản
                    </a>
                </li>
            @endif

            {{-- Banner --}}
            @if(in_array(Auth::user()->role, [0, 1, 2, 4]))
                <li>
                    <a href="{{ route('banners.index') }}" class="{{ request()->routeIs('banners.*') ? 'active' : '' }}">
                        <i class="fas fa-images text-info"></i> Quản lý Banner
                    </a>
                </li>
            @endif

            {{-- 3. HỆ THỐNG & MARKETING --}}
            @if(in_array(Auth::user()->role, [0,1, 2,4]))
            <li class="menu-header">Hệ thống & Marketing</li>
            <li>
                <a href="{{ route('vouchers.index') }}" class="{{ request()->routeIs('vouchers.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt text-warning"></i> Voucher
                </a>
            </li>
            <li>
                <a href="{{ route('admin.shipping_fee.index') }}" class="{{ request()->routeIs('admin.shipping_fee.*') ? 'active' : '' }}">
                    <i class="fas fa-cogs text-secondary"></i> Cấu hình chung
                </a>
            </li>
            @endif

            {{-- 4. NỘI DUNG & CỘNG ĐỒNG --}}
            @if(in_array(Auth::user()->role, [0, 1, 2, 4]))
            <li class="menu-header">Nội dung & Cộng đồng</li>
            <li>
                <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper text-info"></i> Tin tức
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fas fa-comments text-warning"></i> Đánh giá sách
                </a>
            </li>
            @endif
        </ul>
    </nav>

    {{-- 2. MAIN CONTENT --}}
    <div class="main-content">
        {{-- Top Navbar --}}
        <div class="top-navbar">
            <div class="d-flex justify-content-between align-items-center w-100">
                
                {{-- NÚT GỌI MENU VÀ TIÊU ĐỀ --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- Nút Hamburger hiển thị trên điện thoại --}}
                    <button class="btn btn-light d-lg-none shadow-sm rounded-circle d-flex align-items-center justify-content-center" 
                            id="toggleSidebarBtn" style="width: 40px; height: 40px;">
                        <i class="fas fa-bars text-dark"></i>
                    </button>
                    <h5 class="fw-bold mb-0 text-dark">
                        @yield('header', 'Dashboard')
                    </h5>
                </div>
            
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    
                    {{-- CHUÔNG THÔNG BÁO (BẢN FIX CĂN GIỮA MOBILE) --}}
@auth
@if(!in_array(Auth::user()->role, [0, 1]))
<style>
    /* Desktop: Hover để mở */
    @media (min-width: 992px) {
        .hover-dropdown:hover .dropdown-menu { 
            display: block; 
            margin-top: 0 !important; 
            animation: fadeIn 0.2s ease-in; 
        }
    }
    
    .hover-dropdown .dropdown-menu::before {
        content: ''; position: absolute; top: -15px; left: 0; width: 100%; height: 15px; background: transparent;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    /* 🔥 BÍ KÍP CĂN GIỮA TRÊN MOBILE 🔥 */
    @media (max-width: 991px) {
        /* Buộc thằng cha không giới hạn vị trí thằng con */
        .notification-dropdown { position: static !important; } 
        
        .notification-dropdown .dropdown-menu { 
            /* Ép menu nằm chính giữa màn hình, cách 2 mép 10px */
            left: 10px !important; 
            right: 10px !important;
            width: calc(100vw - 20px) !important;
            /* Xóa bỏ các lệnh căn chỉnh mặc định của Bootstrap làm lệch menu */
            transform: none !important; 
            top: 65px !important; 
            position: absolute !important;
        }
    }
</style>

<div class="dropdown hover-dropdown notification-dropdown">
    <a class="nav-link position-relative text-secondary px-2" 
       href="{{ route('notifications.index') }}" 
       id="notificationDropdown" 
       data-bs-toggle="dropdown" 
       data-bs-auto-close="outside"
       onclick="if(window.innerWidth < 992) { event.preventDefault(); }">
        
        <i class="fas fa-bell fs-5"></i>
        @if(Auth::user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" 
                  style="font-size: 0.6rem; transform: translate(-30%, -30%) !important;">
                {{ Auth::user()->unreadNotifications->count() }}
            </span>
        @endif
    </a>
    
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 rounded-4" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
        <li class="p-3 bg-dark text-white d-flex justify-content-between align-items-center sticky-top rounded-top-4">
            <h6 class="mb-0 fw-bold">Thông báo mới</h6>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <a href="{{ route('notifications.readAll') }}" class="text-white small text-decoration-none bg-secondary bg-opacity-25 px-2 py-1 rounded">Đọc tất cả</a>
            @endif
        </li>
        
        <div class="notification-list">
            @forelse(Auth::user()->notifications as $notification)
                <li>
                    <a href="{{ route('notifications.read', $notification->id) }}" class="dropdown-item p-3 border-bottom {{ $notification->read_at ? '' : 'bg-light' }}">
                        <div class="d-flex align-items-start">
                            <div class="me-3 mt-1"><i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} fs-4 text-primary"></i></div>
                            <div class="flex-grow-1">
                                <p class="mb-1 fw-bold small text-dark text-wrap" style="white-space: normal;">{{ $notification->data['title'] }}</p>
                                <p class="mb-1 small text-secondary text-wrap" style="line-height: 1.4; white-space: normal;">{{ $notification->data['message'] }}</p>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            @if(!$notification->read_at)
                                <span class="ms-2 p-1 bg-primary rounded-circle mt-2"></span>
                            @endif
                        </div>
                    </a>
                </li>
            @empty
                <li class="p-5 text-center text-muted">
                    <i class="far fa-bell-slash fs-1 mb-3 opacity-25"></i>
                    <p class="mb-0">Không có thông báo mới nào</p>
                </li>
            @endforelse
        </div>
        
        <li class="p-2 text-center bg-light sticky-bottom rounded-bottom-4 border-top">
            <a href="{{ route('notifications.index') }}" class="text-decoration-none small fw-bold text-primary d-block py-1">
                Xem tất cả thông báo
            </a>
        </li>                            
    </ul>
</div>
@endif
@endauth
                    {{-- Nút Đăng xuất (Tối ưu gọn trên Mobile) --}}
                    <div class="border-start ps-2 ps-md-3">
                        <a href="{{ route('logout') }}" 
                           class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center fw-bold px-2 px-md-3 py-2 rounded-pill shadow-sm"
                           style="transition: all 0.3s ease;"
                           onclick="event.preventDefault(); 
                                    if(confirm('⚠️ Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?')) { 
                                        document.getElementById('logout-form').submit(); 
                                    }">
                            <i class="fas fa-power-off me-0 me-md-2"></i> 
                            {{-- Giấu chữ Đăng xuất trên Mobile --}}
                            <span class="d-none d-md-inline">Đăng xuất</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>

                    {{-- Thông tin User --}}
                    <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 ps-2 ps-md-3 border-start text-decoration-none user-profile-link" style="transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset(Auth::user()->avatar) }}" 
                                class="rounded-circle shadow-sm" 
                                style="width: 38px; height: 38px; object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=2152ff&color=fff" 
                                class="rounded-circle shadow-sm" 
                                width="38" height="38">
                        @endif

                        <div class="d-none d-md-block">
                            <small class="d-block text-muted" style="font-size: 0.7rem; line-height: 1;">Xin chào,</small>
                            <span class="fw-bold text-dark">{{ Auth::user()->name }}</span>
                        </div>
                    </a>
                    
                </div>
            </div>
        </div>
        {{-- Nơi hiển thị nội dung các trang con --}}
        @yield('content')
    </div>

    {{-- 3. SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    {{-- SCRIPT XỬ LÝ MỞ/ĐÓNG MENU TRÊN MOBILE --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            // Bấm nút thì mở Menu và hiện Lớp màng đen
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                });
            }

            // Bấm ra ngoài lớp màng đen thì đóng Menu lại
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    this.classList.remove('show');
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>