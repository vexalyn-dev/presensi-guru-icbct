<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_user_id',
        'sender_id',
        'message',
        'is_read',
    ];

    public function conversationUser()
    {
        return $this->belongsTo(User::class, 'conversation_user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
