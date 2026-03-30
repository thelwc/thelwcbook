<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Khai báo những cột được phép lưu vào database
    protected $fillable = [
        'title',       // Tên sách
        'author',      // Tác giả
        'price',
        'sale_price',  // Giá bán khuyến mãi
        'quantity',    // Số lượng
        'description', // Mô tả
        'image',       // Ảnh bìa
        'category_id', 
        'publisher_id',

        // 🔥 CÁC CỘT MỚI VỪA THÊM (QUAN TRỌNG)
        'published_date',  // Ngày xuất bản
        'cover_type',      // Loại bìa
        'preview_pages', // 🔥 THÊM DÒNG NÀY
        'ebook_sold',
        'dimensions',      // Khổ giấy
        'page_count',      // Số trang
        'is_foreign',      // Sách nước ngoài? (0/1)
        'translator',      // Dịch giả
        'file_preview',    // File đọc thử
        'file_ebook',      // File bán
        'ebook_price',     // Giá Ebook
        'book_content', // <--- THÊM DÒNG NÀY VÀO DANH SÁCH\
        'file_size',      
        'font_family',
    ];

    // --- Khai báo mối quan hệ ---
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->orderBy('created_at', 'desc');
    }
}