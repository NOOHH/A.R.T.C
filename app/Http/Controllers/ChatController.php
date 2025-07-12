<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Admin;
use App\Models\Director;
use App\Jobs\SendMessageJob;
use App\Models\Message;
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

        $messages = Message::betweenUsers($user->id, $otherUser->id)
            ->with(['sender', 'receiver'])
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
}
