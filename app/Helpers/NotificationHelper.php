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
        return Notification::create([
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
            ],
        ]);
    }

    public static function markAllAsRead(User $user): void
    {
        Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}