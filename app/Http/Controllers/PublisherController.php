<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publisher; // Gọi Model Publisher

// use App\Exports\PublishersExport; // Khi nào tạo file Export thì mở dòng này
// use App\Imports\PublishersImport; // Khi nào tạo file Import thì mở dòng này
// use Maatwebsite\Excel\Facades\Excel;

class PublisherController extends Controller
{
    // 1. Danh sách Nhà xuất bản
    public function index()
    {
        // Lấy danh sách, sắp xếp mới nhất lên đầu, phân trang 10 dòng
        $publishers = Publisher::orderBy('id', 'desc')->paginate(10);
        
        // Trả về view trong thư mục admin/publishers
        return view('admin.publishers.index', compact('publishers'));
    }

    // 2. Form thêm mới
    public function create()
    {
        return view('admin.publishers.create');
    }

    // 3. Lưu vào database
    public function store(Request $request)
    {
        // Validate: Tên bắt buộc và không trùng
        $request->validate(
            ['name' => 'required|unique:publishers,name'],
            ['name.required' => 'Tên NXB không được trống', 'name.unique' => 'Tên NXB này đã tồn tại']
        );

        Publisher::create($request->all());

        return redirect()->route('publishers.index')->with('success', 'Thêm Nhà xuất bản thành công!');
    }

    // 4. Form sửa
    public function edit(string $id)
    {
        $publisher = Publisher::findOrFail($id);
        return view('admin.publishers.edit', compact('publisher'));
    }

    // 5. Cập nhật
    public function update(Request $request, string $id)
    {
        $request->validate(
            ['name' => 'required|unique:publishers,name,'.$id], // Cho phép trùng tên với chính nó
            ['name.required' => 'Tên NXB không được trống']
        );

        $publisher = Publisher::findOrFail($id);
        $publisher->update($request->all());
        return redirect()->route('publishers.index')->with('success', 'Cập nhật Nhà xuất bản thành công!');
    }

    // 6. Xóa
    public function destroy(string $id)
    {
        $publisher = Publisher::findOrFail($id);

        // Kiểm tra xem NXB này có sách nào không
        // (Dựa vào relation 'books' đã khai báo trong Model Publisher)
        if ($publisher->books()->count() > 0) {
            return back()->with('error', 'Không thể xóa! Nhà xuất bản này đang có sách liên kết.');
        }

        $publisher->delete();
        return redirect()->route('publishers.index')->with('success', 'Đã xóa Nhà xuất bản!');
    }

    /* |--------------------------------------------------------------------------
    | PHẦN EXCEL (TẠM ĐÓNG VÌ CHƯA CÓ FILE EXPORT/IMPORT CHO PUBLISHER)
    |--------------------------------------------------------------------------
    | Khi nào cậu tạo xong file Export/Import thì mở comment đoạn dưới này ra nhé.
    |
    
    public function export() 
    {
        if (ob_get_length() > 0) ob_end_clean();
        return Excel::download(new PublishersExport, 'danh-sach-nxb.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'Vui lòng chọn file Excel trước!',
            'file.mimes' => 'Chỉ chấp nhận file đuôi .xlsx hoặc .xls'
        ]);

        try {
            Excel::import(new PublishersImport, $request->file('file'));
            return back()->with('success', 'Nhập danh sách thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi file Excel! Vui lòng kiểm tra lại cấu trúc.');
        }
    }
    */
}