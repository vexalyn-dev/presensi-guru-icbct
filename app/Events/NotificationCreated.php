<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notification->notifiable_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title ?? ($this->notification->data['title'] ?? 'Notifikasi'),
            'message' => $this->notification->message ?? ($this->notification->data['message'] ?? ''),
            'icon' => $this->notification->icon ?? ($this->notification->data['icon'] ?? 'bell'),
            'bg_color' => $this->notification->color ?? ($this->notification->data['bg_color'] ?? 'bg-blue-100 text-blue-600'),
            'action_url' => $this->notification->action_url ?? ($this->notification->data['action_url'] ?? null),
            'created_at' => $this->notification->created_at->diffForHumans(),
        ];
    }

    public function broadcastAs()
    {
        return 'notification.created';
    }
}
