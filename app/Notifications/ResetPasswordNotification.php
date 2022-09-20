<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $url, string $name)
    {
        //
        $this->url = $url;
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->greeting('Restablecimiento de contraseña del portal')
            ->line('Hola, ' . $this->name)
            ->line(
                'Parece que olvidaste la contraseña de tu cuenta. Si es así, haz clic en el botón de abajo para restablecerla:',
            )
            ->action('Restablecer contraseña', $this->url)
            ->line(
                'Este enlace para restablecer tu contraseña caducará en 60 minutos.',
            )
            ->line(
                'Si no solicitaste restablecer tu contraseña, puedes ignorar este correo de forma segura.',
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
                //
            ];
    }
}
