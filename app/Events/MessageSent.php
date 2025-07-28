<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * The channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chat->sender_id),
            new PrivateChannel('chat.' . $this->chat->receiver_id),
        ];
    }

    /**
     * The event name that should be broadcasted.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * The data to broadcast.
     */
    public function broadcastWith(): array
    {
        $senderInfo = $this->chat->sender_info;
        
        return [
            'id' => $this->chat->chat_id,
            'sender_id' => $this->chat->sender_id,
            'receiver_id' => $this->chat->receiver_id,
            'message' => $this->chat->message,
            'sent_at' => $this->chat->sent_at->toISOString(),
            'is_read' => $this->chat->is_read,
            'sender' => $senderInfo ? [
                'id' => $senderInfo['id'],
                'name' => $senderInfo['name'],
                'email' => $senderInfo['email'],
                'role' => $senderInfo['role'],
                'type' => $senderInfo['type']
            ] : null
        ];
    }
}
