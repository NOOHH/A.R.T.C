<?php

namespace App\Jobs;

use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    protected $senderId;
    protected $receiverId;
    protected $content;

    public function __construct(int $senderId, int $receiverId, string $content)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->content = $content;
        $this->onQueue('messages');
    }

    public function handle(): void
    {
        try {
            // Create the message with automatic encryption
            $message = Message::create([
                'sender_id' => $this->senderId,
                'receiver_id' => $this->receiverId,
                'content' => $this->content,
                'is_read' => false
            ]);

            // Load relationships for broadcasting
            $message->load(['sender', 'receiver']);

            // Broadcast the message
            broadcast(new MessageSent($message))->toOthers();

            Log::info('Message sent successfully', [
                'message_id' => $message->id,
                'sender_id' => $this->senderId,
                'receiver_id' => $this->receiverId
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send message', [
                'error' => $e->getMessage(),
                'sender_id' => $this->senderId,
                'receiver_id' => $this->receiverId
            ]);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendMessageJob failed permanently', [
            'error' => $exception->getMessage(),
            'sender_id' => $this->senderId,
            'receiver_id' => $this->receiverId
        ]);
    }
}
