<?php

namespace App\Mail;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ReturnRequest $returnRequest,
        public string $updatedBy = 'admin'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Return Request #{$this->returnRequest->id} — " . $this->returnRequest->status_label . " | Jeanzo",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.return-status-updated');
    }

    public function attachments(): array { return []; }
}
