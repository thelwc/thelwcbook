<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Str; // ⚠️ QUAN TRỌNG: Phải thêm dòng này để dùng hàm tạo slug
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoriesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Kiểm tra xem dòng excel có tên danh mục không, nếu không thì bỏ qua
        if (!isset($row['ten_danh_muc']) || $row['ten_danh_muc'] == null) {
            return null; 
        }

        return Category::updateOrCreate(
            [
                'name' => $row['ten_danh_muc'],
            ],
            [
                'name' => $row['ten_danh_muc'],
                'slug' => Str::slug($row['ten_danh_muc']), 
            ]
        );
    }
}