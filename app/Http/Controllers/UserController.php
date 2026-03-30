<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
// ❌ Đã xóa use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    // 1. Danh sách nhân viên & khách hàng
    public function index(Request $request)
    {
        // 1. Khóa bảo mật: Chỉ Sếp, Admin, Quản lý mới được xem
        if (!in_array(Auth::user()->role, [0, 1, 2])) {
            return redirect()->route('orders.index')->with('error', 'Khu vực cấm! Bạn không có quyền xem danh sách nhân sự.');
        }

        $query = User::query(); 

        // 2. 🔍 LỌC TỪ KHÓA (Tên hoặc Email)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 3. 🎯 LỌC NÂNG CAO (Nhiều Checkbox cùng lúc)
        if ($request->has('roles') && is_array($request->roles)) {
            // Nếu người dùng tích vào các ô checkbox -> Lọc theo mảng đó
            $query->whereIn('role', $request->roles);
        } 
        // 4. Back-up: Dành cho lúc bấm từ Dashboard sang (URL chỉ có 1 role)
        elseif ($request->filled('role')) {
            if ($request->role === 'staff') {
                $query->whereIn('role', [0, 1, 2, 3, 4]);
            } else {
                $query->where('role', $request->role);
            }
        }

        // Sắp xếp, phân trang và dán TOÀN BỘ tham số lọc lên URL để không bị mất khi qua trang 2
        $users = $query->orderBy('id', 'asc')->paginate(10);
        $users->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    // --- FORM THÊM MỚI ---
    public function create()
    {
        if (Auth::user()->role != 0) { return redirect()->route('dashboard'); }

        // ❌ Đã xóa lấy danh sách Branch
        return view('admin.users.create');
    }

    // --- XỬ LÝ LƯU MỚI ---
    public function store(Request $request)
    {
        if (Auth::user()->role != 0) { return redirect()->route('dashboard'); }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|integer',
            // ❌ Đã xóa validate branch_id
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            // ❌ Đã xóa branch_id
        ]);

        return redirect()->route('users.index')->with('success', 'Thêm nhân viên thành công!');
    }

    // 3. Hiện form sửa
    public function edit($id)
    {
        if (Auth::user()->role != 0) { return redirect()->route('dashboard'); }

        $user = User::findOrFail($id);
        // ❌ Đã xóa lấy danh sách Branch
        
        return view('admin.users.edit', compact('user'));
    }

    // 4. Lưu thay đổi
    public function update(Request $request, $id)
    {
        if (Auth::user()->role != 0) { return redirect()->route('dashboard'); }

        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|integer',
            // ❌ Đã xóa validate branch_id
        ]);

        // Cập nhật thông tin
        $user->role = $request->role;
        // ❌ Đã xóa $user->branch_id = $request->branch_id;
        $user->save();

        return redirect()->route('users.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    // 2. Xóa tài khoản
    public function destroy($id)
    {
        if (Auth::user()->role != 0) {
            return redirect()->route('dashboard');
        }

        User::destroy($id);
        return redirect()->back()->with('success', 'Đã xóa nhân viên thành công!');
    }
    
    // --- EXCEL ---
    public function export() 
    {
        // 🔥 CHỐT CHẶN: Chỉ Admin mới được xuất dữ liệu tài khoản
        if (Auth::user()->role != 0) {
            return back()->with('error', 'Cút! Bạn không có quyền xuất dữ liệu nhân sự!');
        }

        if (ob_get_length() > 0) ob_end_clean();
        return Excel::download(new UsersExport, 'danh-sach-nhan-su.xlsx');
    }

    public function import(Request $request) 
    {
        // 🔥 CHỐT CHẶN: Chỉ Admin mới được nhập dữ liệu tài khoản
        if (Auth::user()->role != 0) {
            return back()->with('error', 'Cút! Bạn không có quyền nhập dữ liệu nhân sự!');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'Chưa chọn file Excel!',
            'file.mimes' => 'File phải là đuôi .xlsx hoặc .xls'
        ]);

        try {
            $import = new UsersImport(); 
            Excel::import($import, $request->file('file'));

            if ($import->importedCount > 0) {
                return back()->with('success', 'Tuyệt vời Admin! Đã nhập thành công ' . $import->importedCount . ' tài khoản!');
            } else {
                return back()->with('error', 'File xàm! Không tìm thấy tài khoản nào hợp lệ.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi nhập liệu: ' . $e->getMessage());    
        }
    }
}