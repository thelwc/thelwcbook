@extends('client.layouts.master')

@section('content')
<style>
    /* CSS cho cái hộp */
    .mystery-box {
        cursor: pointer;
        transition: transform 0.3s;
        font-size: 80px; /* Kích thước hộp */
        color: #ffc107; /* Màu vàng */
        text-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .mystery-box:hover {
        transform: translateY(-10px); /* Di chuột vào thì hộp bay lên xíu */
    }

    /* Hiệu ứng Rung lắc khi bấm */
    @keyframes shake {
        0% { transform: translate(1px, 1px) rotate(0deg); }
        10% { transform: translate(-1px, -2px) rotate(-1deg); }
        20% { transform: translate(-3px, 0px) rotate(1deg); }
        30% { transform: translate(3px, 2px) rotate(0deg); }
        40% { transform: translate(1px, -1px) rotate(1deg); }
        50% { transform: translate(-1px, 2px) rotate(-1deg); }
        60% { transform: translate(-3px, 1px) rotate(0deg); }
        70% { transform: translate(3px, 1px) rotate(-1deg); }
        80% { transform: translate(-1px, -1px) rotate(1deg); }
        90% { transform: translate(1px, 2px) rotate(0deg); }
        100% { transform: translate(1px, -2px) rotate(-1deg); }
    }
    
    .shaking {
        animation: shake 0.5s;
        animation-iteration-count: infinite;
    }

    /* Kết quả hiện ra */
    .prize-text { font-weight: bold; margin-top: 10px; font-size: 18px; }
    .text-win { color: #198754; } /* Màu xanh lá */
    .text-lose { color: #dc3545; } /* Màu đỏ */
</style>

<div class="container text-center py-5" style="min-height: 60vh;">
    <h1 class="fw-bold mb-3">🎁 HỘP QUÀ BÍ MẬT</h1>

    {{-- TRƯỜNG HỢP 1: ĐƯỢC CHƠI --}}
    @if($canPlay)
        <p class="text-muted fs-5 mb-5">Chọn 1 trong 3 hộp để nhận quà ngẫu nhiên!</p>

        <div class="row justify-content-center">
            {{-- Code 3 cái hộp giữ nguyên --}}
            <div class="col-md-3 col-4">
                <div class="box-container" data-id="1">
                    <i class="fas fa-gift mystery-box"></i>
                    <div class="prize-text d-none"></div>
                </div>
            </div>
            <div class="col-md-3 col-4">
                <div class="box-container" data-id="2">
                    <i class="fas fa-gift mystery-box"></i>
                    <div class="prize-text d-none"></div>
                </div>
            </div>
            <div class="col-md-3 col-4">
                <div class="box-container" data-id="3">
                    <i class="fas fa-gift mystery-box"></i>
                    <div class="prize-text d-none"></div>
                </div>
            </div>
        </div>

    {{-- TRƯỜNG HỢP 2: HẾT LƯỢT (ĐANG CHỜ) --}}
    @else
        <div class="mt-5 p-5 bg-light rounded-3 shadow-sm border border-warning">
            <i class="fas fa-hourglass-half fa-4x text-warning mb-3"></i>
            <h2 class="fw-bold text-dark">Hết lượt quay rồi!</h2>
            <p class="fs-4 mt-3">
                Bạn vui lòng quay lại sau: <br>
                <span class="badge bg-danger fs-3 mt-2">{{ $timeLeft }}</span>
            </p>
            <p class="text-muted mt-3">Mỗi tài khoản chỉ được mở hộp quà 1 lần mỗi 24 giờ.</p>
            
            <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left"></i> Về trang chủ mua sách
            </a>
        </div>
    @endif
    
    {{-- Nút Chơi lại (chỉ hiện khi đang chơi) --}}
    @if($canPlay)
    <div class="mt-5">
        <button onclick="location.reload()" class="btn btn-outline-dark rounded-pill px-4">
            <i class="fas fa-sync-alt me-2"></i> Chơi lại
        </button>
    </div>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let hasPlayed = false; // Biến chặn không cho bấm nhiều hộp

        $('.box-container').click(function() {
            // Nếu đã chơi rồi thì không cho bấm nữa
            if (hasPlayed) return;
            
            let clickedBox = $(this);
            let icon = clickedBox.find('.mystery-box');
            let resultText = clickedBox.find('.prize-text');

            hasPlayed = true; // Đánh dấu là đã chơi

            // 1. Tạo hiệu ứng rung lắc
            icon.addClass('shaking');

            // 2. Gọi Ajax lên server lấy quà
            $.ajax({
                url: "{{ route('game.open') }}",
                method: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    
                    // Giả vờ đợi 1.5 giây cho hồi hộp rồi mới hiện kết quả
                    setTimeout(function() {
                        icon.removeClass('shaking'); // Ngừng rung
                        
                        // Đổi icon hộp đóng -> hộp mở (nếu muốn)
                        icon.removeClass('fa-gift').addClass('fa-box-open');

                        let htmlContent = '';

                        if(response.type === 'win') {
                            // Nếu TRÚNG: Hiện tên + Mã Code đẹp
                            htmlContent = `
                                <div class="text-win animate__animated animate__bounceIn">
                                    <div class="fw-bold">${response.name}</div>
                                    <div class="mt-2">
                                        <small class="text-muted">Mã của bạn:</small><br>
                                        <span class="badge bg-warning text-dark fs-5 border border-dark px-3 py-2 mt-1 copy-code" 
                                              style="cursor:pointer;" title="Bấm để copy">
                                            ${response.code} <i class="fas fa-copy ms-2 small"></i>
                                        </span>
                                    </div>
                                </div>
                            `;
                            
                            // Hiệu ứng pháo hoa (nếu thích thì thêm sau)
                        } else {
                            // Nếu TRƯỢT
                            htmlContent = `<div class="text-lose fw-bold">${response.name}</div>`;
                        }

                        // Nhét HTML vào thẻ hiển thị
                        resultText.html(htmlContent).removeClass('d-none');

                        // Làm mờ 2 hộp còn lại
                        $('.box-container').not(clickedBox).css('opacity', '0.3');

                    }, 1500);
                },
                error: function() {
                    alert('Lỗi kết nối server!');
                    hasPlayed = false;
                    icon.removeClass('shaking');
                }
            });
        });
    });

    // Script bấm vào mã voucher là tự copy
    $(document).on('click', '.copy-code', function() {
        let code = $(this).text().trim(); // Lấy mã
        navigator.clipboard.writeText(code); // Copy vào bộ nhớ
        alert('Đã copy mã: ' + code); // Báo khách biết
    });
</script>
@endsection

