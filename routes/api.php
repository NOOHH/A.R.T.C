<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CompletionController;

/*
|--------------------------------------------------------------------------
| Chat API Routes
|--------------------------------------------------------------------------
|
| Chat API routes with different authentication mechanisms
|
*/

// Chat API routes (auth:sanctum) - currently disabled for testing
/*
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/chat/programs', [App\Http\Controllers\Api\ProgramApiController::class, 'index']);
    Route::get('/chat/batches', [App\Http\Controllers\Api\ProgramApiController::class, 'batches']);
    Route::get('/chat/users', [App\Http\Controllers\ChatController::class, 'getSessionUsers']);
    Route::get('/chat/messages', [App\Http\Controllers\ChatController::class, 'getSessionMessages']);
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendSessionMessage']);
});
*/

// Session-based chat API routes (temporarily removing auth for testing)
Route::middleware(['web'])->group(function () {
    Route::get('/chat/session/programs', [App\Http\Controllers\Api\ProgramApiController::class, 'index']);
    Route::get('/chat/session/batches', [App\Http\Controllers\Api\ProgramApiController::class, 'batches']);
    Route::get('/chat/session/users', [App\Http\Controllers\ChatController::class, 'getSessionUsers']);
    Route::get('/chat/session/messages', [App\Http\Controllers\ChatController::class, 'getSessionMessages']);
    Route::post('/chat/session/send', [App\Http\Controllers\ChatController::class, 'sendSessionMessage']);
    Route::post('/chat/session/clear-history', [App\Http\Controllers\ChatController::class, 'clearSessionHistory']);
    
    // Professor chat routes
    Route::post('/chat/send', [App\Http\Controllers\Professor\ChatController::class, 'sendMessage']);
    Route::get('/chat/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount']);
});

// Chat search route (temporarily removing auth for testing)
Route::post('/chat/session/search', [App\Http\Controllers\ChatController::class, 'sessionSearch'])->middleware(['web']);

// Test route to verify routing is working
Route::get('/test-route', function() {
    return response()->json(['message' => 'Test route works']);
});

// Session debug route
Route::get('/debug-session', function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return response()->json([
        'session_id' => session_id(),
        'laravel_session' => session()->all(),
        'php_session' => $_SESSION ?? [],
        'current_user' => [
            'user_id' => session('user_id') ?? $_SESSION['user_id'] ?? null,
            'student_id' => session('student_id') ?? $_SESSION['student_id'] ?? null,
            'professor_id' => session('professor_id') ?? $_SESSION['professor_id'] ?? null,
            'admin_id' => session('admin_id') ?? $_SESSION['admin_id'] ?? null,
            'role' => session('user_role') ?? session('role') ?? $_SESSION['user_type'] ?? $_SESSION['role'] ?? null,
        ]
    ]);
})->middleware('web');

