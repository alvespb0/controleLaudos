<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;

class ProximoContatoNotification extends Notification
{
    use Queueable;

    protected $lead;
    /**
     * Create a new notification instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

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
            ->mailer('default')
            ->from('documentos@resolvesegmetre.com.br', 'Laudos Resolve Segmetre')
            ->subject('Lembrete: contato agendado com um lead')
            ->greeting("Olá, {$notifiable->name}")
            ->line("Você tem um contato agendado com o cliente {$this->lead->cliente->nome}.")
            ->line('Próximo contato: ' . Carbon::parse($this->lead->proximo_contato)->format('d/m/Y H:i'))
            ->line('Lembre-se de registrar o contato no sistema.')
            ->salutation('Atenciosamente, equipe Resolve Segmetre');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
