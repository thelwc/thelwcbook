<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Thelwc Books</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/jpeg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; }
        .auth-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 450px; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .auth-header { background: #212529; padding: 30px; text-align: center; color: white; }
        .btn-brand { background-color: #212529; color: white; border: none; padding: 12px; font-weight: bold; width: 100%; border-radius: 50px; transition: 0.3s; }
        .btn-brand:hover { background-color: #c5a992; transform: translateY(-2px); }
        .form-control:focus { border-color: #c5a992; box-shadow: 0 0 0 0.25rem rgba(197, 169, 146, 0.25); }
    </style>
</head>
<body>

<div class="auth-wrapper p-3">
    <div class="auth-card">
        {{-- Header --}}
        <div class="auth-header">
            <h3 class="fw-bold mb-0"><i class="fas fa-lock me-2"></i>Quên mật khẩu?</h3>
            <p class="small text-white-50 mb-0 mt-2">Đừng lo, chuyện này xảy ra thường xuyên mà!</p>
        </div>

        <div class="p-4 p-md-5">
            {{-- Thông báo thành công (nếu gửi mail xong) --}}
            @if (session('status'))
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                </div>
            @endif

            <p class="text-muted text-center mb-4">
                Nhập địa chỉ email bạn đã đăng ký, chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu mới.
            </p>

            {{-- FORM GỬI MAIL --}}
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-floating mb-4">
                    <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                    <label for="email">Địa chỉ Email của bạn</label>
                    
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-brand mb-4">
                    <i class="fas fa-paper-plane me-2"></i> Gửi link đặt lại mật khẩu
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-dark small">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>