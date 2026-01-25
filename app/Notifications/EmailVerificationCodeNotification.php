<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\CarbonInterface;

class EmailVerificationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  CarbonInterface  $expiresAt
     */
    public function __construct(
        public string $code,
        public CarbonInterface $expiresAt,
    ) {}

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
        return (new MailMessage)
            ->subject('Tu código de verificación')
            ->line('Usa el siguiente código para verificar tu cuenta:')
            ->line($this->code)
            ->line('Este código expira a las '.$this->expiresAt->format('H:i').' (máximo 15 minutos).')
            ->line('Si no solicitaste este código, puedes ignorar este correo.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'expires_at' => $this->expiresAt->toISOString(),
        ];
    }
}
