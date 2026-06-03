<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserAlert extends Notification
{
    use Queueable;

    protected $user;
    protected $action;

    public function __construct(User $user, $action = 'signup')
    {
        $this->user = $user;
        $this->action = $action;
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
            'user_email' => $this->user->email,
            'message' => $this->getMessage(),
            'type' => 'user',
            'action' => $this->action,
        ];
    }

    protected function getMessage()
    {
        switch ($this->action) {
            case 'login':
                return "User {$this->user->name} has logged in.";
            case 'signup':
            default:
                return "New user registered: {$this->user->name} ({$this->user->email}).";
        }
    }
}