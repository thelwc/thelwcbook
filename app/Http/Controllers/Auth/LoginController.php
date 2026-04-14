<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // ====================================================
    // 🔥 1. HÀM XỬ LÝ ĐĂNG NHẬP CHÍNH (CÓ GHI NHỚ LẠI) 🔥
    // ====================================================
    // public function login(Request $request)
    // {

    //     dd([
    //         'tat_ca_du_lieu_gui_len' => $request->all(),
    //         'bien_ghi_nho_nhan_duoc' => $request->boolean('remember')
    //     ]);
    //     // Kiểm tra xem đã nhập đủ thông tin chưa
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // Bắt biến 'remember' từ giao diện (nếu có check thì là true, không thì false)
    //     // Sửa dòng cũ: $remember = $request->has('remember');
    //     // THÀNH DÒNG MỚI NÀY:
    //     $remember = $request->boolean('remember');

    //     if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
    //         return $this->authenticated($request, Auth::user());
    //     }

    //     // Thực hiện đăng nhập với Auth::attempt kèm theo lệnh $remember
    //     if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
    //         // Đăng nhập thành công -> Chuyển xuống hàm phân quyền bên dưới
    //         return $this->authenticated($request, Auth::user());
    //     }

    //     // Đăng nhập thất bại -> Trả về đúng biến session('error') mà giao diện đang chờ
    //     return back()->with('error', 'Email hoặc mật khẩu không chính xác!');
    // }

    // ====================================================
    // 2. PHÂN QUYỀN SAU KHI ĐĂNG NHẬP
    // ====================================================
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
    // 3. CODE GOOGLE LOGIN
    // ====================================================
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->id)->first();

            // --- PHẦN 1: XỬ LÝ TẠO/CẬP NHẬT TÀI KHOẢN ---
            if ($user) {
                // Trường hợp 1: Khách cũ ĐÃ liên kết Google từ trước
                if (empty($user->avatar)) {
                    $user->avatar = $googleUser->avatar;
                    $user->save();
                }
                Auth::login($user, true); 

            } else {
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // Trường hợp 2: Khách cũ, trùng email nhưng LẦN ĐẦU bấm đăng nhập Google
                    $existingUser->google_id = $googleUser->id; 
                    if (empty($existingUser->avatar)) {
                        $existingUser->avatar = $googleUser->avatar;
                    }
                    $existingUser->save();
                    Auth::login($existingUser, true);
                } else {
                    // Trường hợp 3: Khách mới hoàn toàn
                    $newUser = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'password' => bcrypt(Str::random(16)),
                        'avatar' => $googleUser->avatar,
                        'role' => 5, // Luôn mặc định là Khách hàng
                    ]);
                    Auth::login($newUser, true);
                }
            }
            
            // --- PHẦN 2: LOGIC CHUYỂN HƯỚNG DỰA VÀO ROLE (CHỐNG LỖI CHO ADMIN) ---
            $role = Auth::user()->role;

            // Nếu là Admin / Nhân viên -> Trả về trang quản trị
            if ($role == 0) return redirect()->route('users.index');
            if ($role == 1 || $role == 2) return redirect()->route('dashboard');
            if ($role == 3) return redirect()->route('orders.index');
            if ($role == 4) return redirect()->route('admin.posts.index');

            // 🔥 NẾU LÀ KHÁCH HÀNG (BẤT KỂ CŨ HAY MỚI) -> TRẢ VỀ TRANG ĐANG XEM DỞ 🔥
            if ($role == 5) {
                // Lấy link cũ (nếu không có thì về home)
                $linkChuyenHuong = session('url_truoc_do', route('home'));
                session()->forget('url_truoc_do'); // Xóa nhớ để dọn dẹp
                
                return redirect($linkChuyenHuong)->with('success', 'Đăng nhập Google thành công!');
            }

            // Mặc định an toàn
            return redirect()->route('home');
        
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Lỗi đăng nhập Google: ' . $e->getMessage());
        }
    }
}