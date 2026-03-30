<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Thelwc Books</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <style>
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
            background-image: url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            position: relative;
            min-height: 100vh;
        }
        .auth-bg-image::before {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background: rgba(33, 37, 41, 0.3); /* Lớp phủ tối */
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
            max-width: 420px;
        }

        /* Link hover color */
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
    </style>
</head>
<body>

<div class="container-fluid g-0 auth-wrapper">
    <div class="row g-0">
        <div class="col-lg-7 d-none d-lg-block position-relative auth-bg-image">
            <div class="auth-quote">
                <h2 class="fw-bold display-6 mb-3">"Đọc sách giống như đang trò chuyện với những bộ óc tuyệt vời nhất."</h2>
                <p class="fs-5 opacity-75">— Descartes</p>
            </div>
        </div>

        <div class="col-lg-5 auth-form-container">
            <div class="auth-form-inner">
                
                <div class="text-center mb-5">
                    <a href="{{ route('home') }}" class="d-inline-block mb-3 text-dark fw-bold fs-4">
                        📚 Thelwc Books
                    </a>
                    <h3 class="fw-bold">Chào mừng trở lại!</h3>
                    <p class="text-muted">Nhập thông tin để truy cập tài khoản.</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger rounded-3 border-0 shadow-sm d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success rounded-3 border-0 shadow-sm d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control rounded-3" id="email" name="email" placeholder="name@example.com" required>
                        <label for="email">Địa chỉ Email</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Password" required>
                        <label for="password">Mật khẩu</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label text-secondary" for="remember">Ghi nhớ tôi</label>
                        </div>
                        {{-- Thay dấu # bằng route('password.request') --}}
                        <a href="{{ route('password.request') }}" class="text-dark fw-bold small text-brand-hover">Quên mật khẩu?</a>                    </div>

                    <button type="submit" class="btn btn-dark-brand w-100 rounded-pill fs-6 text-uppercase">
                        Đăng nhập ngay
                    </button>
                </form>

                <div class="position-relative my-4">
                    <hr class="text-secondary opacity-25">
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                        Hoặc tiếp tục với
                    </span>
                </div>

                <a href="{{ route('login.google') }}" class="btn btn-white border w-100 rounded-pill d-flex align-items-center justify-content-center py-2 shadow-sm text-decoration-none transition-hover">
                    <svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                    </svg>
                    <span class="fw-bold text-secondary">Google</span>
                </a>
                <div class="text-center mt-5">
                    <p class="text-muted mb-0">Bạn chưa có tài khoản?</p>
                    <a href="{{ route('register') }}" class="fw-bold text-dark text-brand-hover fs-6">
                        Tạo tài khoản mới <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>