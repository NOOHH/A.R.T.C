<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';
    protected $primaryKey = 'chat_id';
    public $timestamps = true;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body_cipher',
        'is_read',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    /**
     * Encrypt the message body when setting
     */
    public function setBodyAttribute($value)
    {
        $this->attributes['body_cipher'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt the message body when getting
     */
    public function getBodyAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['body_cipher']);
        } catch (\Exception $e) {
            // If decryption fails, return the original value (for backward compatibility)
            return $this->attributes['body_cipher'];
        }
    }

    /**
     * For API compatibility, map message to body
     */
    public function getMessageAttribute()
    {
        return $this->body;
    }

    /**
     * For API compatibility, map message to body
     */
    public function setMessageAttribute($value)
    {
        $this->body = $value;
    }

    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    /**
     * Get the receiver of the message
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'user_id');
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }

    /**
     * Check if message is read
     */
    public function isRead()
    {
        return $this->is_read;
    }

    /**
     * Scope to get conversation between two users
     */
    public function scopeConversation($query, $user1, $user2)
    {
        return $query->where(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user1)->where('receiver_id', $user2);
        })->orWhere(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user2)->where('receiver_id', $user1);
        });
    }

    /**
     * Scope to get unread messages for a user
     */
    public function scopeUnreadForUser($query, $userId)
    {
        return $query->where('receiver_id', $userId)->where('is_read', false);
    }
}
