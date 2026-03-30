<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow
{
    // 🔥 1. Khai báo biến đếm
    public $importedCount = 0; 

    public function model(array $row)
    {
        $email = trim($row['email'] ?? '');

        // Nếu rỗng hoặc sai email -> vứt, đéo đếm!
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null; 
        }

        $excelRole = $row['chuc_vu'] ?? ''; 
        $check = mb_strtolower(trim($excelRole), 'UTF-8');
        $role = 5; 

        if (in_array($check, ['0', 'admin', 'quản trị viên'])) {
            $role = 0;
        } elseif (in_array($check, ['1', 'giám đốc', 'director'])) {
            $role = 1;
        } elseif (in_array($check, ['2', 'quản lý', 'manager'])) {
            $role = 2;
        } elseif (in_array($check, ['3', 'nhân viên', 'staff'])) {
            $role = 3;
        } elseif (in_array($check, ['4', 'kiểm duyệt', 'moderator'])) {
            $role = 4;
        } elseif (in_array($check, ['5', 'khách hàng', 'user', 'customer', ''])) {
            $role = 5;
        }

        // 🔥 2. Đã lọt qua các chốt chặn an toàn -> Cộng biến đếm lên 1
        $this->importedCount++;

        return User::updateOrCreate(
            [ 'email' => $email ],
            [
                'name'     => $row['ho_ten'] ?? 'Chưa đặt tên',
                'role'     => $role, 
                'password' => Hash::make('123456'),
            ]
        );
    }
}