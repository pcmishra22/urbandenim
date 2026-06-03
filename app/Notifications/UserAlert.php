<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserAlert extends Notification
{
    use Queueable;

    protected User $user;
    protected string $type;

    /**
     * @param User $user
     * @param string $type e.g. login
     */
    public function __construct(User $user, string $type = 'login')
    {
        $this->user = $user;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'type' => $this->type,
            'message' => match ($this->type) {
                'login' => "Admin login: {$this->user->name}",
                default => "User event '{$this->type}' for {$this->user->name}",
            },
        ];
    }
}

