<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorRecoveryCodesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<int, string>  $codes
     */
    public function __construct(public array $codes) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Códigos de respaldo (2FA)')
            ->line('Has activado 2FA. Guarda estos códigos de respaldo en un lugar seguro:')
            ->line('');

        foreach ($this->codes as $code) {
            $mail->line($code);
        }

        return $mail
            ->line('')
            ->line('Si no activaste 2FA, cambia tu contraseña inmediatamente.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'codes_count' => count($this->codes),
        ];
    }
}
