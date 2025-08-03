<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Admin;
use App\Models\Director;
use App\Models\Program;
use App\Models\Course;
use App\Models\Module;
use App\Models\Enrollment;

class SearchController extends Controller
{
    /**
     * Main search endpoint for the navbar search
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $type = $request->get('type', 'all');
        $limit = $request->get('limit', 10);
        
        // Use session-based authentication instead of Laravel Auth
        $userId = session('user_id') ?? session('admin_id') ?? session('directors_id') ?? session('professor_id');
        $userRole = session('user_role') ?? session('user_type') ?? session('role') ?? session('type');
        
        Log::info("SearchController DEBUG: Main search called", [
            'query' => $query,
            'type' => $type,
            'limit' => $limit,
            'session_user_id' => $userId,
            'session_user_role' => $userRole,
            'session_user_type' => session('user_type'),
            'session_role' => session('role'),
            'session_type' => session('type'),
            'session_admin_id' => session('admin_id'),
            'session_directors_id' => session('directors_id'),
            'session_professor_id' => session('professor_id'),
            'auth_user_id' => Auth::id(),
            'auth_user_role' => Auth::user() ? Auth::user()->role : 'null'
        ]);
        
        if (strlen($query) < 2) {
            Log::info("SearchController DEBUG: Query too short");
            return response()->json([
                'success' => false,
                'message' => 'Query too short'
            ]);
        }

        // Check authentication using session
        if (!$userId || !$userRole) {
            Log::warning("SearchController DEBUG: No authenticated user in session");
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Create a mock user object for compatibility with existing methods
        $user = (object) [
            'user_id' => $userId,
            'role' => $userRole
        ];
        
        $results = [];

        try {
            Log::info("SearchController DEBUG: About to switch on type", ['type' => $type]);
            
            switch ($type) {
                case 'students':
                    Log::info("SearchController DEBUG: Searching students");
                    $results = $this->searchStudents($query, $limit, $user);
                    break;
                case 'professors':
                    Log::info("SearchController DEBUG: Searching professors");
                    $results = $this->searchProfessors($query, $limit, $user);
                    break;
                case 'programs':
                    Log::info("SearchController DEBUG: Searching programs");
                    $results = $this->searchPrograms($query, $limit, $user);
                    break;
                case 'all':
                default:
                    Log::info("SearchController DEBUG: Searching all - calling searchAll");
                    $results = $this->searchAll($query, $limit, $user);
                    break;
            }

            Log::info("SearchController DEBUG: Search completed", [
                'results_count' => count($results),
                'results_summary' => array_map(function($r) {
                    return ['type' => $r['type'] ?? 'unknown', 'name' => $r['name'] ?? 'unknown'];
                }, $results)
            ]);

            return response()->json([
                'success' => true,
                'results' => $results,
                'total' => count($results)
            ]);

        } catch (\Exception $e) {
            Log::error("SearchController DEBUG: Exception occurred", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Universal search for all types
     */
    public function universalSearch(Request $request)
    {
        return $this->search($request);
    }

