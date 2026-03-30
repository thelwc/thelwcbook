<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Exports\CategoriesExport;
use App\Imports\CategoriesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str; // <--- 1. ĐÃ THÊM THƯ VIỆN NÀY

class CategoryController extends Controller
{
    // 1. Danh sách danh mục
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    // 2. Form thêm mới
    public function create()
    {
        return view('admin.categories.create');
    }

    // 3. Lưu vào database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        // Lấy toàn bộ dữ liệu từ form
        $data = $request->all();

        // 🔥 TỰ ĐỘNG TẠO SLUG TỪ TÊN
        $data['slug'] = Str::slug($request->name);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    // 4. Form sửa
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    // 5. Cập nhật
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            // unique:bảng,cột,id_bỏ_qua (để không báo lỗi trùng với chính nó)
            'name' => 'required|unique:categories,name,' . $id 
        ]);

        $data = $request->all();

        // 🔥 TỰ ĐỘNG CẬP NHẬT SLUG NẾU ĐỔI TÊN
        $data['slug'] = Str::slug($request->name);

        $category->update($data);

        // Code cũ của cậu thiếu dòng return này nên sửa xong nó bị trắng trang, mình đã thêm vào:
        return redirect()->route('categories.index')->with('success', 'Cập nhật thành công!');
    }

    // 6. Xóa
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        // Kiểm tra xem danh mục này có sách nào không
        if ($category->books()->count() > 0) {
            return back()->with('error', 'Không thể xóa! Danh mục này đang chứa sách.');
        }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Đã xóa danh mục!');
    }

    // Xuất Excel
    public function export() 
    {
        if (ob_get_length() > 0) ob_end_clean();
        return Excel::download(new CategoriesExport, 'danh-sach-the-loai.xlsx');
    }

    // Nhập Excel
    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'Vui lòng chọn file Excel trước!',
            'file.mimes' => 'Chỉ chấp nhận file đuôi .xlsx hoặc .xls'
        ]);

        try {
            Excel::import(new CategoriesImport, $request->file('file'));
            return back()->with('success', 'Nhập danh mục thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: File Excel không đúng mẫu! Cần có cột: "Tên Danh Mục"');
        }
    }
}