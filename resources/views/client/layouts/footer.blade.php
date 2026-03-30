<footer class="pt-5 pb-3" style="background-color: #f8f9fa; border-top: 4px solid #212529; font-family: 'Nunito', sans-serif;">
    <div class="container text-start"> {{-- Ép canh trái toàn bộ để Mobile dễ đọc hơn --}}
        <div class="row gy-4"> {{-- Thêm gy-4 để các cột tự động giãn cách xa nhau trên Mobile --}}
            
            {{-- Cột 1: Giới thiệu thương hiệu --}}
            <div class="col-12 col-lg-4 pe-lg-4">
                <h5 class="text-uppercase mb-3 fw-bold" style="color: #0d6efd;">
                    <i class="fas fa-book-open me-2"></i> Thelwc Bookstore
                </h5>
                <p style="font-size: 0.95rem; line-height: 1.6; color: #6c757d;">
                    Khám phá thế giới tri thức vô tận cùng Thelwc. Chúng tôi cam kết mang đến những cuốn sách chất lượng nhất với trải nghiệm mua sắm tuyệt vời.
                </p>
            </div> 

            {{-- Cột 2: Liên kết nhanh --}}
            <div class="col-12 col-md-6 col-lg-3">
                <h5 class="text-uppercase mb-3 fw-bold" style="color: #212529;">Liên kết</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-decoration-none footer-link fw-medium">
                            <i class="fas fa-chevron-right me-2 small"></i>Trang chủ
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('shop') }}" class="text-decoration-none footer-link fw-medium">
                            <i class="fas fa-chevron-right me-2 small"></i>Cửa hàng
                        </a> 
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('cart.index') }}" class="text-decoration-none footer-link fw-medium">
                            <i class="fas fa-chevron-right me-2 small"></i>Giỏ hàng
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('client.account.history') }}" class="text-decoration-none footer-link fw-medium">
                            <i class="fas fa-chevron-right me-2 small"></i>Tra cứu đơn hàng
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Cột 3: Thông tin liên hệ --}}
            <div class="col-12 col-md-6 col-lg-5">
                <h5 class="text-uppercase mb-3 fw-bold" style="color: #212529;">Liên hệ</h5>
                <div class="d-flex align-items-start mb-2" style="color: #6c757d;">
                    <i class="fas fa-home mt-1 me-3 fs-6" style="color: #0d6efd;"></i> 
                    <span>710/2 đường Phan Tôn, TP. Long Xuyên, An Giang</span>
                </div>
                <div class="d-flex align-items-center mb-2" style="color: #6c757d;">
                    <i class="fas fa-envelope me-3 fs-6" style="color: #0d6efd;"></i> 
                    <span>letheluc04@gmail.com</span>
                </div>
                <div class="d-flex align-items-center mb-2" style="color: #6c757d;">
                    <i class="fas fa-phone me-3 fs-6" style="color: #0d6efd;"></i> 
                    <span>0964617664</span>
                </div>
                
                {{-- Mạng xã hội (Nút nền đen chữ trắng như yêu cầu) --}}
                <div class="mt-4 d-flex gap-2">
                    <a href="https://www.facebook.com/share/19mA1RZJHx/" class="btn rounded-circle shadow-sm social-btn" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/Thelwccc?igsh=MjNybmMweWlyZWQw" class="btn rounded-circle shadow-sm social-btn" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/@Thelwcnne?_r=1&_t=ZS-93JPBXkm0VG" class="btn rounded-circle shadow-sm social-btn" title="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>

        <hr class="mt-5 mb-4" style="border-color: #dee2e6;">

        {{-- Thanh Copyright --}}
        <div class="row align-items-center">
            <div class="col-md-7 text-center text-md-start mb-2 mb-md-0">
                <p class="mb-0 small" style="color: #6c757d;">
                    © 2026 <strong style="color: #212529;">Thelwc Books</strong>. All rights reserved.
                </p>
            </div>
            <div class="col-md-5 text-center text-md-end">
                <p class="mb-0 small" style="color: #6c757d;">
                    Designed by <span class="fw-bold" style="color: #0d6efd;">Lê Thế Lực</span>
                </p>
            </div>
        </div>
    </div>
</footer>

{{-- SCRIPT CHUNG CHO TOÀN BỘ WEB --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

{{-- CSS CHUẨN MÀU CỦA THELWC --}}
<style>
    /* CSS cho Link Liên kết */
    .footer-link { 
        color: #6c757d; 
        transition: all 0.3s ease; 
        display: inline-block; 
    }
    .footer-link:hover { 
        color: #c5a992 !important; /* Màu hover riêng biệt */
        transform: translateX(6px); 
    }
    
    /* CSS cho Nút Mạng Xã Hội */
    .social-btn { 
        width: 42px; 
        height: 42px; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        background-color: #212529; /* Nền chính */
        color: #ffffff; /* Chữ trắng */
        transition: all 0.3s ease; 
        border: none;
    }
    .social-btn:hover { 
        background-color: #0d6efd; /* Đổi sang màu nổi bật khi di chuột */
        color: #ffffff;
        transform: translateY(-4px); 
        box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3) !important;
    }
</style>