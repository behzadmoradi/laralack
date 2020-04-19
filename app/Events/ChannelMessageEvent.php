<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channelId;
    public $message;

    public function __construct($channelId, $message)
    {
        $this->channelId = $channelId;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('message.channel.' . $this->channelId);
    }
}
