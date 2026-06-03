<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderAlert extends Notification
{
    use Queueable;

    protected $order;
    protected $type;

    public function __construct(Order $order, $type = 'new_order')
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->user->name ?? 'Guest',
            'amount' => $this->order->total_price,
            'status' => $this->order->status,
            'message' => $this->getMessage(),
            'type' => 'order',
        ];
    }

    protected function getMessage()
    {
        switch ($this->type) {
            case 'cancelled':
                return "Order #{$this->order->id} has been cancelled.";
            case 'new_order':
            default:
                return "New order received: #{$this->order->id}.";
        }
    }
}