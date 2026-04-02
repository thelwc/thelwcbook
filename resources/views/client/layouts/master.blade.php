<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Thelwc - Cửa hàng sách Thelwc')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    
    <style>
        /* GLOBAL STYLE */
        body { font-family: 'Nunito', sans-serif; color: #212529; background-color: #f8f9fa; }
        a { text-decoration: none; color: inherit; transition: all 0.2s ease; }
        a:hover { color: #c5a992 !important; }

        /* CARD STYLE */
        .card { border: 1px solid #e0e0e0; transition: transform 0.3s, box-shadow 0.3s; background-color: #ffffff; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); border-color: #c5a992 !important; }
        .card-img-custom { width: 100%; aspect-ratio: 2/3; object-fit: cover; }

        /* COLORS */
        .text-primary { color: #0d6efd !important; }
        .text-secondary { color: #6c757d !important; }
        .text-danger { color: #dc3545 !important; }
        .bg-light { background-color: #f8f9fa !important; }
        .bg-white { background-color: #ffffff !important; }

        /* BUTTONS */
        .btn-dark { background-color: #212529; border-color: #212529; color: #ffffff; }
        .btn-dark:hover { background-color: #000000; border-color: #000000; color: #c5a992; }
        .btn-outline-dark { color: #212529; border-color: #212529; }
        .btn-outline-dark:hover { background-color: #212529; color: #ffffff !important; }

        /* NOTIFICATION & MENU */
        .badge.bg-primary { background-color: #0d6efd !important; color: #ffffff !important; }
        .user-dropdown, .cart-dropdown, .notification-dropdown { position: relative; height: 100%; display: flex; align-items: center; }
        .user-dropdown.logged-in:hover .dropdown-menu, 
        .cart-dropdown:hover .dropdown-menu,
        .notification-dropdown:hover .dropdown-menu { 
            display: block !important; margin-top: 0; top: 100%; right: 0; left: auto; animation: fadeIn 0.2s ease; 
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-toggle::after { display: none; }
        .cart-dropdown .dropdown-menu { min-width: 320px; padding: 0; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden; background-color: #ffffff; }
        .cart-item-scroll { max-height: 300px; overflow-y: auto; }
        .cart-item-scroll::-webkit-scrollbar { width: 5px; }
        .cart-item-scroll::-webkit-scrollbar-thumb { background: #c5a992; border-radius: 10px; }
        .hover-bg:hover { background-color: #f8f9fa; }

        /* CSS RIÊNG CHO SWIPER NÚT ĐỎ */
        .swiper-button-next, .swiper-button-prev { color: #dc3545; background: rgba(255,255,255,0.8); width: 40px; height: 40px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .swiper-button-next::after, .swiper-button-prev::after { font-size: 18px; font-weight: bold; }
        .swiper-button-next:hover, .swiper-button-prev:hover { background: #dc3545; color: #fff; }

        /* --- MEGA MENU CSS --- */
        .dropdown-menu.mega-menu {
            width: 90vw; max-width: 1200px; left: 50% !important; transform: translateX(-50%) !important;
            padding: 30px; border-radius: 0 0 12px 12px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.1); margin-top: 0;
        }
        @media (min-width: 992px) {
            .nav-item.dropdown:hover .dropdown-menu.mega-menu { display: block; animation: slideUp 0.3s ease; }
        }
        @keyframes slideUp { from { opacity: 0; transform: translate(-50%, 20px); } to { opacity: 1; transform: translate(-50%, 0); } }
        .mega-title { font-weight: 800; color: #212529; text-transform: uppercase; margin-bottom: 15px; font-size: 1rem; padding-bottom: 10px; border-bottom: 2px solid #dc3545; display: inline-block; }
        .mega-list { list-style: none; padding: 0; }
        .mega-list li { margin-bottom: 8px; }
        .mega-list li a { color: #6c757d; font-size: 0.95rem; font-weight: 600; transition: all 0.2s; display: block; }
        .mega-list li a:hover { color: #dc3545 !important; transform: translateX(5px); }
        .highlight-item a { color: #dc3545 !important; font-weight: 800 !important; }
        .highlight-item a:hover { text-decoration: underline; }

        /* 🔥 CSS NÚT BACK TO TOP (Đè ngay trên Chatbot) 🔥 */
        #btnBackToTop {
            position: fixed;
            bottom: 90px; /* Nâng lên 90px để né con Bot ở 20px */
            right: 20px;  /* Thẳng hàng với con Bot */
            background-color: #212529;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            cursor: pointer;
            z-index: 99998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        #btnBackToTop:hover {
            background-color: #c5a992;
            transform: translateY(-3px);
        }
        #btnBackToTop.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* 🔥 CSS TÙY CHỈNH MÀU SẮC CHATBOT 🔥 */
        df-messenger {
            /* Tông màu Đen nhám và Nâu tây đồng bộ layout */
            --df-messenger-button-titlebar-color: #212529; 
            --df-messenger-button-titlebar-font-color: #ffffff;
            --df-messenger-chat-background-color: #f8f9fa; 
            --df-messenger-font-color: #212529;
            --df-messenger-send-icon: #c5a992; 
            
            --df-messenger-bot-message: #ffffff;
            --df-messenger-user-message: #ebdcd0; 
            
            z-index: 99999;
        }
    </style>
    
    @yield('styles')
</head>
<body>

    @include('client.layouts.header')
    @yield('content')
    @include('client.layouts.footer')

    {{-- 🔥 NÚT BACK TO TOP 🔥 --}}
    <div id="btnBackToTop" title="Lên đầu trang">
        <i class="fas fa-arrow-up"></i>
    </div>

    {{-- ========================================== --}}
    {{-- 🔥 ĐOẠN CODE HIỂN THỊ POPUP THÔNG BÁO 🔥 --}}
    {{-- ========================================== --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(Session::has('warning'))
                Swal.fire({
                    icon: 'warning', title: 'Lưu ý nhỏ!', text: '{!! Session::get("warning") !!}',
                    confirmButtonColor: '#ffc107', confirmButtonText: 'Tôi hiểu, tiếp tục mua'
                });
            @endif

            @if(Session::has('success'))
                Swal.fire({
                    icon: 'success', title: 'Thành công!', text: '{!! Session::get("success") !!}',
                    showConfirmButton: false, timer: 1500
                });
            @endif

            @if(Session::has('error'))
                Swal.fire({
                    icon: 'error', title: 'Thất bại!', text: '{!! Session::get("error") !!}',
                    confirmButtonColor: '#dc3545'
                });
            @endif
        });
    </script>

    @yield('scripts')

    {{-- ========================================== --}}
    {{-- 🤖 CHATBOT THELWC BOOKS (CỐ ĐỊNH GÓC DƯỚI) --}}
    {{-- ========================================== --}}
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <df-messenger
        intent="WELCOME"
        chat-title="Hỗ trợ Thelwc Books"
        agent-id="5248c276-64b6-46c1-9489-da9e979534e1"
        language-code="vi"
        chat-icon="https://cdn-icons-png.flaticon.com/512/4712/4712010.png" 
    ></df-messenger>

    {{-- 🧠 SCRIPT: CLICK RA NGOÀI ĐÓNG BOT + XỬ LÝ NÚT BACK TO TOP --}}
    <script>
        // 1. TỰ ĐỘNG ĐÓNG BOT KHI CLICK RA NGOÀI
        window.addEventListener('click', function(e) {
            const dfMessenger = document.querySelector('df-messenger');
            if (dfMessenger) {
                if (!dfMessenger.contains(e.target)) {
                    dfMessenger.removeAttribute('expand');
                }
            }
        });

        // 2. CHỨC NĂNG BACK TO TOP
        const btnBackToTop = document.getElementById("btnBackToTop");
        
        // Hiện nút khi cuộn xuống 300px
        window.onscroll = function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                btnBackToTop.classList.add("show");
            } else {
                btnBackToTop.classList.remove("show");
            }
        };
        
        // Bấm nút thì cuộn mượt lên trên
        btnBackToTop.addEventListener("click", function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>