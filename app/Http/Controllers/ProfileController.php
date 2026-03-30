<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // 1. Hiển thị form
    public function edit()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Nếu là tài khoản nội bộ (Admin, Quản lý...) -> Gọi file giao diện Admin
        if (in_array($user->role, [0, 1, 2, 3, 4])) {
            return view('admin.profile', compact('user')); // 🔥 Tớ đã thêm compact('user') ở đây!
        }

        // Nếu là Khách hàng bình thường -> Gọi file giao diện Khách
        return view('client.account.profile', compact('user')); // 🔥 Nhớ thêm compact('user') ở đây nữa!
    }

    // 2. Xử lý cập nhật
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480', // Tối đa 2MB
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
            'avatar.max' => 'Ảnh tối đa 20MB thôi nhé!',
        ]);

        // Chuẩn bị dữ liệu để lưu
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        // Xử lý Upload Avatar
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có (Optional - để tiết kiệm dung lượng)
            // if ($user->avatar && file_exists(public_path($user->avatar))) {
            //     unlink(public_path($user->avatar));
            // }

            $file = $request->file('avatar');
            $filename = time() . '_avt_' . $user->id . '.' . $file->getClientOriginalExtension();
            
            // Lưu vào public/uploads/avatars
            $file->move(public_path('uploads/avatars'), $filename);
            
            $data['avatar'] = 'uploads/avatars/' . $filename;
        }

        // Cập nhật vào DB
        $user->update($data);

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }
}