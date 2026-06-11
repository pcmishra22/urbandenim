<?php
namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDispatchedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public ?string $trackingNumber = null,
        public ?string $courierName = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Order #' . $this->order->id . ' Has Been Shipped 🚚 — Jeanzo');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-dispatched');
    }
}
