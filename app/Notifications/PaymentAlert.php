<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentAlert extends Notification
{
    use Queueable;

    protected array $data;

    /**
     * @param array $data  Keys: order_id, amount, currency, status, message
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return array_merge([
            'type'     => 'payment',
            'message'  => "Payment received for Order #{$this->data['order_id']}",
        ], $this->data);
    }
}
