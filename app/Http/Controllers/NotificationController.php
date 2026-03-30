<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Trang danh sách tất cả thông báo
    public function index()
    {
        // Lấy thông báo của user đang đăng nhập, phân trang 10 cái
        $notifications = \Illuminate\Support\Facades\Auth::user()->notifications()->paginate(10);

        // ĐỊNH TUYẾN: Nếu là Admin/Nhân sự (Role 0, 1, 2, 3, 4) -> Trả về giao diện Admin
        if (in_array(auth()->user()->role, [0, 1, 2, 3, 4])) {
            return view('admin.notifications.index', compact('notifications'));
        }

        // ĐỊNH TUYẾN: Nếu là Khách hàng (Role 5) -> Trả về giao diện Khách như cũ
        return view('client.notifications.index', compact('notifications'));
    }

    // Đánh dấu tất cả là đã đọc
    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Đã đánh dấu tất cả là đã đọc.');
    }
}