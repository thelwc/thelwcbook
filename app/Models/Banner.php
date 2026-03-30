<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    // 🔥 THÊM ĐOẠN NÀY ĐỂ CHO PHÉP LƯU DỮ LIỆU
    protected $fillable = [
        'title', 
        'description', 
        'image', 
        'link', 
        'status', 
        'order'
    ];
}