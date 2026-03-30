<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'address', 'note', 'payment_method', 'total_price', 'status', 'discount', 'coupon_code', 'shipping_fee' // <--- THÊM DÒNG NÀY VÀO LÀ XONG!
    ];

    // --- CẬU KIỂM TRA KỸ ĐOẠN NÀY ---
    // Phải có hàm này thì mới lấy được danh sách sách trong đơn
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Quan hệ với User (nếu cần hiển thị tên user đặt)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}