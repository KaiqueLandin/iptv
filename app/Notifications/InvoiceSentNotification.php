<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceSentNotification extends Notification
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invoice = $this->invoice;

        return (new MailMessage)
            ->subject("Fatura {$invoice->invoice_number} — R$ {$invoice->total}")
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line("Você tem uma fatura aberta no valor de **R$ {$invoice->total}**.")
            ->line("Número: {$invoice->invoice_number}")
            ->line('Vencimento: '.($invoice->due_date?->format('d/m/Y') ?? '—'))
            ->action('Ver fatura', url('/'))
            ->line('Qualquer dúvida, entre em contato com nosso suporte.')
            ->salutation('Equipe ClickTV');
    }
}
