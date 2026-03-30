<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::all();
    }

    public function map($user): array
    {
        // 💡 BẢNG TỪ ĐIỂN DỊCH TỪ SỐ SANG CHỮ KHI XUẤT EXCEL
        // Chuẩn theo đúng hệ thống của cậu: 
        // 0: Admin, 1: Giám đốc, 2: Quản lý, 3: Nhân viên, 4: Kiểm duyệt, 5: Khách hàng
        
        $roleName = 'Khách hàng'; // Mặc định nếu không nằm trong các số kia

        if ($user->role == 0) {
            $roleName = 'Quản trị viên';
        } elseif ($user->role == 1) {
            $roleName = 'Giám đốc';
        } elseif ($user->role == 2) {
            $roleName = 'Quản lý';
        } elseif ($user->role == 3) {
            $roleName = 'Nhân viên';
        } elseif ($user->role == 4) {
            $roleName = 'Kiểm duyệt';
        } elseif ($user->role == 5) {
            $roleName = 'Khách hàng';
        }

        return [
            $user->id,
            $user->name,
            $user->email,
            $roleName,
        ];
    }

    public function headings(): array
    {
        return ['ID', 'Họ Tên', 'Email', 'Chức Vụ'];
    }
}