    /**
     * Advanced search with filters
     */
    public function advancedSearch(Request $request)
    {
        $query = $request->get('query', '');
        $role = $request->get('role', '');
        $status = $request->get('status', '');
        $program_id = $request->get('program', '');
        $limit = $request->get('limit', 50);

        $user = Auth::user();
        $results = [];

        // Build query based on role filter
        if ($role === 'student') {
            $results = $this->advancedSearchStudents($query, $status, $program_id, $limit, $user);
        } elseif ($role === 'professor') {
            $results = $this->advancedSearchProfessors($query, $status, $limit, $user);
        } else {
            // Search all
            $students = $this->advancedSearchStudents($query, $status, $program_id, $limit/2, $user);
            $professors = $this->advancedSearchProfessors($query, $status, $limit/2, $user);
            $results = array_merge($students, $professors);
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($results)
        ]);
    }

    /**
     * Get user profile details
     */
    public function getProfile(Request $request)
    {
        $user_id = $request->get('user_id');
        $type = $request->get('type', 'user');

        Log::info("SearchController DEBUG: getProfile called", [
            'user_id' => $user_id,
            'type' => $type,
            'request_auth_user' => Auth::id(),
            'session_user_id' => session('user_id'),
            'session_user_role' => session('user_role'),
        ]);

        if (!$user_id) {
            Log::error("SearchController DEBUG: getProfile - no user_id provided");
            return response()->json([
                'success' => false,
                'message' => 'User ID required in request.'
            ]);
        }

        try {
            if ($type === 'program') {
                Log::info("SearchController DEBUG: getProfile - calling getProgramDetails");
                return $this->getProgramDetails($user_id);
            } else {
                Log::info("SearchController DEBUG: getProfile - calling getUserProfile");
                return $this->getUserProfile($user_id);
            }
        } catch (\Exception $e) {
            Log::error("SearchController DEBUG: getProfile exception", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user_id,
                'type' => $type
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Profile not found: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'user_id' => $user_id,
                'type' => $type
            ]);
        }
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('query', '');
        $user = Auth::user();
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = [];

        // Get user suggestions
        $users = User::where(function($q) use ($query) {
            $q->where('user_firstname', 'LIKE', "%{$query}%")
              ->orWhere('user_lastname', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
        })->limit(5)->get(['user_id', 'user_firstname', 'user_lastname', 'role']);

        foreach ($users as $userItem) {
            $suggestions[] = $userItem->user_firstname . ' ' . $userItem->user_lastname;
        }

        // Get program suggestions if user can see them
        if ($user && in_array($user->role, ['admin', 'director', 'professor'])) {
            $programs = Program::where('program_name', 'LIKE', "%{$query}%")
                              ->where('is_archived', false)
                              ->limit(3)
                              ->pluck('program_name');
            $suggestions = array_merge($suggestions, $programs->toArray());
        }

        return response()->json(['suggestions' => array_unique($suggestions)]);
    }

    /**
     * Search all users and programs
     */
    private function searchAll($query, $limit, $user)
    {
        Log::info("SearchController DEBUG: searchAll called", [
            'query' => $query,
            'limit' => $limit,
            'user_id' => $user ? $user->user_id : 'null',
            'user_role' => $user ? $user->role : 'null'
        ]);
        
        $results = [];
        
        // For students: Show ALL available programs and professors
        if ($user && $user->role === 'student') {
            Log::info("SearchController DEBUG: User is student, calling searchAllProgramsForStudent");
            
            // Search ALL active programs (not just enrolled ones)
            $programResults = $this->searchAllProgramsForStudent($query, $limit, $user);
            $results = array_merge($results, $programResults);
            
            Log::info("SearchController DEBUG: Got program results", ['count' => count($programResults)]);
            
            // Search ALL professors
            $professorResults = $this->searchAllProfessorsForStudent($query, $limit - count($results), $user);
            $results = array_merge($results, $professorResults);
            
            Log::info("SearchController DEBUG: Got professor results", ['count' => count($professorResults)]);
            Log::info("SearchController DEBUG: Total results for student", ['count' => count($results)]);
            
            return array_slice($results, 0, $limit);
        }
        
        // For admins, directors, professors: Search users
        $userResults = $this->searchUsers($query, $limit, $user);
        $results = array_merge($results, $userResults);
        
        // Search programs if user can see them (now also for students)
        if ($user && in_array($user->role, ['admin', 'director', 'professor', 'student'])) {
            $programResults = $this->searchPrograms($query, $limit - count($results), $user);
            $results = array_merge($results, $programResults);
        }
        
        return array_slice($results, 0, $limit);
    }

    /**
     * Search users (students, professors, admins)
     */
    private function searchUsers($query, $limit, $user)
    {
        $query_parts = explode(' ', $query);
        
        $users = User::where(function($q) use ($query, $query_parts) {
            $q->where('user_firstname', 'LIKE', "%{$query}%")
              ->orWhere('user_lastname', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
            
            // Search full name
            if (count($query_parts) >= 2) {
                $q->orWhere(function($subQ) use ($query_parts) {
                    $subQ->where('user_firstname', 'LIKE', "%{$query_parts[0]}%")
                         ->where('user_lastname', 'LIKE', "%{$query_parts[1]}%");
                });
            }
        });

        // Apply role-based filtering
        if ($user) {
            switch ($user->role) {
                case 'student':
                    $users->whereIn('role', ['professor', 'admin', 'director']);
                    break;
                case 'professor':
                    $users->whereIn('role', ['student', 'admin', 'director']);
                    break;
                // admin and director can see all
            }
        }

        $users = $users->limit($limit)->get();
        
        return $users->map(function($userItem) {
            return [
                'id' => $userItem->user_id ?? '',
                'type' => 'user',
                'name' => (isset($userItem->user_firstname) ? $userItem->user_firstname : '') . ' ' . (isset($userItem->user_lastname) ? $userItem->user_lastname : ''),
                'email' => $userItem->email ?? '',
                'role' => isset($userItem->role) ? ucfirst($userItem->role) : '',
                'avatar' => $this->getUserAvatar($userItem),
                'status' => isset($userItem->is_online) && $userItem->is_online ? 'Online' : 'Offline',
                'profile_url' => $this->getUserProfileUrl($userItem)
            ];
        })->toArray();
    }

    /**
     * Search students specifically
     */
    private function searchStudents($query, $limit, $user)
    {
        if (!$user || !in_array($user->role, ['admin', 'director', 'professor'])) {
            return [];
        }

        $query_parts = explode(' ', $query);
        
        $students = User::where('role', 'student')
            ->where(function($q) use ($query, $query_parts) {
                $q->where('user_firstname', 'LIKE', "%{$query}%")
                  ->orWhere('user_lastname', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
                
                if (count($query_parts) >= 2) {
                    $q->orWhere(function($subQ) use ($query_parts) {
                        $subQ->where('user_firstname', 'LIKE', "%{$query_parts[0]}%")
                             ->where('user_lastname', 'LIKE', "%{$query_parts[1]}%");
                    });
                }
            })
            ->with(['student.enrollments.program'])
            ->limit($limit)
            ->get();

        return $students->map(function($student) {
            $enrollments = $student->student ? $student->student->enrollments : collect();
            $programs = $enrollments->map(function($enrollment) {
                return $enrollment->program ? $enrollment->program->program_name : null;
            })->filter()->unique();

            $studentId = $student->student ? $student->student->student_id : ($student->user_id ?? '');

            return [
                'id' => $student->user_id ?? '',
                'type' => 'student',
                'name' => (isset($student->user_firstname) ? $student->user_firstname : '') . ' ' . (isset($student->user_lastname) ? $student->user_lastname : ''),
                'email' => $student->email ?? '',
                'role' => 'Student',
                'programs' => $programs->toArray(),
                'avatar' => $this->getUserAvatar($student),
                'status' => isset($student->is_online) && $student->is_online ? 'Online' : 'Offline',
                'profile_url' => $student->student ? $this->getStudentProfileUrl($studentId) : '#',
                'student_id' => $studentId
            ];
        })->toArray();
    }

    /**
     * Search professors specifically
     */
    private function searchProfessors($query, $limit, $user)
    {
        // Allow admin, director, student, and professor roles to search professors
        if (!$user || !in_array($user->role, ['admin', 'director', 'student', 'professor'])) {
            return [];
        }

        $query_parts = explode(' ', $query);
        
        $professors = Professor::where('professor_archived', false)
            ->where(function($q) use ($query, $query_parts) {
                $q->where('professor_first_name', 'LIKE', "%{$query}%")
                  ->orWhere('professor_last_name', 'LIKE', "%{$query}%")
                  ->orWhere('professor_name', 'LIKE', "%{$query}%")
                  ->orWhere('professor_email', 'LIKE', "%{$query}%");
                
                if (count($query_parts) >= 2) {
                    $q->orWhere(function($subQ) use ($query_parts) {
                        $subQ->where('professor_first_name', 'LIKE', "%{$query_parts[0]}%")
                             ->where('professor_last_name', 'LIKE', "%{$query_parts[1]}%");
                    });
                }
            })
            ->limit($limit)
            ->get();

        return $professors->map(function($professor) {
            $professorName = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
            if (empty($professorName)) {
                $professorName = $professor->professor_name ?? '';
            }
            
            return [
                'id' => $professor->professor_id,
                'type' => 'professor',
                'name' => $professorName,
                'email' => $professor->professor_email ?? '',
                'role' => 'Professor',
                'avatar' => $this->getUserAvatar((object)['email' => $professor->professor_email]),
                'status' => 'Available',
                'profile_url' => route('admin.professors.show', $professor->professor_id)
            ];
        })->toArray();
    }

    /**
     * Search programs
     */
    private function searchPrograms($query, $limit, $user)
    {
        // Allow students to search all available programs
        if (!$user || !in_array($user->role, ['admin', 'director', 'professor', 'student'])) {
            return [];
        }
        $programs = Program::where(function($q) use ($query) {
                $q->where('program_name', 'LIKE', "%{$query}%")
                  ->orWhere('program_description', 'LIKE', "%{$query}%");
            })
            ->where('is_archived', false)
            ->where('is_active', true)
            ->with(['modules.courses'])
            ->limit($limit)
            ->get();
        return $programs->map(function($program) {
            $moduleCount = $program->modules->count();
            $courseCount = $program->modules->sum(function($module) {
                return $module->courses->count();
            });
            return [
                'id' => $program->program_id,
                'type' => 'program',
                'name' => $program->program_name,
                'description' => $program->program_description,
                'modules_count' => $moduleCount,
                'courses_count' => $courseCount,
                'created_at' => $program->created_at->format('M d, Y'),
                'profile_url' => route('profile.program', $program->program_id)
            ];
        })->toArray();
    }

    /**
     * Advanced search for students
     */
    private function advancedSearchStudents($query, $status, $program_id, $limit, $user)
    {
        if (!$user || !in_array($user->role, ['admin', 'director', 'professor'])) {
            return [];
        }

        $students = User::where('role', 'student');

        if ($query) {
            $students->where(function($q) use ($query) {
                $q->where('user_firstname', 'LIKE', "%{$query}%")
                  ->orWhere('user_lastname', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            });
        }

        if ($status === 'active') {
            $students->where('is_online', true);
        } elseif ($status === 'inactive') {
            $students->where('is_online', false);
        }

        if ($program_id) {
            $students->whereHas('student.enrollments', function($q) use ($program_id) {
                $q->where('program_id', $program_id);
            });
        }

        return $this->formatStudentResults($students->limit($limit)->get());
    }

    /**
     * Advanced search for professors
     */
    private function advancedSearchProfessors($query, $status, $limit, $user)
    {
        if (!$user || !in_array($user->role, ['admin', 'director', 'student'])) {
            return [];
        }

        $professors = User::where('role', 'professor');

        if ($query) {
            $professors->where(function($q) use ($query) {
                $q->where('user_firstname', 'LIKE', "%{$query}%")
                  ->orWhere('user_lastname', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            });
        }

        if ($status === 'active') {
            $professors->where('is_online', true);
        } elseif ($status === 'inactive') {
            $professors->where('is_online', false);
        }

        return $this->formatProfessorResults($professors->limit($limit)->get());
    }

    /**
     * Get detailed user profile
     */
    private function getUserProfile($user_id)
    {
        Log::info("SearchController DEBUG: getUserProfile called", ['user_id' => $user_id]);
        $user = User::with(['student.enrollments.program'])->find($user_id);
        if (!$user) {
            Log::warning("SearchController DEBUG: getUserProfile - user not found", ['user_id' => $user_id]);
            return response()->json([
                'success' => false,
                'message' => 'User not found in users table.',
                'user_id' => $user_id
            ]);
        }
        Log::info("SearchController DEBUG: getUserProfile - user found", ['user_id' => $user_id, 'role' => $user->role]);
        $profile = [
            'id' => $user->user_id,
            'name' => $user->user_firstname . ' ' . $user->user_lastname,
            'email' => $user->email,
            'role' => ucfirst($user->role),
            'avatar' => $this->getUserAvatar($user),
            'status' => $user->is_online ? 'Online' : 'Offline',
            'last_seen' => $user->last_seen,
            'created_at' => $user->created_at
        ];

        // Add role-specific data
        if ($user->role === 'student' && $user->student) {
            $enrollments = $user->student->enrollments->map(function($enrollment) {
                return [
                    'program' => $enrollment->program ? $enrollment->program->program_name : 'Unknown',
                    'enrolled_at' => $enrollment->created_at,
                    'status' => 'Active' // You might have a status field
                ];
            });
            $profile['enrollments'] = $enrollments;
            $profile['student_id'] = $user->student->student_id;
        } else if ($user->role === 'student') {
            // Fallback: try to get student_id from Student model
            $student = \App\Models\Student::where('user_id', $user->user_id)->first();
            if ($student) {
                $profile['student_id'] = $student->student_id;
            }
        }

        // Add professor data using email matching
        if ($user->role === 'professor') {
            $professor = Professor::where('professor_email', $user->email)->first();
            if ($professor) {
                $profile['professor_id'] = $professor->professor_id;
                // You can add more professor-specific data here
            }
        }

        Log::info("SearchController DEBUG: getUserProfile - returning profile", ['user_id' => $user_id, 'profile' => $profile]);
        return response()->json([
            'success' => true,
            'profile' => $profile
        ]);
    }

    /**
     * Get detailed program information
     */
    private function getProgramDetails($program_id)
    {
        $program = Program::with(['modules.courses', 'enrollments.student.user'])
            ->findOrFail($program_id);
        
        $modules = $program->modules->map(function($module) {
            return [
                'id' => $module->module_id,
                'name' => $module->module_name,
                'description' => $module->module_description,
                'courses' => $module->courses->map(function($course) {
                    return [
                        'id' => $course->subject_id,
                        'name' => $course->subject_name,
                        'description' => $course->subject_description
                    ];
                })
            ];
        });

        $enrolledStudents = $program->enrollments->map(function($enrollment) {
            $user = $enrollment->student ? $enrollment->student->user : null;
            if ($user) {
                return [
                    'id' => $user->user_id,
                    'name' => $user->user_firstname . ' ' . $user->user_lastname,
                    'email' => $user->email,
                    'enrolled_at' => $enrollment->created_at
                ];
            }
            return null;
        })->filter();

        $programDetails = [
            'id' => $program->program_id,
            'name' => $program->program_name,
            'description' => $program->program_description,
            'modules' => $modules,
            'enrolled_students' => $enrolledStudents,
            'total_modules' => $modules->count(),
            'total_courses' => $modules->sum(function($module) { 
                return $module['courses']->count(); 
            }),
            'total_students' => $enrolledStudents->count(),
            'created_at' => $program->created_at
        ];

        return response()->json([
            'success' => true,
            'program' => $programDetails
        ]);
    }

    /**
     * Helper methods
     */
    private function getUserAvatar($user)
    {
        // Check if user has a profile photo, otherwise return a generated avatar
        // First check if it's a student object with profile_photo
        if (isset($user->profile_photo) && $user->profile_photo) {
            return asset('storage/profile-photos/' . $user->profile_photo);
        }
        
        // If it's a user object, check if they have a student record with profile_photo
        if (isset($user->role) && $user->role === 'student') {
            $student = Student::where('user_id', $user->user_id)->first();
            if ($student && $student->profile_photo) {
                return asset('storage/profile-photos/' . $student->profile_photo);
            }
        }
        
        // Generate a simple avatar using the user's initials
        $initials = '';
        if (isset($user->user_firstname) && $user->user_firstname) {
            $initials .= strtoupper(substr($user->user_firstname, 0, 1));
        }
        if (isset($user->user_lastname) && $user->user_lastname) {
            $initials .= strtoupper(substr($user->user_lastname, 0, 1));
        }
        
        // Return a data URL for a simple colored avatar with initials
        $colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c', '#fd7e14'];
        $colorIndex = ord($initials[0] ?? 'A') % count($colors);
        $color = $colors[$colorIndex];
        
        // For now, return a placeholder or use a service like Gravatar
        $email = $user->email ?? 'default@example.com';
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=120";
    }

    private function getUserProfileUrl($user)
    {
        switch ($user->role) {
            case 'student':
                // We need to find the student_id for the route
                $student = $user->student;
                if ($student) {
                    return $this->getStudentProfileUrl($student->student_id);
                }
                return '#';
            case 'professor':
                // For professors, we'll create a profile route later
                return '#';
            default:
                return '#';
        }
    }

    /**
     * Get student profile URL based on current user role
     */
    private function getStudentProfileUrl($studentId)
    {
        // Check multiple session variables for user role
        $currentUserRole = session('user_role') ?? session('user_type') ?? session('role') ?? session('type');
        
        Log::info("SearchController DEBUG: getStudentProfileUrl", [
            'studentId' => $studentId,
            'currentUserRole' => $currentUserRole,
            'session_user_role' => session('user_role'),
            'session_user_type' => session('user_type'),
            'session_role' => session('role'),
            'session_type' => session('type')
        ]);
        
        if ($currentUserRole === 'professor') {
            return route('professor.students.index');
        } else {
            return route('admin.students.show', $studentId);
        }
    }

    private function formatStudentResults($students)
    {
        return $students->map(function($student) {
            return [
                'id' => $student->user_id ?? '',
                'type' => 'student',
                'name' => (isset($student->user_firstname) ? $student->user_firstname : '') . ' ' . (isset($student->user_lastname) ? $student->user_lastname : ''),
                'email' => $student->email ?? '',
                'role' => 'Student',
                'avatar' => $this->getUserAvatar($student),
                'status' => isset($student->is_online) && $student->is_online ? 'Online' : 'Offline',
                'profile_url' => $this->getStudentProfileUrl($student->user_id ?? '')
            ];
        })->toArray();
    }

    private function formatProfessorResults($professors)
    {
        return $professors->map(function($professor) {
            $professorName = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
            if (empty($professorName)) {
                $professorName = $professor->professor_name ?? '';
            }
            
            return [
                'id' => $professor->professor_id,
                'type' => 'professor',
                'name' => $professorName,
                'email' => $professor->professor_email ?? '',
                'role' => 'Professor',
                'avatar' => $this->getUserAvatar((object)['email' => $professor->professor_email]),
                'status' => 'Available',
                'profile_url' => route('admin.professors.show', $professor->professor_id)
            ];
        })->toArray();
    }

    /**
     * Search for student: all programs/courses/modules + professors teaching enrolled programs
     */
    private function searchStudentEnrolledOnly($query, $limit, $user)
    {
        $results = [];
        
        // Get student's enrolled programs for professor filtering
        $student = Student::where('user_id', $user->user_id)->first();
        $enrolledProgramIds = $student ? $student->enrollments->pluck('program_id')->unique() : collect();

        // Search ALL programs with modules and courses (not just enrolled ones)
        $programs = Program::where(function($programQuery) use ($query) {
                $programQuery->where('program_name', 'like', "%{$query}%")
                    ->orWhere('program_description', 'like', "%{$query}%")
                    ->orWhereHas('modules', function($moduleQuery) use ($query) {
                        $moduleQuery->where('module_name', 'like', "%{$query}%")
                            ->orWhere('module_description', 'like', "%{$query}%")
                            ->orWhereHas('courses', function($courseQuery) use ($query) {
                                $courseQuery->where('subject_name', 'like', "%{$query}%")
                                    ->orWhere('subject_description', 'like', "%{$query}%");
                            });
                    });
            })
            ->where('is_archived', false)
            ->where('is_active', true)
            ->with(['modules.courses', 'professors'])
            ->limit(7)
            ->get();

        foreach ($programs as $program) {
            $isEnrolled = $enrolledProgramIds->contains($program->program_id);
            $coursesCount = $program->modules->sum(function($module) {
                return $module->courses->count();
            });
            
            $results[] = [
                'id' => $program->program_id,
                'type' => 'program',
                'name' => $program->program_name,
                'email' => null,
                'role' => $isEnrolled ? 'Enrolled Program' : 'Available Program',
                'avatar' => asset('images/program-icon.png'),
                'status' => $isEnrolled ? 'Enrolled' : 'Available',
                'profile_url' => '#',
                'modules_count' => $program->modules->count(),
                'courses_count' => $coursesCount,
                'professors' => $program->professors->map(function($prof) {
                    $name = trim(($prof->professor_first_name ?? '') . ' ' . ($prof->professor_last_name ?? ''));
                    return !empty($name) ? $name : ($prof->professor_name ?? '');
                })->filter()->toArray(),
                'description' => $program->program_description,
                'is_enrolled' => $isEnrolled
            ];
        }

        // Search professors - only those teaching enrolled programs (for security)
        if (!$enrolledProgramIds->isEmpty()) {
            $professorIds = collect();
            foreach ($enrolledProgramIds as $programId) {
                $program = Program::find($programId);
                if ($program) {
                    $professorIds = $professorIds->merge($program->professors->pluck('professor_id'));
                }
            }
            
            if (!$professorIds->isEmpty()) {
                $professors = Professor::whereIn('professor_id', $professorIds->unique())
                    ->where('professor_archived', false)
                    ->where(function($professorQuery) use ($query) {
                        $professorQuery->where('professor_first_name', 'like', "%{$query}%")
                            ->orWhere('professor_last_name', 'like', "%{$query}%")
                            ->orWhere('professor_name', 'like', "%{$query}%")
                            ->orWhere('professor_email', 'like', "%{$query}%");
                    })
                    ->with('programs')
                    ->limit(3)
                    ->get();

                foreach ($professors as $professor) {
                    $enrolledPrograms = $professor->programs->whereIn('program_id', $enrolledProgramIds)->pluck('program_name')->toArray();
                    
                    $professorName = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
                    if (empty($professorName)) {
                        $professorName = $professor->professor_name ?? '';
                    }
                        
                    $results[] = [
                        'id' => $professor->professor_id,
                        'type' => 'professor',
                        'name' => $professorName,
                        'email' => $professor->professor_email ?? '',
                        'role' => 'Professor',
                        'avatar' => $this->getUserAvatar((object)['email' => $professor->professor_email]),
                        'status' => 'Available',
                        'profile_url' => '#',
                        'programs' => $enrolledPrograms,
                        'description' => 'Teaching: ' . implode(', ', $enrolledPrograms)
                    ];
                }
            }
        }

        return array_slice($results, 0, $limit);
    }

    /**
     * Search ALL programs for students (not restricted to enrolled programs)
     */
    private function searchAllProgramsForStudent($query, $limit, $user)
    {
        // Debug logging
        Log::info("SearchController DEBUG: searchAllProgramsForStudent called", [
            'query' => $query,
            'limit' => $limit,
            'user_id' => $user ? $user->user_id : 'null',
            'user_role' => $user ? $user->role : 'null'
        ]);

        // Get student's enrolled programs to mark them properly
        $student = Student::where('user_id', $user->user_id)->first();
        $enrolledProgramIds = $student ? $student->enrollments->pluck('program_id')->unique() : collect();
        
        Log::info("SearchController DEBUG: Student info", [
            'student_found' => $student ? true : false,
            'student_id' => $student ? $student->student_id : 'null',
            'enrolled_program_ids' => $enrolledProgramIds->toArray()
        ]);

        // Search ALL active programs with modules and courses
        $programs = Program::where(function($programQuery) use ($query) {
                $programQuery->where('program_name', 'like', "%{$query}%")
                    ->orWhere('program_description', 'like', "%{$query}%")
                    ->orWhereHas('modules', function($moduleQuery) use ($query) {
                        $moduleQuery->where('module_name', 'like', "%{$query}%")
                            ->orWhere('module_description', 'like', "%{$query}%")
                            ->orWhereHas('courses', function($courseQuery) use ($query) {
                                $courseQuery->where('subject_name', 'like', "%{$query}%")
                                    ->orWhere('subject_description', 'like', "%{$query}%");
                            });
                    });
            })
            ->where('is_archived', false)
            ->where('is_active', true)
            ->with(['modules.courses', 'professors'])
            ->limit($limit)
            ->get();

        Log::info("SearchController DEBUG: Programs found", [
            'programs_count' => $programs->count(),
            'programs' => $programs->map(function($p) {
                return [
                    'id' => $p->program_id,
                    'name' => $p->program_name,
                    'is_active' => $p->is_active,
                    'is_archived' => $p->is_archived
                ];
            })->toArray()
        ]);

        $results = [];
        foreach ($programs as $program) {
            $isEnrolled = $enrolledProgramIds->contains($program->program_id);
            $coursesCount = $program->modules->sum(function($module) {
                return $module->courses->count();
            });
            
            $result = [
                'id' => $program->program_id,
                'type' => 'program',
                'name' => $program->program_name,
                'email' => null,
                'role' => $isEnrolled ? 'Enrolled Program' : 'Available Program',
                'avatar' => $this->getUserAvatar((object)['email' => 'program@artc.edu']),
                'status' => $isEnrolled ? 'Enrolled' : 'Available',
                'profile_url' => route('profile.program', $program->program_id),
                'modules_count' => $program->modules->count(),
                'courses_count' => $coursesCount,
                'professors' => $program->professors->map(function($prof) {
                    $name = trim(($prof->professor_first_name ?? '') . ' ' . ($prof->professor_last_name ?? ''));
                    return !empty($name) ? $name : ($prof->professor_name ?? '');
                })->filter()->toArray(),
                'description' => $program->program_description,
                'is_enrolled' => $isEnrolled
            ];
            
            $results[] = $result;
            Log::info("SearchController DEBUG: Added result", $result);
        }

        Log::info("SearchController DEBUG: Final results", [
            'results_count' => count($results),
            'results' => $results
        ]);

        return $results;
    }

    /**
     * Search ALL professors for students (not restricted to enrolled program professors)
     */
    private function searchAllProfessorsForStudent($query, $limit, $user)
    {
        // Get student's enrolled programs to show which professors teach their programs
        $student = Student::where('user_id', $user->user_id)->first();
        $enrolledProgramIds = $student ? $student->enrollments->pluck('program_id')->unique() : collect();

        // Search ALL professors in the professors table
        $professors = Professor::where('professor_archived', false)
            ->where(function($professorQuery) use ($query) {
                $professorQuery->where('professor_first_name', 'like', "%{$query}%")
                    ->orWhere('professor_last_name', 'like', "%{$query}%")
                    ->orWhere('professor_name', 'like', "%{$query}%")
                    ->orWhere('professor_email', 'like', "%{$query}%");
            })
            ->with('programs')
            ->limit($limit)
            ->get();

        $results = [];
        foreach ($professors as $professor) {
            $allPrograms = $professor->programs->pluck('program_name')->toArray();
            
            $enrolledPrograms = $professor->programs->whereIn('program_id', $enrolledProgramIds)->pluck('program_name')->toArray();
            
            $isMyProfessor = !empty($enrolledPrograms);
            
            $professorName = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
            if (empty($professorName)) {
                $professorName = $professor->professor_name ?? '';
            }
            
            $results[] = [
                'id' => $professor->professor_id,
                'type' => 'professor',
                'name' => $professorName,
                'email' => $professor->professor_email ?? '',
                'role' => $isMyProfessor ? 'Your Professor' : 'Professor',
                'avatar' => $this->getUserAvatar((object)['email' => $professor->professor_email]),
                'status' => 'Available', // Professors table doesn't have online status
                'profile_url' => '#',
                'programs' => $isMyProfessor ? $enrolledPrograms : $allPrograms,
                'description' => $isMyProfessor ? 
                    'Teaching your: ' . implode(', ', $enrolledPrograms) :
                    'Teaching: ' . implode(', ', $allPrograms)
            ];
        }

        return $results;
    }

    /**
     * Show user profile page
     */
    public function showUserProfile($id)
    {
        $user = User::with(['student.enrollments.program'])->findOrFail($id);
        
        $profile = [
            'id' => $user->user_id,
            'name' => $user->user_firstname . ' ' . $user->user_lastname,
            'email' => $user->email,
            'role' => ucfirst($user->role),
            'avatar' => $this->getUserAvatar($user),
            'status' => $user->is_online ? 'Online' : 'Offline',
            'last_seen' => $user->last_seen,
            'created_at' => $user->created_at
        ];

        // Add role-specific data
        if ($user->role === 'student' && $user->student) {
            $profile['enrollments'] = $user->student->enrollments->map(function($enrollment) {
                return [
                    'program_id' => $enrollment->program_id,
                    'program' => $enrollment->program ? $enrollment->program->program_name : 'Unknown',
                    'enrolled_at' => $enrollment->created_at,
                    'status' => 'Active'
                ];
            });
            $profile['student_id'] = $user->student->student_id;
        }

        if ($user->role === 'professor') {
            // For professors, we need to find the professor record by email matching
            $professor = Professor::where('professor_email', $user->email)->with(['programs.modules.courses'])->first();
            if ($professor) {
                $profile['programs'] = $professor->programs->map(function($program) {
                    return [
                        'program_id' => $program->program_id,
                        'program_name' => $program->program_name,
                        'program_description' => $program->program_description,
                        'modules_count' => $program->modules->count(),
                        'students_count' => $program->students->count()
                    ];
                });
                $profile['professor_id'] = $professor->professor_id;
            }
        }

        return view('profiles.user', compact('profile', 'user'));
    }

    /**
     * Show professor profile page
     */
    public function showProfessorProfile($id)
    {
        $professor = Professor::with(['programs.modules.courses', 'batches.students.user'])->findOrFail($id);
        
        $profile = [
            'id' => $professor->professor_id,
            'name' => $professor->professor_first_name . ' ' . $professor->professor_last_name,
            'email' => $professor->professor_email,
            'role' => 'Professor',
            'avatar' => $this->getUserAvatar((object)['email' => $professor->professor_email]),
            'status' => 'Available',
            'created_at' => $professor->created_at ?? now(),
            'professor_id' => $professor->professor_id
        ];

        // Add professor-specific data
        if ($professor->programs) {
            $profile['programs'] = $professor->programs->map(function($program) {
                return [
                    'program_id' => $program->program_id,
                    'program_name' => $program->program_name,
                    'program_description' => $program->program_description,
                    'modules_count' => $program->modules->count(),
                    'students_count' => $program->students->count()
                ];
            });
        }

        // Add batches data
        if ($professor->batches) {
            $profile['batches'] = $professor->batches->map(function($batch) {
                return [
                    'batch_id' => $batch->batch_id,
                    'batch_name' => $batch->batch_name,
                    'program_name' => $batch->program ? $batch->program->program_name : 'Unknown',
                    'students_count' => $batch->students->count()
                ];
            });
        }

        return view('profiles.professor', compact('profile', 'professor'));
    }

    /**
     * Show program profile page
     */
    public function showProgramProfile($id)
    {
        $program = Program::with(['modules.courses', 'professors', 'students.user'])->findOrFail($id);
        // Fetch user_ids for professors by matching emails (since no user_id in professors table)
        $professorEmails = $program->professors->pluck('professor_email')->all();
        $userMap = User::whereIn('email', $professorEmails)->pluck('user_id', 'email');
        $profileData = [
            'id' => $program->program_id,
            'name' => $program->program_name,
            'description' => $program->program_description,
            'is_active' => $program->is_active ?? true,
            'is_archived' => $program->is_archived,
            'created_at' => $program->created_at,
            'modules' => $program->modules->map(function($module) {
                return [
                    'module_id' => $module->modules_id,
                    'module_name' => $module->module_name,
                    'module_description' => $module->module_description,
                    'courses_count' => $module->courses->count(),
                    'courses' => $module->courses->map(function($course) {
                        return [
                            'course_id' => $course->subject_id,
                            'course_title' => $course->subject_name,
                            'course_description' => $course->subject_description
                        ];
                    })
                ];
            }),
            'professors' => $program->professors->map(function($professor) use ($userMap) {
                $name = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
                if (empty($name)) {
                    $name = $professor->professor_name ?? '';
                }
                $email = $professor->professor_email ?? '';
                return [
                    'professor_id' => $professor->professor_id,
                    'name' => $name,
                    'email' => $email,
                    'avatar' => $this->getUserAvatar((object)['email' => $email]),
                    'user_id' => $userMap[$email] ?? null,
                ];
            }),
            'students' => $program->students->map(function($student) {
                return [
                    'student_id' => $student->student_id,
                    'user_id' => $student->user_id,
                    'name' => ($student->user ? (($student->user->user_firstname ?? '') . ' ' . ($student->user->user_lastname ?? '')) : ''),
                    'email' => $student->user ? ($student->user->email ?? '') : '',
                    'avatar' => $student->user ? $this->getUserAvatar($student->user) : ''
                ];
            })
        ];

        return view('profiles.program', compact('profileData', 'program'));
    }

    /**
     * Admin search endpoint for the admin dashboard searchbar
     */
    public function adminSearch(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        $limit = $request->get('limit', 10);
        
        Log::info("AdminSearch DEBUG: Called with params", [
            'query' => $query,
            'type' => $type,
            'limit' => $limit
        ]);
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query too short',
                'results' => []
            ]);
        }

        $results = [];

        try {
            // Search students
            if ($type === 'all' || $type === 'students') {
                $students = DB::table('students')
                    ->leftJoin('users', 'students.user_id', '=', 'users.user_id')
                    ->where(function($q) use ($query) {
                        $q->where('students.firstname', 'like', "%{$query}%")
                          ->orWhere('students.lastname', 'like', "%{$query}%")
                          ->orWhere('students.student_id', 'like', "%{$query}%")
                          ->orWhere('users.email', 'like', "%{$query}%");
                    })
                    ->select('students.student_id as id', 'students.firstname', 'students.lastname', 'users.email', 'students.profile_photo')
                    ->limit(5)
                    ->get();
                    
                foreach ($students as $student) {
                    // Generate avatar
                    $avatar = null;
                    if ($student->profile_photo) {
                        // Check if profile_photo already contains the directory path
                        if (strpos($student->profile_photo, 'profile_photos/') === 0) {
                            $avatar = asset('storage/' . $student->profile_photo);
                        } else {
                            $avatar = asset('storage/profile-photos/' . $student->profile_photo);
                        }
                    } else {
                        $email = $student->email ?: 'default@example.com';
                        $hash = md5(strtolower(trim($email)));
                        $avatar = "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=120";
                    }
                    
                    $results[] = [
                        'id' => $student->id,
                        'title' => trim($student->firstname . ' ' . $student->lastname),
                        'subtitle' => $student->email ?: 'No email',
                        'type' => 'student',
                        'icon' => 'bi-person-fill',
                        'avatar' => $avatar,
                        'url' => route('admin.students.show', $student->id)
                    ];
                }
            }
            
            // Search professors
            if ($type === 'all' || $type === 'professors') {
                $professors = DB::table('professors')
                    ->where('professor_archived', false)
                    ->where(function($q) use ($query) {
                        $q->where('professor_first_name', 'like', "%{$query}%")
                          ->orWhere('professor_last_name', 'like', "%{$query}%")
                          ->orWhere('professor_name', 'like', "%{$query}%")
                          ->orWhere('professor_email', 'like', "%{$query}%");
                    })
                    ->select('professor_id as id', 'professor_first_name', 'professor_last_name', 'professor_name', 'professor_email', 'profile_photo')
                    ->limit(5)
                    ->get();
                    
                foreach ($professors as $professor) {
                    $name = trim(($professor->professor_first_name ?? '') . ' ' . ($professor->professor_last_name ?? ''));
                    if (empty($name)) {
                        $name = $professor->professor_name ?? 'Unknown Professor';
                    }
                    
                    // Generate avatar
                    $avatar = null;
                    if ($professor->profile_photo) {
                        // Check if profile_photo already contains the directory path
                        if (strpos($professor->profile_photo, 'profile_photos/') === 0) {
                            $avatar = asset('storage/' . $professor->profile_photo);
                        } else {
                            $avatar = asset('storage/profile-photos/' . $professor->profile_photo);
                        }
                    } else {
                        $email = $professor->professor_email ?: 'default@example.com';
                        $hash = md5(strtolower(trim($email)));
                        $avatar = "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=120";
                    }
                    
                    $results[] = [
                        'id' => $professor->id,
                        'title' => $name,
                        'subtitle' => $professor->professor_email ?? 'No email',
                        'type' => 'professor',
                        'icon' => 'bi-person-badge-fill',
                        'avatar' => $avatar,
                        'url' => route('profile.professor', $professor->id)
                    ];
                }
            }
            
            // Search programs
            if ($type === 'all' || $type === 'programs') {
                $programs = DB::table('programs')
                    ->where('program_name', 'like', "%{$query}%")
                    ->where('is_archived', false)
                    ->select('program_id as id', 'program_name', 'program_description')
                    ->limit(5)
                    ->get();
                    
                foreach ($programs as $program) {
                    $results[] = [
                        'id' => $program->id,
                        'title' => $program->program_name,
                        'subtitle' => $program->program_description ?: 'No description',
                        'type' => 'program',
                        'icon' => 'bi-collection-fill',
                        'url' => route('profile.program', $program->id)
                    ];
                }
            }

            Log::info("AdminSearch DEBUG: Found results", [
                'count' => count($results),
                'results' => $results
            ]);

            return response()->json([
                'success' => true,
                'results' => $results,
                'total' => count($results)
            ]);

        } catch (\Exception $e) {
            Log::error("AdminSearch DEBUG: Exception occurred", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage(),
                'results' => []
            ], 500);
        }
    }
}