<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $senderName;
    public string $senderEmail;
    public string $contactSubject;
    public string $userMessage;

    public function __construct(
        string $senderName,
        string $senderEmail,
        string $contactSubject,
        string $userMessage
    ) {
        $this->senderName     = $senderName;
        $this->senderEmail    = $senderEmail;
        $this->contactSubject = $contactSubject;
        $this->userMessage    = $userMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contact Form: ' . $this->contactSubject,
            replyTo: [$this->senderEmail],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact');
    }
}
