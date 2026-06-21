<?php

namespace App\Mail;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ReturnRequest $returnRequest,
        public string $recipientType = 'admin' // 'admin' or 'vendor'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Return Request #{$this->returnRequest->id} — Order #{$this->returnRequest->order_id} | Jeanzo",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.return-requested');
    }

    public function attachments(): array
    {
        return [];
    }
}
