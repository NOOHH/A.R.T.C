<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The chat message that was just sent.
     *
     * @var \App\Models\Chat
     */
    public Chat $chat;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Chat  $chat
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * The name of the event when broadcast.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * The channels the event should broadcast on.
     *
     * Both the sender and receiver subscribe to their own private channels.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel[]
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chat->sender_id),
            new PrivateChannel('chat.' . $this->chat->receiver_id),
        ];
    }

    /**
     * The data to broadcast.
     *
     * @return array<string,mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'chat_id'       => $this->chat->chat_id,
            'sender_id'     => $this->chat->sender_id,
            'receiver_id'   => $this->chat->receiver_id,
            'message'       => $this->chat->message,
            'sender_name'   => optional($this->chat->sender)->name,
            'is_read'       => (bool) $this->chat->isRead(),
            'created_at'    => $this->chat->created_at->toDateTimeString(),
            'timestamp'     => $this->chat->created_at->format('H:i'),
        ];
    }
}
