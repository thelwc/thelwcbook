<style>
    /* ====== DESKTOP ====== */
    @media (min-width: 992px) {
        .mega-menu-wrapper {
            position: static !important;
        }

        .mega-menu-dropdown {
            width: 100% !important;
            left: 0 !important;
            right: 0 !important;
            transform: none !important;
        }
    }

    /* ====== MOBILE ====== */
    @media (max-width: 991.98px) {
        .mega-menu-wrapper {
            position: static !important;
        }

        .mobile-search-wrapper {
            position: relative !important;
            width: 100% !important;
            display: block !important;
        }

        .mobile-search-wrapper .dropdown-menu {
            position: absolute !important;
            /* inset: top right bottom left. Ép dính chặt lề trái phải */
            inset: 100% 0 auto 0 !important;
            transform: none !important;
            /* Phá vỡ tọa độ ảo của Bootstrap */
            width: 100% !important;
            max-width: 100% !important;
            margin: 8px 0 0 0 !important;
            box-sizing: border-box !important;
        }

        /* khi dropdown danh mục mở ra trên mobile */
        .mega-menu-wrapper>.dropdown-menu {
            position: static !important;
            float: none !important;
            width: 100% !important;
            margin-top: .5rem !important;
            /* 🔥 QUAN TRỌNG */
            max-height: 60vh;
            /* giới hạn chiều cao */
            overflow-y: auto;
            /* cho phép scroll */

            -webkit-overflow-scrolling: touch;
            /* mượt trên iOS */
            transform: none !important;
            box-shadow: none !important;
            border: 0 !important;
            background: transparent !important;
        }

        /* đẹp thanh scroll */
        .mega-menu-wrapper>.dropdown-menu::-webkit-scrollbar {
            width: 4px;
        }

        .mega-menu-wrapper>.dropdown-menu::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        .mega-menu-wrapper .container {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .mega-menu-wrapper .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .mega-menu-wrapper .col-6 {
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }

        .mega-menu-wrapper .col-lg-5 {
            display: none !important;
        }

        .mega-menu-wrapper .list-unstyled {
            margin-bottom: 0;
        }

        /* giãn khoảng cách cho dễ nhìn */
        .mega-menu-wrapper .list-unstyled li {
            margin-bottom: 10px !important;
        }

        /* chữ không xuống dòng */
        .mega-menu-wrapper .list-unstyled li a {
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }
    }

    /* hover desktop */
    @media (min-width: 992px) {
        .hover-dropdown:hover>.dropdown-menu {
            display: block;
            margin-top: 0;
            animation: fadeIn 0.2s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    }
</style>
<header id="header" class="site-header sticky-top">
    <nav id="header-nav" class="navbar navbar-expand-lg py-2 py-lg-3 bg-white shadow-sm">
        <div class="container">

            {{-- LOGO --}}
            <a class="navbar-brand me-lg-4" href="{{ route('home') }}">
                <h3 class="fw-bold mb-0" style="color: #212529;">Thelwc Books</h3>
            </a>

            {{-- MOBILE TOP ICONS --}}
            <div class="d-flex align-items-center d-lg-none gap-2">
                @auth
                {{-- THÔNG BÁO MOBILE --}}
                <div class="dropdown">
                    <a class="nav-link position-relative px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5 text-secondary"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; margin-top: 8px;">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mobile-dropdown-center p-0" aria-labelledby="notificationDropdownMobile">
                        <li class="p-3 bg-dark text-white rounded-top-4 small fw-bold d-flex justify-content-between align-items-center">
                            <span>Thông báo</span>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                            <a href="{{ route('notifications.readAll') }}" class="text-white small text-decoration-none">Đánh dấu đã đọc</a>
                            @endif
                        </li>

                        @forelse(Auth::user()->notifications as $notification)
                        <li>
                            <a href="{{ route('notifications.read', $notification->id) }}"
                                class="dropdown-item p-3 border-bottom {{ $notification->read_at ? '' : 'bg-light' }}">
                                <div class="d-flex align-items-start">
                                    <div class="me-3 mt-1">
                                        <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} fs-5 text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-bold small text-dark">
                                            {{ $notification->data['title'] ?? 'Thông báo' }}
                                        </p>
                                        <p class="mb-1 small text-secondary text-wrap">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    @if(!$notification->read_at)
                                    <span class="ms-auto p-1 bg-primary rounded-circle mt-2"></span>
                                    @endif
                                </div>
                            </a>
                        </li>
                        @empty
                        <li class="p-4 text-center text-muted">
                            <p class="mb-0 small">Không có thông báo mới nào</p>
                        </li>
                        @endforelse

                        <li class="p-2 text-center bg-light sticky-bottom rounded-bottom-4">
                            <a href="{{ route('notifications.index') }}" class="text-decoration-none small fw-bold text-dark d-block">
                                Xem tất cả
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- AVATAR MOBILE --}}
                <div class="dropdown">
                    <a class="nav-link px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.Auth::user()->name }}"
                            class="rounded-circle border"
                            style="width: 28px; height: 28px; object-fit: cover;">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mobile-dropdown-center p-3">
                        <li class="small fw-bold text-primary text-uppercase mb-2">{{ Auth::user()->name }}</li>
                        <li><a class="dropdown-item rounded-3 mb-1" href="{{ route('profile.edit') }}"><i class="fas fa-user-cog me-2"></i> Hồ sơ</a></li>
                        <li><a class="dropdown-item rounded-3 mb-1" href="{{ route('user.my_books') }}"><i class="fas fa-book-reader me-2"></i> Tủ sách</a></li>
                        <li><a class="dropdown-item rounded-3 mb-1" href="{{ route('client.account.history') }}"><i class="fas fa-history me-2"></i> Lịch sử mua hàng</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Thoát
                            </a>
                        </li>
                    </ul>
                </div>
                @else
                <a href="{{ route('login') }}" class="nav-link px-2">
                    <i class="fas fa-user fs-5 text-secondary"></i>
                </a>
                @endauth

                {{-- GIỎ HÀNG MOBILE --}}
                <a href="{{ route('cart.index') }}" class="position-relative px-2 text-dark">
                    <i class="fas fa-shopping-cart fs-5"></i>
                    @if(session('cart'))
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; margin-top: 8px;">
                        {{ array_sum(array_column(session('cart'), 'quantity')) }}
                    </span>
                    @endif
                </a>

                {{-- NÚT MENU --}}
                <button class="navbar-toggler border-0 p-1 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#bdNavbar">
                    <i class="fas fa-bars fs-4 text-dark"></i>
                </button>
            </div>

            {{-- MENU CHÍNH --}}
            <div class="collapse navbar-collapse" id="bdNavbar">

                {{-- NAV LINKS --}}
                <ul class="navbar-nav text-uppercase fw-bold mt-3 mt-lg-0 me-auto">
                    <li class="nav-item">
                        <a class="nav-link me-lg-4 active" href="{{ route('home') }}">Trang chủ</a>
                    </li>

                    {{-- Đoạn CSS này bạn có thể đặt ở trên cùng của file hoặc cho vào file .css riêng --}}


                    {{-- HTML DANH MỤC ĐÃ ĐƯỢC SỬA LỖI --}}
                    <li class="nav-item dropdown mega-menu-wrapper">
                        <a class="nav-link dropdown-toggle me-lg-4" href="#" data-bs-toggle="dropdown" data-bs-display="static">
                            Danh mục
                        </a>

                        <div class="dropdown-menu mega-menu bg-white shadow-lg border-0 rounded-bottom-4 mega-menu-dropdown">
                            <div class="container py-3">
                                <div class="row">
                                    <div class="col-lg-7 border-end-lg">
                                        <h6 class="text-primary mb-3 fw-bold">
                                            <i class="fas fa-book-open me-2"></i> Thể Loại Sách
                                        </h6>

                                        <div class="row">
                                            @foreach($categories_menu->chunk(ceil($categories_menu->count() / 2)) as $chunk)
                                            <div class="col-6">
                                                <ul class="list-unstyled">
                                                    @foreach($chunk as $cate)
                                                    <li class="mb-1">
                                                        <a href="{{ route('category.show', $cate->slug) }}" class="text-decoration-none text-dark d-block py-1 text-truncate small">
                                                            <i class="fas fa-caret-right text-muted me-1"></i> {{ $cate->name }}
                                                        </a>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-lg-5 d-none d-lg-block ps-4">
                                        <h6 class="text-danger mb-3 fw-bold">
                                            <i class="fas fa-fire me-2"></i> Xu Hướng
                                        </h6>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">
                                                <a href="{{ route('new.arrivals') }}" class="text-dark text-decoration-none fw-bold">
                                                    <span class="badge bg-danger me-2">HOT</span> Sách Mới
                                                </a>
                                            </li>
                                            <li class="mb-2">
                                                <a href="{{ route('flash.sale') }}" class="text-dark text-decoration-none fw-bold">
                                                    <span class="badge bg-warning text-dark me-2">SALE</span> Deal Sốc
                                                </a>
                                            </li>
                                            <li class="mb-2">
                                                <a href="{{ route('best.sellers') }}" class="text-dark text-decoration-none fw-bold">
                                                    <span class="badge bg-success me-2">TOP</span> Bán Chạy
                                                </a>
                                            </li>
                                        </ul>
                                        <img src="https://cdn0.fahasa.com/media/wysiwyg/Thang-01-2025/Lixi_Tet_310x210.jpg" class="w-100 rounded shadow-sm mt-2" style="height: 120px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link me-lg-4" href="{{ route('posts.index') }}">Tin tức</a>
                    </li>
                </ul>

                {{-- SEARCH: PC HIỆN NGANG, MOBILE NẰM TRONG MENU --}}
                <div class="d-flex align-items-center mt-3 mt-lg-0 flex-grow-1" style="min-width: 250px;">

                    {{-- Khung Form Tìm Kiếm --}}
                    <form action="{{ route('search') }}" method="GET" class="position-relative w-100 mobile-search-wrapper" id="searchForm">
                        <div class="position-relative">
                            <input type="text" name="keyword" id="searchInput" autocomplete="off" class="form-control rounded-pill bg-light border-0 ps-3 pe-5 py-2 shadow-sm"
                                placeholder="Tìm sách, tác giả..." style="font-size: 0.9rem;" value="{{ request('keyword') }}">

                            {{-- Nút Search thu nhỏ nằm lọt lòng bên phải --}}
                            <button class="btn btn-dark rounded-circle position-absolute top-50 translate-middle-y shadow-sm" type="submit"
                                style="right: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; z-index: 5; padding: 0;">
                                <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                            </button>
                        </div>

                        {{-- Khung hiển thị gợi ý tìm kiếm --}}
                        <div id="searchSuggestions" class="position-absolute w-100 bg-white shadow-lg rounded-3 mt-1 d-none"
                            style="z-index: 1050; max-height: 350px; overflow-y: auto; top: 100%; border: 1px solid #eee;"></div>
                    </form>

                    {{-- Nút Modal Bộ lọc nâng cao (cùng hàng) --}}
                    <button type="button" class="btn btn-light rounded-circle ms-2 shadow-sm border flex-shrink-0"
                        data-bs-toggle="modal" data-bs-target="#advancedSearchModal" title="Tìm kiếm nâng cao"
                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-sliders-h text-dark"></i>
                    </button>

                </div>

                {{-- ICONS DESKTOP --}}
                <div class="d-none d-lg-flex align-items-center ms-3 gap-3">
                    <a href="{{ route('favorites.index') }}" class="nav-link text-dark position-relative">
                        <i class="fas fa-heart fs-5 text-danger"></i>
                        @if(Auth::check() && Auth::user()->favorites()->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.6rem;">
                            {{ Auth::user()->favorites()->count() }}
                        </span>
                        @endif
                    </a>

                    @auth
                    <div class="dropdown hover-dropdown">
                        <a class="nav-link text-secondary position-relative p-0" href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell fs-5"></i>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.6rem;">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 p-0 rounded-4 noti-dropdown-fix">
                            {{-- HEADER (Cố định ở trên cùng) --}}
                            <li class="p-3 bg-dark text-white rounded-top-4 small fw-bold d-flex justify-content-between align-items-center position-sticky top-0" style="z-index: 10;">
                                <span>Thông báo</span>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                <a href="{{ route('notifications.readAll') }}" class="text-white small text-decoration-none">Đánh dấu đã đọc</a>
                                @endif
                            </li>

                            {{-- DANH SÁCH THÔNG BÁO (Giới hạn 5 tin bằng ->take(5)) --}}
                            @forelse(Auth::user()->notifications->take(5) as $notification)
                            <li>
                                <a href="{{ route('notifications.read', $notification->id) }}" class="dropdown-item p-3 border-bottom {{ $notification->read_at ? '' : 'bg-light' }}">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 mt-1">
                                            <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} fs-4 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 fw-bold small text-dark">{{ $notification->data['title'] ?? 'Thông báo' }}</p>
                                            <p class="mb-1 small text-secondary text-wrap text-break">{{ $notification->data['message'] ?? '' }}</p>
                                            <small class="text-muted" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(!$notification->read_at)
                                        <span class="ms-auto p-1 bg-primary rounded-circle mt-2"></span>
                                        @endif
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li class="p-4 text-center small text-muted">Không có thông báo mới nào</li>
                            @endforelse

                            {{-- FOOTER (Cố định ở dưới cùng) --}}
                            <li class="p-2 text-center bg-light position-sticky bottom-0 rounded-bottom-4" style="z-index: 10;">
                                <a href="{{ route('notifications.index') }}" class="text-decoration-none small fw-bold text-dark d-block">
                                    Xem tất cả
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endauth

                    @guest
                    <a href="{{ route('login') }}" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold">Đăng nhập</a>
                    @else
                    <div class="dropdown hover-dropdown">
                        <a class="nav-link p-0 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                            <span class="small fw-bold text-dark d-none d-xl-inline">{{ Auth::user()->name }}</span>
                            <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.Auth::user()->name }}"
                                class="rounded-circle border"
                                style="width: 35px; height: 35px; object-fit: cover;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2" style="min-width: 180px;">
                            <li>
                                <a class="dropdown-item rounded-3 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-cog text-primary"></i> Hồ sơ
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item rounded-3 d-flex align-items-center gap-2" href="{{ route('user.my_books') }}">
                                    <i class="fas fa-book-reader text-success"></i> Tủ sách
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item rounded-3 d-flex align-items-center gap-2" href="{{ route('client.account.history') }}">
                                    <i class="fas fa-history text-info"></i> Lịch sử mua hàng
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger fw-bold" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endguest

                    <div class="dropdown hover-dropdown">
                        <a href="{{ route('cart.index') }}" class="nav-link text-dark position-relative p-0">
                            <i class="fas fa-shopping-cart fs-5"></i>
                            @if(session('cart'))
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.6rem;">
                                {{ array_sum(array_column(session('cart'), 'quantity')) }}
                            </span>
                            @endif
                        </a>

                        {{-- Phần Dropdown hiển thị khi Hover --}}
                        <ul class="dropdown-menu shadow-lg border-0 rounded-4 p-0 cart-dropdown-fix overflow-hidden" style="min-width: 320px;">
                            {{-- Header giỏ hàng --}}
                            <li>
                                <div class="p-3 border-bottom d-flex justify-content-between bg-light">
                                    <h6 class="m-0 fw-bold">Giỏ hàng của bạn</h6>
                                </div>
                            </li>

                            @if(session('cart'))
                            {{-- Danh sách sản phẩm --}}
                            <li>
                                <div class="cart-item-scroll p-2" style="max-height: 300px; overflow-y: auto;">
                                    @php $total = 0; @endphp
                                    @foreach(session('cart') as $details)
                                    @php $total += $details['price'] * $details['quantity']; @endphp
                                    <div class="d-flex align-items-center mb-2 p-2 hover-bg rounded">
                                        <img src="{{ asset($details['image']) }}" width="50" height="70" class="rounded border object-fit-cover">
                                        <div class="ms-3">
                                            <h6 class="mb-0 small fw-bold text-truncate" style="max-width: 180px;">{{ $details['title'] ?? $details['name'] }}</h6>
                                            <small class="text-secondary">x{{ $details['quantity'] }}</small>
                                            <span class="text-danger fw-bold small ms-2">{{ number_format($details['price']) }}đ</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </li>

                            {{-- Footer tổng tiền & nút xem giỏ --}}
                            <li>
                                <div class="p-3 bg-light border-top text-center">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold small text-dark">Tổng cộng:</span>
                                        <span class="fw-bold text-danger fs-6">{{ number_format($total) }}đ</span>
                                    </div>
                                    <a href="{{ route('cart.index') }}" class="btn btn-danger w-100 rounded-pill fw-bold btn-sm">
                                        Xem giỏ hàng
                                    </a>
                                </div>
                            </li>
                            @else
                            {{-- Trạng thái giỏ hàng trống --}}
                            <li>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-basket fa-2x text-muted mb-2 opacity-50"></i>
                                    <p class="small text-muted mb-0">Giỏ hàng của bạn đang trống</p>
                                </div>
                            </li>
                            <li class="p-3 pt-0">
                                <a href="{{ route('cart.index') }}" class="btn btn-danger w-100 rounded-pill fw-bold btn-sm">
                                    Đi đến giỏ hàng
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
    @media (max-width: 991px) {
        .mobile-dropdown-center {
            position: fixed !important;
            top: 70px !important;
            left: 10px !important;
            right: 10px !important;
            width: calc(100vw - 20px) !important;
            transform: none !important;
            z-index: 2000;
            max-height: 70vh;
            overflow-y: auto;
        }

        .mobile-dropdown-center .dropdown-item {
            white-space: normal !important;
        }

        .navbar-collapse {
            background: #fff;
            padding: 20px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
    }

    @media (min-width: 992px) {
        .hover-dropdown:hover>.dropdown-menu {
            display: block;
            margin-top: 0;
            animation: fadeIn 0.2s;
        }

        .cart-dropdown-fix {
            right: -10px !important;
            /* Kéo lệch sang phải một xíu cho cân giữa icon */
            left: auto !important;
            /* Hủy căn trái mặc định */
        }

        /* Thêm CSS cho bảng thông báo PC */
        .noti-dropdown-fix {
            width: 290px !important;
            /* Làm bảng nhỏ lại xíu */
            max-height: 400px;
            /* Giới hạn chiều cao để bật thanh cuộn */
            overflow-y: auto;
            /* Bật thanh cuộn dọc */
            overflow-x: hidden;
            right: -90px !important;
            /* Kéo hộp sang trái để canh giữa icon chuông */
            left: auto !important;
        }

        /* Custom thanh cuộn cho đẹp và gọn */
        .noti-dropdown-fix::-webkit-scrollbar {
            width: 5px;
        }

        .noti-dropdown-fix::-webkit-scrollbar-track {
            background: transparent;
        }

        .noti-dropdown-fix::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 10px;
        }

        .noti-dropdown-fix::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;
        }

        /* Vùng đệm hover chống bị tắt menu ngang */
        .noti-dropdown-fix::before {
            content: "";
            position: absolute;
            top: -15px;
            left: 0;
            width: 100%;
            height: 15px;
            background: transparent;
        }

        /* Mở rộng vùng hover phía trên một chút để lúc di chuột từ icon xuống menu không bị tắt ngang */
        .cart-dropdown-fix::before {
            content: "";
            position: absolute;
            top: -15px;
            left: 0;
            width: 100%;
            height: 15px;
            background: transparent;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    }

    .hover-suggest:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
