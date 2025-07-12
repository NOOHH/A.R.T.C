<?php

// ===== MIGRATION: Add soft deletes and indexes to messages table =====
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void 
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add soft deletes
            $table->softDeletes();
            
            // Add indexes for performance
            $table->index(['sender_id', 'receiver_id', 'created_at']);
            $table->index(['receiver_id', 'is_read']);
            $table->index('created_at');
        });
    }

    public function down(): void 
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['sender_id', 'receiver_id', 'created_at']);
            $table->dropIndex(['receiver_id', 'is_read']);
            $table->dropIndex(['created_at']);
        });
    }
};

// ===== MODEL: Enhanced Message model with encryption and soft deletes =====
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id', 
        'content',
        'is_read'
    ];

    protected $casts = [
        'content' => 'encrypted',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'user_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeBetweenUsers($query, $user1, $user2)
    {
        return $query->where(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user1)->where('receiver_id', $user2);
        })->orWhere(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user2)->where('receiver_id', $user1);
        });
    }

    public function scopeOlderThan($query, $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }
}

// ===== JOB: Queue job for sending messages =====
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

    protected $messageData;

    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
        $this->onQueue('messages');
    }

    public function handle(): void
    {
        try {
            // Create the message with automatic encryption
            $message = Message::create([
                'sender_id' => $this->messageData['sender_id'],
                'receiver_id' => $this->messageData['receiver_id'],
                'content' => $this->messageData['content'],
                'is_read' => false
            ]);

            // Load relationships for broadcasting
            $message->load(['sender', 'receiver']);

            // Broadcast the message
            broadcast(new MessageSent($message))->toOthers();

            Log::info('Message sent successfully', [
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send message', [
                'error' => $e->getMessage(),
                'data' => $this->messageData
            ]);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendMessageJob failed permanently', [
            'error' => $exception->getMessage(),
            'data' => $this->messageData
        ]);
    }
}

// ===== EVENT: Broadcast event for real-time messaging =====
<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->receiver_id),
            new PrivateChannel('chat.' . $this->message->sender_id)
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'content' => $this->message->content, // Will be decrypted automatically
            'sender_name' => $this->message->sender->name ?? 'Unknown',
            'sender_avatar' => strtoupper(substr($this->message->sender->name ?? 'U', 0, 1)),
            'is_read' => $this->message->is_read,
            'created_at' => $this->message->created_at->toISOString(),
            'timestamp' => $this->message->created_at->format('H:i')
        ];
    }
}

