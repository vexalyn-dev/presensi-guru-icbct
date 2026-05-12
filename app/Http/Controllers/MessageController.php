<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Admin: Show list of conversations
    public function index()
    {
        // Get users who have messages, sorted by latest
        // For simplicity, we can get all users who are not admin, 
        // or just group messages by `conversation_user_id`
        
        $users = User::whereHas('messagesAsConversationUser')
                    ->with(['messagesAsConversationUser' => function($q) {
                        $q->latest()->limit(1);
                    }])
                    ->get()
                    ->sortByDesc(function($user) {
                        return $user->messagesAsConversationUser->first()->created_at ?? null;
                    });
                    
        return view('messages.index', compact('users'));
    }

    // Common: Fetch messages for a specific user conversation
    public function fetchMessages(User $user)
    {
        $currentUser = Auth::user();
        
        // If teacher is trying to fetch someone else's messages
        if (!$currentUser->isAdmin() && $currentUser->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Message::where('conversation_user_id', $user->id)
                    ->with('sender')
                    ->oldest()
                    ->get();
                    
        // Mark messages as read if the recipient is the current user
        // If current user is Admin, mark messages where sender is NOT admin as read
        // If current user is Teacher, mark messages where sender IS admin as read
        Message::where('conversation_user_id', $user->id)
                    ->where('is_read', false)
                    ->where('sender_id', '!=', $currentUser->id)
                    ->update(['is_read' => true]);

        return response()->json($messages);
    }

    // Common: Send a new message
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_user_id' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user();
        $targetUserId = $request->conversation_user_id;

        // Verify authorization
        if (!$currentUser->isAdmin() && $currentUser->id != $targetUserId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'conversation_user_id' => $targetUserId,
            'sender_id' => $currentUser->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json($message->load('sender'));
    }
}
