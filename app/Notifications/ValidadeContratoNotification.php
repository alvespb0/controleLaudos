<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use App\Models\Laudo;
use Illuminate\Support\Facades\Log;

class ValidadeContratoNotification extends Notification
{
    use Queueable;

    protected $laudo;
    /**
     * Create a new notification instance.
     */
    public function __construct(Laudo $laudo)
    {
        $this->laudo = $laudo;
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
            ->subject('Lembrete: contrato proximo da validade')
            ->greeting("Olá, {$notifiable->name}")
            ->line("O laudo do cliente: {$this->laudo->cliente->nome} Está próximo da data de validade.")
            ->line("Data de fim de contrato: {$this->laudo->data_fim_contrato}.")
            ->line("Aproveite esta oportunidade para entrar em contato com o cliente, e apresentar uma nova proposta!")
            ->line("Lembre-se, entrar em contato com o cliente antes do término do contrato demonstra proatividade
                    e comprometimento, reforçando sua relação de confiança e destacando sua dedicação em antecipar as
                    necessidades dele.")
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
