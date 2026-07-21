<?php

use Illuminate\Broadcasting\Broadcast;
use Illuminate\Support\Facades\Broadcast as BroadcastFacade;

BroadcastFacade::channel('notifications.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
