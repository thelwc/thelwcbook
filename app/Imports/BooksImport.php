<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class BooksImport implements ToModel, WithHeadingRow
{
    // 🔥 Gài máy đếm chống báo cáo láo
    public $importedCount = 0; 

    public function model(array $row)
    {
        // 1. Chặn file rác
        if (!array_key_exists('ten_sach', $row)) {
            throw new \Exception('Sai định dạng file! File Excel bắt buộc phải có cột "Tên Sách".');
        }

        if (empty($row['ten_sach'])) {
            return null;
        }

        // 2. Tự động lấy ID Danh Mục & NXB
        $categoryId = null;
        if (!empty($row['danh_muc'])) {
            $category = Category::where('name', 'like', '%' . $row['danh_muc'] . '%')->first();
            $categoryId = $category ? $category->id : null; 
        }

        $publisherId = null;
        if (!empty($row['nha_xuat_ban'])) {
            $publisher = Publisher::where('name', 'like', '%' . $row['nha_xuat_ban'] . '%')->first();
            $publisherId = $publisher ? $publisher->id : null;
        }

        // 3. Xử lý logic Sách Nước Ngoài
        $is_foreign = 0;
        $checkForeign = mb_strtolower(trim($row['sach_nuoc_ngoai'] ?? ''), 'UTF-8');
        if (in_array($checkForeign, ['có', 'co', 'yes', '1'])) {
            $is_foreign = 1;
        }

        // 4. Xử lý Ngày Xuất Bản (Chống lỗi format Excel)
        $published_date = null;
        if (!empty($row['ngay_xuat_ban'])) {
            try {
                $published_date = Carbon::parse(str_replace('/', '-', $row['ngay_xuat_ban']))->format('Y-m-d');
            } catch (\Exception $e) {
                $published_date = null; // Bỏ qua nếu nhập ngày linh tinh
            }
        }

        // ✅ Lọt qua các ải -> Cộng máy đếm lên 1
        $this->importedCount++;

        // 5. Nhập đủ 100% data vào DB
        return Book::updateOrCreate(
            ['title' => $row['ten_sach']],
            [
                'author'        => $row['tac_gia'] ?? null,
                'category_id'   => $categoryId,
                'publisher_id'  => $publisherId,

                // --- Sách In ---
                'price'         => $row['gia_goc'] ?? 0,
                'sale_price'    => $row['gia_khuyen_mai'] ?? 0,
                'quantity'      => $row['ton_kho'] ?? 0,
                'total_sold'    => $row['da_ban_in'] ?? 0,
                'cover_type'    => $row['loai_bia'] ?? null,
                'dimensions'    => $row['kich_thuoc'] ?? null,
                'page_count'    => $row['so_trang'] ?? null,
                'published_date'=> $published_date,
                'is_foreign'    => $is_foreign,
                'translator'    => $is_foreign ? ($row['dich_gia'] ?? null) : null,

                // --- Ebook ---
                'ebook_price'   => $row['gia_ebook'] ?? null,
                'ebook_sold'    => $row['da_ban_ebook'] ?? 0,
                'preview_pages' => $row['trang_doc_thu'] ?? 0,
                'font_family'   => $row['font_chu'] ?? null,

                // --- Khác ---
                'description'   => $row['mo_ta'] ?? null,
                'created_at'    => $row['ngay_tao'] ?? now(),
            ]
        );
    }
}