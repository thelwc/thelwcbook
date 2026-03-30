<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) return redirect('login');

        $user = Auth::user();
        // Bảng quy đổi quyền
        $roleIds = [
            'admin' => 0, 'director' => 1, 'manager' => 2, 
            'staff' => 3, 'moderator' => 4, 'customer' => 5
        ];

        // Admin (0) luôn được qua
        if ($user->role == 0) return $next($request);

        // Kiểm tra các quyền khác
        foreach ($roles as $role) {
            if (isset($roleIds[$role]) && $user->role == $roleIds[$role]) {
                return $next($request);
            }
        }
        
        // Không có quyền -> Báo lỗi hoặc về trang chủ
        abort(403, 'Bạn không đủ thẩm quyền vào khu vực này!');
    }
}