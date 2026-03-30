<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Hàm hiển thị giao diện cài đặt (Bước 2 tớ nói ở trên)
    public function index()
    {
        return view('admin.shipping_fee.index');
    }

    // Hàm lưu dữ liệu
    public function update(Request $request)
    {
        // 🔥 CHỐT CHẶN BẢO MẬT: Chỉ Role 1 (Giám đốc) và 2 (Quản lý) mới được phép đi tiếp
        if (!in_array(Auth::user()->role, [1, 2])) {
            return back()->with('error', 'Cảnh báo: Bạn không có quyền thay đổi phí vận chuyển! Chỉ Giám đốc và Quản lý mới được thao tác.');
        }

        // Cập nhật mốc Freeship
        Setting::updateOrCreate(
            ['key' => 'free_ship_threshold'],
            ['value' => $request->free_ship_threshold]
        );

        // Cập nhật tiền ship mặc định
        Setting::updateOrCreate(
            ['key' => 'shipping_fee'],
            ['value' => $request->shipping_fee]
        );

        return back()->with('success', 'Đã cập nhật biểu phí vận chuyển thành công!');
    }
}