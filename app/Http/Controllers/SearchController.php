<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Enhanced search functionality with role-based access
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('type', 'all'); // all, students, professors
        $limit = $request->input('limit', 10);
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }
        
        $currentUser = auth()->user();
        $results = [];
        
        // Role-based search logic
        switch ($currentUser->role) {
            case 'admin':
            case 'director':
                // Admin and director can search both students and professors
                $results = $this->searchAllUsers($query, $type, $limit);
                break;
                
            case 'professor':
                // Professors can search students only
                $results = $this->searchStudents($query, $limit);
                break;
                
            case 'student':
                // Students can search professors only
                $results = $this->searchProfessors($query, $limit);
                break;
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($results)
        ]);
    }
    
    /**
     * Search all users (for admin/director)
     */
    private function searchAllUsers($query, $type, $limit)
    {
        $queryBuilder = User::query();
        
        // Filter by type if specified
        if ($type === 'students') {
            $queryBuilder->where('role', 'student');
        } elseif ($type === 'professors') {
            $queryBuilder->where('role', 'professor');
        } else {
            // Search both students and professors
            $queryBuilder->whereIn('role', ['student', 'professor']);
        }
        
        // Search by name, email, or ID
        $queryBuilder->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%")
              ->orWhere('id', 'LIKE', "%{$query}%");
        });
        
        $users = $queryBuilder->limit($limit)->get();
        
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar ?? asset('images/default-avatar.png'),
                'profile_url' => $this->getProfileUrl($user),
                'status' => $user->status ?? 'active'
            ];
        });
    }
    
    /**
     * Search students only (for professors)
     */
    private function searchStudents($query, $limit)
    {
        $users = User::where('role', 'student')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('id', 'LIKE', "%{$query}%");
            })
            ->limit($limit)
            ->get();
        
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar ?? asset('images/default-avatar.png'),
                'profile_url' => $this->getProfileUrl($user),
                'status' => $user->status ?? 'active',
                'enrollment_info' => $this->getStudentEnrollmentInfo($user->id)
            ];
        });
    }
    
    /**
     * Search professors only (for students)
     */
    private function searchProfessors($query, $limit)
    {
        $users = User::where('role', 'professor')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('id', 'LIKE', "%{$query}%");
            })
            ->limit($limit)
            ->get();
        
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar ?? asset('images/default-avatar.png'),
                'profile_url' => $this->getProfileUrl($user),
                'status' => $user->status ?? 'active',
                'specialization' => $user->specialization ?? 'General',
                'programs' => $this->getProfessorPrograms($user->id)
            ];
        });
    }
    
    /**
     * Get profile URL based on user role
     */
    private function getProfileUrl($user)
    {
        switch ($user->role) {
            case 'student':
                return route('student.profile', $user->id);
            case 'professor':
                return route('professor.profile', $user->id);
            default:
                return '#';
        }
    }
    
    /**
     * Get student enrollment information
     */
    private function getStudentEnrollmentInfo($studentId)
    {
        try {
            $enrollments = DB::table('enrollments')
                ->join('batches', 'enrollments.batch_id', '=', 'batches.id')
                ->join('programs', 'batches.program_id', '=', 'programs.id')
                ->where('enrollments.student_id', $studentId)
                ->select('programs.name as program_name', 'batches.name as batch_name', 'enrollments.status')
                ->get();
            
            return $enrollments->map(function ($enrollment) {
                return [
                    'program' => $enrollment->program_name,
                    'batch' => $enrollment->batch_name,
                    'status' => $enrollment->status
                ];
            });
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get professor programs
     */
    private function getProfessorPrograms($professorId)
    {
        try {
            $programs = DB::table('professor_programs')
                ->join('programs', 'professor_programs.program_id', '=', 'programs.id')
                ->where('professor_programs.professor_id', $professorId)
                ->select('programs.name', 'programs.description')
                ->get();
            
            return $programs->map(function ($program) {
                return [
                    'name' => $program->name,
                    'description' => $program->description
                ];
            });
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Advanced search with filters
     */
    public function advancedSearch(Request $request)
    {
        $query = $request->input('query');
        $role = $request->input('role'); // student, professor
        $status = $request->input('status'); // active, inactive
        $program = $request->input('program'); // program ID
        $batch = $request->input('batch'); // batch ID
        $limit = $request->input('limit', 20);
        
        $currentUser = auth()->user();
        
        // Check permissions
        if (!in_array($currentUser->role, ['admin', 'director', 'professor'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $queryBuilder = User::query();
        
        // Role-based access control
        if ($currentUser->role === 'professor') {
            $queryBuilder->where('role', 'student');
        } elseif (in_array($currentUser->role, ['admin', 'director'])) {
            if ($role) {
                $queryBuilder->where('role', $role);
            } else {
                $queryBuilder->whereIn('role', ['student', 'professor']);
            }
        }
        
        // Search filters
        if ($query) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('id', 'LIKE', "%{$query}%");
            });
        }
        
        if ($status) {
            $queryBuilder->where('status', $status);
        }
        
        // Program and batch filters (for students)
        if ($program || $batch) {
            $queryBuilder->whereExists(function ($q) use ($program, $batch) {
                $q->select(DB::raw(1))
                  ->from('enrollments')
                  ->whereRaw('enrollments.student_id = users.id');
                
                if ($program) {
                    $q->whereExists(function ($bq) use ($program) {
                        $bq->select(DB::raw(1))
                           ->from('batches')
                           ->whereRaw('batches.id = enrollments.batch_id')
                           ->where('batches.program_id', $program);
                    });
                }
                
                if ($batch) {
                    $q->where('enrollments.batch_id', $batch);
                }
            });
        }
        
        $users = $queryBuilder->limit($limit)->get();
        
        $results = $users->map(function ($user) {
            $result = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar ?? asset('images/default-avatar.png'),
                'profile_url' => $this->getProfileUrl($user),
                'status' => $user->status ?? 'active'
            ];
            
            // Add role-specific information
            if ($user->role === 'student') {
                $result['enrollment_info'] = $this->getStudentEnrollmentInfo($user->id);
            } elseif ($user->role === 'professor') {
                $result['specialization'] = $user->specialization ?? 'General';
                $result['programs'] = $this->getProfessorPrograms($user->id);
            }
            
            return $result;
        });
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($results)
        ]);
    }
    
    /**
     * Get search suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('query');
        $currentUser = auth()->user();
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'suggestions' => []
            ]);
        }
        
        $suggestions = [];
        
        // Role-based suggestions
        switch ($currentUser->role) {
            case 'admin':
            case 'director':
                $suggestions = $this->getAdminSuggestions($query);
                break;
                
            case 'professor':
                $suggestions = $this->getProfessorSuggestions($query);
                break;
                
            case 'student':
                $suggestions = $this->getStudentSuggestions($query);
                break;
        }
        
        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }
    
    /**
     * Get admin suggestions
     */
    private function getAdminSuggestions($query)
    {
        $users = User::whereIn('role', ['student', 'professor'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'email', 'role']);
        
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'label' => $user->name . ' (' . $user->email . ')'
            ];
        });
    }
    
    /**
     * Get professor suggestions
     */
    private function getProfessorSuggestions($query)
    {
        $students = User::where('role', 'student')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'email', 'role']);
        
        return $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'role' => $student->role,
                'label' => $student->name . ' (' . $student->email . ')'
            ];
        });
    }
    
    /**
     * Get student suggestions
     */
    private function getStudentSuggestions($query)
    {
        $professors = User::where('role', 'professor')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'email', 'role']);
        
        return $professors->map(function ($professor) {
            return [
                'id' => $professor->id,
                'name' => $professor->name,
                'email' => $professor->email,
                'role' => $professor->role,
                'label' => $professor->name . ' (' . $professor->email . ')'
            ];
        });
    }
}
