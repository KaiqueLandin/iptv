<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceActivatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Service $service) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $service = $this->service;

        $mail = (new MailMessage)
            ->subject('Seu acesso IPTV está pronto! 🎉')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Seu pagamento foi confirmado e seu acesso já está ativo.')
            ->line('**Dados de acesso:**');

        if ($service->activation_code) {
            $mail->line('Código de ativação: '.$service->activation_code);
        }
        if ($service->username) {
            $mail->line('Usuário: '.$service->username);
        }
        if ($service->password) {
            $mail->line('Senha: '.$service->password);
        }
        if ($service->access_url) {
            $mail->line('URL de acesso: '.$service->access_url);
        }
        if ($service->expires_at) {
            $mail->line('Válido até: '.$service->expires_at->format('d/m/Y'));
        }

        return $mail
            ->action('Ver no painel', url('/'))
            ->line('Você também pode consultar esses dados a qualquer momento no seu painel.')
            ->salutation('Equipe ClickTV');
    }
}