</style>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let searchTimeout = null;
        const baseUrl = "{{ asset('') }}";

        $('#searchInput').on('keyup', function() {
            clearTimeout(searchTimeout);

            let query = $(this).val();
            let resultBox = $('#searchSuggestions');

            if (query.length > 1) {
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('ajax.search') }}",
                        type: "GET",
                        data: {
                            keyword: query
                        },
                        success: function(data) {
                            try {
                                resultBox.empty();

                                if (data.length > 0) {
                                    resultBox.removeClass('d-none');

                                    data.forEach(function(book) {
                                        let pubName = (book.publisher && book.publisher.name) ? book.publisher.name : 'Chưa cập nhật';
                                        let pubYear = book.published_date ? new Date(book.published_date).getFullYear() : '---';
                                        let authorName = book.author ? book.author : 'Đang cập nhật';
                                        let bookTitle = book.title ? book.title : 'Đang cập nhật';
                                        let bookUrl = baseUrl + "book/" + book.id;

                                        let finalImageUrl = 'https://via.placeholder.com/300x450?text=No+Image';
                                        if (book.image) {
                                            if (book.image.startsWith('http')) {
                                                finalImageUrl = book.image;
                                            } else {
                                                let imgPath = book.image;
                                                if (!imgPath.includes('uploads')) {
                                                    imgPath = 'uploads/' + imgPath;
                                                }
                                                finalImageUrl = baseUrl + imgPath.replace(/^\//, '');
                                            }
                                        }

                                        let priceGoc = parseFloat(book.price) || 0;
                                        let priceKhuyenMai = parseFloat(book.sale_price) || 0;
                                        let priceHtml = '';

                                        if (priceKhuyenMai > 0 && priceKhuyenMai < priceGoc) {
                                            priceHtml = `
                                                <div class="text-danger fw-bold small" style="font-size: 0.9rem;">${new Intl.NumberFormat('vi-VN').format(priceKhuyenMai)}đ</div>
                                                <div class="text-muted text-decoration-line-through" style="font-size: 0.7rem;">${new Intl.NumberFormat('vi-VN').format(priceGoc)}đ</div>
                                            `;
                                        } else {
                                            priceHtml = `<div class="text-danger fw-bold small" style="font-size: 0.9rem;">${new Intl.NumberFormat('vi-VN').format(priceGoc)}đ</div>`;
                                        }

                                        let html = `
                                            <a href="${bookUrl}" class="d-flex align-items-center p-3 text-decoration-none text-dark border-bottom hover-suggest transition-all">
                                                <div class="me-3 flex-shrink-0">
                                                    <img src="${finalImageUrl}" class="rounded shadow-sm border bg-light" style="width: 45px; height: 60px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x450?text=Error';">
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden pe-3">
                                                    <div class="fw-bold text-primary mb-1 text-truncate" style="font-size: 0.95rem;">${bookTitle}</div>
                                                    <div class="small text-muted d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.75rem;">
                                                        <span title="Tác giả" class="text-truncate" style="max-width: 120px;"><i class="fas fa-pen-nib me-1"></i>${authorName}</span>
                                                        <span class="text-secondary opacity-50">|</span>
                                                        <span title="Nhà xuất bản" class="text-truncate" style="max-width: 150px;"><i class="fas fa-building me-1"></i>${pubName}</span>
                                                        <span class="text-secondary opacity-50">|</span>
                                                        <span title="Năm xuất bản"><i class="far fa-calendar-alt me-1"></i>${pubYear}</span>
                                                    </div>
                                                </div>
                                                <div class="ms-auto text-end flex-shrink-0">
                                                    ${priceHtml}
                                                </div>
                                            </a>
                                        `;
                                        resultBox.append(html);
                                    });
                                } else {
                                    resultBox.removeClass('d-none');
                                    resultBox.html(`
                                        <div class="p-4 text-center text-muted">
                                            <i class="fas fa-search-minus fa-2x mb-2 opacity-25"></i>
                                            <div class="small fw-bold">Không tìm thấy sách phù hợp!</div>
                                            <div style="font-size: 0.75rem;">Thử gõ từ khóa khác xem sao...</div>
                                        </div>
                                    `);
                                }
                            } catch (error) {
                                console.error("Lỗi khi vẽ HTML:", error);
                            }
                        },
                        error: function(xhr) {
                            console.error("Lỗi Backend:", xhr.responseText);
                        }
                    });
                }, 300);
            } else {
                resultBox.addClass('d-none');
            }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#searchForm').length) {
                $('#searchSuggestions').addClass('d-none');
            }
        });
    });
