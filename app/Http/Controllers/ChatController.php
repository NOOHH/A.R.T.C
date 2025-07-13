<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Admin;
use App\Models\Director;
use App\Jobs\SendMessageJob;
use App\Models\Chat;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Search users for chat functionality
     */
    public function searchUsers(Request $request)
    {
        $request->validate([
            'type' => 'required|in:student,professor,support,admin,director',
            'search' => 'nullable|string|max:50'
        ]);

        $type = $request->input('type');
        $search = $request->input('search', '');
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        try {
            $users = [];

            switch ($type) {
                case 'student':
                    $users = $this->getStudents($search, $currentUser);
                    break;
                case 'professor':
                    $users = $this->getProfessors($search, $currentUser);
                    break;
                case 'support':
                    $users = $this->getSupportUsers($search, $currentUser);
                    break;
                case 'admin':
                    $users = $this->getAdmins($search, $currentUser);
                    break;
                case 'director':
                    $users = $this->getDirectors($search, $currentUser);
                    break;
            }

            return response()->json([
                'success' => true,
                'users' => $users,
                'total' => count($users)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to search users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students for chat
     */
    private function getStudents($search, $currentUser)
    {
        // Only admins, directors, and professors can chat with students
        if (!in_array($currentUser->role, ['admin', 'director', 'professor'])) {
            return [];
        }

        $query = User::where('role', 'student');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->select('id', 'name', 'email', 'role', 'created_at')
                    ->orderBy('name')
                    ->limit(20)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'avatar' => $this->getInitials($user->name),
                            'status' => $this->getUserStatus($user->id),
                            'last_seen' => $user->updated_at ? $user->updated_at->diffForHumans() : null
                        ];
                    });
    }

    /**
     * Get professors for chat
     */
    private function getProfessors($search, $currentUser)
    {
        $query = User::where('role', 'professor');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->select('id', 'name', 'email', 'role', 'created_at')
                    ->orderBy('name')
                    ->limit(20)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'avatar' => $this->getInitials($user->name),
                            'status' => $this->getUserStatus($user->id),
                            'last_seen' => $user->updated_at ? $user->updated_at->diffForHumans() : null
                        ];
                    });
    }

    /**
     * Get support users for chat
     */
    private function getSupportUsers($search, $currentUser)
    {
        $query = User::whereIn('role', ['admin', 'director']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->select('id', 'name', 'email', 'role', 'created_at')
                    ->orderBy('name')
                    ->limit(20)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name . ' (Support)',
                            'email' => $user->email,
                            'role' => $user->role,
                            'avatar' => $this->getInitials($user->name),
                            'status' => $this->getUserStatus($user->id),
                            'last_seen' => $user->updated_at ? $user->updated_at->diffForHumans() : null
                        ];
                    });
    }

    /**
     * Get admins for chat
     */
    private function getAdmins($search, $currentUser)
    {
        // Only other admins and directors can chat with admins
        if (!in_array($currentUser->role, ['admin', 'director'])) {
            return [];
        }

        $query = User::where('role', 'admin')
                    ->where('id', '!=', $currentUser->id); // Exclude current user

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->select('id', 'name', 'email', 'role', 'created_at')
                    ->orderBy('name')
                    ->limit(20)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'avatar' => $this->getInitials($user->name),
                            'status' => $this->getUserStatus($user->id),
                            'last_seen' => $user->updated_at ? $user->updated_at->diffForHumans() : null
                        ];
                    });
    }

    /**
     * Get directors for chat
     */
    private function getDirectors($search, $currentUser)
    {
        // Only admins and other directors can chat with directors
        if (!in_array($currentUser->role, ['admin', 'director'])) {
            return [];
        }

        $query = User::where('role', 'director')
                    ->where('id', '!=', $currentUser->id); // Exclude current user

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->select('id', 'name', 'email', 'role', 'created_at')
                    ->orderBy('name')
                    ->limit(20)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'avatar' => $this->getInitials($user->name),
                            'status' => $this->getUserStatus($user->id),
                            'last_seen' => $user->updated_at ? $user->updated_at->diffForHumans() : null
                        ];
                    });
    }

    /**
     * Get user initials for avatar
     */
    private function getInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }
        
        return $initials ?: 'U';
    }

    /**
     * Get user status (mock implementation)
     */
    private function getUserStatus($userId)
    {
        // Mock status - in real implementation, this would check actual user activity
        $statuses = ['online', 'away', 'offline'];
        return $statuses[array_rand($statuses)];
    }

    /**
     * Send message to user
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);

        // Check if user can send messages to this receiver
        if (!Gate::allows('send-message', $receiver)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Dispatch the job to handle message sending asynchronously
            SendMessageJob::dispatch($user->id, $receiver->id, $request->content);
            
            return response()->json(['message' => 'Message sent successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to queue message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    public function getMessages(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'limit' => 'integer|min:1|max:100',
        ]);

        $user = Auth::user();
        $otherUser = User::findOrFail($request->user_id);

        // Check if user can view messages with this user
        if (!Gate::allows('view-messages', $otherUser)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Chat::where(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $otherUser->id);
            })
            ->orWhere(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $otherUser->id)
                      ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit($request->input('limit', 20))
            ->get()
            ->reverse()
            ->values();

        return response()->json($messages);
    }

    /**
     * Enhanced search for chatbox with role-based filtering
     */
    public function enhancedSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'type' => 'nullable|in:all,students,professors,admins,directors',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $query = $request->input('query');
        $type = $request->input('type', 'all');
        $limit = $request->input('limit', 20);
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        try {
            $results = [];

            // Role-based search logic
            switch ($currentUser->role) {
                case 'admin':
                case 'director':
                    // Admin and director can search all user types
                    if ($type === 'all') {
                        $results = array_merge(
                            $this->searchUsersInDatabase('students', $query, $limit / 3),
                            $this->searchUsersInDatabase('professors', $query, $limit / 3),
                            $this->searchUsersInDatabase('admins', $query, $limit / 3)
                        );
                    } else {
                        $results = $this->searchUsersInDatabase($type, $query, $limit);
                    }
                    break;
                    
                case 'professor':
                    // Professors can search students and admins
                    if ($type === 'all') {
                        $results = array_merge(
                            $this->searchUsersInDatabase('students', $query, $limit / 2),
                            $this->searchUsersInDatabase('admins', $query, $limit / 2)
                        );
                    } elseif (in_array($type, ['students', 'admins'])) {
                        $results = $this->searchUsersInDatabase($type, $query, $limit);
                    }
                    break;
                    
                case 'student':
                    // Students can search professors and admins
                    if ($type === 'all') {
                        $results = array_merge(
                            $this->searchUsersInDatabase('professors', $query, $limit / 2),
                            $this->searchUsersInDatabase('admins', $query, $limit / 2)
                        );
                    } elseif (in_array($type, ['professors', 'admins'])) {
                        $results = $this->searchUsersInDatabase($type, $query, $limit);
                    }
                    break;
            }

            // Sort results by name
            usort($results, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return response()->json([
                'success' => true,
                'results' => array_slice($results, 0, $limit),
                'total' => count($results)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search users in database by type
     */
    private function searchUsersInDatabase($type, $query, $limit)
    {
        $results = [];
        
        switch ($type) {
            case 'students':
                $users = User::where('role', 'student')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', '%' . $query . '%')
                          ->orWhere('email', 'like', '%' . $query . '%');
                    })
                    ->limit($limit)
                    ->get();
                break;
                
            case 'professors':
                $users = User::where('role', 'professor')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', '%' . $query . '%')
                          ->orWhere('email', 'like', '%' . $query . '%');
                    })
                    ->limit($limit)
                    ->get();
                break;
                
            case 'admins':
                $users = User::where('role', 'admin')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', '%' . $query . '%')
                          ->orWhere('email', 'like', '%' . $query . '%');
                    })
                    ->limit($limit)
                    ->get();
                break;
                
            case 'directors':
                $users = User::where('role', 'director')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', '%' . $query . '%')
                          ->orWhere('email', 'like', '%' . $query . '%');
                    })
                    ->limit($limit)
                    ->get();
                break;
                
            default:
                return [];
        }
        
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $this->getInitials($user->name),
                'status' => $this->getUserStatus($user->id),
                'last_seen' => $user->updated_at ? $user->updated_at->diffForHumans() : null,
                'profile_info' => $this->getUserProfileInfo($user)
            ];
        }
        
        return $results;
    }
    
    /**
     * Get user profile information
     */
    private function getUserProfileInfo($user)
    {
        $info = [];
        
        switch ($user->role) {
            case 'student':
                // Get student enrollment info
                $enrollments = User::find($user->id)->enrollments ?? [];
                $info['enrollments'] = count($enrollments);
                break;
                
            case 'professor':
                // Get professor specialization
                $info['specialization'] = $user->specialization ?? 'General';
                break;
                
            case 'admin':
                $info['department'] = $user->department ?? 'Administration';
                break;
        }
        
        return $info;
    }
    
    /**
     * Save chat message to database
     */
    public function saveChatMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|max:1000',
            'chat_type' => 'nullable|string|in:direct,group,support'
        ]);

        $currentUser = Auth::user();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        try {
            // In a real application, you would save to a messages table
            // For now, we'll simulate saving and return success
            
            $messageData = [
                'sender_id' => $currentUser->id,
                'recipient_id' => $request->recipient_id,
                'message' => $request->message,
                'chat_type' => $request->chat_type ?? 'direct',
                'sent_at' => now(),
                'is_read' => false
            ];
            
            // Here you would typically save to database:
            // ChatMessage::create($messageData);
            
            return response()->json([
                'success' => true,
                'message' => 'Message saved successfully',
                'data' => $messageData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save message',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get chat history between users
     */
    public function getChatHistory(Request $request, $userId)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        try {
            // In a real application, you would fetch from messages table
            // For now, return mock data
            
            $chatPartner = User::find($userId);
            
            if (!$chatPartner) {
                return response()->json(['error' => 'User not found'], 404);
            }
            
            $mockMessages = [
                [
                    'id' => 1,
                    'sender_id' => $currentUser->id,
                    'recipient_id' => $userId,
                    'content' => 'Hello! How are you?',
                    'sent_at' => now()->subMinutes(30),
                    'is_read' => true,
                    'sender_name' => $currentUser->name
                ],
                [
                    'id' => 2,
                    'sender_id' => $userId,
                    'recipient_id' => $currentUser->id,
                    'content' => 'Hi! I\'m doing great, thanks for asking!',
                    'sent_at' => now()->subMinutes(25),
                    'is_read' => true,
                    'sender_name' => $chatPartner->name
                ]
            ];
            
            return response()->json([
                'success' => true,
                'messages' => $mockMessages,
                'user' => [
                    'id' => $chatPartner->id,
                    'name' => $chatPartner->name,
                    'email' => $chatPartner->email,
                    'role' => $chatPartner->role
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load chat history',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users for session-based chat
     */
    public function getSessionUsers(Request $request)
    {
        $type = $request->input('type');
        $search = $request->input('q', '');
        $program = $request->input('program', '');
        $batch = $request->input('batch', '');
        $mode = $request->input('mode', '');
        
        // Get current user from session - check both session formats
        $currentUserId = session('user_id') ?? $_SESSION['user_id'] ?? null;
        $currentUserRole = session('user_role') ?? session('role') ?? $_SESSION['user_type'] ?? 'guest';
        
        if (!$currentUserId) {
            return response()->json([
                'error' => 'Not authenticated',
                'debug' => [
                    'session_user_id' => session('user_id'),
                    'session_user_role' => session('user_role'),
                    'session_role' => session('role'),
                    'php_session_user_id' => $_SESSION['user_id'] ?? null,
                    'php_session_user_type' => $_SESSION['user_type'] ?? null,
                    'session_logged_in' => session('logged_in'),
                ]
            ], 401);
        }
        
        try {
            $users = collect();
            
            switch ($type) {
                case 'student':
                    $users = $this->getSessionStudents($search, $currentUserRole, $program, $batch, $mode);
                    break;
                case 'professor':
                    $users = $this->getSessionProfessors($search, $currentUserRole, $program, $batch, $mode);
                    break;
                case 'admin':
                    $users = $this->getSessionAdmins($search, $currentUserRole);
                    break;
                case 'director':
                    $users = $this->getSessionDirectors($search, $currentUserRole);
                    break;
            }
            
            return response()->json([
                'success' => true,
                'data' => $users->toArray(),
                'total' => $users->count(),
                'debug' => [
                    'current_user_id' => $currentUserId,
                    'current_user_role' => $currentUserRole,
                    'search_type' => $type,
                    'search_query' => $search
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching session users: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Send message via session-based chat
     */
    public function sendSessionMessage(Request $request)
    {
        $currentUser = $this->getCurrentSessionUser();
        
        if (!$currentUser['id']) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        try {
            // Basic validation
            $receiverId = $request->input('receiver_id');
            $message = $request->input('message');
            $receiverType = $request->input('receiver_type');
            
            if (!$receiverId || !$message) {
                return response()->json(['error' => 'Receiver ID and message are required'], 400);
            }
            
            // Create message entry in database
            $messageData = [
                'sender_id' => $currentUser['id'],
                'sender_type' => $currentUser['role'],
                'receiver_id' => $receiverId,
                'receiver_type' => $receiverType,
                'message' => $message,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Insert into messages table
            $messageId = DB::table('messages')->insertGetId($messageData);
            
            return response()->json([
                'success' => true,
                'id' => $messageId,
                'message' => 'Message sent successfully',
                'data' => [
                    'id' => $messageId,
                    'sender_id' => $currentUser['id'],
                    'receiver_id' => $receiverId,
                    'message' => $message,
                    'sent_at' => now()->toISOString(),
                    'sender_name' => $currentUser['name'],
                    'receiver_type' => $receiverType
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending session message: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to send message',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get messages for session-based chat
     */
    public function getSessionMessages(Request $request)
    {
        $with = $request->input('with');
        $currentUser = $this->getCurrentSessionUser();
        
        if (!$currentUser['id']) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        try {
            // Get messages between current user and specified user
            $messages = collect([]);
            
            if ($with) {
                // Get messages from database
                $messages = DB::table('messages')
                    ->where(function ($query) use ($currentUser, $with) {
                        $query->where('sender_id', $currentUser['id'])
                              ->where('receiver_id', $with);
                    })
                    ->orWhere(function ($query) use ($currentUser, $with) {
                        $query->where('sender_id', $with)
                              ->where('receiver_id', $currentUser['id']);
                    })
                    ->orderBy('sent_at', 'asc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $messages->toArray(),
                'debug' => [
                    'current_user_id' => $currentUser['id'],
                    'with_user_id' => $with,
                    'message' => 'Messages loaded successfully'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching session messages: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch messages',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear chat history for session-based chat
     */
    public function clearSessionHistory(Request $request)
    {
        $with = $request->input('with');
        $currentUserId = session('user_id');
        
        if (!$currentUserId) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        try {
            Message::where(function ($query) use ($currentUserId, $with) {
                $query->where('sender_id', $currentUserId)
                      ->where('receiver_id', $with);
            })
            ->orWhere(function ($query) use ($currentUserId, $with) {
                $query->where('sender_id', $with)
                      ->where('receiver_id', $currentUserId);
            })
            ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Chat history cleared successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing session chat history: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to clear chat history',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get programs for session-based chat
     */
    public function getSessionPrograms(Request $request)
    {
        try {
            $programs = \App\Models\Program::where('is_archived', false)
                ->select('program_id', 'program_name', 'program_description')
                ->orderBy('program_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $programs->toArray()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching session programs: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch programs',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get students for session-based chat
     */
    private function getSessionStudents($search, $currentUserRole, $program, $batch, $mode)
    {
        // Only admins, directors, and professors can chat with students
        if (!in_array($currentUserRole, ['admin', 'director', 'professor'])) {
            return collect();
        }
        
        try {
            $query = User::where('role', 'student');
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
            
            return $query->select('id', 'name', 'email', 'role', 'created_at')
                        ->orderBy('name')
                        ->limit(20)
                        ->get()
                        ->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name ?? 'Unknown',
                                'email' => $user->email ?? 'No email',
                                'role' => $user->role ?? 'student'
                            ];
                        });
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return collect();
        }
    }
    
    /**
     * Get professors for session-based chat
     */
    private function getSessionProfessors($search, $currentUserRole, $program, $batch, $mode)
    {
        try {
            // Search in professors table instead of users table
            $query = Professor::where('professor_archived', false);
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('professor_name', 'like', '%' . $search . '%')
                      ->orWhere('professor_first_name', 'like', '%' . $search . '%')
                      ->orWhere('professor_last_name', 'like', '%' . $search . '%')
                      ->orWhere('professor_email', 'like', '%' . $search . '%');
                });
            }
            
            return $query->select('professor_id as id', 'professor_name as name', 'professor_first_name as first_name', 'professor_last_name as last_name', 'professor_email as email')
                        ->orderBy('professor_name')
                        ->limit(20)
                        ->get()
                        ->map(function ($professor) {
                            $fullName = trim(($professor->first_name ?? '') . ' ' . ($professor->last_name ?? ''));
                            return [
                                'id' => $professor->id,
                                'name' => $fullName ?: ($professor->name ?? 'Unknown Professor'),
                                'first_name' => $professor->first_name ?? '',
                                'last_name' => $professor->last_name ?? '',
                                'email' => $professor->email ?? 'No email',
                                'role' => 'professor'
                            ];
                        });
        } catch (\Exception $e) {
            Log::error('Error fetching professors: ' . $e->getMessage());
            return collect();
        }
    }
    
    /**
     * Get admins for session-based chat
     */
    private function getSessionAdmins($search, $currentUserRole)
    {
        try {
            // Search in admins table using correct column names
            $query = Admin::query();
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('admin_name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
            
            return $query->select('admin_id as id', 'admin_name as name', 'email', 'created_at')
                        ->orderBy('admin_name')
                        ->limit(20)
                        ->get()
                        ->map(function ($admin) {
                            return [
                                'id' => $admin->id,
                                'name' => $admin->name ?? 'Unknown Admin',
                                'first_name' => $admin->name ?? 'Unknown',
                                'last_name' => '',
                                'email' => $admin->email ?? 'No email',
                                'role' => 'admin'
                            ];
                        });
        } catch (\Exception $e) {
            Log::error('Error fetching admins: ' . $e->getMessage());
            return collect();
        }
    }
    
    /**
     * Get directors for session-based chat
     */
    private function getSessionDirectors($search, $currentUserRole)
    {
        try {
            // Search in directors table instead of users table
            $query = Director::query();
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('directors_name', 'like', '%' . $search . '%')
                      ->orWhere('directors_first_name', 'like', '%' . $search . '%')
                      ->orWhere('directors_last_name', 'like', '%' . $search . '%')
                      ->orWhere('directors_email', 'like', '%' . $search . '%');
                });
            }
            
            return $query->select('directors_id as id', 'directors_name as name', 'directors_first_name as first_name', 'directors_last_name as last_name', 'directors_email as email', 'created_at')
                        ->orderBy('directors_name')
                        ->limit(20)
                        ->get()
                        ->map(function ($director) {
                            return [
                                'id' => $director->id,
                                'name' => $director->name ?? (($director->first_name ?? '') . ' ' . ($director->last_name ?? '')),
                                'first_name' => $director->first_name ?? '',
                                'last_name' => $director->last_name ?? '',
                                'email' => $director->email ?? 'No email',
                                'role' => 'director'
                            ];
                        });
        } catch (\Exception $e) {
            Log::error('Error fetching directors: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get professors for session-based chat API
     */
    public function getSessionProfessorsAPI(Request $request)
    {
        $search = $request->get('search', '');
        $program = $request->get('program');
        $batch = $request->get('batch');
        $mode = $request->get('mode');
        
        // Get current user role
        $currentUserRole = $this->getCurrentUserRole();
        
        $users = $this->getSessionProfessors($search, $currentUserRole, $program, $batch, $mode);
        
        return response()->json([
            'success' => true,
            'data' => $users,
            'count' => $users->count(),
            'search_term' => $search,
            'debug' => [
                'user_role' => $currentUserRole,
                'auth_session' => isset($_SESSION['admin_logged_in']),
                'laravel_session' => session()->has('user_id')
            ]
        ]);
    }
    
    /**
     * Get admins for session-based chat API
     */
    public function getSessionAdminsAPI(Request $request)
    {
        $search = $request->get('search', '');
        
        // Get current user role
        $currentUserRole = $this->getCurrentUserRole();
        
        $users = $this->getSessionAdmins($search, $currentUserRole);
        
        return response()->json([
            'success' => true,
            'data' => $users,
            'count' => $users->count(),
            'search_term' => $search,
            'debug' => [
                'user_role' => $currentUserRole,
                'auth_session' => isset($_SESSION['admin_logged_in']),
                'laravel_session' => session()->has('user_id')
            ]
        ]);
    }
    
    /**
     * Get directors for session-based chat API
     */
    public function getSessionDirectorsAPI(Request $request)
    {
        $search = $request->get('search', '');
        
        // Get current user role
        $currentUserRole = $this->getCurrentUserRole();
        
        $users = $this->getSessionDirectors($search, $currentUserRole);
        
        return response()->json([
            'success' => true,
            'data' => $users,
            'count' => $users->count(),
            'search_term' => $search,
            'debug' => [
                'user_role' => $currentUserRole,
                'auth_session' => isset($_SESSION['admin_logged_in']),
                'laravel_session' => session()->has('user_id')
            ]
        ]);
    }

    /**
     * Get current session user info
     */
    private function getCurrentSessionUser()
    {
        $userId = session('user_id');
        $userRole = session('user_role') ?? session('role');
        
        // If no user_id, check for specific role-based IDs
        if (!$userId) {
            if (session('professor_id')) {
                $userId = session('professor_id');
                $userRole = 'professor';
            } elseif (session('student_id')) {
                $userId = session('student_id');
                $userRole = 'student';
            } elseif (session('director_id')) {
                $userId = session('director_id');
                $userRole = 'director';
            } elseif (session('admin_id') || isset($_SESSION['admin_id'])) {
                $userId = session('admin_id') ?? $_SESSION['admin_id'];
                $userRole = 'admin';
            }
        }
        
        return [
            'id' => $userId,
            'role' => $userRole,
            'name' => session('user_name') ?? session('name') ?? 'Unknown User'
        ];
    }
}
