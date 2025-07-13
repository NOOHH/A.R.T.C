<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Search users by role and query
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:student,professor,admin,director',
                'q' => 'nullable|string|max:255',
                'program' => 'nullable|integer',
                'batch' => 'nullable|integer'
            ]);

            $currentUserId = session('user_id');
            if (!$currentUserId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $query = User::where('role', $request->type)
                        ->where('user_id', '!=', $currentUserId);

            // Apply search filter
            if ($request->filled('q')) {
                $search = $request->q;
                $query->where(function($q) use ($search) {
                    $q->where('user_firstname', 'like', '%' . $search . '%')
                      ->orWhere('user_lastname', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }

            $users = $query->select('user_id as id', 'user_firstname', 'user_lastname', 'email', 'role', 'is_online', 'last_seen')
                          ->limit(20)
                          ->get()
                          ->map(function($user) {
                              return [
                                  'id' => $user->id,
                                  'name' => trim($user->user_firstname . ' ' . $user->user_lastname),
                                  'email' => $user->email,
                                  'role' => $user->role,
                                  'is_online' => $user->is_online,
                                  'last_seen' => $user->last_seen
                              ];
                          });

            return response()->json([
                'data' => $users
            ]);

        } catch (\Exception $e) {
            Log::error('User search error: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'Error searching users'
            ], 500);
        }
    }

    /**
     * Get current user info
     */
    public function me(Request $request)
    {
        $currentUserId = session('user_id');
        
        if (!$currentUserId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $user = User::find($currentUserId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $user->user_id,
                'name' => trim($user->user_firstname . ' ' . $user->user_lastname),
                'email' => $user->email,
                'role' => $user->role,
                'is_online' => $user->is_online,
                'last_seen' => $user->last_seen
            ]
        ]);
    }
}