</script>
{{-- ===================== MODAL TÌM KIẾM NÂNG CAO ===================== --}}
<div class="modal fade" id="advancedSearchModal" tabindex="-1" aria-labelledby="advancedSearchLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            {{-- Header --}}
            <div class="modal-header bg-light border-0 rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="advancedSearchLabel">
                    <i class="fas fa-filter text-primary me-2"></i> Bộ Lọc Tìm Kiếm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-4">
                <form action="{{ route('search') }}" method="GET">

                    {{-- 1. Từ khóa --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Từ khóa (Tên sách, tác giả...)</label>
                        <input type="text" name="keyword" class="form-control" placeholder="Nhập từ khóa..." value="{{ request('keyword') }}">
                    </div>

                    {{-- 2. Khoảng giá --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Khoảng giá (VNĐ)</label>
                        <div class="input-group">
                            <input type="number" name="min_price" class="form-control" placeholder="Tối thiểu" value="{{ request('min_price') }}" min="0">
                            <span class="input-group-text bg-white border-start-0 border-end-0 text-muted">-</span>
                            <input type="number" name="max_price" class="form-control" placeholder="Tối đa" value="{{ request('max_price') }}" min="0">
                        </div>
                    </div>

                    {{-- 3. Thể loại (Checkbox cuộn) --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Thể loại sách (Có thể chọn nhiều)</label>
                        <div class="border rounded-3 p-2 bg-white shadow-sm" style="max-height: 160px; overflow-y: auto;">
                            @foreach($categories_menu as $cate)
                            <div class="form-check mb-1">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="category_id[]" value="{{ $cate->id }}" id="cat_{{ $cate->id }}"
                                    {{ is_array(request('category_id')) && in_array($cate->id, request('category_id')) ? 'checked' : '' }}>
                                <label class="form-check-label cursor-pointer" for="cat_{{ $cate->id }}" style="font-size: 0.9rem;">
                                    {{ $cate->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Phân cột cho Loại bìa & Nguồn gốc --}}
                    <div class="row">
                        {{-- 4. Loại bìa --}}
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-secondary d-block">Hình thức bìa</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="cover_type[]" value="Mềm" id="coverSoft" {{ is_array(request('cover_type')) && in_array('Mềm', request('cover_type')) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="coverSoft">Bìa Mềm</label>
                            </div>
                            <div class="form-check form-check-inline mt-1">
                                <input class="form-check-input" type="checkbox" name="cover_type[]" value="Cứng" id="coverHard" {{ is_array(request('cover_type')) && in_array('Cứng', request('cover_type')) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="coverHard">Bìa Cứng</label>
                            </div>
                        </div>

                        {{-- 5. Nguồn gốc --}}
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-secondary d-block">Nguồn gốc</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="is_foreign[]" value="0" id="originLocal" {{ is_array(request('is_foreign')) && in_array('0', request('is_foreign')) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="originLocal">Trong nước</label>
                            </div>
                            <div class="form-check form-check-inline mt-1">
                                <input class="form-check-input" type="checkbox" name="is_foreign[]" value="1" id="originForeign" {{ is_array(request('is_foreign')) && in_array('1', request('is_foreign')) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="originForeign">Nước ngoài</label>
                            </div>
                        </div>
                    </div> {{-- Đóng row --}}

                    {{-- Nút Submit --}}
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-dark rounded-pill fw-bold py-2">
                            <i class="fas fa-search me-2"></i> ÁP DỤNG LỌC
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
{{-- ========================================================= --}}