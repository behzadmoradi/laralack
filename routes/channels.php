<?php
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('message.channel.{channelId}', function ($user, $channelId) {
    $allChannels = User::find($user->id)->channels()->pluck('id')->toArray();
    if (in_array($channelId, $allChannels)) {
        return true;
    }
});

Broadcast::channel('channel.deletion.channel', function ($user) {
    return $user;
});

Broadcast::channel('start.chat.websocket.channel', function ($user) {
    return $user;
});

Broadcast::channel('chat.message.websocket.channel.{userId}.{recipientId}', function ($user, $userId, $recipientId) {
    if ($user->id == $userId || $user->id == $recipientId) {
        return true;
    }
});

Broadcast::channel('online.users.websocket.channel', function ($user) {
    return $user;
});

Broadcast::channel('is.typing.websocket.channel', function ($user) {
    return $user;
});
