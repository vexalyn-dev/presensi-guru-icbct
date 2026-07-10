<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('notifiable_type', get_class(Auth::user()))
            ->where('notifiable_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('teacher.notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        $notification = Notification::where('id', $id)
            ->where('notifiable_type', get_class(Auth::user()))
            ->where('notifiable_id', Auth::id())
            ->first();

        if ($notification && !$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('notifiable_type', get_class(Auth::user()))
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }
}
