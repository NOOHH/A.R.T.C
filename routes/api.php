<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|-----------------------------------------------------------// Chat API routes (auth:sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/chat/programs', [App\Http\Controllers\Api\ProgramApiController::class, 'index']);
    Route::get('/chat/batches', [App\Http\Controllers\Api\ProgramApiController::class, 'batches']);
    Route::get('/chat/users', [App\Http\Controllers\ChatController::class, 'getSessionUsers']);
    Route::get('/chat/messages', [App\Http\Controllers\ChatController::class, 'getSessionMessages']);
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendSessionMessage']);
});ion-based chat API routes (alternative for session auth)
Route::middleware(['web'])->group(function () {
    Route::get('/chat/session/programs', [App\Http\Controllers\Api\ProgramApiController::class, 'index']);
    Route::get('/chat/session/batches', [App\Http\Controllers\Api\ProgramApiController::class, 'batches']);
    Route::get('/chat/session/users', [App\Http\Controllers\ChatController::class, 'getSessionUsers']);
    Route::get('/chat/session/messages', [App\Http\Controllers\ChatController::class, 'getSessionMessages']);
    Route::post('/chat/session/send', [App\Http\Controllers\ChatController::class, 'sendSessionMessage']);
    Route::post('/chat/session/clear-history', [App\Http\Controllers\ChatController::class, 'clearSessionHistory']);
});-----
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
        ->get();
    return response()->json($modules);
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

// Chat API routes (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chat/programs', [App\Http\Controllers\Api\ProgramApiController::class, 'index']);
    Route::get('/chat/batches', [App\Http\Controllers\Api\ProgramApiController::class, 'batches']);
    Route::get('/chat/users', [App\Http\Controllers\Api\ChatApiController::class, 'users']);
    Route::get('/chat/messages', [App\Http\Controllers\Api\ChatApiController::class, 'messages']);
    Route::post('/chat/send', [App\Http\Controllers\Api\ChatApiController::class, 'send']);
});

// Session-based chat API routes (with proper session auth)
Route::middleware(['web', App\Http\Middleware\SessionAuth::class])->group(function () {
    // User info endpoint
    Route::get('/me', [App\Http\Controllers\Api\UserController::class, 'me']);
    
    // Chat endpoints
    Route::get('/chat/session/programs', [App\Http\Controllers\Api\ProgramApiController::class, 'index']);
    Route::get('/chat/session/batches', [App\Http\Controllers\Api\ProgramApiController::class, 'batches']);
    Route::get('/chat/session/users', [App\Http\Controllers\Api\ChatApiController::class, 'users']);
    Route::get('/chat/session/messages', [App\Http\Controllers\Api\ChatApiController::class, 'messages']);
    Route::post('/chat/session/send', [App\Http\Controllers\Api\ChatApiController::class, 'send']);
    Route::get('/chat/session/recent', [App\Http\Controllers\Api\ChatApiController::class, 'recent']);
    
    // User search
    Route::get('/users/search', [App\Http\Controllers\Api\UserController::class, 'search']);
    
    // Professor specific routes
    Route::get('/professor/assigned-programs', [App\Http\Controllers\Api\ProfessorProgramController::class, 'index']);
    
    // Student specific routes
    Route::get('/student/enrolled-programs', function () {
        $studentId = session('user_id');
        if (!$studentId) {
            return response()->json(['programs' => []]);
        }
        
        // Get enrolled programs for student (simplified)
        $programs = \Illuminate\Support\Facades\DB::table('programs')
            ->where('is_archived', false)
            ->select('program_id as id', 'program_name')
            ->get();
            
        return response()->json(['programs' => $programs]);
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
