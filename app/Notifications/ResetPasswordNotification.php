<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from('informatica@fiscalia.gob.bo', 'MINISTERIO PÚBLICO')
            ->subject('Solicitud de restablecimiento de contraseña.')
            ->greeting('Hola ' . $notifiable->name . '.')
            ->line('Recibes este correo electrónico porque se solicitó un restablecimiento de contraseña para tu cuenta.')
            ->action('Restablecer contraseña', url(config('app.url').route('password.reset', $this->token, false)))
            ->line('Si no realizaste esta petición, puedes ignorar este correo y nada habrá cambiado.')
            ->salutation('¡Saludos!');
    }
}
