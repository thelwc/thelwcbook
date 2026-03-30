<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang đọc PDF: {{ $book->title }}</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* --- CỐ ĐỊNH LAYOUT KHÔNG CHO BODY CUỘN --- */
html, body {
    margin: 0; padding: 0;
    height: 100%; /* Bắt buộc 100% */
    width: 100%;
    overflow: hidden; /* Cấm body cuộn */
    font-family: 'Nunito', sans-serif;
    background: var(--bg-color);
    color: var(--text-color);
}

:root {
    --bg-color: #f5f6f8;
    --bg-paper: #ffffff;
    --text-color: #1f2937;
    --ui-bg: #ffffff;
    --ui-border: #e5e7eb;
    --accent: #3b82f6;
    --accent-hover: #2563eb;
}

/* themes */
body.theme-sepia { --bg-color: #f4ecd8; --bg-paper: #fdf6e3; --text-color: #5b4636; --ui-bg: #efe4d2; --ui-border: #d3c4b1; }
body.theme-dark { --bg-color: #121212; --bg-paper: #1e1e1e; --text-color: #e5e7eb; --ui-bg: #1f1f1f; --ui-border: #333; }

/* SIDEBAR */
.sidebar { 
    width: 320px; 
    height: 100%; /* Full chiều cao */
    background: var(--ui-bg); 
    border-right: 1px solid var(--ui-border); 
    padding: 28px; 
    display: flex; flex-direction: column; 
    flex-shrink: 0; /* Không cho co lại */
    z-index: 10;
}
.book-cover { width: 150px; height: 220px; margin: 0 auto 18px; border-radius: 8px; object-fit: cover; box-shadow: 0 10px 25px rgba(0,0,0,.15); }
.book-title { text-align: center; font-weight: 800; }
.book-author { text-align: center; opacity: .7; font-size: .9rem; }
.back-btn { display: inline-flex; gap: 8px; align-items: center; font-weight: 700; color: var(--text-color); text-decoration: none; margin-bottom: 16px; }
.back-btn:hover { color: var(--accent); }

/* MAIN CONTAINER */
.container-flex {
    display: flex;
    height: 100%; /* Quan trọng */
    width: 100%;
}

.main-reader { 
    flex: 1; 
    display: flex; 
    flex-direction: column; 
    height: 100%; 
    min-width: 0; /* Fix lỗi flexbox tràn */
    position: relative;
}

/* HEADER - CỐ ĐỊNH */
.reader-header { 
    height: 64px; 
    padding: 0 20px; 
    background: var(--ui-bg); 
    border-bottom: 1px solid var(--ui-border); 
    display: flex; align-items: center; justify-content: space-between; 
    flex-shrink: 0; /* Không bị đè bẹp */
    z-index: 50; 
}
.settings-bar { display: flex; align-items: center; gap: 16px; }
.zoom-group { display: flex; gap: 6px; }
.tool-btn { width: 38px; height: 38px; border-radius: 8px; border: 1px solid var(--ui-border); background: var(--bg-paper); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: .2s; }
.tool-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
.theme-dot { width: 18px; height: 18px; border-radius: 50%; border: 2px solid #ccc; cursor: pointer; }
.theme-dot.light { background: #fff; }
.theme-dot.sepia { background: #f4ecd8; }
.theme-dot.dark { background: #111; border-color: #444; }

/* PDF VIEWPORT - CUỘN Ở ĐÂY */
#pdf-viewport { 
    flex-grow: 1; /* Chiếm hết khoảng trống còn lại */
    background: var(--bg-color); 
    padding: 40px; 
    overflow: auto; /* 🔥 THANH CUỘN NẰM Ở ĐÂY 🔥 */
    display: flex;
    justify-content: center; /* Căn giữa ngang */
    align-items: flex-start; /* Căn trên để scroll xuống */
    position: relative;
}

canvas { 
    background: var(--bg-paper); 
    border-radius: 4px; 
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
    display: block;
    /* Bỏ max-width để zoom thoải mái */
}
body.theme-dark canvas { filter: invert(1) hue-rotate(180deg) contrast(.85); }

/* FOOTER - CỐ ĐỊNH */
.reader-footer { 
    height: 54px; 
    background: var(--ui-bg); 
    border-top: 1px solid var(--ui-border); 
    padding: 0 20px; 
    display: flex; align-items: center; gap: 16px; 
    font-size: .9rem; font-weight: 600; 
    flex-shrink: 0; /* Không bị đè bẹp */
    z-index: 50; 
}
input[type=range] { flex: 1; cursor: pointer; }

/* NÚT ĐIỀU HƯỚNG */
.nav-overlay { 
    position: fixed; top: 50%; transform: translateY(-50%); 
    width: 50px; height: 50px; border-radius: 50%; 
    background: rgba(0,0,0,.3); color: #fff; border: none;
    display: flex; align-items: center; justify-content: center; cursor: pointer; 
    z-index: 100; backdrop-filter: blur(4px); transition: .2s; 
}
.nav-overlay:hover { background: var(--accent); box-shadow: 0 5px 15px rgba(0,0,0,.3); }
.nav-prev { left: 340px; } /* Né sidebar */
.nav-next { right: 20px; }
.nav-overlay:disabled { opacity: 0; pointer-events: none; }

/* MOBILE */
@media (max-width: 900px) {
    .sidebar { display: none; }
    .nav-prev { left: 10px; }
    #pdf-viewport { padding: 10px; }
}
</style>
</head>

<body class="theme-light">

<div class="container-flex">
    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <a href="{{ route('book.detail', $book->id) }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Quay lại chi tiết
        </a>
        @if($book->image)
            <img src="{{ asset(str_contains($book->image, 'uploads') ? $book->image : 'uploads/' . $book->image) }}" class="book-cover">
        @endif
        <div class="book-title">{{ $book->title }}</div>
        <div class="book-author">{{ $book->author }}</div>
        <div style="margin-top:auto;text-align:center;font-size:.8rem;opacity:.6">
            © thelwc books pdf reader
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main-reader">

        {{-- HEADER (CỐ ĐỊNH) --}}
        <div class="reader-header">
            <div style="font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-pdf text-danger"></i> 
                <span class="d-none d-md-inline">BẢN ĐỌC PDF</span>
            </div>

            <div class="settings-bar">
                <div class="zoom-group">
                    <button class="tool-btn" id="zoom_out" title="Thu nhỏ"><i class="fas fa-minus"></i></button>
                    <button class="tool-btn" id="zoom_in" title="Phóng to"><i class="fas fa-plus"></i></button>
                </div>
                <div style="width:1px;height:22px;background:var(--ui-border)"></div>
                <div style="display:flex;gap:8px">
                    <div class="theme-dot light" onclick="setTheme('light')" title="Sáng"></div>
                    <div class="theme-dot sepia" onclick="setTheme('sepia')" title="Vàng nhạt"></div>
                    <div class="theme-dot dark" onclick="setTheme('dark')" title="Tối"></div>
                </div>
            </div>
        </div>

        {{-- VIEWPORT (CUỘN NỘI DUNG) --}}
        <div id="pdf-viewport">
            <button class="nav-overlay nav-prev" id="prev"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-overlay nav-next" id="next"><i class="fas fa-chevron-right"></i></button>
            <canvas id="the-canvas"></canvas>
        </div>

        {{-- FOOTER (CỐ ĐỊNH) --}}
        <div class="reader-footer">
            <span style="min-width: 80px; font-weight: bold;">
                <span id="page_num_display">1</span> / <span id="real_total_pages">--</span>
            </span>
            <input type="range" id="page_slider" min="1" max="1" value="1">
            <span id="page_count_display" style="min-width: 40px; text-align: right;">--</span>
        </div>

    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    // --- CẤU HÌNH (ĐÃ DIỆT SẠCH LỖI ĐỎ) ---
    var url = "{!! route('book.content', $book->id) !!}";
    
    // Đưa logic check preview vào if của JS thay vì if của Blade
    if ("{{ request()->query('mode') }}" === "preview") {
        url += "?mode=preview";
    }

    // Bọc kết quả của PHP vào chuỗi "", sau đó dùng JS để ép kiểu
    var isFullAccess = JSON.parse("{{ $isFullAccess ? 'true' : 'false' }}");
    var maxPages = isFullAccess ? 10000 : parseInt("{{ $book->preview_pages ?? 10 }}");
    
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

    var pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.0, 
        canvas = document.getElementById('the-canvas'),
        ctx = canvas.getContext('2d'),
        autoFitted = false;

    // --- RENDER PAGE ---
    // --- RENDER PAGE (ĐÃ ĐỘ LẠI ĐỘ NÉT 4K) ---
    function renderPage(num) {
        pageRendering = true;
        
        var effectiveTotal = Math.min(pdfDoc.numPages, maxPages);
        if (num > effectiveTotal) num = effectiveTotal;

        // Tự động kéo lên đầu khi lật trang mới
        document.getElementById('pdf-viewport').scrollTop = 0;

        pdfDoc.getPage(num).then(function(page) {
            
            // Auto Fit lần đầu
            if (!autoFitted) {
                var containerWidth = document.getElementById('pdf-viewport').clientWidth - 80;
                var baseViewport = page.getViewport({ scale: 1.0 });
                scale = containerWidth / baseViewport.width;
                if(scale > 1.5) scale = 1.5; // Cho scale mặc định to hơn xíu cho dễ đọc
                autoFitted = true;
            }

            var viewport = page.getViewport({ scale: scale });

            // 🔥 BÍ KÍP CHỐNG MỜ MẮT Ở ĐÂY 🔥
            // Lấy tỷ lệ điểm ảnh của thiết bị (Màn hình xịn thì số này sẽ là 2, 3...)
            var outputScale = window.devicePixelRatio || 1;

            // 1. Phóng to độ phân giải THỰC TẾ của khung tranh (canvas)
            canvas.width = Math.floor(viewport.width * outputScale);
            canvas.height = Math.floor(viewport.height * outputScale);

            // 2. Ép nó hiển thị đúng KÍCH THƯỚC ẢO bằng CSS để không bị tràn màn hình
            canvas.style.width = Math.floor(viewport.width) + "px";
            canvas.style.height = Math.floor(viewport.height) + "px";

            // 3. Báo cho PDF.js biết phải scale nét vẽ lên cho tương xứng
            var transform = outputScale !== 1 
                ? [outputScale, 0, 0, outputScale, 0, 0] 
                : null;

            var renderContext = { 
                canvasContext: ctx, 
                transform: transform, // Nhét cái transform nét căng vào đây
                viewport: viewport 
            };

            var renderTask = page.render(renderContext);

            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });

        // UI Update
        document.getElementById('page_num_display').innerText = num;
        document.getElementById('page_slider').value = num;
        
        document.getElementById('prev').disabled = (num <= 1);
        document.getElementById('next').disabled = (num >= pdfDoc.numPages);
    }

    function queueRenderPage(num) {
        if (pageRendering) pageNumPending = num;
        else renderPage(num);
    }

    // --- BUTTON EVENTS ---
    function onPrevPage() {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum);
    }

    function onNextPage() {
        var effectiveTotal = Math.min(pdfDoc.numPages, maxPages);
        
        // Nếu đang ở trang giới hạn (VD: trang 10) và bấm Next
        if (pageNum >= effectiveTotal) {
            if (!isFullAccess) {
                // Hiện thông báo mời mua sách (Dùng confirm để có 2 nút OK/Cancel)
                var xacNhan = confirm("Bạn đã đọc hết phần đọc thử rồi! 🥺\n\nBạn có muốn quay lại trang chi tiết để rinh cuốn sách này về và đọc trọn bộ không?");
                
                // Nếu khách bấm OK -> Chở khách về thẳng trang chi tiết sách
                if (xacNhan) {
                    window.location.href = "{{ route('book.detail', $book->id) }}";
                }
            }
            return; // Dừng lại, không cho lật trang nữa
        }
        
        // Nếu chưa tới giới hạn thì lật trang bình thường
        pageNum++;
        queueRenderPage(pageNum);
    }

    document.getElementById('prev').addEventListener('click', onPrevPage);
    document.getElementById('next').addEventListener('click', onNextPage);

    // --- ZOOM ---
    document.getElementById('zoom_in').addEventListener('click', () => {
        scale += 0.2; 
        queueRenderPage(pageNum);
    });

    document.getElementById('zoom_out').addEventListener('click', () => {
        if (scale > 0.4) {
            scale -= 0.2; 
            queueRenderPage(pageNum);
        }
    });

    // --- SLIDER ---
    document.getElementById('page_slider').addEventListener('input', function() {
        pageNum = parseInt(this.value);
        queueRenderPage(pageNum);
    });

    // --- KEYBOARD ---
    document.addEventListener('keydown', function(e) {
        if (e.key === "ArrowLeft") onPrevPage();
        if (e.key === "ArrowRight") onNextPage();
    });

    // --- INIT ---
    pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        var effectiveTotal = Math.min(pdfDoc.numPages, maxPages);
        document.getElementById('real_total_pages').textContent = pdfDoc.numPages;
        document.getElementById('page_count_display').textContent = effectiveTotal;
        document.getElementById('page_slider').max = effectiveTotal;
        
        renderPage(pageNum);
    }).catch(function(error) {
        console.error('Lỗi:', error);
        alert('Lỗi tải file. Vui lòng thử lại.');
    });

    function setTheme(theme) {
        document.body.className = 'theme-' + theme;
    }
    
    document.addEventListener('contextmenu', event => event.preventDefault());

</script>
</body>
</html>