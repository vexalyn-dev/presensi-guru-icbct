<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    /**
     * Token reset password
     */
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $appName = config('app.name', 'ICB CINTA TEKNIKA');

        return (new MailMessage)
            ->subject('Reset Password Akun ' . $appName)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kamu menerima email ini karena ada permintaan reset password untuk akunmu.')
            ->line('Klik tombol di bawah untuk membuat password baru. Link ini berlaku selama **60 menit** ya.')
            ->action('Reset Password Sekarang', $resetUrl)
            ->line('Kalau kamu nggak merasa minta reset password, abaikan aja email ini — akunmu tetap aman kok.')
            ->salutation('Salam, Tim ' . $appName);
    }
}
