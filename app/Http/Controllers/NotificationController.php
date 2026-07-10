<?php

namespace App\Http\Controllers;

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

        return view('notifications.index', compact('notifications'));
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

    public function clearAll()
    {
        Notification::where('notifiable_type', get_class(Auth::user()))
            ->where('notifiable_id', Auth::id())
            ->delete();

        return back()->with('success', 'Semua notifikasi telah dihapus');
    }
}