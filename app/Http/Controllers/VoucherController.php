<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    // 1. Danh sách Voucher
    public function index()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    // 2. Hiện form tạo mới
    public function create()
    {
        return view('admin.vouchers.create');
    }

    // 3. Lưu Voucher mới
    public function store(Request $request)
    {
        // 1. Tự động viết hoa Mã Code trước khi kiểm tra
        $request->merge(['code' => strtoupper($request->code)]);

        // 2. Validate dữ liệu
        $request->validate([
            'code' => 'required|unique:vouchers,code', // Đã bỏ rule 'uppercase'
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date', // Ngày kết thúc phải sau ngày bắt đầu
        ], [
            'code.unique' => 'Mã này đã tồn tại!',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu!',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
            'start_date.required' => 'Chưa chọn ngày bắt đầu.',
            'end_date.required' => 'Chưa chọn ngày kết thúc.'
        ]);

        // 3. Lưu vào DB
        Voucher::create($request->all());

        return redirect()->route('vouchers.index')->with('success', 'Tạo mã giảm giá thành công!');
    }

    // 4. Hiện form sửa
    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    // 5. Cập nhật Voucher
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        
        $request->validate([
            'code' => 'required|uppercase|unique:vouchers,code,' . $id,
            'value' => 'required|numeric|min:0',
            'end_date' => 'required|date|after:start_date',
        ]);

        $voucher->update($request->all());

        return redirect()->route('vouchers.index')->with('success', 'Cập nhật thành công!');
    }

    // 6. Xóa Voucher
    public function destroy($id)
    {
        Voucher::findOrFail($id)->delete();
        return redirect()->route('vouchers.index')->with('success', 'Đã xóa mã giảm giá!');
    }
}