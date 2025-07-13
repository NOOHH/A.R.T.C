<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Chat;
use App\Http\Resources\UserResource;
use App\Http\Resources\MessageResource;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ChatApiController extends Controller
{
    /**
     * Get filtered users for chat
     */
    public function users(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:student,professor,admin,director,support,faq',
                'program' => 'nullable|exists:programs,program_id',
                'mode' => 'nullable|in:synchronous,asynchronous',
                'batch' => 'nullable|exists:batches,batch_id',
                'q' => 'nullable|string|max:255'
            ]);

            // Get current user from session
            $currentUserId = session('user_id');
            if (!$currentUserId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $users = collect();

            // Handle different user types
            switch ($request->type) {
                case 'professor':
                    // Get professors from users table
                    $query = User::where('role', 'professor')
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
                    
                    $users = $query->limit(50)->get();
                    break;
                    
                case 'student':
                    // Get students from users table
                    $query = User::where('role', 'student')
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
                    
                    $users = $query->limit(50)->get();
                    break;
                    
                case 'admin':
                case 'director':
                    // Get admins/directors from users table
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
                    
                    $users = $query->limit(50)->get();
                    break;
                    
                case 'support':
                    // Get support users (admins and directors)
                    $query = User::whereIn('role', ['admin', 'director'])
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
                    
                    $users = $query->limit(50)->get();
                    break;
                    
                default:
                    $users = collect();
                    break;
            }

            return response()->json([
                'data' => UserResource::collection($users)
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API users error: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'Error retrieving users'
            ], 500);
        }
    }

    /**
     * Get messages between users
     */
    public function messages(Request $request)
    {
        try {
            $request->validate([
                'with' => 'required|integer'
            ]);

            $currentUserId = session('user_id');
            if (!$currentUserId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $withUserId = $request->with;
            
            // Check if the target user exists
            $targetUser = User::find($withUserId);
            if (!$targetUser) {
                return response()->json(['error' => 'Target user not found'], 404);
            }

            // Get messages between current user and target user
            $messages = Chat::conversation($currentUserId, $withUserId)
                ->with(['sender', 'receiver'])
                ->orderBy('sent_at', 'asc')
                ->limit(50)
                ->get();

            // Mark messages as read
            Chat::where('sender_id', $withUserId)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'data' => MessageResource::collection($messages)
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API messages error: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'Error retrieving messages'
            ], 500);
        }
    }

    /**
     * Send a message
     */
    public function send(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required|integer',
                'message' => 'required|string|max:1000'
            ]);

            $currentUserId = session('user_id');
            
            if (!$currentUserId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Check if receiver exists
            $receiver = User::find($request->receiver_id);
            if (!$receiver) {
                return response()->json(['error' => 'Receiver not found'], 404);
            }

            // Check if receiver is not the same as sender
            if ($currentUserId == $request->receiver_id) {
                return response()->json(['error' => 'Cannot send message to yourself'], 400);
            }

            $chat = Chat::create([
                'sender_id' => $currentUserId,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message, // This will be encrypted automatically
                'sent_at' => now(),
                'is_read' => false
            ]);

            // Load relationships for response
            $chat->load(['sender', 'receiver']);

            // Broadcast the message (if you have broadcasting setup)
            try {
                broadcast(new MessageSent($chat))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast message: ' . $e->getMessage());
            }

            return response()->json([
                'data' => new MessageResource($chat),
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API send error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to send message',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent conversations
     */
    public function recent(Request $request)
    {
        try {
            $currentUserId = session('user_id');
            
            if (!$currentUserId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get latest message for each conversation
            $conversations = DB::table('chats')
                ->select([
                    'chats.*',
                    'users.user_firstname',
                    'users.user_lastname', 
                    'users.email',
                    'users.role',
                    'users.is_online',
                    'users.last_seen'
                ])
                ->join('users', function($join) use ($currentUserId) {
                    $join->on('users.user_id', '=', DB::raw("
                        CASE 
                            WHEN chats.sender_id = {$currentUserId} THEN chats.receiver_id
                            ELSE chats.sender_id
                        END
                    "));
                })
                ->where(function($query) use ($currentUserId) {
                    $query->where('chats.sender_id', $currentUserId)
                          ->orWhere('chats.receiver_id', $currentUserId);
                })
                ->orderBy('chats.sent_at', 'desc')
                ->get()
                ->groupBy(function($item) use ($currentUserId) {
                    return $item->sender_id == $currentUserId ? $item->receiver_id : $item->sender_id;
                })
                ->map(function($messages) {
                    return $messages->first();
                });

            return response()->json([
                'data' => $conversations->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API recent error: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'Error retrieving recent conversations'
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
            'data' => new UserResource($user)
        ]);
    }
}
