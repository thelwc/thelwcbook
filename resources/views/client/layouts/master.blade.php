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

        /* 🔥 CSS NÚT BACK TO TOP (Đã fix lỗi che nút Gửi) 🔥 */
        #btnBackToTop {
            position: fixed;
            bottom: 27px; /* Căn giữa dọc cho bằng với nút Chatbot (60px) */
            right: 100px; /* Đẩy sang trái để nhường chỗ cho khung Chatbot */
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
            z-index: 9998; /* Để thấp hơn z-index của khung chat một xíu */
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

        /* ========================================== */
        /* 🔥 CSS TÙY CHỈNH THELWC AI CHATBOT 🔥 */
        /* ========================================== */
        #chat-btn {
            position: fixed; bottom: 20px; right: 20px; z-index: 9999;
            width: 60px; height: 60px; border-radius: 50%;
            background-color: #212529; color: #ffffff; /* Nút chính nền tối, chữ trắng */
            display: flex; justify-content: center; align-items: center;
            font-size: 24px; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        #chat-btn:hover { 
            background-color: #c5a992; /* Hover màu nổi bật */
            transform: translateY(-3px) scale(1.05); 
        }

        #chat-window {
            position: fixed; bottom: 90px; right: 20px; z-index: 9999;
            width: 360px; height: 500px; max-width: 90vw; max-height: 80vh; /* Responsive Mobile */
            background-color: #ffffff;
            border-radius: 12px; /* Bo góc chuẩn BS5 */
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: none; flex-direction: column; overflow: hidden;
            font-family: 'Nunito', sans-serif; /* Đồng bộ font toàn form */
            border: 1px solid #e0e0e0;
        }
        
        .chat-header {
            background-color: #212529; color: #ffffff; padding: 15px 20px;
            font-weight: 700; display: flex; justify-content: space-between; align-items: center;
        }
        .chat-header span { font-size: 1.05rem; }
        .chat-header i.close-btn { cursor: pointer; font-size: 1.2rem; transition: color 0.2s; }
        .chat-header i.close-btn:hover { color: #dc3545; } /* Đỏ cảnh báo khi hover nút Tắt */

        .chat-body {
            flex: 1; padding: 20px; overflow-y: auto; 
            background-color: #f8f9fa; /* Nền sáng dịu mắt */
            display: flex; flex-direction: column; gap: 15px;
        }
        .chat-body::-webkit-scrollbar { width: 6px; }
        .chat-body::-webkit-scrollbar-thumb { background: #c5a992; border-radius: 10px; }
        
        .msg { 
            max-width: 85%; padding: 12px 16px; border-radius: 12px; 
            font-size: 0.95rem; line-height: 1.5; word-wrap: break-word;
        }
        /* Style cho link (đường dẫn sách) trong chat */
        .msg a { color: #0d6efd; font-weight: 600; text-decoration: underline; transition: all 0.2s;}
        .msg a:hover { color: #c5a992 !important; }

        .msg-bot { 
            background-color: #ffffff; color: #212529; /* Chữ chính */
            align-self: flex-start; border-bottom-left-radius: 4px; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.05); border: 1px solid #e0e0e0;
        }
        .msg-user { 
            background-color: #212529; color: #ffffff; /* Tin nhắn khách đồng màu nút chính */
            align-self: flex-end; border-bottom-right-radius: 4px; 
            font-weight: 500; box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .chat-footer {
            padding: 15px; background-color: #ffffff; border-top: 1px solid #e0e0e0;
            display: flex; gap: 10px; align-items: center;
        }
        .chat-footer input {
            flex: 1; padding: 12px 18px; border: 1px solid #ced4da; 
            border-radius: 25px; outline: none; transition: all 0.2s;
            font-family: 'Nunito', sans-serif; font-size: 0.95rem; color: #212529;
            background-color: #f8f9fa;
        }
        .chat-footer input::placeholder { color: #6c757d; } /* Chữ phụ */
        .chat-footer input:focus { 
            border-color: #c5a992; background-color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(197, 169, 146, 0.25); /* Focus ring mềm mại */
        }
        .chat-footer button {
            background-color: #212529; color: #ffffff; border: none;
            width: 45px; height: 45px; border-radius: 50%; cursor: pointer;
            display: flex; justify-content: center; align-items: center; 
            transition: all 0.2s ease; font-size: 1.1rem;
        }
        .chat-footer button:hover { 
            background-color: #c5a992; transform: translateY(-2px);
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
    {{-- 🤖 AI CHATBOT THELWC BOOKS (TỰ CODE) --}}
    {{-- ========================================== --}}
    <div id="chat-btn"><i class="fas fa-comment-dots"></i></div>

    <div id="chat-window">
        <div class="chat-header">
            <span><i class="fas fa-robot me-2" style="color: #c5a992;"></i> Thelwc AI</span>
            <i class="fas fa-times close-btn" onclick="toggleChat()" title="Đóng"></i>
        </div>
        <div class="chat-body" id="chat-box">
            <div class="msg msg-bot">Chào sếp! Tớ là trợ lý AI thông minh của Thelwc Books. Sếp cần tìm sách gì hay có câu hỏi nào hôm nay không? 📚</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="chat-input" placeholder="Nhập câu hỏi của sếp..." onkeypress="handleEnter(event)">
            <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
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

    {{-- 🧠 SCRIPT: XỬ LÝ THELWC AI VÀ BACK TO TOP --}}
    <script>
        // 1. CHỨC NĂNG BACK TO TOP
        const btnBackToTop = document.getElementById("btnBackToTop");
        
        window.onscroll = function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                btnBackToTop.classList.add("show");
            } else {
                btnBackToTop.classList.remove("show");
            }
        };
        
        btnBackToTop.addEventListener("click", function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // 2. CHỨC NĂNG THELWC AI CHATBOT
        const chatBtn = document.getElementById('chat-btn');
        const chatWindow = document.getElementById('chat-window');
        const chatBox = document.getElementById('chat-box');
        const chatInput = document.getElementById('chat-input');

        // Mở/Đóng chat
        chatBtn.addEventListener('click', toggleChat);
        function toggleChat() {
            chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
            if(chatWindow.style.display === 'flex') {
                chatInput.focus();
            }
        }

        // Bắt sự kiện phím Enter
        function handleEnter(e) {
            if (e.key === 'Enter') sendMessage();
        }

        // Gửi tin nhắn
        async function sendMessage() {
            const text = chatInput.value.trim();
            if (!text) return;

            // In tin nhắn của User ra
            appendMessage(text, 'msg-user');
            chatInput.value = '';

            // Hiện trạng thái Bot đang gõ
            const loadingId = 'loading-' + Date.now();
            appendMessage('<i class="fas fa-ellipsis-h fa-fade"></i> Đang suy nghĩ...', 'msg-bot', loadingId);

            // Gửi dữ liệu qua Controller Laravel
            try {
                const response = await fetch('{{ route("chatbot.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Lấy token để bảo mật Form
                    },
                    body: JSON.stringify({ message: text })
                });
                
                const data = await response.json();
                
                // Xóa chữ Đang gõ...
                const loadingMsg = document.getElementById(loadingId);
                if(loadingMsg) loadingMsg.remove();
                
                // Format văn bản từ Markdown sang HTML (xuống dòng và in đậm)
                let formattedReply = data.reply.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>').replace(/\n/g, '<br>');
                appendMessage(formattedReply, 'msg-bot');

            } catch (error) {
                const loadingMsg = document.getElementById(loadingId);
                if(loadingMsg) loadingMsg.remove();
                appendMessage('Oops! Hình như đứt cáp mạng rồi sếp ơi, thử lại xíu nhé!', 'msg-bot');
            }
        }

        // Hàm in tin nhắn và cuộn xuống cuối
        function appendMessage(text, className, id = '') {
            const div = document.createElement('div');
            div.className = `msg ${className}`;
            div.innerHTML = text;
            if (id) div.id = id;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</body>
</html>