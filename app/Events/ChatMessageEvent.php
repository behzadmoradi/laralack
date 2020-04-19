<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;
    public $recipientId;

    public function __construct($message, $userId, $recipientId)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->recipientId = $recipientId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.message.websocket.channel.' . $this->userId . '.' . $this->recipientId);
    }
}
