<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotify extends Notification
{
    use Queueable;
    public $review;

    public function __construct($review) { $this->review = $review; }
    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Đánh giá mới! ⭐',
            'message' => 'Khách hàng '.$this->review->user->name.' vừa đánh giá sách: '.$this->review->book->title,
            'link' => route('admin.reviews.index'), // Đổi thành link cho khớp với Blade
            'icon' => 'fas fa-star text-warning'    // Icon ngôi sao màu vàng
        ];
    }
}