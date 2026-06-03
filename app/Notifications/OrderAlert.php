<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderAlert extends Notification
{
    use Queueable;

    protected Order $order;
    protected string $type;

    /**
     * @param Order  $order
     * @param string $type  e.g. new_order | status_changed | cancelled
     */
    public function __construct(Order $order, string $type = 'new_order')
    {
        $this->order = $order;
        $this->type  = $type;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id'   => $this->order->id,
            'user_id'    => $this->order->user_id,
            'type'       => $this->type,
            'status'     => $this->order->status,
            'total'      => $this->order->total_price,
            'message'    => match ($this->type) {
                'new_order'      => "New order #{$this->order->id} placed — ₹{$this->order->total_price}",
                'status_changed' => "Order #{$this->order->id} status changed to {$this->order->status}",
                'cancelled'      => "Order #{$this->order->id} has been cancelled",
                default          => "Order #{$this->order->id} event: {$this->type}",
            },
        ];
    }
}
