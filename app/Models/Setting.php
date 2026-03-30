<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    
    public static function calculateShipping($subtotal, $needsShipping = true)
{
    // Lấy cấu hình từ DB, nếu không có thì dùng giá trị mặc định
    $mocFreeship = self::where('key', 'free_ship_threshold')->value('value') ?? 500000;
    $phiShipMacDinh = self::where('key', 'shipping_fee')->value('value') ?? 30000;

    // Logic tính toán
    if (!$needsShipping) {
        return 0; // Không cần ship (Ebook)
    }

    if ($subtotal >= $mocFreeship) {
        return 0; // Đạt mốc Freeship
    }

    return (int)$phiShipMacDinh; // Trả về phí ship mặc định
}
}
