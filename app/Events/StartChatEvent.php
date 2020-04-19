<?php
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StartChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $recipient_id;

    public function __construct($recipientId, $user)
    {
        $this->user = $user;
        $this->recipient_id = $recipientId;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('start.chat.websocket.channel');
    }
}
