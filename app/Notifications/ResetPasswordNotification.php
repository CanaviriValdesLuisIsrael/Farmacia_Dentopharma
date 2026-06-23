<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Services\BrevoMailer;

class ResetPasswordNotification extends Notification
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        try {
            BrevoMailer::send(
                $notifiable->email,
                $notifiable->name,
                'Restablecer Contraseña - Farmacia Dentopharma',
                "
                <h2>Restablecer Contraseña</h2>
                <p>Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta en <strong>Farmacia Dentopharma</strong>.</p>
                <p><a href='{$url}' style='background:#3490dc;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Restablecer Contraseña</a></p>
                <p>Este enlace expirará en <strong>60 minutos</strong>.</p>
                <p>Si no solicitaste restablecer tu contraseña, ignora este mensaje.</p>
                <p>Saludos,<br><strong>Farmacia Dentopharma</strong></p>
                "
            );
        } catch (\Exception $e) {
            \Log::error('Error enviando reset password: ' . $e->getMessage());
        }

        // Fallback a mail normal (Mailtrap en local)
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Restablecer Contraseña - Farmacia Dentopharma')
            ->line('Recibiste este correo porque solicitaste restablecer tu contraseña.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace expirará en 60 minutos.');
    }
}