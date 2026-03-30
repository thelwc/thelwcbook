<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictStaffFromFrontend
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem user đã đăng nhập chưa
        if (Auth::check()) {
            
            // 🔥 Danh sách các quyền (role) BỊ NHỐT trong trang Admin
            // Dựa theo code cũ, tớ thấy 0, 1, 2, 3, 4 của cậu là Admin/Nhân viên
            $staffRoles = [0, 1, 2, 3, 4]; 

            // Nếu role của người này nằm trong danh sách cấm ra ngoài
            if (in_array(Auth::user()->role, $staffRoles)) {
                
                // Trục xuất ngược lại vào trang Quản lý đơn hàng và báo lỗi
                return redirect()->route('orders.index')
                                 ->with('error', 'CẢNH BÁO: Tài khoản nội bộ không được phép ra ngoài mua hàng!');
            }
        }

        // Nếu là khách hàng bình thường (role khác mấy số trên) -> Cho đi tiếp
        return $next($request);
    }
}