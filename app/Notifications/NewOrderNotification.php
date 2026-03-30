<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database']; // Lưu vào database
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => 'Đơn hàng mới #' . $this->order->id,
            'message' => 'Khách hàng ' . $this->order->name . ' vừa đặt đơn mới.',
            // Link này khớp với route: name('orders.show') trong web.php
            'link'    => route('orders.show', $this->order->id), 
            'icon'    => 'fas fa-shopping-bag text-success'
        ];
    }
}