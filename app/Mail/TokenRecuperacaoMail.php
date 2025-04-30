<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TokenRecuperacaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $nome;

    /**
     * Create a new message instance.
     */
    public function __construct($token, $nome)
    {
        $this->token = $token;
        $this->nome = $nome;    
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Token Recuperacao Senha',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Auth/Mail/Email_token_recuperacao',
            with: [
                'token' => $this->token,
                'nome' => $this->nome,
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
        return [];
    }
}
