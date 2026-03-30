<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ForceAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ chặn duy nhất ông Admin (0) - Vì ông này quản trị hệ thống, ko mua bán
        if (Auth::check() && Auth::user()->role == 0) {
            return redirect()->route('users.index');
        }

        // Còn lại: Giám đốc (1), Quản lý (2), Nhân viên (3), Kiểm duyệt (4), Khách (5)
        // -> ĐỀU ĐƯỢC PHÉP XEM WEBSITE
        return $next($request);
    }
}