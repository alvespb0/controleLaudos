<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;

class ClienteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $body;
    public $subject;
    public $files;
    public $replyToEmail;
    public $fromName;

    /**
     * Create a new message instance.
     */
    public function __construct($body, $subject, array $files = [], $replyToEmail = null, $fromName = null)
    {
        $this->body = $body;
        $this->subject = $subject;
        $this->files = $files;
        $this->replyToEmail = $replyToEmail;
        $this->fromName = $fromName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $replyTo = null;

        // Só define replyTo se houver email válido
        if (!empty($this->replyToEmail)) {
            $replyTo = [
                new Address($this->replyToEmail, $this->fromName)
            ];
        }
        #dd($replyTo);

        return new Envelope(
            subject: $this->subject,
            replyTo: $replyTo,
            from: new Address(config('mail.mailers.laudos.from.address'), $this->fromName ?? 'laudos')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail/Email_Cliente',
            with: [
                'body' => $this->body 
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        foreach ($this->files as $file) {
            $attachments[] = Attachment::fromData(
                fn () => $file['content'], 
                $file['name']
            )->withMime($file['mime']);
        }

        return $attachments;
    }
}
