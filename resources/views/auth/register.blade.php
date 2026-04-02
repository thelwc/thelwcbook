<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Thelwc Books</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <style>
        /* Ẩn con mắt mặc định của trình duyệt Edge */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
        /* --- CSS NHÚNG TRỰC TIẾP --- */
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }

        .auth-wrapper {
            min-height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        /* Cột ảnh bên trái */
        .auth-bg-image {
            background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            position: relative;
            min-height: 100vh;
        }
        .auth-bg-image::before {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background: rgba(33, 37, 41, 0.3);
        }
        .auth-quote {
            position: absolute;
            bottom: 60px;
            left: 60px;
            right: 60px;
            color: #ffffff;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        /* Form bên phải */
        .auth-form-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #ffffff;
            padding: 40px;
        }
        .auth-form-inner {
            width: 100%;
            max-width: 450px; /* Tăng nhẹ chiều rộng cho thoáng */
        }

        /* Link hover */
        a { text-decoration: none; transition: all 0.3s ease; }
        a:hover, .text-brand-hover:hover { color: #c5a992 !important; }

        /* Nút bấm Custom */
        .btn-dark-brand {
            background-color: #212529;
            border: 1px solid #212529;
            color: #ffffff;
            padding: 12px 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-dark-brand:hover {
            background-color: #c5a992;
            border-color: #c5a992;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(197, 169, 146, 0.4);
        }

        /* Input Custom */
        .form-floating > .form-control:focus {
            border-color: #c5a992;
            box-shadow: 0 0 0 0.25rem rgba(197, 169, 146, 0.25);
        }
        .form-floating > label { color: #6c757d; }

        /* --- TÙY CHỈNH MỚI: NÚT ẨN HIỆN MẬT KHẨU --- */
        .password-container {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
            padding: 5px;
        }
        .password-toggle:hover {
            color: #212529;
        }
        /* Đẩy text sang trái để không đè lên icon mắt */
        .form-control-password {
            padding-right: 45px !important; 
        }
    </style>
</head>
<body>

<div class="container-fluid g-0 auth-wrapper">
    <div class="row g-0">
        <div class="col-lg-7 d-none d-lg-block position-relative auth-bg-image">
            <div class="auth-quote">
                <h2 class="fw-bold display-6 mb-3">"Một cuốn sách thực sự hay nên đọc trong tuổi trẻ, rồi đọc lại khi đã trưởng thành."</h2>
                <p class="fs-5 opacity-75">— Robertson Davies</p>
            </div>
        </div>

        <div class="col-lg-5 auth-form-container">
            <div class="auth-form-inner">
                
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="d-inline-block mb-3 text-dark fw-bold fs-4">
                        📚 Thelwc Books
                    </a>
                    <h3 class="fw-bold">Tham gia cùng chúng tôi</h3>
                    <p class="text-muted">Tạo tài khoản để quản lý đơn hàng dễ dàng.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 border-0 shadow-sm p-2 mb-3 small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control rounded-3" id="name" name="name" placeholder="Nguyễn Văn A" required value="{{ old('name') }}">
                        <label for="name">Họ và tên</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control rounded-3" id="email" name="email" placeholder="name@example.com" required value="{{ old('email') }}">
                        <label for="email">Địa chỉ Email</label>
                    </div>

                    <div class="form-floating mb-3 password-container">
                        <input type="password" class="form-control rounded-3 form-control-password" id="password" name="password" placeholder="Password" required>
                        <label for="password">Mật khẩu</label>
                        <span class="password-toggle" onclick="togglePassword('password', 'icon-pass')">
                            <i class="far fa-eye" id="icon-pass"></i>
                        </span>
                    </div>

                    <div class="form-floating mb-4 password-container">
                        <input type="password" class="form-control rounded-3 form-control-password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                        <label for="password_confirmation">Nhập lại mật khẩu</label>
                        <span class="password-toggle" onclick="togglePassword('password_confirmation', 'icon-confirm')">
                            <i class="far fa-eye" id="icon-confirm"></i>
                        </span>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label text-muted small" for="terms">
                            Tôi đồng ý với <a href="{{ route('terms') }}" class="text-dark fw-bold text-brand-hover" target="_blank">Điều khoản</a> 
                            và <a href="{{ route('policy') }}" class="text-dark fw-bold text-brand-hover" target="_blank">Chính sách bảo mật</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-dark-brand w-100 rounded-pill fs-6 text-uppercase">
                        Đăng ký tài khoản
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Bạn đã có tài khoản?</p>
                    <a href="{{ route('login') }}" class="fw-bold text-dark text-brand-hover fs-6">
                        Đăng nhập ngay <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

</body>
</html>