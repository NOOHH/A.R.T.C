<?php

namespace App\Services;

use App\Models\User;
use App\Events\UserOnlineStatusChanged;
use Illuminate\Support\Facades\Log;

class UserOnlineStatusService
{
    /**
     * Set user as online
     */
    public function setUserOnline($userId)
    {
        try {
            $user = User::find($userId);
            if ($user) {
                $user->setOnline();
                
                // Broadcast status change
                broadcast(new UserOnlineStatusChanged($userId, true, $user->last_seen));
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error setting user online: ' . $e->getMessage());
        }
        
        return false;
    }

    /**
     * Set user as offline
     */
    public function setUserOffline($userId)
    {
        try {
            $user = User::find($userId);
            if ($user) {
                $user->setOffline();
                
                // Broadcast status change
                broadcast(new UserOnlineStatusChanged($userId, false, $user->last_seen));
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error setting user offline: ' . $e->getMessage());
        }
        
        return false;
    }

    /**
     * Update user's last seen timestamp
     */
    public function updateLastSeen($userId)
    {
        try {
            $user = User::find($userId);
            if ($user) {
                $user->last_seen = now();
                $user->save();
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error updating last seen: ' . $e->getMessage());
        }
        
        return false;
    }
}
