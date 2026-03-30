<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\File; // Thư viện để xử lý file (xóa ảnh cũ)

class BannerController extends Controller
{
    /**
     * 1. DANH SÁCH BANNER (INDEX)
     */
    public function index()
    {
        // Lấy danh sách banner, sắp xếp theo thứ tự ưu tiên (order)
        $banners = Banner::orderBy('order', 'asc')->latest()->paginate(10);
        
        // Trả về view: resources/views/admin/banners/index.blade.php
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * 2. HIỆN FORM THÊM MỚI (CREATE)
     */
    public function create()
    {
        // Trả về view: resources/views/admin/banners/create.blade.php
        return view('admin.banners.create');
    }

    /**
     * 3. XỬ LÝ LƯU BANNER MỚI (STORE)
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480', // Bắt buộc có ảnh
            'order' => 'integer|min:0',
            'status' => 'required|in:active,hidden'
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề banner',
            'image.required' => 'Vui lòng chọn hình ảnh',
            'image.image' => 'File tải lên phải là hình ảnh',
        ]);

        $input = $request->all();

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName(); // Đặt tên file theo thời gian để tránh trùng
            $file->move(public_path('uploads/banners'), $filename);
            
            // Lưu đường dẫn vào DB
            $input['image'] = 'uploads/banners/' . $filename;
        }

        Banner::create($input);

        return redirect()->route('banners.index')->with('success', 'Thêm banner mới thành công!');
    }

    /**
     * 4. HIỆN FORM SỬA (EDIT)
     */
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        // Trả về view: resources/views/admin/banners/edit.blade.php
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * 5. XỬ LÝ CẬP NHẬT (UPDATE)
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480', // Ảnh không bắt buộc khi sửa
            'order' => 'integer|min:0',
            'status' => 'required|in:active,hidden'
        ]);

        $input = $request->all();

        // Xử lý ảnh mới (Nếu có upload ảnh mới thì xóa ảnh cũ đi)
        if ($request->hasFile('image')) {
            // 1. Xóa ảnh cũ trong folder nếu tồn tại
            if (File::exists(public_path($banner->image))) {
                File::delete(public_path($banner->image));
            }

            // 2. Upload ảnh mới
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $filename);
            $input['image'] = 'uploads/banners/' . $filename;
        } else {
            // Nếu không chọn ảnh mới thì giữ nguyên đường dẫn cũ
            $input['image'] = $banner->image;
        }

        $banner->update($input);

        return redirect()->route('banners.index')->with('success', 'Cập nhật banner thành công!');
    }

    /**
     * 6. XÓA BANNER (DESTROY)
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Xóa file ảnh trong thư mục uploads trước
        if (File::exists(public_path($banner->image))) {
            File::delete(public_path($banner->image));
        }

        // Xóa dữ liệu trong DB
        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Đã xóa banner!');
    }
    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Đảo ngược trạng thái: Nếu đang active (hoặc 1) thì thành hidden (hoặc 0), và ngược lại
        if ($banner->status == 'active' || $banner->status == 1) {
            $banner->status = 'hidden'; // Hoặc số 0 tùy database của cậu
        } else {
            $banner->status = 'active'; // Hoặc số 1
        }
        
        $banner->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái banner!');
    }
}