// Current user info endpoint (for chat authentication)
Route::get('/me', function () {
    // Check Laravel Auth first (for professors)
    $authUser = Auth::user();
    if ($authUser) {
        if ($authUser instanceof \App\Models\Professor) {
            return response()->json([
                'id' => $authUser->professor_id,
                'role' => 'professor',
                'name' => trim(($authUser->professor_first_name ?? '') . ' ' . ($authUser->professor_last_name ?? '')) ?: ($authUser->professor_name ?? 'Unknown Professor'),
                'email' => $authUser->professor_email ?? 'No email',
                'isAuthenticated' => true,
                'auth_type' => 'laravel'
            ]);
        } elseif (isset($authUser->role)) {
            return response()->json([
                'id' => $authUser->id ?? $authUser->user_id,
                'role' => $authUser->role,
                'name' => $authUser->name ?? ($authUser->user_firstname . ' ' . $authUser->user_lastname) ?? 'Unknown User',
                'email' => $authUser->email ?? 'No email',
                'isAuthenticated' => true,
                'auth_type' => 'laravel'
            ]);
        }
    }
    
    // Fallback to session-based authentication
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $userId = session('user_id') ?? $_SESSION['user_id'] ?? null;
    $userRole = session('user_role') ?? session('role') ?? $_SESSION['user_type'] ?? $_SESSION['role'] ?? null;
    $userName = session('user_name') ?? session('name') ?? $_SESSION['user_name'] ?? $_SESSION['name'] ?? 'Unknown User';
    $userEmail = session('user_email') ?? session('email') ?? $_SESSION['user_email'] ?? $_SESSION['email'] ?? 'No email';
    
    // Check for specific role-based IDs
    if (!$userId) {
        if (session('student_id') || (isset($_SESSION['student_id']) && $_SESSION['student_id'])) {
            $userId = session('student_id') ?? $_SESSION['student_id'];
            $userRole = 'student';
            $userName = session('student_name') ?? $_SESSION['student_name'] ?? $userName;
        } elseif (session('admin_id') || (isset($_SESSION['admin_id']) && $_SESSION['admin_id'])) {
            $userId = session('admin_id') ?? $_SESSION['admin_id'];
            $userRole = 'admin';
            $userName = session('admin_name') ?? $_SESSION['admin_name'] ?? $userName;
        } elseif (session('director_id') || (isset($_SESSION['director_id']) && $_SESSION['director_id'])) {
            $userId = session('director_id') ?? $_SESSION['director_id'];
            $userRole = 'director';
            $userName = session('director_name') ?? $_SESSION['director_name'] ?? $userName;
        }
    }
    
    if ($userId && $userRole) {
        return response()->json([
            'id' => $userId,
            'role' => $userRole,
            'name' => $userName,
            'email' => $userEmail,
            'isAuthenticated' => true,
            'auth_type' => 'session'
        ]);
    }
    
    return response()->json([
        'id' => null,
        'role' => 'guest',
        'name' => 'Guest',
        'email' => 'No email',
        'isAuthenticated' => false,
        'auth_type' => 'none',
        'debug' => [
            'session_user_id' => session('user_id'),
            'session_role' => session('role'),
            'php_session_user_id' => $_SESSION['user_id'] ?? null,
            'php_session_user_type' => $_SESSION['user_type'] ?? null
        ]
    ], 401);
})->middleware('web');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['web'])->get('/user-info', function () {
    $user = Auth::user();
    return response()->json([
        'id' => $user?->id,
        'name' => $user?->name,
        'role' => $user?->role,
        'isAuthenticated' => !!$user,
    ]);
});

// Student progress tracking routes
// (leave these in web middleware if needed for session-based progress)
Route::middleware(['web'])->group(function () {
    Route::post('/student/module-progress', function (Request $request) {
        try {
            // Basic validation
            $data = $request->validate([
                'moduleId' => 'required|integer',
                'completed' => 'required|array',
                'totalItems' => 'required|integer',
                'completedCount' => 'required|integer',
                'percentage' => 'required|integer|min:0|max:100'
            ]);
            
            // Here you could save to database if needed
            // For now, just return success
            return response()->json([
                'success' => true,
                'message' => 'Progress saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save progress'
            ], 500);
        }
    });
    
    Route::post('/student/complete-module', [App\Http\Controllers\CompletionController::class, 'markModuleComplete']);
});

// These should NOT be in a web middleware group:
// Route::post('/student/complete-course', [App\Http\Controllers\CompletionController::class, 'markCourseComplete']);
// Route::post('/student/complete-content', [App\Http\Controllers\CompletionController::class, 'markContentComplete']);

// Ensure session is available for completion routes
Route::middleware(['web'])->group(function () {
    Route::post('/student/complete-course', [App\Http\Controllers\CompletionController::class, 'markCourseComplete']);
    Route::post('/student/complete-content', [App\Http\Controllers\CompletionController::class, 'markContentComplete']);
});

// Programs list API route for navbar dropdown
Route::get('/programs', function () {
    $programs = \App\Models\Program::where('is_archived', false)
        ->select('program_id', 'program_name', 'program_description')
        ->orderBy('program_name')
        ->get();
    return response()->json(['success' => true, 'data' => $programs]);
});

// Program details API route
Route::get('/programs/{id}', function ($id) {
    $program = \App\Models\Program::find($id);
    if (!$program || $program->is_archived) {
        return response()->json(['error' => 'Program not found'], 404);
    }
    return response()->json($program);
});

// Program modules API route
Route::get('/programs/{id}/modules', function ($id) {
    $modules = \App\Models\Module::where('program_id', $id)
        ->where('is_archived', false)
        ->orderBy('module_order', 'asc')
        ->get(['modules_id', 'module_name']);
    
    // wrap it:
    return response()->json([
        'success' => true,
        'modules' => $modules
    ]);
});


