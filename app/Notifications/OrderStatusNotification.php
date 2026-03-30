<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $statusText = '';
        $icon = '';
        
        if($this->order->status == 'confirmed' || $this->order->status == 1) {
            $statusText = 'đã được xác nhận!';
            $icon = 'fas fa-check-circle text-primary';
        } elseif($this->order->status == 'cancelled' || $this->order->status == 2) {
            $statusText = 'đã bị hủy.';
            $icon = 'fas fa-times-circle text-danger';
        } else {
            $statusText = 'đã cập nhật trạng thái.';
            $icon = 'fas fa-info-circle text-info';
        }

        return [
            'title'   => 'Cập nhật đơn hàng #' . $this->order->id,
            'message' => 'Đơn hàng của bạn ' . $statusText,
            
            // 🔥 QUAN TRỌNG: Đã sửa thành 'client.account.history.detail' cho khớp với web.php
            'link'    => route('client.account.history.detail', $this->order->id),
            
            'icon'    => $icon
        ];
    }
}