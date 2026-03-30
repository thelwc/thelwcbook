<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
{
    // 1. Kiểm tra đã đăng nhập chưa
    if (Auth::check()) {
        $role = Auth::user()->role;

        // 2. Cho phép Admin (0) HOẶC Staff (1) đi qua
        // (Lưu ý: role là số tinyInteger nhé)
        if ($role == 0 || $role == 1) {
            return $next($request);
        }
    }

    // 3. Nếu là khách (2) hoặc chưa đăng nhập -> Đuổi về
    return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập!');
}
}