// Admin search API route
Route::post('/admin/search', function (Request $request) {
    $query = $request->input('query');
    $type = $request->input('type', 'all');
    
    if (empty($query)) {
        return response()->json(['results' => [], 'suggestions' => []]);
    }
    
    $results = [];
    $suggestions = [];
    
    // Search students
    if ($type === 'all' || $type === 'students') {
        $students = \Illuminate\Support\Facades\DB::table('students')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->where(function($q) use ($query) {
                $q->where('students.firstname', 'like', "%{$query}%")
                  ->orWhere('students.lastname', 'like', "%{$query}%")
                  ->orWhere('students.student_id', 'like', "%{$query}%")
                  ->orWhere('users.email', 'like', "%{$query}%");
            })
            ->select('students.student_id as id', 'students.firstname', 'students.lastname', 'users.email')
            ->limit(5)
            ->get();
            
        foreach ($students as $student) {
            $results[] = [
                'id' => $student->id,
                'name' => $student->firstname . ' ' . $student->lastname,
                'subtitle' => $student->email ?: 'No email',
                'type' => 'student'
            ];
        }
    }
    
    // Search professors
    if ($type === 'all' || $type === 'professors') {
        $professors = \Illuminate\Support\Facades\DB::table('professors')
            ->where(function($q) use ($query) {
                $q->where('professors.professors_first_name', 'like', "%{$query}%")
                  ->orWhere('professors.professors_last_name', 'like', "%{$query}%")
                  ->orWhere('professors.professors_email', 'like', "%{$query}%");
            })
            ->select('professors.professors_id as id', 'professors.professors_first_name', 'professors.professors_last_name', 'professors.professors_email')
            ->limit(5)
            ->get();
            
        foreach ($professors as $professor) {
            $results[] = [
                'id' => $professor->id,
                'name' => $professor->professors_first_name . ' ' . $professor->professors_last_name,
                'subtitle' => $professor->professors_email,
                'type' => 'professor'
            ];
        }
    }
    
    // Search programs
    if ($type === 'all' || $type === 'programs') {
        $programs = \Illuminate\Support\Facades\DB::table('programs')
            ->where('program_name', 'like', "%{$query}%")
            ->where('is_archived', false)
            ->select('program_id as id', 'program_name', 'program_description')
            ->limit(5)
            ->get();
            
        foreach ($programs as $program) {
            $results[] = [
                'id' => $program->id,
                'name' => $program->program_name,
                'subtitle' => $program->program_description ?: 'No description',
                'type' => 'program'
            ];
        }
    }
    
    // Generate suggestions based on query
    $suggestions = [
        'students with "' . $query . '"',
        'professors with "' . $query . '"',
        'programs with "' . $query . '"'
    ];
    
    return response()->json([
        'results' => $results,
        'suggestions' => array_slice($suggestions, 0, 3)
    ]);
});

// Referral API routes
Route::middleware('web')->group(function () {
    Route::post('/validate-referral-code', [App\Http\Controllers\Api\ReferralController::class, 'validateReferralCode']);
    Route::get('/referral/analytics', [App\Http\Controllers\Api\ReferralController::class, 'getReferralAnalytics']);
    Route::get('/referral/stats/{type}/{id}', [App\Http\Controllers\Api\ReferralController::class, 'getReferrerStats']);
    
    // Test endpoint for referral settings
    Route::get('/test-referral-settings', function () {
        $enabled = DB::table('admin_settings')->where('setting_key', 'referral_enabled')->value('setting_value') ?? '0';
        $required = DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value') ?? '0';
        
        return response()->json([
            'referral_enabled' => $enabled,
            'referral_required' => $required
        ]);
    });
});

// Remove old deprecated routes
Route::middleware('web')->group(function () {
    // Keep existing deprecated routes for backward compatibility
    Route::get('/api/student/enrolled-programs', function () {
        return response()->json(['programs' => []]);
    });
    
    Route::get('/api/professor/assigned-programs', function () {
        return response()->json(['programs' => []]);
    });
});

