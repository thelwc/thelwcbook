<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order; // Nhớ thêm dòng này

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $order; // Biến chứa thông tin đơn hàng

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('🔥 Xác nhận đơn hàng thành công - Thelwc Books')
                    ->view('emails.order_placed'); // Trỏ tới file giao diện mail
    }
}