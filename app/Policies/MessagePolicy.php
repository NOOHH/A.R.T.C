<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can send messages to another user.
     */
    public function sendMessage(User $user, User $receiver)
    {
        // Users can send messages to anyone except themselves
        if ($user->id === $receiver->id) {
            return false;
        }

        // Check if users are in the same batch or have appropriate roles
        if ($user->role === 'director' || $receiver->role === 'director') {
            return true;
        }

        if ($user->role === 'professor' || $receiver->role === 'professor') {
            return true;
        }

        if ($user->role === 'student' && $receiver->role === 'student') {
            // Students can message each other if they're in the same batch
            return $this->areInSameBatch($user, $receiver);
        }

        return false;
    }

    /**
     * Determine whether the user can view messages with another user.
     */
    public function viewMessages(User $user, User $otherUser)
    {
        // Same rules as sending messages
        return $this->sendMessage($user, $otherUser);
    }

    /**
     * Determine whether the user can view the message.
     */
    public function view(User $user, Message $message)
    {
        return $user->id === $message->sender_id || $user->id === $message->receiver_id;
    }

    /**
     * Determine whether the user can delete the message.
     */
    public function delete(User $user, Message $message)
    {
        // Only sender can delete their own messages
        return $user->id === $message->sender_id;
    }

    /**
     * Check if two users are in the same batch.
     */
    private function areInSameBatch(User $user1, User $user2)
    {
        // Check if both users have enrollments in the same batch
        $user1Batches = $user1->enrollments()->pluck('batch_id');
        $user2Batches = $user2->enrollments()->pluck('batch_id');
        
        return $user1Batches->intersect($user2Batches)->isNotEmpty();
    }
}
