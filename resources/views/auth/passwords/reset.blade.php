<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Thelwc Books</title>
    
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
            <h3 class="fw-bold mb-0"><i class="fas fa-key me-2"></i>Đặt lại mật khẩu</h3>
            <p class="small text-white-50 mb-0 mt-2">Nhập mật khẩu mới của bạn bên dưới.</p>
        </div>

        <div class="p-4 p-md-5">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                {{-- TOKEN BẮT BUỘC (Ẩn) --}}
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email (Tự điền) --}}
                <div class="form-floating mb-3">
                    <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ $email ?? old('email') }}" required readonly>
                    <label for="email">Email của bạn</label>
                    @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                {{-- Mật khẩu mới --}}
                <div class="form-floating mb-3">
                    <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" 
                           id="password" name="password" required autofocus placeholder="Mật khẩu mới">
                    <label for="password">Mật khẩu mới</label>
                    @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                {{-- Nhập lại mật khẩu --}}
                <div class="form-floating mb-4">
                    <input type="password" class="form-control rounded-3" 
                           id="password-confirm" name="password_confirmation" required placeholder="Xác nhận mật khẩu">
                    <label for="password-confirm">Nhập lại mật khẩu mới</label>
                </div>

                <button type="submit" class="btn btn-brand mb-4">
                    <i class="fas fa-save me-2"></i> Lưu mật khẩu mới
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>