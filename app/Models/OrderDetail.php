<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = ['order_id', 'book_id', 'quantity', 'price', 'type'];

    // Chi tiết này thuộc về đơn hàng nào
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Chi tiết này là của cuốn sách nào
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}