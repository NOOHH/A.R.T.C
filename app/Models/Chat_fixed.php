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
        'message',  // Allow setting via message attribute
        'body',     // Allow setting via body attribute
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
     * Encrypt the message when setting (supports both 'message' and 'body' attributes)
     */
    public function setMessageAttribute($value)
    {
        $this->attributes['body_cipher'] = Crypt::encryptString($value);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body_cipher'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt the message when getting (supports both 'message' and 'body' attributes)
     */
    public function getMessageAttribute()
    {
        try {
            return isset($this->attributes['body_cipher']) && $this->attributes['body_cipher'] ? 
                Crypt::decryptString($this->attributes['body_cipher']) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getBodyAttribute()
    {
        try {
            return isset($this->attributes['body_cipher']) && $this->attributes['body_cipher'] ? 
                Crypt::decryptString($this->attributes['body_cipher']) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the sender of the message (Laravel relationship)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    /**
     * Get the receiver of the message (Laravel relationship)
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'user_id');
    }

    /**
     * Get sender information from appropriate table
     */
    public function getSenderInfoAttribute()
    {
        // Try User table first
        $user = User::find($this->sender_id);
        if ($user) {
            return [
                'id' => $user->user_id,
                'name' => trim($user->user_firstname . ' ' . $user->user_lastname),
                'email' => $user->email,
                'role' => $user->role,
                'type' => 'user'
            ];
        }

        // Try Professor table
        $professor = Professor::find($this->sender_id);
        if ($professor) {
            $name = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
            if (empty($name)) $name = $professor->professor_name ?? 'Unknown Professor';
            
            return [
                'id' => $professor->professor_id,
                'name' => $name,
                'email' => $professor->professor_email,
                'role' => 'professor',
                'type' => 'professor'
            ];
        }

        // Try Admin table
        $admin = Admin::find($this->sender_id);
        if ($admin) {
            return [
                'id' => $admin->admin_id,
                'name' => $admin->admin_name,
                'email' => $admin->email,
                'role' => 'admin',
                'type' => 'admin'
            ];
        }

        // Try Director table
        $director = Director::find($this->sender_id);
        if ($director) {
            $name = trim(($director->directors_first_name ?? '') . ' ' . ($director->directors_last_name ?? ''));
            if (empty($name)) $name = $director->directors_name ?? 'Unknown Director';
            
            return [
                'id' => $director->directors_id,
                'name' => $name,
                'email' => $director->directors_email,
                'role' => 'director',
                'type' => 'director'
            ];
        }

        return null;
    }

    /**
     * Get receiver information from appropriate table
     */
    public function getReceiverInfoAttribute()
    {
        // Try User table first
        $user = User::find($this->receiver_id);
        if ($user) {
            return [
                'id' => $user->user_id,
                'name' => trim($user->user_firstname . ' ' . $user->user_lastname),
                'email' => $user->email,
                'role' => $user->role,
                'type' => 'user'
            ];
        }

        // Try Professor table
        $professor = Professor::find($this->receiver_id);
        if ($professor) {
            $name = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
            if (empty($name)) $name = $professor->professor_name ?? 'Unknown Professor';
            
            return [
                'id' => $professor->professor_id,
                'name' => $name,
                'email' => $professor->professor_email,
                'role' => 'professor',
                'type' => 'professor'
            ];
        }

        // Try Admin table
        $admin = Admin::find($this->receiver_id);
        if ($admin) {
            return [
                'id' => $admin->admin_id,
                'name' => $admin->admin_name,
                'email' => $admin->email,
                'role' => 'admin',
                'type' => 'admin'
            ];
        }

        // Try Director table
        $director = Director::find($this->receiver_id);
        if ($director) {
            $name = trim(($director->directors_first_name ?? '') . ' ' . ($director->directors_last_name ?? ''));
            if (empty($name)) $name = $director->directors_name ?? 'Unknown Director';
            
            return [
                'id' => $director->directors_id,
                'name' => $name,
                'email' => $director->directors_email,
                'role' => 'director',
                'type' => 'director'
            ];
        }

        return null;
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
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for messages between two users
     */
    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        });
    }

    /**
     * Get formatted sent time
     */
    public function getFormattedSentAtAttribute()
    {
        return $this->sent_at ? $this->sent_at->format('Y-m-d H:i:s') : null;
    }
}
