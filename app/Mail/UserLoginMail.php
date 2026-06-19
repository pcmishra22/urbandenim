<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserLoginMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $loginAt,
        public string $ip
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Login to Your Jeanzo Account');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.user-login');
    }
}
