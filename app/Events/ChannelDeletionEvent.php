<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelDeletionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channel_id;

    public function __construct($channelId)
    {
        $this->channel_id = $channelId;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('channel.deletion.channel');
    }
}
