<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\RateLimiter; 
use Illuminate\Support\Str; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Nhớ import Model User

class AuthController extends Controller
{
    // 1. Hiện form đăng nhập
    public function showLogin() {
        return view('auth.login');
    }

    // 2. Xử lý đăng nhập (Giữ nguyên Rate Limit xịn của cậu + Thêm Ghi nhớ + Logic phân luồng)
    public function login(Request $request) {
        // Validate
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 🔥 LẤY GIÁ TRỊ TỪ Ô CHECKBOX "GHI NHỚ" 🔥
        $remember = $request->boolean('remember');

        // --- RATE LIMIT (CHỐNG SPAM) ---
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Bạn đã nhập sai quá nhiều. Vui lòng thử lại sau $seconds giây.");
        }
        // -------------------------------

        // 🔥 KIỂM TRA ĐĂNG NHẬP: NHỚ TRUYỀN BIẾN $remember VÀO 🔥
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            
            // Thành công -> Xóa bộ đếm lỗi
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // --- BẮT ĐẦU LOGIC CHIA LUỒNG MỚI (THEO SỐ) ---
            $role = Auth::user()->role;

            // 1. Admin (0) -> Về quản lý User
            if ($role == 0) {
                return redirect()->route('users.index');
            }

            // 2. Giám đốc (1) & Quản lý (2) -> Về Dashboard
            if ($role == 1 || $role == 2) {
                return redirect()->route('dashboard');
            }

            // 3. Nhân viên (3) -> Về Đơn hàng
            if ($role == 3) {
                return redirect()->route('orders.index');
            }

            // 4. Kiểm duyệt (4) -> Về Tin tức
            if ($role == 4) {
                return redirect()->route('admin.posts.index');
            }

            // 5. Khách hàng (5) hoặc khác -> Về trang chủ mua sách
            return redirect()->route('home');
            // --- KẾT THÚC LOGIC ---
        }

        // Thất bại -> Tăng lỗi
        RateLimiter::hit($throttleKey, 300); // Khóa 5 phút (300s) nếu sai nhiều
        $retriesLeft = RateLimiter::retriesLeft($throttleKey, 5);

        return back()->with('error', "Email hoặc mật khẩu không đúng! Bạn còn $retriesLeft lần thử.");
    }

    // 3. Đăng xuất
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home'); // Đăng xuất xong về trang chủ
    }

    // 4. Hiện form đăng ký
    public function showRegister() {
        return view('auth.register');
    }

    // 5. Xử lý đăng ký
    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6'
        ]);

        // Tạo tài khoản mới
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            
            // ⚠️ QUAN TRỌNG: Sửa thành số 5 (Khách hàng)
            // Tuyệt đối không để số 2 (Quản lý)
            'role' => 5 
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký thành công! Hãy đăng nhập ngay.');
    }
}