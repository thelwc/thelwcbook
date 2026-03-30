<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang đọc: {{ $book->title }}</title>
    
    {{-- 🔥 ĐÃ BỔ SUNG BOOTSTRAP ĐỂ CÁC CLASS D-NONE HOẠT ĐỘNG 🔥 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&family=Nunito:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* MÀU SẮC */
            --bg-color: #ffffff;
            --text-color: #2c3e50;
            --ui-bg: #f8f9fa;
            --ui-border: #e9ecef;
            --accent: #3b82f6;
            
            /* CẤU HÌNH */
            --gap: 60px; /* Khoảng cách giữa 2 trang */
            --font-size: 18px;
            --font-family: 'Merriweather', serif;
        }

        /* THEMES */
        body.theme-sepia { --bg-color: #f4ecd8; --text-color: #5b4636; --ui-bg: #eaddcf; --ui-border: #d3c4b1; }
        body.theme-dark  { --bg-color: #1a1a1a; --text-color: #d1d5db; --ui-bg: #222; --ui-border: #333; }

        body {
            margin: 0; padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Nunito', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex; flex-direction: column;
        }

        /* HEADER */
        .reader-header {
            height: 60px; flex-shrink: 0;
            display: flex; justify-content: space-between; align-items: center;
            padding: 0 20px;
            background-color: var(--ui-bg); border-bottom: 1px solid var(--ui-border);
            z-index: 100;
        }
        .back-btn { text-decoration: none; color: var(--text-color); font-weight: 700; opacity: 0.8; display: flex; align-items: center; gap: 5px; }
        
        /* 🔥 FIX LỖI ÉP CHỮ TRÊN MOBILE 🔥 */
        .settings-bar { 
            display: flex; gap: 8px; align-items: center; 
            overflow-x: auto; /* Cho phép vuốt ngang nếu màn hình quá nhỏ */
            scrollbar-width: none; /* Ẩn thanh cuộn Firefox */
        }
        .settings-bar::-webkit-scrollbar { display: none; } /* Ẩn thanh cuộn Chrome */
        
        .tool-btn { 
            background: none; border: 1px solid var(--ui-border); cursor: pointer; 
            color: var(--text-color); padding: 5px 10px; border-radius: 4px; 
            white-space: nowrap; flex-shrink: 0;
        }
        .theme-dot { 
            width: 24px; height: 24px; border-radius: 50%; cursor: pointer; 
            border: 1px solid #999; display: inline-block; margin-left: 5px; flex-shrink: 0;
        }
        .theme-dot.light { background: #fff; } .theme-dot.sepia { background: #f4ecd8; } .theme-dot.dark { background: #1a1a1a; }

        /* --- CONTAINER BAO NGOÀI (TẠO LỀ) --- */
        #reader-container {
            flex-grow: 1;
            position: relative;
            overflow: hidden; 
            width: 100%;
            /* TẠO LỀ TRÁI PHẢI BẰNG PADDING */
            padding: 40px 12%; 
            box-sizing: border-box;
        }

        /* --- KHUNG CHỨA NỘI DUNG (KHÔNG PADDING) --- */
        #content-scroller {
            height: 100%;
            width: 100%; 
            overflow-x: scroll;     
            overflow-y: hidden;     
            scroll-behavior: smooth; 
            
            /* Chia cột */
            column-fill: auto;
            column-gap: var(--gap);
            
            font-family: var(--font-family);
            font-size: var(--font-size);
            line-height: 1.8;
            text-align: justify;
            
            /* Ẩn thanh cuộn */
            scrollbar-width: none; 
            -ms-overflow-style: none;
        }
        #content-scroller::-webkit-scrollbar { display: none; }

        /* CSS NỘI DUNG */
        #content-scroller p, h1, h2, h3, li { margin-bottom: 1em; break-inside: avoid; }
        #content-scroller img {
            max-width: 100%; height: auto; 
            display: block; margin: 10px auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* NÚT ĐIỀU HƯỚNG */
        .nav-overlay {
            position: absolute; top: 0; bottom: 0; width: 80px; z-index: 50;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--text-color); opacity: 0; transition: 0.2s;
        }
        .nav-overlay:hover { opacity: 1; background: rgba(0,0,0,0.05); }
        .nav-prev { left: 0; }
        .nav-next { right: 0; }

        /* FOOTER */
        .reader-footer {
            height: 40px; flex-shrink: 0;
            background-color: var(--ui-bg); border-top: 1px solid var(--ui-border);
            display: flex; align-items: center; justify-content: space-between; padding: 0 20px; font-size: 0.85rem;
        }
        input[type=range] { flex-grow: 1; margin: 0 20px; cursor: pointer; }

        /* SIDEBAR */
        .sidebar {
            width: 320px; background-color: var(--ui-bg); border-right: 1px solid var(--ui-border);
            padding: 30px; display: flex; flex-direction: column; z-index: 200; flex-shrink: 0;
        }
        .book-cover { width: 140px; height: 210px; object-fit: cover; border-radius: 6px; margin: 0 auto 20px; display: block; border: 1px solid #ccc;}
        .book-title { font-weight: 800; text-align: center; margin-bottom: 5px; color: var(--text-color); }
        .book-author { text-align: center; color: var(--text-color); opacity: 0.7; font-size: 0.9rem; }

        @media (max-width: 900px) { 
            .sidebar { display: none; } 
            #reader-container { padding: 20px 20px; } 
        }
    </style>
</head>
<body class="theme-light">

    <div style="display: flex; height: 100%;">
        <aside class="sidebar">
            <a href="{{ route('book.detail', $book->id) }}" class="back-btn" style="margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Thoát</a>
            @if($book->image)
                <img src="{{ asset(str_contains($book->image, 'uploads') ? $book->image : 'uploads/' . $book->image) }}" class="book-cover">
            @endif
            <div class="book-title">{{ $book->title }}</div>
            <div class="book-author">{{ $book->author }}</div>
        </aside>

        <div style="flex-grow: 1; display: flex; flex-direction: column; width: 100%; overflow: hidden;">
            {{-- Header --}}
            <div class="reader-header">
                {{-- Nút Back cho Mobile (Đã sửa lỗi margin) --}}
                <div class="d-md-none flex-shrink-0 me-3">
                    <a href="{{ route('book.detail', $book->id) }}" class="back-btn m-0" style="margin-bottom: 0;"><i class="fas fa-arrow-left fs-5"></i></a>
                </div>
                
                {{-- Tiêu đề cho PC --}}
                <div class="fw-bold d-none d-md-block text-muted flex-shrink-0 me-3 text-nowrap">Bản đọc văn bản</div>
                
                {{-- Thanh công cụ --}}
                <div class="settings-bar ms-auto">
                    <button class="tool-btn" onclick="setFont('Merriweather, serif')">Serif</button>
                    <button class="tool-btn" onclick="setFont('Nunito, sans-serif')">Sans</button>
                    <div style="border-left: 1px solid var(--ui-border); height: 20px; margin: 0 5px;"></div>
                    <button class="tool-btn" onclick="changeSize(-1)">A-</button>
                    <button class="tool-btn" onclick="changeSize(1)">A+</button>
                    <div style="border-left: 1px solid var(--ui-border); height: 20px; margin: 0 5px;"></div>
                    <div class="theme-dot light" onclick="setTheme('light')"></div>
                    <div class="theme-dot sepia" onclick="setTheme('sepia')"></div>
                    <div class="theme-dot dark" onclick="setTheme('dark')"></div>
                </div>
            </div>

            {{-- Container có Padding bao ngoài --}}
            <div id="reader-container">
                <div class="nav-overlay nav-prev" onclick="prevPage()"><i class="fas fa-chevron-left fa-2x"></i></div>
                <div class="nav-overlay nav-next" onclick="nextPage()"><i class="fas fa-chevron-right fa-2x"></i></div>

                {{-- Scroller nằm bên trong, Full size --}}
                <div id="content-scroller">
                    @if($book->book_content)
                        {!! $book->book_content !!}
                    @else
                        <div style="height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                            <h3>Nội dung đang cập nhật...</h3>
                            <p class="text-muted">Cuốn sách này chưa có dữ liệu văn bản.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="reader-footer">
                <span id="page-display" style="white-space: nowrap;">Trang 1</span>
                <input type="range" id="page-slider" min="1" max="1" value="1" step="1">
                <span id="percent-display">0%</span>
            </div>
        </div>
    </div>

    <script>
        const scroller = document.getElementById('content-scroller');
        const container = document.getElementById('reader-container'); 
        const root = document.documentElement;
        
        const GAP = 60; 
        let currentPage = 1;
        let totalPages = 1;
        let pageWidth = 0; 

        // --- 1. TÍNH TOÁN KÍCH THƯỚC ---
        function calculateLayout() {
            pageWidth = scroller.clientWidth; 
            
            scroller.style.columnWidth = `${pageWidth}px`;
            
            setTimeout(() => {
                const totalScrollWidth = scroller.scrollWidth;
                
                totalPages = Math.ceil(totalScrollWidth / (pageWidth + GAP));
                
                if (totalPages < 1) totalPages = 1;
                document.getElementById('page-slider').max = totalPages;
                updateUI();
            }, 100);
        }

        // --- 2. HÀM CUỘN TRANG ---
        function scrollToPage(page) {
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            currentPage = page;

            const targetScrollLeft = (page - 1) * (pageWidth + GAP);
            
            scroller.scrollTo({
                left: targetScrollLeft,
                behavior: 'smooth'
            });
            updateUI();
        }

        function nextPage() { scrollToPage(currentPage + 1); }
        function prevPage() { scrollToPage(currentPage - 1); }

        function updateUI() {
            document.getElementById('page-display').innerText = `Trang ${currentPage} / ${totalPages}`;
            document.getElementById('page-slider').value = currentPage;
            let percent = Math.round((currentPage / totalPages) * 100);
            document.getElementById('percent-display').innerText = `${percent}%`;
        }

        // --- 3. CÀI ĐẶT UI ---
        let fontSize = 18;
        function changeSize(delta) {
            fontSize += delta;
            if(fontSize < 12) fontSize = 12;
            if(fontSize > 30) fontSize = 30;
            root.style.setProperty('--font-size', `${fontSize}px`);
            setTimeout(() => { calculateLayout(); scrollToPage(currentPage); }, 200);
        }

        function setFont(font) {
            root.style.setProperty('--font-family', font);
            setTimeout(calculateLayout, 200);
        }

        function setTheme(theme) {
            document.body.className = `theme-${theme}`;
        }

        // --- EVENTS ---
        window.onload = calculateLayout;
        window.addEventListener('resize', () => { calculateLayout(); scrollToPage(currentPage); });
        document.getElementById('page-slider').addEventListener('input', function() { scrollToPage(parseInt(this.value)); });
        document.addEventListener('keydown', (e) => {
            if(e.key === "ArrowLeft") prevPage();
            if(e.key === "ArrowRight") nextPage();
        });
        document.addEventListener('contextmenu', event => event.preventDefault());
    </script>
</body>
</html>