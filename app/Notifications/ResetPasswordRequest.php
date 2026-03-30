<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordRequest extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Tạo đường dẫn reset password
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('🔒 Yêu cầu đặt lại mật khẩu - Thelwc Books') // Tiêu đề mail
            ->greeting('Xin chào bạn!')
            ->line('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu lấy lại mật khẩu cho tài khoản của bạn.')
            ->action('👉 Đặt lại mật khẩu ngay', $url) // Nút bấm
            ->line('Liên kết đặt lại mật khẩu này sẽ hết hạn sau 60 phút.')
            ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.')
            ->salutation('Trân trọng, Admin Thelwc');
    }
}