// ===== CONTROLLER: Enhanced ChatController methods =====
<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,user_id',
            'content' => 'required|string|max:1000'
        ]);

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;
        $content = trim($request->content);

        // Prevent sending to self
        if ($senderId == $receiverId) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot send message to yourself'
            ], 400);
        }

        // Check if content is not empty after trimming
        if (empty($content)) {
            return response()->json([
                'success' => false,
                'error' => 'Message content cannot be empty'
            ], 400);
        }

        try {
            // Dispatch job for async processing
            SendMessageJob::dispatch([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'content' => $content
            ]);

            Log::info('Message queued for sending', [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message queued for delivery',
                'status' => 'queued'
            ], 202); // 202 Accepted

        } catch (\Exception $e) {
            Log::error('Failed to queue message', [
                'error' => $e->getMessage(),
                'sender_id' => $senderId,
                'receiver_id' => $receiverId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }

    public function getMessages(Request $request, $userId): JsonResponse
    {
        $currentUserId = Auth::id();
        
        // Authorization check
        if (!Gate::allows('view-messages', $userId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Message::betweenUsers($currentUserId, $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'total' => $messages->total()
            ]
        ]);
    }

    public function markAsRead(Request $request, $messageId): JsonResponse
    {
        $message = Message::findOrFail($messageId);
        
        if (!Gate::allows('view-messages', $message)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }

    public function deleteMessage(Request $request, $messageId): JsonResponse
    {
        $message = Message::findOrFail($messageId);
        
        if (!Gate::allows('delete-message', $message)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete(); // Soft delete

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }
}

// ===== CONSOLE COMMAND: Prune old messages =====
<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PruneMessages extends Command
{
    protected $signature = 'messages:prune {--days=365 : Number of days to retain messages}';
    protected $description = 'Delete messages older than specified days (default: 365 days)';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        if ($days < 1) {
            $this->error('Days must be a positive integer');
            return 1;
        }

        $this->info("Pruning messages older than {$days} days...");

        try {
            // Get count before deletion
            $count = Message::olderThan($days)->count();
            
            if ($count === 0) {
                $this->info('No messages to prune.');
                return 0;
            }

            // Confirm in production
            if (app()->environment('production')) {
                if (!$this->confirm("This will permanently delete {$count} messages. Continue?")) {
                    $this->info('Operation cancelled.');
                    return 0;
                }
            }

            // Force delete (bypass soft deletes)
            $deleted = Message::olderThan($days)->forceDelete();

            $this->info("Successfully pruned {$deleted} messages.");
            
            Log::info('Messages pruned', [
                'deleted_count' => $deleted,
                'retention_days' => $days
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to prune messages: ' . $e->getMessage());
            
            Log::error('Message pruning failed', [
                'error' => $e->getMessage(),
                'retention_days' => $days
            ]);

            return 1;
        }
    }
}

// ===== KERNEL SCHEDULE: Add to app/Console/Kernel.php =====
protected function schedule(Schedule $schedule): void
{
    // Prune messages daily at 2 AM
    $schedule->command('messages:prune')->dailyAt('02:00');
    
    // Alternative: Prune weekly on Sunday at 3 AM
    // $schedule->command('messages:prune')->weeklyOn(0, '03:00');
}

// ===== CHANNELS: Add to routes/channels.php =====
<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

// Private chat channel authorization
Broadcast::channel('chat.{userId}', function (User $user, int $userId) {
    // User can only access their own chat channel
    // Admins and directors can access any channel
    return $user->user_id === $userId || 
           in_array($user->role, ['admin', 'director']);
});

// Presence channel for online users (optional)
Broadcast::channel('online-users', function (User $user) {
    return [
        'id' => $user->user_id,
        'name' => $user->name,
        'role' => $user->role,
        'avatar' => strtoupper(substr($user->name, 0, 1))
    ];
});

// ===== POLICY: Add to app/Policies/MessagePolicy.php =====
<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Message $message): bool
    {
        // Admin and director can view all messages
        if (in_array($user->role, ['admin', 'director'])) {
            return true;
        }

        // User can view if they are sender or receiver
        return $user->user_id === $message->sender_id || 
               $user->user_id === $message->receiver_id;
    }

    public function delete(User $user, Message $message): bool
    {
        // Admin and director can delete any message
        if (in_array($user->role, ['admin', 'director'])) {
            return true;
        }

        // User can only delete their own sent messages
        return $user->user_id === $message->sender_id;
    }

    public function viewMessages(User $user, int $userId): bool
    {
        // Admin and director can view all conversations
        if (in_array($user->role, ['admin', 'director'])) {
            return true;
        }

        // User can view their own conversations
        return $user->user_id === $userId;
    }
}

// ===== JAVASCRIPT: Echo listener example =====
/*
// Add to your main JS file or chat component

// Initialize Echo with Pusher
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Listen for messages on user's private channel
const userId = window.Laravel.user.user_id;

window.Echo.private(`chat.${userId}`)
    .listen('.message.sent', (e) => {
        console.log('New message received:', e);
        
        // Add message to chat interface
        addMessageToChat({
            id: e.id,
            sender_id: e.sender_id,
            sender_name: e.sender_name,
            sender_avatar: e.sender_avatar,
            content: e.content,
            timestamp: e.timestamp,
            is_own: e.sender_id === userId
        });
        
        // Show notification if chat is not focused
        if (document.hidden) {
            showNotification(`New message from ${e.sender_name}`, e.content);
        }
        
        // Play notification sound
        playNotificationSound();
        
        // Update unread count
        updateUnreadCount();
    });

// Enhanced send message function
async function sendMessage(receiverId, content) {
    try {
        // Show message immediately (optimistic UI)
        const tempMessage = {
            id: 'temp-' + Date.now(),
            sender_id: userId,
            sender_name: window.Laravel.user.name,
            sender_avatar: window.Laravel.user.name.charAt(0).toUpperCase(),
            content: content,
            timestamp: new Date().toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            }),
            is_own: true,
            status: 'sending'
        };
        
        addMessageToChat(tempMessage);
        
        // Send to server
        const response = await fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                receiver_id: receiverId,
                content: content
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Update message status to sent
            updateMessageStatus(tempMessage.id, 'sent');
        } else {
            // Show error and remove temp message
            updateMessageStatus(tempMessage.id, 'failed');
            showError(data.error || 'Failed to send message');
        }
        
    } catch (error) {
        console.error('Error sending message:', error);
        updateMessageStatus(tempMessage.id, 'failed');
        showError('Network error. Please check your connection.');
    }
}

// Helper functions
function addMessageToChat(message) {
    const chatContainer = document.getElementById('chat-messages');
    const messageElement = createMessageElement(message);
    chatContainer.appendChild(messageElement);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function createMessageElement(message) {
    const div = document.createElement('div');
    div.className = `message ${message.is_own ? 'own' : 'other'}`;
    div.dataset.messageId = message.id;
    
    div.innerHTML = `
        <div class="message-avatar">${message.sender_avatar}</div>
        <div class="message-content">
            <div class="message-header">
                <span class="sender-name">${message.sender_name}</span>
                <span class="timestamp">${message.timestamp}</span>
            </div>
            <div class="message-text">${escapeHtml(message.content)}</div>
            ${message.status ? `<div class="message-status">${message.status}</div>` : ''}
        </div>
    `;
    
    return div;
}

function updateMessageStatus(messageId, status) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageElement) {
        const statusElement = messageElement.querySelector('.message-status');
        if (statusElement) {
            statusElement.textContent = status;
            statusElement.className = `message-status status-${status}`;
        }
    }
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showNotification(title, body) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: body,
            icon: '/favicon.ico'
        });
    }
}

function playNotificationSound() {
    const audio = new Audio('/sounds/notification.mp3');
    audio.volume = 0.3;
    audio.play().catch(e => console.log('Could not play notification sound'));
}
*/
