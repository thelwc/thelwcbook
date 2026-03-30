<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
// --- THÊM 4 DÒNG NÀY ---
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    protected function authenticated(Request $request, $user)
    {
        // 1. Admin (0) -> Vào quản lý User
        if ($user->role == 0) {
            return redirect()->route('users.index');
        }

        // 2. Giám đốc (1) & Quản lý (2) -> Vào Dashboard
        if (in_array($user->role, [1, 2])) {
            return redirect()->route('dashboard');
        }

        // 3. Nhân viên (3) -> Vào Xử lý đơn hàng
        if ($user->role == 3) {
            return redirect()->route('orders.index');
        }

        // 4. Kiểm duyệt viên (4) -> Vào Quản lý Tin tức
        if ($user->role == 4) {
            return redirect()->route('admin.posts.index');
        }

        // 5. Khách hàng (5) -> Về trang chủ
        return redirect()->route('home');
    }

    // ====================================================
    // BẮT ĐẦU PHẦN CODE GOOGLE LOGIN (Thelwc thêm vào)
    // ====================================================

    // 1. Chuyển hướng người dùng sang trang đăng nhập của Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Google gọi ngược về đây sau khi user chọn tài khoản xong
   // 2. Google gọi ngược về đây sau khi user chọn tài khoản xong
    public function handleGoogleCallback()
    {
        try {
            // 1. Lấy thông tin từ Google
            $googleUser = Socialite::driver('google')->user();

            // 2. Tìm user đã từng login bằng Google chưa
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // === SỬA ĐOẠN NÀY: KIỂM TRA ẢNH TRƯỚC KHI CHO VÀO ===
                // Nếu avatar đang trống (do cậu vừa xóa), thì lấy lại ảnh Google
                if (empty($user->avatar)) {
                    $user->avatar = $googleUser->avatar;
                    $user->save();
                }
                // ====================================================

                Auth::login($user);
                return redirect()->route('home');

            } else {
                // B. Chưa có google_id -> Kiểm tra xem email có trùng không
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    $existingUser->google_id = $googleUser->id; // Cập nhật ID Google

                    // Nếu khách chưa có ảnh thì lấy ảnh Google
                    if (empty($existingUser->avatar)) {
                        $existingUser->avatar = $googleUser->avatar;
                    }
                    
                    $existingUser->save();
                    Auth::login($existingUser);
                } else {
                    // Tạo tài khoản mới
                    $newUser = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'password' => bcrypt(Str::random(16)),
                        'avatar' => $googleUser->avatar,
                        'role' => 5,
                    ]);
                    Auth::login($newUser);
                }
                
                return redirect()->route('home');
            }
        
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Lỗi đăng nhập Google: ' . $e->getMessage());
        }
    }
}