<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatRoom;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat.{id}', function ($user, $id) {
    $authorized = ChatRoom::whereHas('participants', function ($query) use ($user) {
        $query->where('chat_room_participants.user_id', $user->user_id);
    })->where('id', $id)->exists();
    
    if ($authorized) {
        return [
            'id' => $user->user_id,
            'name' => $user->name,
            'initials' => $user->initials(),
            'role' => $user->roles->first()?->role_name,
        ];
    }
    
    return false;
});
