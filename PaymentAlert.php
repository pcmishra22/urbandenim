<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentAlert extends Notification
{
    use Queueable;

    protected $data;

    /**
     * @param array $data Expected keys: order_id, amount, currency, status, message
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->data['order_id'] ?? null,
            'amount' => $this->data['amount'] ?? 0,
            'currency' => $this->data['currency'] ?? 'USD',
            'status' => $this->data['status'] ?? 'pending',
            'message' => $this->data['message'] ?? 'A payment update has occurred.',
            'type' => 'payment',
        ];
    }
}