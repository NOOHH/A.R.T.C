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
        
        // Get current user from Auth or session fallback
        $authUser = Auth::user();
        if ($authUser && $authUser instanceof Professor) {
            // Authenticated professor
            $currentUser = (object) [
                'user_id' => $authUser->professor_id,
                'id' => $authUser->professor_id,
                'role' => 'professor',
                'name' => trim(($authUser->professor_first_name ?? '') . ' ' . ($authUser->professor_last_name ?? '')) ?: ($authUser->professor_name ?? 'Unknown Professor')
            ];
        } elseif ($authUser && isset($authUser->role)) {
            // Other Auth user (admin, director, etc)
            $currentUser = (object) [
                'user_id' => $authUser->id ?? $authUser->user_id,
                'id' => $authUser->id ?? $authUser->user_id,
                'role' => $authUser->role,
                'name' => $authUser->name ?? ($authUser->user_firstname . ' ' . $authUser->user_lastname) ?? 'Unknown User'
            ];
        } else {
            // Fallback to session-based authentication
            $sessionUser = $this->getCurrentSessionUser();
            if (!$sessionUser['id']) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }
            $currentUser = (object) [
                'user_id' => $sessionUser['id'],
                'id' => $sessionUser['id'],
                'role' => $sessionUser['role'],
                'name' => $sessionUser['name']
            ];
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
                'total' => count($users),
                'search' => $search,
                'type' => $type,
                'current_user' => [
                    'id' => $currentUser->user_id ?? $currentUser->id,
                    'role' => $currentUser->role
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching users: ' . $e->getMessage());
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
                $q->where('user_firstname', 'like', '%' . $search . '%')
                  ->orWhere('user_lastname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereRaw("CONCAT(user_firstname, ' ', user_lastname) LIKE ?", ['%' . $search . '%']);
            });
        }

        return $query->select('user_id as id', 'user_firstname', 'user_lastname', 'email', 'role', 'created_at')
                    ->orderBy('user_firstname')
                    ->limit(20)
                    ->get()
                    ->map(function ($user) {
                        $fullName = trim($user->user_firstname . ' ' . $user->user_lastname);
                        return [
                            'id' => $user->id,
                            'name' => $fullName,
                            'email' => $user->email,
                            'role' => $user->role,
                            'avatar' => $this->getInitials($fullName),
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
        // Use the Professor model for more complete data
        $query = Professor::where('professor_archived', false);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('professor_first_name', 'like', '%' . $search . '%')
                  ->orWhere('professor_last_name', 'like', '%' . $search . '%')
                  ->orWhere('professor_name', 'like', '%' . $search . '%')
                  ->orWhere('professor_email', 'like', '%' . $search . '%')
                  ->orWhereRaw("CONCAT(professor_first_name, ' ', professor_last_name) LIKE ?", ['%' . $search . '%']);
            });
        }

        return $query->select('professor_id as id', 'professor_first_name', 'professor_last_name', 'professor_name', 'professor_email', 'created_at')
                    ->orderBy('professor_first_name')
                    ->limit(20)
                    ->get()
                    ->map(function ($professor) {
                        $fullName = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
                        if (empty($fullName)) {
                            $fullName = $professor->professor_name ?? 'Unknown Professor';
                        }
                        return [
                            'id' => $professor->id,
                            'name' => $fullName,
                            'email' => $professor->professor_email,
                            'role' => 'professor',
                            'avatar' => $this->getInitials($fullName),
                            'status' => $this->getUserStatus($professor->id),
                            'last_seen' => $professor->updated_at ? $professor->updated_at->diffForHumans() : null
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
        // Students and professors can chat with admins for support
        // Only exclude other admins from seeing themselves
        $query = Admin::query();
        
        // If current user is an admin, exclude themselves
        if ($currentUser->role === 'admin' && isset($currentUser->admin_id)) {
            $query->where('admin_id', '!=', $currentUser->admin_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('admin_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->select('admin_id as id', 'admin_name', 'email', 'created_at')
                    ->orderBy('admin_name')
                    ->limit(20)
                    ->get()
                    ->map(function ($admin) {
                        return [
                            'id' => $admin->id,
                            'name' => $admin->admin_name,
                            'email' => $admin->email,
                            'role' => 'admin',
                            'avatar' => $this->getInitials($admin->admin_name),
                            'status' => $this->getUserStatus($admin->id),
                            'last_seen' => $admin->updated_at ? $admin->updated_at->diffForHumans() : null
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

        $query = Director::where('directors_archived', false)
                        ->where('directors_id', '!=', $currentUser->directors_id ?? 0); // Exclude current user

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('directors_name', 'like', '%' . $search . '%')
                  ->orWhere('directors_first_name', 'like', '%' . $search . '%')
                  ->orWhere('directors_last_name', 'like', '%' . $search . '%')
                  ->orWhere('directors_email', 'like', '%' . $search . '%')
                  ->orWhereRaw("CONCAT(directors_first_name, ' ', directors_last_name) LIKE ?", ['%' . $search . '%']);
            });
        }

        return $query->select('directors_id as id', 'directors_name', 'directors_first_name', 'directors_last_name', 'directors_email', 'created_at')
                    ->orderBy('directors_first_name')
                    ->limit(20)
                    ->get()
                    ->map(function ($director) {
                        $fullName = trim(($director->directors_first_name ?? '') . ' ' . ($director->directors_last_name ?? ''));
                        if (empty($fullName)) {
                            $fullName = $director->directors_name ?? 'Unknown Director';
                        }
                        return [
                            'id' => $director->id,
                            'name' => $fullName,
                            'email' => $director->directors_email,
                            'role' => 'director',
                            'avatar' => $this->getInitials($fullName),
                            'status' => $this->getUserStatus($director->id),
                            'last_seen' => $director->updated_at ? $director->updated_at->diffForHumans() : null
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

        $authUser = Auth::user();
        if ($authUser && $authUser instanceof Professor) {
            $user = (object) [
                'user_id' => $authUser->professor_id,
                'id' => $authUser->professor_id,
                'role' => 'professor',
                'name' => trim(($authUser->professor_first_name ?? '') . ' ' . ($authUser->professor_last_name ?? '')) ?: ($authUser->professor_name ?? 'Unknown Professor')
            ];
        } elseif ($authUser && isset($authUser->role)) {
            $user = (object) [
                'user_id' => $authUser->id ?? $authUser->user_id,
                'id' => $authUser->id ?? $authUser->user_id,
                'role' => $authUser->role,
                'name' => $authUser->name ?? ($authUser->user_firstname . ' ' . $authUser->user_lastname) ?? 'Unknown User'
            ];
        } else {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
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

        $authUser = Auth::user();
        if ($authUser && $authUser instanceof Professor) {
            $user = (object) [
                'user_id' => $authUser->professor_id,
                'id' => $authUser->professor_id,
                'role' => 'professor',
                'name' => trim(($authUser->professor_first_name ?? '') . ' ' . ($authUser->professor_last_name ?? '')) ?: ($authUser->professor_name ?? 'Unknown Professor')
            ];
        } elseif ($authUser && isset($authUser->role)) {
            $user = (object) [
                'user_id' => $authUser->id ?? $authUser->user_id,
                'id' => $authUser->id ?? $authUser->user_id,
                'role' => $authUser->role,
                'name' => $authUser->name ?? ($authUser->user_firstname . ' ' . $authUser->user_lastname) ?? 'Unknown User'
            ];
        } else {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
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
        // TEMPORARY: Return test data to verify API connectivity
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'id' => 8,
                    'name' => 'Test Professor',
                    'role' => 'professor',
                    'email' => 'test@example.com'
                ]
            ],
            'total' => 1,
            'debug' => [
                'message' => 'API endpoint working - authentication temporarily bypassed',
                'session_data' => [
                    'user_id' => session('user_id'),
                    'all_session' => session()->all(),
                    'php_session' => $_SESSION ?? []
                ]
            ]
        ]);
        
        /*
        $type = $request->input('type');
        $search = $request->input('q', '');
        $program = $request->input('program', '');
        $batch = $request->input('batch', '');
        $mode = $request->input('mode', '');
        
        // Get current user from session - check both session formats
        $currentUserId = session('user_id') ?? $_SESSION['user_id'] ?? null;
        $currentUserRole = session('user_role') ?? session('role') ?? $_SESSION['user_type'] ?? 'guest';
        
        // Log debugging info
        Log::info('getSessionUsers called', [
            'type' => $type,
            'search' => $search,
            'current_user_id' => $currentUserId,
            'current_user_role' => $currentUserRole,
            'session_data' => [
                'laravel_user_id' => session('user_id'),
                'laravel_role' => session('user_role'),
                'php_session_user_id' => $_SESSION['user_id'] ?? null,
                'php_session_user_type' => $_SESSION['user_type'] ?? null,
            ]
        ]);
        
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
                default:
                    // Return all available user types based on current user's role
                    switch ($currentUserRole) {
                        case 'admin':
                        case 'director':
                            $users = $users->merge($this->getSessionStudents($search, $currentUserRole, $program, $batch, $mode))
                                          ->merge($this->getSessionProfessors($search, $currentUserRole, $program, $batch, $mode))
                                          ->merge($this->getSessionAdmins($search, $currentUserRole));
                            break;
                        case 'professor':
                            $users = $users->merge($this->getSessionStudents($search, $currentUserRole, $program, $batch, $mode))
                                          ->merge($this->getSessionAdmins($search, $currentUserRole));
                            break;
                        case 'student':
                            $users = $users->merge($this->getSessionProfessors($search, $currentUserRole, $program, $batch, $mode))
                                          ->merge($this->getSessionAdmins($search, $currentUserRole));
                            break;
                    }
            }
            
            Log::info('getSessionUsers result', [
                'users_count' => $users->count(),
                'type' => $type,
                'current_user_role' => $currentUserRole
            ]);
            
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
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage()
            ], 500);
        }
        */
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
            
            if (!$receiverId || !$message) {
                return response()->json(['error' => 'Receiver ID and message are required'], 400);
            }
            
            // Validate message length
            if (strlen($message) > 1000) {
                return response()->json(['error' => 'Message too long'], 400);
            }
            
            // Create encrypted message entry using Chat model
            $chat = Chat::create([
                'sender_id' => $currentUser['id'],
                'receiver_id' => $receiverId,
                'message' => $message, // This will be automatically encrypted by the model
                'sent_at' => now(),
                'is_read' => false
            ]);
            
            // Load sender relationship for broadcasting
            $chat->load('sender');
            
            // Broadcast the message via WebSocket if available
            try {
                if (class_exists('\App\Events\MessageSent')) {
                    broadcast(new \App\Events\MessageSent($chat))->toOthers();
                }
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast message: ' . $e->getMessage());
                // Continue execution even if broadcasting fails
            }
            
            return response()->json([
                'success' => true,
                'id' => $chat->chat_id,
                'message' => 'Message sent successfully',
                'data' => [
                    'id' => $chat->chat_id,
                    'sender_id' => $currentUser['id'],
                    'receiver_id' => $receiverId,
                    'message' => $message, // Send plain text in response (encrypted in DB)
                    'sent_at' => $chat->sent_at->toISOString(),
                    'sender_name' => $currentUser['name'],
                    'is_read' => false
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending session message: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
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
        $after = $request->input('after'); // For pagination/real-time updates
        $currentUser = $this->getCurrentSessionUser();
        
        if (!$currentUser['id']) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        try {
            // Get messages between current user and specified user using Chat model
            $query = Chat::with(['sender', 'receiver'])
                ->where(function ($q) use ($currentUser, $with) {
                    $q->where('sender_id', $currentUser['id'])
                      ->where('receiver_id', $with);
                })
                ->orWhere(function ($q) use ($currentUser, $with) {
                    $q->where('sender_id', $with)
                      ->where('receiver_id', $currentUser['id']);
                });
            
            // If 'after' parameter is provided, get only newer messages
            if ($after) {
                $query->where('chat_id', '>', $after);
            }
            
            $messages = $query->orderBy('sent_at', 'asc')->get();
            
            // Transform messages for frontend
            $transformedMessages = $messages->map(function ($message) {
                $senderInfo = $message->sender_info;
                return [
                    'id' => $message->chat_id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message, // Automatically decrypted by model
                    'sent_at' => $message->sent_at->toISOString(),
                    'is_read' => $message->is_read,
                    'sender' => $senderInfo ? [
                        'id' => $senderInfo['id'],
                        'name' => $senderInfo['name'],
                        'email' => $senderInfo['email'],
                        'role' => $senderInfo['role'],
                        'type' => $senderInfo['type']
                    ] : [
                        'id' => $message->sender_id,
                        'name' => 'Unknown User',
                        'role' => 'unknown',
                        'type' => 'unknown'
                    ]
                ];
            });
            
            return response()->json([
                'success' => true,
                'messages' => $transformedMessages,
                'data' => $transformedMessages, // For backward compatibility
                'debug' => [
                    'current_user_id' => $currentUser['id'],
                    'with_user_id' => $with,
                    'message_count' => $messages->count(),
                    'after' => $after
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching session messages: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
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
        $currentUser = $this->getCurrentSessionUser();
        
        if (!$currentUser['id']) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        try {
            $deletedCount = Chat::where(function ($query) use ($currentUser, $with) {
                $query->where('sender_id', $currentUser['id'])
                      ->where('receiver_id', $with);
            })
            ->orWhere(function ($query) use ($currentUser, $with) {
                $query->where('sender_id', $with)
                      ->where('receiver_id', $currentUser['id']);
            })
            ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Chat history cleared successfully',
                'deleted_count' => $deletedCount
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
     * Get unread message count for current user
     */
    public function getUnreadCount(Request $request)
    {
        $currentUser = $this->getCurrentSessionUser();
        
        if (!$currentUser['id']) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        try {
            $unreadCount = Chat::where('receiver_id', $currentUser['id'])
                ->where('is_read', false)
                ->count();
            
            return response()->json([
                'success' => true,
                'count' => $unreadCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to get unread count',
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
            // Search in users table where role is student
            $query = User::where('role', 'student');
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('user_firstname', 'like', '%' . $search . '%')
                      ->orWhere('user_lastname', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhereRaw("CONCAT(user_firstname, ' ', user_lastname) LIKE ?", ['%' . $search . '%']);
                });
            }
            
            return $query->select('user_id as id', 'user_firstname', 'user_lastname', 'email', 'role', 'created_at')
                        ->orderBy('user_firstname')
                        ->limit(20)
                        ->get()
                        ->map(function ($user) {
                            return [
                                'id' => $user->id, // This is user_id from users table
                                'name' => trim($user->user_firstname . ' ' . $user->user_lastname) ?: 'Unknown Student',
                                'first_name' => $user->user_firstname ?? '',
                                'last_name' => $user->user_lastname ?? '',
                                'email' => $user->email ?? 'No email',
                                'role' => 'student'
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
                                'id' => $professor->id, // This is professor_id from professors table
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
     * Get current session user info (with Auth support for professors)
     */
    private function getCurrentSessionUser()
    {
        // First check if user is authenticated via Laravel Auth (for professors)
        $authUser = Auth::user();
        if ($authUser) {
            // Determine role based on the guard or model type
            if ($authUser instanceof Professor) {
                return [
                    'id' => $authUser->professor_id,
                    'role' => 'professor', 
                    'name' => trim(($authUser->professor_first_name ?? '') . ' ' . ($authUser->professor_last_name ?? '')) ?: ($authUser->professor_name ?? 'Unknown Professor')
                ];
            } elseif (isset($authUser->role)) {
                // Handle other Auth users with role attribute
                return [
                    'id' => $authUser->id ?? $authUser->user_id,
                    'role' => $authUser->role,
                    'name' => $authUser->name ?? trim(($authUser->user_firstname ?? '') . ' ' . ($authUser->user_lastname ?? '')) ?? 'Unknown User'
                ];
            }
        }
        
        // Fallback to session-based authentication for other user types
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = session('user_id') ?? $_SESSION['user_id'] ?? null;
        $userRole = session('user_role') ?? session('role') ?? $_SESSION['user_type'] ?? $_SESSION['role'] ?? null;
        $userName = session('user_name') ?? session('name') ?? $_SESSION['user_name'] ?? $_SESSION['name'] ?? 'Unknown User';
        
        // For students, use the user_id from users table (not student_id from students table)
        // The chat system uses user_id as the primary identifier
        if ($userRole === 'student' && $userId) {
            // Get student name from users table or students table
            $user = User::find($userId);
            if ($user) {
                $userName = trim($user->user_firstname . ' ' . $user->user_lastname);
            } else {
                // Fallback: check students table for student with this user_id
                $studentRecord = Student::where('user_id', $userId)->first();
                if ($studentRecord) {
                    $userName = trim($studentRecord->firstname . ' ' . $studentRecord->lastname);
                }
            }
        }
        
        // If no user_id found, check for specific role-based IDs
        if (!$userId) {
            // Check for student session
            if (session('student_id') || (isset($_SESSION['student_id']) && $_SESSION['student_id'])) {
                // For students, we need to get the user_id, not the student_id
                $studentId = session('student_id') ?? $_SESSION['student_id'];
                $student = Student::where('student_id', $studentId)->first();
                if ($student) {
                    $userId = $student->user_id; // Use user_id for chat system
                    $userRole = 'student';
                    $userName = trim($student->firstname . ' ' . $student->lastname);
                } else {
                    // Fallback: assume student_id is the same as user_id
                    $userId = $studentId;
                    $userRole = 'student';
                    $userName = session('student_name') ?? $_SESSION['student_name'] ?? $userName;
                }
            }
            // Check for admin session
            elseif (session('admin_id') || (isset($_SESSION['admin_id']) && $_SESSION['admin_id'])) {
                $userId = session('admin_id') ?? $_SESSION['admin_id'];
                $userRole = 'admin';
                $userName = session('admin_name') ?? $_SESSION['admin_name'] ?? $userName;
            }
            // Check for professor session
            elseif (session('professor_id') || (isset($_SESSION['professor_id']) && $_SESSION['professor_id'])) {
                $userId = session('professor_id') ?? $_SESSION['professor_id'];
                $userRole = 'professor';
                $professor = Professor::find($userId);
                if ($professor) {
                    $userName = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? '')) ?: ($professor->professor_name ?? 'Unknown Professor');
                } else {
                    $userName = session('professor_name') ?? $_SESSION['professor_name'] ?? $userName;
                }
            }
            // Check for director session
            elseif (session('director_id') || (isset($_SESSION['director_id']) && $_SESSION['director_id'])) {
                $userId = session('director_id') ?? $_SESSION['director_id'];
                $userRole = 'director';
                $userName = session('director_name') ?? $_SESSION['director_name'] ?? $userName;
            }
        }
        
        return [
            'id' => $userId,
            'role' => $userRole,
            'name' => $userName
        ];
    }

    /**
     * Get current user role (simplified version)
     */
    private function getCurrentUserRole()
    {
        $user = $this->getCurrentSessionUser();
        return $user['role'];
    }

    /**
     * General search endpoint for chat users
     */
    public function sessionSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:50'
        ]);

        $query = $request->input('query');
        // Get current user from Auth or session fallback
        $authUser = Auth::user();
        if ($authUser && $authUser instanceof Professor) {
            // Authenticated professor
            $currentUser = (object) [
                'user_id' => $authUser->professor_id,
                'id' => $authUser->professor_id, // for compatibility
                'role' => 'professor',
                'name' => trim(($authUser->professor_first_name ?? '') . ' ' . ($authUser->professor_last_name ?? '')) ?: ($authUser->professor_name ?? 'Unknown Professor')
            ];
        } elseif ($authUser && isset($authUser->role)) {
            // Other Auth user (admin, director, etc)
            $currentUser = (object) [
                'user_id' => $authUser->id ?? $authUser->user_id,
                'id' => $authUser->id ?? $authUser->user_id,
                'role' => $authUser->role,
                'name' => $authUser->name ?? ($authUser->user_firstname . ' ' . $authUser->user_lastname) ?? 'Unknown User'
            ];
        } else {
            // Fallback to session-based authentication
            $sessionUser = $this->getCurrentSessionUser();
            if (!$sessionUser['id']) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }
            $currentUser = (object) [
                'user_id' => $sessionUser['id'],
                'id' => $sessionUser['id'],
                'role' => $sessionUser['role'],
                'name' => $sessionUser['name']
            ];
        }

        try {
            $allUsers = [];

            // Search across all user types based on current user's role
            switch ($currentUser->role) {
                case 'admin':
                case 'director':
                    // Admins and directors can search all user types
                    $allUsers = array_merge(
                        $this->getStudents($query, $currentUser)->toArray(),
                        $this->getProfessors($query, $currentUser)->toArray(),
                        $this->getAdmins($query, $currentUser)->toArray(),
                        $this->getDirectors($query, $currentUser)->toArray()
                    );
                    break;
                    
                case 'professor':
                    // Professors can search students and support
                    $allUsers = array_merge(
                        $this->getStudents($query, $currentUser)->toArray(),
                        $this->getAdmins($query, $currentUser)->toArray()
                    );
                    break;
                    
                case 'student':
                    // Students can search professors and admins
                    $allUsers = array_merge(
                        $this->getProfessors($query, $currentUser)->toArray(),
                        $this->getAdmins($query, $currentUser)->toArray()
                    );
                    break;
                    
                default:
                    $allUsers = [];
            }

            // Remove duplicates and sort by name
            $uniqueUsers = [];
            $seenIds = [];
            
            foreach ($allUsers as $user) {
                $uniqueKey = $user['id'] . '_' . $user['role'];
                if (!in_array($uniqueKey, $seenIds)) {
                    $seenIds[] = $uniqueKey;
                    $uniqueUsers[] = $user;
                }
            }

            // Sort by name
            usort($uniqueUsers, function($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });

            // Limit results to 20
            $uniqueUsers = array_slice($uniqueUsers, 0, 20);

            return response()->json([
                'success' => true,
                'data' => $uniqueUsers,
                'total' => count($uniqueUsers),
                'query' => $query,
                'current_user_role' => $currentUser->role
            ]);

        } catch (\Exception $e) {
            Log::error('Error in sessionSearch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
