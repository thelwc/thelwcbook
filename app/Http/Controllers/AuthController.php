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
        // Lưu link trước đó, loại trừ trang login/register
        $urlTruocDo = url()->previous();
        if (!str_contains($urlTruocDo, 'login') && !str_contains($urlTruocDo, 'register')) {
            session(['url_truoc_do' => $urlTruocDo]);
        }
        return view('auth.login');
    }

    // 2. Xử lý đăng nhập
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $remember = $request->boolean('remember');
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Bạn đã nhập sai quá nhiều. Vui lòng thử lại sau $seconds giây.");
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $role = Auth::user()->role;

            // 1. Admin (0)
            if ($role == 0) return redirect()->route('users.index');
            // 2. Giám đốc (1) & Quản lý (2)
            if ($role == 1 || $role == 2) return redirect()->route('dashboard');
            // 3. Nhân viên (3)
            if ($role == 3) return redirect()->route('orders.index');
            // 4. Kiểm duyệt (4)
            if ($role == 4) return redirect()->route('admin.posts.index');

            // 5. 🔥 KHÁCH HÀNG (5): ÁP DỤNG TRẢ VỀ TRANG CŨ 🔥
            if ($role == 5) {
                // Lấy link cũ (nếu có), không có thì mặc định về home
                $linkChuyenHuong = session('url_truoc_do', route('home'));
                session()->forget('url_truoc_do'); // Xóa nhớ
                
                return redirect($linkChuyenHuong)->with('success', 'Đăng nhập thành công!');
            }

            return redirect()->route('home');
        }

        RateLimiter::hit($throttleKey, 300);
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