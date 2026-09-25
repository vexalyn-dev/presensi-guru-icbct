<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationHelper
{
    public static function send(
        User $user,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        string $icon = 'bell',
        string $color = 'bg-blue-100 text-blue-600'
    ): Notification {
        // Save both `color` and `bg_color` to remain compatible with views/JS
        $notification = Notification::create([
            'id' => Str::uuid()->toString(),
            'type' => $type,
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'icon' => $icon,
                'color' => $color,
                'bg_color' => $color,
            ],
        ]);

        // Broadcast real-time event for this notification (if broadcasting configured)
        try {
            event(new \App\Events\NotificationCreated($notification));
        } catch (\Throwable $e) {
            // ignore if broadcasting not configured
        }

        return $notification;
    }

    public static function markAllAsRead(User $user): void
    {
        Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}