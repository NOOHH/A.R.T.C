<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $receiverId;

    /**
     * Create a new event instance.
     *
     * @param array $message The chat message data
     * @param int $receiverId The ID of the message receiver
     */
    public function __construct($message, $receiverId)
    {
        $this->message = $message;
        $this->receiverId = $receiverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->receiverId);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ];
    }
}
