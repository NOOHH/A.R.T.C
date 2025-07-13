<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Admin;
use App\Models\Director;
use App\Models\Chat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EnhancedChatController extends Controller
{
    /**
     * Get current authenticated user info from session
     */
    private function getCurrentUser()
    {
        $userId = null;
        $userRole = null;

        // Check Laravel session first
        if (session('logged_in') && session('user_id')) {
            $userId = session('user_id');
            $userRole = session('user_role') ?? session('role') ?? 'user';
        }
        // Check admin session
        elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
            $userId = $_SESSION['admin_id'] ?? null;
            $userRole = 'admin';
        }
        // Check specific role sessions
        elseif (session('student_logged_in') && session('student_id')) {
            $userId = session('student_id');
            $userRole = 'student';
        }
        elseif (session('professor_logged_in') && session('professor_id')) {
            $userId = session('professor_id');
            $userRole = 'professor';
        }
        elseif (session('director_logged_in') && session('director_id')) {
            $userId = session('director_id');
            $userRole = 'director';
        }

        return [
            'id' => $userId,
            'role' => $userRole,
            'authenticated' => !is_null($userId)
        ];
    }

    /**
     * Get all available users for chat based on current user's role
     */
    public function getSessionUsers(Request $request)
    {
        try {
            $currentUser = $this->getCurrentUser();
            
            if (!$currentUser['authenticated']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not authenticated',
                    'debug' => [
                        'session_user_id' => session('user_id'),
                        'session_logged_in' => session('logged_in'),
                        'php_session_admin_id' => $_SESSION['admin_id'] ?? null,
                        'php_session_admin_logged_in' => $_SESSION['admin_logged_in'] ?? false,
                    ]
                ], 401);
            }

            $type = $request->input('type', 'all');
            $search = $request->input('q', '');
            $users = collect();

            // Get students
            if ($type === 'all' || $type === 'student') {
                $students = $this->getStudents($search, $currentUser);
                $users = $users->merge($students);
            }

            // Get professors
            if ($type === 'all' || $type === 'professor') {
                $professors = $this->getProfessors($search, $currentUser);
                $users = $users->merge($professors);
            }

            // Get admins
            if ($type === 'all' || $type === 'admin') {
                $admins = $this->getAdmins($search, $currentUser);
                $users = $users->merge($admins);
            }

            // Get directors
            if ($type === 'all' || $type === 'director') {
                $directors = $this->getDirectors($search, $currentUser);
                $users = $users->merge($directors);
            }

            return response()->json([
                'success' => true,
                'data' => $users->toArray(),
                'total' => $users->count(),
                'debug' => [
                    'current_user_id' => $currentUser['id'],
                    'current_user_role' => $currentUser['role'],
                    'search_type' => $type,
                    'search_query' => $search
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getSessionUsers: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students for chat
     */
    private function getStudents($search, $currentUser)
    {
        try {
            $query = DB::table('students')
                ->leftJoin('users', 'students.user_id', '=', 'users.user_id')
                ->where('students.is_archived', false);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('students.firstname', 'like', "%{$search}%")
                      ->orWhere('students.lastname', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%");
                });
            }

            // Don't include current user
            if ($currentUser['role'] === 'student') {
                $query->where('students.user_id', '!=', $currentUser['id']);
            }

            return $query->select(
                'students.user_id as id',
                DB::raw("CONCAT(students.firstname, ' ', students.lastname) as name"),
                'users.email',
                DB::raw("'student' as role"),
                'students.program_id',
                'students.batch_id'
            )
            ->limit(20)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name ?? 'Unknown Student',
                    'email' => $student->email ?? 'No email',
                    'role' => 'student',
                    'program_id' => $student->program_id,
                    'batch_id' => $student->batch_id,
                    'is_online' => true
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get professors for chat
     */
    private function getProfessors($search, $currentUser)
    {
        try {
            $query = DB::table('professors')
                ->where('is_archived', false);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('professor_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Don't include current user
            if ($currentUser['role'] === 'professor') {
                $query->where('professor_id', '!=', $currentUser['id']);
            }

            return $query->select(
                'professor_id as id',
                'professor_name as name',
                'email',
                DB::raw("'professor' as role")
            )
            ->limit(20)
            ->get()
            ->map(function($professor) {
                return [
                    'id' => $professor->id,
                    'name' => $professor->name ?? 'Unknown Professor',
                    'email' => $professor->email ?? 'No email',
                    'role' => 'professor',
                    'is_online' => true
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error fetching professors: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get admins for chat
     */
    private function getAdmins($search, $currentUser)
    {
        try {
            $query = DB::table('admins');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('admin_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Don't include current user
            if ($currentUser['role'] === 'admin') {
                $query->where('admin_id', '!=', $currentUser['id']);
            }

            return $query->select(
                'admin_id as id',
                'admin_name as name',
                'email',
                DB::raw("'admin' as role")
            )
            ->limit(20)
            ->get()
            ->map(function($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name ?? 'Unknown Admin',
                    'email' => $admin->email ?? 'No email',
                    'role' => 'admin',
                    'is_online' => true
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error fetching admins: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get directors for chat
     */
    private function getDirectors($search, $currentUser)
    {
        try {
            $query = DB::table('directors')
                ->where('is_archived', false);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('director_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Don't include current user
            if ($currentUser['role'] === 'director') {
                $query->where('director_id', '!=', $currentUser['id']);
            }

            return $query->select(
                'director_id as id',
                'director_name as name',
                'email',
                DB::raw("'director' as role")
            )
            ->limit(20)
            ->get()
            ->map(function($director) {
                return [
                    'id' => $director->id,
                    'name' => $director->name ?? 'Unknown Director',
                    'email' => $director->email ?? 'No email',
                    'role' => 'director',
                    'is_online' => true
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error fetching directors: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get messages between current user and another user
     */
    public function getSessionMessages(Request $request)
    {
        try {
            $currentUser = $this->getCurrentUser();
            
            if (!$currentUser['authenticated']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not authenticated'
                ], 401);
            }

            $withUserId = $request->input('with');
            
            if (!$withUserId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing with parameter'
                ], 400);
            }

            $messages = Chat::where(function ($query) use ($currentUser, $withUserId) {
                $query->where('sender_id', $currentUser['id'])
                      ->where('receiver_id', $withUserId);
            })
            ->orWhere(function ($query) use ($currentUser, $withUserId) {
                $query->where('sender_id', $withUserId)
                      ->where('receiver_id', $currentUser['id']);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($currentUser) {
                return [
                    'id' => $message->chat_id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'content' => $message->message,
                    'created_at' => $message->created_at->toISOString(),
                    'is_read' => !is_null($message->read_at),
                    'is_mine' => $message->sender_id == $currentUser['id'],
                    'sender_name' => $this->getUserName($message->sender_id)
                ];
            });

            // Mark messages as read
            Chat::where('sender_id', $withUserId)
                ->where('receiver_id', $currentUser['id'])
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'data' => $messages->toArray(),
                'total' => $messages->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getSessionMessages: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch messages',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message
     */
    public function sendSessionMessage(Request $request)
    {
        try {
            $currentUser = $this->getCurrentUser();
            
            if (!$currentUser['authenticated']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not authenticated'
                ], 401);
            }

            $request->validate([
                'receiver_id' => 'required|integer',
                'message' => 'required|string|max:1000'
            ]);

            $receiverId = $request->input('receiver_id');
            $messageContent = $request->input('message');

            // Create the chat message
            $chat = Chat::create([
                'sender_id' => $currentUser['id'],
                'receiver_id' => $receiverId,
                'message' => $messageContent,
                'sent_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'id' => $chat->chat_id,
                'message' => 'Message sent successfully',
                'data' => [
                    'id' => $chat->chat_id,
                    'sender_id' => $chat->sender_id,
                    'receiver_id' => $chat->receiver_id,
                    'content' => $chat->message,
                    'created_at' => $chat->created_at->toISOString(),
                    'is_read' => false,
                    'is_mine' => true,
                    'sender_name' => $this->getUserName($chat->sender_id),
                    'sender_role' => $currentUser['role']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in sendSessionMessage: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear chat history with a user
     */
    public function clearSessionHistory(Request $request)
    {
        try {
            $currentUser = $this->getCurrentUser();
            
            if (!$currentUser['authenticated']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not authenticated'
                ], 401);
            }

            $withUserId = $request->input('with');
            
            if (!$withUserId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing with parameter'
                ], 400);
            }

            Chat::where(function ($query) use ($currentUser, $withUserId) {
                $query->where('sender_id', $currentUser['id'])
                      ->where('receiver_id', $withUserId);
            })
            ->orWhere(function ($query) use ($currentUser, $withUserId) {
                $query->where('sender_id', $withUserId)
                      ->where('receiver_id', $currentUser['id']);
            })
            ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat history cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in clearSessionHistory: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to clear chat history',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user name by ID and role
     */
    private function getUserName($userId)
    {
        try {
            // Try to find in different user tables
            $student = DB::table('students')->where('user_id', $userId)->first();
            if ($student) {
                return $student->firstname . ' ' . $student->lastname;
            }

            $professor = DB::table('professors')->where('professor_id', $userId)->first();
            if ($professor) {
                return $professor->professor_name;
            }

            $admin = DB::table('admins')->where('admin_id', $userId)->first();
            if ($admin) {
                return $admin->admin_name;
            }

            $director = DB::table('directors')->where('director_id', $userId)->first();
            if ($director) {
                return $director->director_name;
            }

            return 'Unknown User';

        } catch (\Exception $e) {
            Log::error('Error getting user name: ' . $e->getMessage());
            return 'Unknown User';
        }
    }

    /**
     * Get conversations/recent chats for current user
     */
    public function getSessionConversations(Request $request)
    {
        try {
            $currentUser = $this->getCurrentUser();
            
            if (!$currentUser['authenticated']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not authenticated'
                ], 401);
            }

            // Get recent conversations
            $conversations = DB::table('chats')
                ->select(
                    DB::raw('CASE 
                        WHEN sender_id = ? THEN receiver_id 
                        ELSE sender_id 
                    END as other_user_id'),
                    DB::raw('MAX(created_at) as last_message_time'),
                    DB::raw('COUNT(*) as message_count'),
                    DB::raw('SUM(CASE WHEN receiver_id = ? AND read_at IS NULL THEN 1 ELSE 0 END) as unread_count')
                )
                ->where(function($query) use ($currentUser) {
                    $query->where('sender_id', $currentUser['id'])
                          ->orWhere('receiver_id', $currentUser['id']);
                })
                ->groupBy('other_user_id')
                ->orderBy('last_message_time', 'desc')
                ->setBindings([$currentUser['id'], $currentUser['id']])
                ->limit(20)
                ->get()
                ->map(function($conversation) {
                    return [
                        'user_id' => $conversation->other_user_id,
                        'user_name' => $this->getUserName($conversation->other_user_id),
                        'last_message_time' => $conversation->last_message_time,
                        'message_count' => $conversation->message_count,
                        'unread_count' => $conversation->unread_count
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $conversations->toArray(),
                'total' => $conversations->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getSessionConversations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch conversations',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