// Modular Enrollment Wizard API Routes
Route::middleware('web')->group(function () {
    // Get packages for step 1
    Route::get('/packages', function () {
        try {
            $packages = \App\Models\Package::where('package_type', 'modular')
                ->with('program')
                ->select('package_id', 'package_name', 'description', 'price', 'amount', 'program_id', 'allowed_modules', 'extra_module_price')
                ->get()
                ->map(function ($package) {
                    return [
                        'package_id' => $package->package_id,
                        'package_name' => $package->package_name,
                        'description' => $package->description,
                        'price' => $package->price ?? $package->amount,
                        'allowed_modules' => $package->allowed_modules ?? 2,
                        'extra_module_price' => $package->extra_module_price ?? 1500,
                        'program_id' => $package->program_id
                    ];
                });
            
            return response()->json(['success' => true, 'packages' => $packages]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });

    // Check email availability for step 5
    Route::get('/check-email', function (Request $request) {
        try {
            $email = $request->get('email');
            
            if (!$email) {
                return response()->json(['available' => false, 'message' => 'Email is required']);
            }

            // Check across all user tables
            $emailExists = \App\Models\User::where('email', $email)->exists() ||
                          \App\Models\Professor::where('professor_email', $email)->exists() ||
                          \App\Models\Admin::where('email', $email)->exists() ||
                          \App\Models\Director::where('directors_email', $email)->exists();

            return response()->json(['available' => !$emailExists]);
        } catch (\Exception $e) {
            return response()->json(['available' => true, 'message' => 'Error checking email: ' . $e->getMessage()]);
        }
    });

    // Get form requirements for step 6 (dynamic forms based on admin settings)
    Route::get('/form-requirements', function (Request $request) {
        try {
            $type = $request->get('type', 'modular');
            
            $requirements = \App\Models\FormRequirement::where('is_active', true)
                ->where(function($query) use ($type) {
                    $query->where('program_type', $type)
                          ->orWhere('program_type', 'both');
                })
                ->orderBy('sort_order')
                ->get();

            return response()->json(['success' => true, 'requirements' => $requirements]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });
    
    // Get available programs for modular enrollment (with filtering)
    Route::get('/enrollment/available-programs', function (Request $request) {
        try {
            // Get current session student info
            $student = session('student');
            $studentId = $student['id'] ?? null;
            
            // Start with base query for programs with modular packages
            $query = \App\Models\Program::where('is_archived', false)
                ->whereHas('packages', function($q) {
                    $q->where('package_type', 'modular');
                })
                ->with(['modules' => function($q) {
                    $q->where('is_archived', false)
                      ->orderBy('module_order', 'asc')
                      ->select('modules_id', 'module_name', 'module_description', 'program_id');
                }, 'packages' => function($q) {
                    $q->where('package_type', 'modular');
                }]);
            
            // If we have a student, filter out programs they're already enrolled in
            if ($studentId) {
                // Exclude programs with full-plan enrollments
                $query->whereNotExists(function($subQuery) use ($studentId) {
                    $subQuery->select(DB::raw(1))
                        ->from('student_enrollments')
                        ->whereColumn('student_enrollments.program_id', 'programs.program_id')
                        ->where('student_enrollments.student_id', $studentId)
                        ->where('student_enrollments.plan_id', 1); // Full plan
                });
                
                // Exclude programs with existing modular enrollments
                $query->whereNotExists(function($subQuery) use ($studentId) {
                    $subQuery->select(DB::raw(1))
                        ->from('student_enrollments')
                        ->whereColumn('student_enrollments.program_id', 'programs.program_id')
                        ->where('student_enrollments.student_id', $studentId)
                        ->where('student_enrollments.plan_id', 2); // Modular plan
                });
            }
            
            $programs = $query->select('program_id', 'program_name', 'program_description')
                ->orderBy('program_name')
                ->get();
            
            // Filter out programs with no modules
            $programs = $programs->filter(function($program) {
                return $program->modules && $program->modules->count() > 0;
            });
            
            return response()->json([
                'success' => true, 
                'programs' => $programs->values()->toArray()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching available programs for modular enrollment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    });
});

// Admin Package Management API Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/packages', [App\Http\Controllers\AdminPackageController::class, 'getPackages']);
    Route::post('/packages', [App\Http\Controllers\AdminPackageController::class, 'store']);
    Route::get('/packages/{id}', [App\Http\Controllers\AdminPackageController::class, 'show']);
    Route::put('/packages/{id}', [App\Http\Controllers\AdminPackageController::class, 'update']);
    Route::delete('/packages/{id}', [App\Http\Controllers\AdminPackageController::class, 'destroy']);
    
    Route::get('/programs', [App\Http\Controllers\AdminPackageController::class, 'getPrograms']);
    Route::get('/courses', [App\Http\Controllers\AdminPackageController::class, 'getCourses']);
    Route::get('/modules', [App\Http\Controllers\AdminPackageController::class, 'getModules']);
    
    // Package-specific relationships
    Route::post('/packages/{id}/courses', [App\Http\Controllers\AdminPackageController::class, 'attachCourses']);
    Route::post('/packages/{id}/modules', [App\Http\Controllers\AdminPackageController::class, 'attachModules']);
    
    // Testing endpoints
    Route::post('/packages/test-relationships', [App\Http\Controllers\AdminPackageController::class, 'testRelationships']);
    Route::post('/packages/test-pivot-tables', [App\Http\Controllers\AdminPackageController::class, 'testPivotTables']);
});

Route::get('/debug-session', function () {
    Log::info('DEBUG: /debug-session', [
        'user_id' => session('user_id'),
        'all_session' => session()->all()
    ]);
    return response()->json(['session' => session()->all()]);
});
