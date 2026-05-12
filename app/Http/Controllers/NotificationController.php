<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        auth()->user()->unreadNotifications->markAsRead();
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return back();
    }

    public function clearAll()
    {
        auth()->user()->notifications()->delete();
        
        return back()->with('success', 'Semua notifikasi telah dihapus');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back();
    }
}
