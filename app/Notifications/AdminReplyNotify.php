<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminReplyNotify extends Notification
{
    use Queueable;
    public $review;

    public function __construct($review) { $this->review = $review; }
    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Admin đã phản hồi 💬',
            'message' => 'Thelwc Books vừa trả lời đánh giá của bạn về cuốn: '.$this->review->book->title,
            'link' => route('book.detail', $this->review->book_id),
            'icon' => 'fas fa-comment-dots text-primary'            // Icon tin nhắn màu xanh
        ];
    }
}