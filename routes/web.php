<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

// Broadcasting Authentication (for Laravel Echo + Pusher)
Broadcast::routes(['middleware' => ['web']]);

// Admin: Delete a meeting for a professor
Route::delete('/admin/professors/{professor}/meetings/{meeting}', [App\Http\Controllers\AdminProfessorController::class, 'deleteMeeting'])->name('admin.professors.deleteMeeting');
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UnifiedLoginController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\AdminProgramController;
use App\Http\Controllers\AdminModuleController;
use App\Http\Controllers\AdminCourseController;
use App\Http\Controllers\AdminDirectorController;
use App\Http\Controllers\AdminStudentListController;
use App\Http\Controllers\AdminPackageController;    // ← NEW
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\CertificateController;

// Chat search route  
Route::post('/api/chat/session/search', [App\Http\Controllers\ChatController::class, 'sessionSearch'])->middleware('web')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
use App\Http\Controllers\Admin\EducationLevelController;
use App\Http\Controllers\AdminProfessorController;
use App\Http\Controllers\AdminBatchController;
use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\BoardPassersController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfessorDashboardController;
use App\Http\Controllers\ProfessorMeetingController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\FormRequirementController;
use App\Models\Program;
use App\Http\Controllers\TestController;
use App\Models\Package;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\Admin\BatchEnrollmentController;
use App\Http\Controllers\ModularRegistrationController;
use App\Http\Controllers\EmergencyModularController;
use App\Http\Controllers\EnrollmentDebugController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StudentModuleController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\Professor\AnnouncementController as ProfessorAnnouncementController;

// routes/web.php

// Immediate search endpoint - placed at top to avoid middleware conflicts
Route::get('/search-now', function(\Illuminate\Http\Request $request) {
    try {
        $query = $request->get('query', '');
        $type = $request->get('type', 'all');
        $limit = (int) $request->get('limit', 10);
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query too short',
                'results' => []
            ]);
        }
        
        $results = [];
        
        // Direct database queries
        if ($type === 'all' || $type === 'students') {
            $students = \Illuminate\Support\Facades\DB::table('students')
                ->select('student_id as id', 'firstname', 'lastname', 'email')
                ->where(function($q) use ($query) {
                    $q->where('firstname', 'LIKE', "%{$query}%")
                      ->orWhere('lastname', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->limit($limit)
                ->get();
                
            foreach ($students as $student) {
                $results[] = [
                    'type' => 'student',
                    'id' => $student->id,
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'email' => $student->email,
                    'url' => '/professor/view/student/' . $student->id
                ];
            }
        }
        
        if ($type === 'all' || $type === 'professors') {
            $professors = \Illuminate\Support\Facades\DB::table('professors')
                ->select('professor_id as id', 'professor_first_name', 'professor_last_name', 'professor_email')
                ->where(function($q) use ($query) {
                    $q->where('professor_first_name', 'LIKE', "%{$query}%")
                      ->orWhere('professor_last_name', 'LIKE', "%{$query}%")
                      ->orWhere('professor_email', 'LIKE', "%{$query}%");
                })
                ->limit($limit)
                ->get();
                
            foreach ($professors as $professor) {
                $results[] = [
                    'type' => 'professor',
                    'id' => $professor->id,
                    'name' => $professor->professor_first_name . ' ' . $professor->professor_last_name,
                    'email' => $professor->professor_email,
                    'url' => '/professor/view/professor/' . $professor->id
                ];
            }
        }
        
        if ($type === 'all' || $type === 'programs') {
            $programs = \Illuminate\Support\Facades\DB::table('programs')
                ->select('program_id as id', 'program_name', 'program_description')
                ->where('program_name', 'LIKE', "%{$query}%")
                ->limit($limit)
                ->get();
                
            foreach ($programs as $program) {
                $results[] = [
                    'type' => 'program',
                    'id' => $program->id,
                    'name' => $program->program_name,
                    'description' => $program->program_description,
                    'url' => '/professor/view/program/' . $program->id
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($results),
            'message' => count($results) > 0 ? 'Search completed successfully' : 'No results found'
        ]);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Search error: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Search error: ' . $e->getMessage(), 
            'results' => []
        ]);
    }
});

use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\StudentPaymentModalController;

Route::get(
    '/student/module/{module}/course/{course}/content-items',
    [StudentModuleController::class, 'getCourseContent']
)->name('student.module.course.content-items');

Route::middleware(['web','check.session','role.dashboard']) // whatever guards you need
     ->prefix('api')
     ->group(function(){
         Route::get('referral/analytics', [ReferralController::class,'getReferralAnalytics']);
         Route::post('validate-referral-code', [ReferralController::class,'validateReferralCode']);
     });

/*
|--------------------------------------------------------------------------
| Test DB Connection
|--------------------------------------------------------------------------
*/
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Connected to DB successfully!";
    } catch (\Exception $e) {
        return "❌ DB connection failed: " . $e->getMessage();
    }
});

// Test authentication debug route
Route::get('/test-auth-debug', function () {
    try {
        // Check users table
        $users = DB::table('users')->select('user_id', 'user_firstname', 'user_lastname', 'email', 'role')->take(5)->get();
        
        // Check admins table
        $admins = DB::table('admins')->select('admin_id', 'admin_name', 'email')->take(5)->get();
        
        // Check students table
        $students = DB::table('students')->select('student_id', 'user_id', 'firstname', 'lastname', 'email')->take(5)->get();
        
        return response()->json([
            'users' => $users,
            'admins' => $admins,
            'students' => $students,
            'session_data' => [
                'user_id' => session('user_id'),
                'user_name' => session('user_name'),
                'user_role' => session('user_role'),
                'logged_in' => session('logged_in')
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


// Chat debug route
Route::get('/chat-debug', function () {
    return view('chat-debug');
});

// Quick test to create sample programs
Route::get('/seed-programs', [TestController::class, 'seedPrograms']);

// Test database structure
Route::get('/test-db-structure', [TestController::class, 'testDatabaseConnection']);

/*
|--------------------------------------------------------------------------
| Batch Enrollment Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin/batches')->middleware(['admin.director.auth'])->group(function () {
    Route::get('/', [BatchEnrollmentController::class, 'index'])->name('admin.batches.index');
    Route::get('/create', [BatchEnrollmentController::class, 'create'])->name('admin.batches.create');
    Route::post('/', [BatchEnrollmentController::class, 'store'])->name('admin.batches.store');
    Route::get('/{id}', [BatchEnrollmentController::class, 'show'])->name('admin.batches.show');
    Route::put('/{id}', [BatchEnrollmentController::class, 'update'])->name('admin.batches.update');
    Route::post('/{id}/toggle-status', [BatchEnrollmentController::class, 'toggleStatus'])->name('admin.batches.toggle-status');
    Route::post('/{id}/approve', [BatchEnrollmentController::class, 'approveBatch'])->name('admin.batches.approve');
    Route::post('/{id}/complete', [BatchEnrollmentController::class, 'completeBatch'])->name('admin.batches.complete');
    Route::post('/{id}/reopen', [BatchEnrollmentController::class, 'reopenBatch'])->name('admin.batches.reopen');
    Route::get('/{id}/students', [BatchEnrollmentController::class, 'students'])->name('admin.batches.students');
    
    // Additional batch management routes
    Route::get('/get-by-program', [BatchEnrollmentController::class, 'getBatchesByProgram'])->name('admin.batches.get-by-program');
    Route::put('/{id}/update', [BatchEnrollmentController::class, 'updateBatch'])->name('admin.batches.update-batch');
    Route::delete('/{id}', [BatchEnrollmentController::class, 'deleteBatch'])->name('admin.batches.delete');
    Route::post('/{id}/add-students', [BatchEnrollmentController::class, 'addStudentsToBatch'])->name('admin.batches.add-students');
    Route::delete('/{batchId}/students/{studentId}', [BatchEnrollmentController::class, 'removeStudentFromBatch'])->name('admin.batches.remove-student');
    Route::get('/{id}/export', [BatchEnrollmentController::class, 'exportBatchEnrollments'])->name('admin.batches.export');
    Route::get('/{id}/available-students', [BatchEnrollmentController::class, 'getAvailableStudents'])->name('admin.batches.available-students');
    
    // Student movement routes - removed move-to-pending and move-to-current as drag-and-drop is now purely visual
    Route::post('/{batchId}/enrollments/{enrollmentId}/add-to-batch', [BatchEnrollmentController::class, 'addStudentToBatch'])->name('admin.batches.add-to-batch');
    Route::post('/{batchId}/enrollments/{enrollmentId}/move-to-current', [BatchEnrollmentController::class, 'moveStudentToCurrent'])->name('admin.batches.move-to-current');
    Route::post('/{batchId}/enrollments/{enrollmentId}/move-to-pending', [BatchEnrollmentController::class, 'moveStudentToPending'])->name('admin.batches.move-to-pending');
});

// Alias route for batch enrollment (for backward compatibility)
Route::get('/admin/student/enrollment/batch', [BatchEnrollmentController::class, 'index'])
     ->name('admin.student.enrollment.batch')
     ->middleware(['admin.director.auth']);

/*
|--------------------------------------------------------------------------
| Professor Management Routes  
|--------------------------------------------------------------------------
*/
// Admin can create meetings for professors
Route::post('/admin/professors/{id}/meetings', [\App\Http\Controllers\AdminProfessorController::class, 'createMeeting'])
    ->middleware(['admin.director.auth'])
    ->name('admin.professors.create-meeting');

Route::get('/admin/professors/{id}/batches', [\App\Http\Controllers\AdminProfessorController::class, 'getProfessorBatches'])
    ->middleware(['admin.director.auth'])
    ->name('admin.professors.batches');

// Registration and document validation routes - accessible for registration
Route::middleware(['web'])->group(function () {
    Route::post('/registration/validate-document', [RegistrationController::class, 'validateDocument'])->name('registration.validate-document');
    Route::get('/api/batches/{programId}', [RegistrationController::class, 'getBatchesForProgram'])->name('api.batches.program');
    Route::post('/registration/batch-enrollment', [RegistrationController::class, 'saveBatchEnrollment'])->name('registration.batch-enrollment');
});

// OCR File validation routes - accessible for registration
Route::middleware(['web'])->group(function(){
    Route::get('/registration/user-prefill', 
        [\App\Http\Controllers\RegistrationController::class, 'userPrefill']
    )->name('registration.userPrefill');

    Route::get('/registration/user-prefill-data', 
        [\App\Http\Controllers\RegistrationController::class, 'userPrefill']
    )->name('registration.user-prefill-data');

    Route::post('/registration/validate-file', 
        [\App\Http\Controllers\RegistrationController::class, 'validateFileUpload']
    )->name('registration.validateFile');
    
    // Education levels route for registration form
    Route::get('/api/education-levels/{plan?}', [EducationLevelController::class, 'getForPlan'])->name('api.education-levels.plan');
});

// Debug routes for testing
Route::get('/test-file-upload', function() {
    return view('test_file_upload');
});

Route::get('/test-registration-routes', function() {
    return response()->json([
        'success' => true,
        'message' => 'Registration routes are working',
        'routes' => [
            'user-prefill' => route('registration.userPrefill'),
            'user-prefill-data' => route('registration.user-prefill-data'), 
            'validate-file' => route('registration.validateFile')
        ]
     ]);
});

Route::get('/test-export-route', function() {
    return response()->json([
        'success' => true,
        'message' => 'Export route test',
        'export_route' => route('admin.batches.export', 1),
        'batch_routes' => [
            'index' => route('admin.batches.index'),
            'export_template' => '/admin/batches/{id}/export'
        ]
    ]);
});

/*
|--------------------------------------------------------------------------
| Test Settings
|--------------------------------------------------------------------------
*/
Route::get('/test-settings', function () {
    $settingsPath = storage_path('app/settings.json');
    $settings = \App\Helpers\SettingsHelper::getSettings();
    
    return response()->json([
        'settings_file_exists' => file_exists($settingsPath),
        'settings_path' => $settingsPath,
        'current_settings' => $settings,
        'enrollment_styles' => \App\Helpers\SettingsHelper::getEnrollmentStyles(),
        'navbar_styles' => \App\Helpers\SettingsHelper::getNavbarStyles(),
        'footer_styles' => \App\Helpers\SettingsHelper::getFooterStyles(),
        'program_card_styles' => \App\Helpers\SettingsHelper::getProgramCardStyles(),
        'button_styles' => \App\Helpers\SettingsHelper::getButtonStyles(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Public Site Routes
|--------------------------------------------------------------------------
*/
// Homepage
Route::get('/', [App\Http\Controllers\HomepageController::class, 'index'])->name('home');

// Review Programs page
Route::get('/review-programs', function() {
    return view('welcome.review-programs');
})->name('review-programs');

// Add this route for programs listing that redirects to review-programs
Route::get('/programs', function() {
    return redirect()->route('review-programs');
})->name('programs.index');

// Individual program details
Route::get('/programs/{id}', function($id) {
    $program = \App\Models\Program::with('modules')->findOrFail($id);
    return view('programs.show', compact('program'));
})->name('programs.show');

// Enrollment selection
// Enrollment index page - serves as both selection and index
Route::get('/enrollment', [StudentRegistrationController::class, 'showEnrollmentSelection'])
     ->name('enrollment.index');

// Full enrollment form (GET)
Route::get('/enrollment/full', [StudentRegistrationController::class, 'showRegistrationForm'])
     ->name('enrollment.full');

// Modular enrollment form (GET)
Route::get('/enrollment/modular', [ModularRegistrationController::class, 'showForm'])->name('enrollment.modular');

// Alternative route for modular enrollment (in case the main route has issues)
Route::get('/modular-enrollment', [ModularRegistrationController::class, 'showForm'])->name('enrollment.modular.alt');

// Emergency fallback route for modular enrollment (simplest possible handler)
Route::get('/emergency-modular', [EmergencyModularController::class, 'showEmergencyModularForm'])->name('enrollment.modular.emergency');

// Redirect simplified modular route to the main modular enrollment page
Route::get('/simplified-modular', function() {
    return redirect()->route('enrollment.modular');
})->name('enrollment.modular.simplified');

// Debug and testing routes for enrollment
Route::get('/log-click', [EnrollmentDebugController::class, 'logClick'])->name('debug.log-click');
Route::get('/test-modular-view', [EnrollmentDebugController::class, 'testModularView'])->name('debug.test-modular-view');
Route::get('/go-to-modular', [EnrollmentDebugController::class, 'redirectToModular'])->name('debug.redirect-to-modular');
Route::get('/direct-to-modular', [EnrollmentDebugController::class, 'forceDirectNavigation'])->name('debug.force-modular');

// Modular enrollment submission
Route::post('/enrollment/modular/submit', [ModularRegistrationController::class, 'submitEnrollment'])->name('enrollment.modular.submit');

// Modular enrollment validation
Route::post('/enrollment/modular/validate', [ModularRegistrationController::class, 'validateStep'])->name('enrollment.modular.validate');

// API endpoints for modular enrollment
Route::middleware(['web'])->group(function () {
    Route::get('/api/modular/batches/{programId}', [ModularRegistrationController::class, 'getBatchesForProgram'])->name('api.modular.batches.program');
    Route::post('/modular/registration/validate-file', [ModularRegistrationController::class, 'validateFileUpload'])->name('modular.registration.validateFile');
    Route::get('/modular/registration/user-prefill', [ModularRegistrationController::class, 'userPrefill'])->name('modular.registration.userPrefill');
});

// Enrollment-specific OTP and validation routes
Route::post('/enrollment/send-otp', [StudentRegistrationController::class, 'sendEnrollmentOTP'])->name('enrollment.send-otp');
Route::post('/enrollment/verify-otp', [StudentRegistrationController::class, 'verifyEnrollmentOTP'])->name('enrollment.verify-otp');
Route::post('/enrollment/validate-referral', [StudentRegistrationController::class, 'validateEnrollmentReferral'])->name('enrollment.validate-referral');
Route::post('/enrollment/check-email', [StudentRegistrationController::class, 'checkEmailAvailability'])->name('enrollment.check-email');
Route::post('/enrollment/create-auto-batch', [StudentRegistrationController::class, 'createAutoBatchPublic'])->name('enrollment.create-auto-batch');
Route::post('/check-email-availability', [StudentRegistrationController::class, 'checkEmailAvailability'])->name('check.email.availability');

// Test route for enrollment testing
Route::get('/test-enrollment', function() {
    return view('test-enrollment');
})->name('test.enrollment');

// Test route for modular enrollment testing
Route::get('/test-modular-enrollment', function() {
    return view('test-modular-enrollment');
})->name('test.modular.enrollment');

// Test route for comprehensive system testing
Route::get('/test-all-fixes', function() {
    return view('test-all-fixes');
})->name('test.all.fixes');

// Debug redirect test
Route::get('/debug-redirect', function() {
    return view('debug-redirect');
});

// Debug session test
Route::get('/debug-session', function() {
    return response()->json([
        'session_data' => session()->all(),
        'user_id' => session('user_id'),
        'user_role' => session('user_role'),
        'role' => session('role'),
        'logged_in' => session('logged_in')
    ]);
});

// CSRF token endpoint for testing
Route::get('/csrf-token', function() {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
});

// Unified login page and authentication for all user types
Route::get('/login', [UnifiedLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UnifiedLoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [UnifiedLoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/reset', [UnifiedLoginController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [UnifiedLoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [UnifiedLoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [UnifiedLoginController::class, 'reset'])->name('password.update');

// Signup page
Route::get('/signup', [App\Http\Controllers\SignupController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [App\Http\Controllers\SignupController::class, 'signup'])->name('user.signup');
Route::post('/signup/send-otp', [App\Http\Controllers\SignupController::class, 'sendOTP'])->name('signup.send.otp');
Route::post('/signup/verify-otp', [App\Http\Controllers\SignupController::class, 'verifyOTP'])->name('signup.verify.otp');
Route::post('/check-email-availability', [App\Http\Controllers\SignupController::class, 'checkEmailAvailability'])->name('check.email.availability');

// Legacy student authentication routes (now handled by UnifiedLoginController)
Route::post('/student/login', [UnifiedLoginController::class, 'login'])->name('student.login');
Route::post('/student/logout', [UnifiedLoginController::class, 'logout'])->name('student.logout');

    // Student dashboard and related routes  
    
    Route::middleware(['check.session', 'role.dashboard'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/settings', [StudentController::class, 'settings'])->name('student.settings');
    Route::put('/student/settings', [StudentController::class, 'updateSettings'])->name('student.settings.update');
    
    // Student search route
    Route::get('/student/search', [App\Http\Controllers\SearchController::class, 'search'])
         ->name('student.search');
    
    // Course route - moved inside middleware group for authentication
    Route::get('/student/course/{courseId}', [StudentDashboardController::class, 'course'])->name('student.course');
    
    // Test route for debugging student settings
    Route::get('/test-student-settings', function () {
        // Find student with ID 2025-07-00001
        $student = \App\Models\Student::where('student_id', '2025-07-00001')->first();
        
        if (!$student) {
            return response()->json([
                'error' => 'Student with ID 2025-07-00001 not found',
                'total_students' => \App\Models\Student::count(),
                'recent_students' => \App\Models\Student::orderBy('created_at', 'desc')->take(5)->get()
            ]);
        }
        
        // Simulate logged in student using the actual student's user_id
        session([
            'logged_in' => true,
            'user_id' => $student->user_id,
            'user_role' => 'student'
        ]);
        
        $user = \App\Models\User::find($student->user_id);
        $formRequirements = \App\Models\FormRequirement::active()->get();
        
        return view('test-settings', compact('user', 'student', 'formRequirements'));
    })->name('test.student.settings');
    
    // Password management routes
    Route::post('/student/change-password', [StudentController::class, 'changePassword'])->name('student.change-password');
    Route::post('/student/reset-password', [StudentController::class, 'resetPassword'])->name('student.reset-password');
    Route::post('/student/send-otp', [StudentController::class, 'sendOTP'])->name('student.send-otp');
    Route::post('/student/verify-email-otp', [StudentController::class, 'verifyEmailOTP'])->name('student.verify-email-otp');
    
    Route::get('/student/meetings', [\App\Http\Controllers\ClassMeetingController::class, 'studentMeetings'])->name('student.meetings');
    Route::get('/student/meetings/upcoming', [\App\Http\Controllers\ClassMeetingController::class, 'studentUpcomingMeetings'])
        ->name('student.meetings.upcoming');
    Route::post('/student/meetings/{id}/access', [\App\Http\Controllers\ClassMeetingController::class, 'logStudentAccess'])->name('student.meetings.access');
    Route::get('/student/calendar', [App\Http\Controllers\StudentCalendarController::class, 'index'])->name('student.calendar');
    Route::get('/student/calendar/events', [App\Http\Controllers\StudentCalendarController::class, 'getEvents'])->name('student.calendar.events');
    Route::get('/student/calendar/today', [App\Http\Controllers\StudentCalendarController::class, 'getTodaySchedule'])->name('student.calendar.today');
    Route::get('/student/calendar/event/{type}/{id}', [App\Http\Controllers\StudentCalendarController::class, 'getEventDetails'])->name('student.calendar.event');
    // Route::get('/student/module/{moduleId}', [StudentDashboardController::class, 'module'])->name('student.module'); // Disabled - using student-course instead
    
    // Enrolled Courses page - NEW
    Route::get('/student/enrolled-courses', [StudentDashboardController::class, 'enrolledCourses'])->name('student.enrolled-courses');
    
    // Paywall route
    Route::get('/student/paywall', [StudentDashboardController::class, 'paywall'])->name('student.paywall');
    
    // Module completion route
    Route::post('/student/module/{moduleId}/complete', [StudentDashboardController::class, 'completeModule'])->name('student.module.complete');
    
    // Get module courses with lessons and content
    Route::get('/student/module/{moduleId}/courses', [StudentDashboardController::class, 'getModuleCourses'])->name('student.module.courses');
    
    // Assignment submission routes
    Route::post('/student/assignment/submit', [StudentDashboardController::class, 'submitAssignment'])->name('student.assignment.submit');
    Route::post('/student/submit-assignment', [StudentDashboardController::class, 'submitAssignmentFile'])->name('student.submit-assignment');
    
    // Quiz routes inside middleware
    Route::post('/student/quiz/{quizId}/start', [StudentDashboardController::class, 'startQuiz'])->name('student.quiz.start');
    Route::get('/student/quiz/{moduleId}/practice', [StudentDashboardController::class, 'practiceQuiz'])->name('student.quiz.practice');
    Route::post('/student/quiz/{moduleId}/submit', [StudentDashboardController::class, 'submitQuiz'])->name('student.quiz.submit');
    Route::get('/student/quiz/take/{attemptId}', [StudentDashboardController::class, 'takeQuiz'])->name('student.quiz.take');
    Route::post('/student/quiz/submit/{attemptId}', [StudentDashboardController::class, 'submitQuizAttempt'])->name('student.quiz.submit.attempt');
    Route::get('/student/quiz/results/{attemptId}', [StudentDashboardController::class, 'showQuizResults'])->name('student.quiz.results');
    
    // AI-generated quiz routes
    Route::get('/student/ai-quiz/{quizId}/start', [StudentDashboardController::class, 'startAiQuiz'])->name('student.ai-quiz.start');
    Route::post('/student/ai-quiz/{quizId}/submit', [StudentDashboardController::class, 'submitAiQuiz'])->name('student.ai-quiz.submit');
    
    // Completion routes - moved inside middleware for proper authentication
    Route::post('/student/complete-course', [\App\Http\Controllers\CompletionController::class, 'markCourseComplete']);
    Route::post('/student/complete-content', [\App\Http\Controllers\CompletionController::class, 'markContentComplete']);
    Route::post('/student/complete-module', [App\Http\Controllers\CompletionController::class, 'markModuleComplete']);
    Route::post('/student/module/{moduleId}/check-completion', [App\Http\Controllers\CompletionController::class, 'checkModuleCompletion']);
    
    // Content view route - moved back inside middleware for authentication
    Route::get('/student/content/{contentId}/view', [StudentDashboardController::class, 'viewContent'])->name('student.content.view');
}); // END OF MIDDLEWARE GROUP

// Simple test route
Route::get('/test-content-view/{contentId}', function($contentId) {
    try {
        $content = \Illuminate\Support\Facades\DB::table('content_items')->where('id', $contentId)->first();
        if (!$content) {
            return response()->json(['error' => 'Content not found', 'content_id' => $contentId]);
        }
        
        // Test controller method
        $controller = new \App\Http\Controllers\StudentDashboardController();
        return $controller->viewContent($contentId);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(), 
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

    Route::get('/student/content/{contentId}', [StudentDashboardController::class, 'getContent'])->name('student.content');
    Route::get('/student/content/{contentId}/submission-info', [StudentDashboardController::class, 'getSubmissionInfo'])->name('student.submission-info');
    Route::get('/student/content/{contentId}/submissions', [App\Http\Controllers\StudentDashboardController::class, 'getSubmissionsByContent']);
    Route::get('/student/quiz/{moduleId}/practice', [StudentDashboardController::class, 'practiceQuiz'])->name('student.quiz.practice');
    Route::post('/student/quiz/{moduleId}/submit', [StudentDashboardController::class, 'submitQuiz'])->name('student.quiz.submit');

    // AI-generated quiz routes
    Route::get('/student/ai-quiz/{quizId}/start', [StudentDashboardController::class, 'startAiQuiz'])->name('student.ai-quiz.start');
    Route::post('/student/ai-quiz/{quizId}/submit', [StudentDashboardController::class, 'submitAiQuiz'])->name('student.ai-quiz.submit');

    // Payment routes
    Route::post('/student/payment/process', [App\Http\Controllers\StudentPaymentController::class, 'processPayment'])->name('student.payment.process');
    Route::get('/student/payment/history', [App\Http\Controllers\StudentPaymentController::class, 'paymentHistory'])->name('student.payment.history');
    Route::get('/student/payment/methods', [App\Http\Controllers\StudentPaymentController::class, 'getPaymentMethods'])->name('student.payment.methods');
    Route::get('/student/payment/enrollment/{id}/details', [App\Http\Controllers\StudentPaymentController::class, 'getEnrollmentDetails'])->name('student.payment.enrollment.details');

    // Rejected Registration Management Routes
    Route::get('/student/enrollment/{id}/rejection-details', [StudentController::class, 'getRejectionDetails'])->name('student.enrollment.rejection-details');
    Route::get('/student/enrollment/{id}/edit-form', [StudentController::class, 'getEditForm'])->name('student.enrollment.edit-form');
    Route::put('/student/enrollment/{id}/resubmit', [StudentController::class, 'resubmitRegistration'])->name('student.enrollment.resubmit');
    Route::delete('/student/enrollment/{id}/delete', [StudentController::class, 'deleteRegistration'])->name('student.enrollment.delete');
    Route::post('/student/content/{id}/complete', [App\Http\Controllers\StudentDashboardController::class, 'markContentDone']);
    Route::post('/student/assignment/save-draft', [App\Http\Controllers\StudentDashboardController::class, 'saveAssignmentDraft']);
    Route::post('/student/assignment/remove-draft', [App\Http\Controllers\StudentDashboardController::class, 'removeAssignmentDraft']);
    Route::post('/student/uncomplete-module', [App\Http\Controllers\StudentDashboardController::class, 'uncompleteModule']);
    Route::post('/student/complete-module/{id}', [App\Http\Controllers\StudentDashboardController::class, 'completeModule']);

// Update overdue deadlines (can be called by admin or cron job)
Route::post('/student/update-overdue-deadlines', [App\Http\Controllers\StudentDashboardController::class, 'updateOverdueDeadlines']);

// API endpoint to get program ID for a module
Route::get('/api/module/{moduleId}/program', function($moduleId) {
    $module = \App\Models\Module::find($moduleId);
    if ($module) {
        return response()->json(['program_id' => $module->program_id]);
    }
    return response()->json(['error' => 'Module not found'], 404);
});

// Test routes for debugging rejection details
Route::get('/test-rejection-details', function () {
    return view('test-rejection-details');
});

// Include comprehensive database test
require __DIR__ . '/test-db-check.php';

// Include Cloud Security Quiz routes
require __DIR__ . '/cloud-security.php';

// Comprehensive test page
Route::get('/test-comprehensive', function () {
    return view('test-comprehensive');
});

// Test content view without authentication
Route::get('/test-content/{contentId}', function ($contentId) {
    // Simulate a logged-in student (assuming user ID 1 is a student)
    $user = \App\Models\User::find(1);
    if (!$user) {
        return response()->json(['error' => 'No test user found'], 404);
    }
    
    // Manually authenticate the user for testing
    Auth::login($user);
    
    // Call the controller method
    $controller = new \App\Http\Controllers\StudentDashboardController();
    return $controller->viewContent($contentId);
});

Route::get('/test-database-structure', function () {
    try {
        $enrollmentColumns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM enrollments");
        $registrationColumns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM registrations");
        
        // Check specific enrollment ID 1753045091
        $enrollment = \Illuminate\Support\Facades\DB::select("SELECT * FROM enrollments WHERE enrollment_id = 1753045091");
        $registration = \Illuminate\Support\Facades\DB::select("SELECT * FROM registrations WHERE registration_id = 1753045091");
        
        return response()->json([
            'success' => true,
            'enrollment_columns' => $enrollmentColumns,
            'registration_columns' => $registrationColumns,
            'enrollment_1753045091' => $enrollment,
            'registration_1753045091' => $registration
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

Route::get('/test-session-info', function () {
    return response()->json([
        'success' => true,
        'session_data' => [
            'user_id' => session('user_id'),
            'user_email' => session('user_email'),
            'all_session' => session()->all()
        ]
    ]);
});

/*
|--------------------------------------------------------------------------
| Student Actions
|--------------------------------------------------------------------------
*/
// Student register POST
Route::post('/student/register', [StudentRegistrationController::class, 'store'])
     ->name('student.register');

// Account creation POST (separate from full registration)
Route::post('/student/account/create', [StudentRegistrationController::class, 'handleAccountCreation'])
     ->name('student.account.create');

// Public route for getting batches by program (for registration form)
Route::get('/batches/by-program', [StudentRegistrationController::class, 'getBatchesByProgram'])
     ->name('public.batches.by-program');

// Registration success page
Route::get('/registration/success', function() {
    return view('registration.success');
})->name('registration.success');

// Test registration form
Route::get('/test-registration', function () {
    $formRequirements = App\Models\FormRequirement::active()->forProgram('full')->get();
    return view('test-registration', compact('formRequirements'));
})->name('test.registration');

// Clean test route for registration fixes
Route::get('/test-registration-fixes', function() { 
    return view('test-fixes', [
        'programs' => \App\Models\Program::where('is_archived', false)->get(), 
        'packages' => \App\Models\Package::all()
    ]); 
});

Route::get('/test-registration-terms', function() { 
    return view('registration-fixes-test'); 
});

Route::get('/test-all-fixes', function() { 
    return view('all-fixes-test'); 
});

// Check if email exists
Route::post('/check-email', [StudentRegistrationController::class, 'checkEmailExists'])
     ->name('check.email');

/*
|--------------------------------------------------------------------------
| Program Routes
|--------------------------------------------------------------------------
*/
// Programs listing page
Route::get('/programs', function () {
    $programs = \App\Models\Program::where('is_archived', false)
                                   ->with(['modules' => function($query) {
                                       $query->where('is_archived', false)
                                             ->orderBy('module_order')
                                             ->orderBy('module_name');
                                   }])
                                   ->get();
    
    return view('programs.index', compact('programs'));
})->name('programs.index');

// Program details page
Route::get('/programs/{id}', function ($id) {
    $program = \App\Models\Program::where('program_id', $id)
                                  ->where('is_archived', false)
                                  ->with(['modules' => function($query) {
                                      $query->where('is_archived', false)
                                            ->orderBy('module_order')
                                            ->orderBy('module_name');
                                  }])
                                  ->firstOrFail();
    
    return view('programs.show', compact('program'));
})->name('programs.show');

// API endpoint for programs dropdown (for navbar)
Route::get('/api/programs', function () {
    $programs = \App\Models\Program::where('is_archived', false)
                                   ->select('program_id', 'program_name', 'program_description')
                                   ->get();
    
    return response()->json($programs);
})->name('api.programs');

// API endpoint for modules by program
Route::get('/api/programs/{programId}/modules', function ($programId) {
    try {
        // Fetch modules with their courses
        $modules = DB::table('modules')
            ->where('program_id', $programId)
            ->where('is_archived', false)
            ->orderBy('module_order', 'asc')
            ->select('modules_id', 'module_name', 'module_description', 'program_id')
            ->get();

        $moduleIds = $modules->pluck('modules_id')->toArray();
        $courses = DB::table('courses')
            ->whereIn('module_id', $moduleIds)
            ->select('subject_id as course_id', 'subject_name as course_name', 'subject_description as description', 'module_id')
            ->get();

        $coursesByModule = [];
        foreach ($courses as $course) {
            $coursesByModule[$course->module_id][] = [
                'course_id' => $course->course_id,
                'course_name' => $course->course_name,
                'description' => $course->description,
            ];
        }

        $transformedModules = [];
        foreach ($modules as $module) {
            $transformedModules[] = [
                'module_id' => $module->modules_id,
                'module_name' => $module->module_name,
                'description' => $module->module_description,
                'program_id' => $module->program_id,
                'courses' => $coursesByModule[$module->modules_id] ?? [],
            ];
        }

        return response()->json([
            'success' => true,
            'modules' => $transformedModules
        ]);
    } catch (\Exception $e) {
        Log::error('Error loading modules:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false,
            'message' => 'Error loading modules: ' . $e->getMessage()
        ], 500);
    }
})->name('api.programs.modules');

// API endpoint for modules by package
Route::get('/api/packages/{packageId}/modules', function ($packageId) {
    try {
        // Get the package
        $package = DB::table('packages')->where('package_id', $packageId)->first();
        
        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        }
        
        // Debug logging
        Log::info('Package modules API called', [
            'package_id' => $packageId,
            'package_name' => $package->package_name,
            'package_type' => $package->package_type,
            'program_id' => $package->program_id
        ]);
        
        // For modular packages, we can get modules directly from the package_modules table
        // or from the program associated with the package
        $modules = collect([]); // Initialize as a collection
        
        // First, try to get modules directly from package_modules table
        $packageModules = DB::table('package_modules')
            ->join('modules', 'package_modules.modules_id', '=', 'modules.modules_id')
            ->where('package_modules.package_id', $packageId)
            ->where('modules.is_archived', false)
            ->select('modules.modules_id', 'modules.module_name', 'modules.module_description', 'modules.program_id')
            ->get();
        
        Log::info('Package modules from pivot table', [
            'package_modules_count' => $packageModules->count(),
            'package_modules' => $packageModules->toArray()
        ]);
        
        if ($packageModules->isNotEmpty()) {
            $modules = $packageModules;
        } else {
            // Fallback: get modules from the program associated with this package
            if ($package->program_id) {
                $programModules = DB::table('modules')
                    ->where('program_id', $package->program_id)
                    ->where('is_archived', false)
                    ->orderBy('module_order', 'asc')
                    ->select('modules_id', 'module_name', 'module_description', 'program_id')
                    ->get();
                
                Log::info('Program modules fallback', [
                    'program_id' => $package->program_id,
                    'program_modules_count' => $programModules->count(),
                    'program_modules' => $programModules->toArray()
                ]);
                
                $modules = $programModules;
            } else {
                Log::warning('Package has no program_id and no package_modules entries', [
                    'package_id' => $packageId,
                    'package_name' => $package->package_name
                ]);
            }
        }
        
        if ($modules->isEmpty()) { // Now safe to use isEmpty()
            Log::info('No modules found for package', [
                'package_id' => $packageId,
                'package_name' => $package->package_name
            ]);
            
            return response()->json([
                'success' => true,
                'modules' => [],
                'message' => 'No modules found for this package',
                'debug_info' => [
                    'package_id' => $packageId,
                    'package_name' => $package->package_name,
                    'package_type' => $package->package_type,
                    'program_id' => $package->program_id,
                    'package_modules_count' => $packageModules->count() ?? 0
                ]
            ]);
        }
        
        $moduleIds = $modules->pluck('modules_id')->toArray();
        $courses = DB::table('courses')
            ->whereIn('module_id', $moduleIds)
            ->select('subject_id as course_id', 'subject_name as course_name', 'subject_description as description', 'module_id')
            ->get();

        $coursesByModule = [];
        foreach ($courses as $course) {
            $coursesByModule[$course->module_id][] = [
                'course_id' => $course->course_id,
                'course_name' => $course->course_name,
                'description' => $course->description,
            ];
        }

        // Get program names for modules
        $programIds = $modules->pluck('program_id')->unique()->toArray();
        $programs = DB::table('programs')
            ->whereIn('program_id', $programIds)
            ->select('program_id', 'program_name')
            ->get()
            ->keyBy('program_id');

        $transformedModules = [];
        foreach ($modules as $module) {
            $program = $programs->get($module->program_id);
            $transformedModules[] = [
                'module_id' => $module->modules_id,
                'module_name' => $module->module_name,
                'description' => $module->module_description,
                'program_id' => $module->program_id,
                'program_name' => $program ? $program->program_name : 'Unknown Program',
                'courses' => $coursesByModule[$module->modules_id] ?? [],
            ];
        }

        return response()->json([
            'success' => true,
            'modules' => $transformedModules,
            'package_name' => $package->package_name
        ]);
    } catch (\Exception $e) {
        Log::error('Error loading package modules:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false,
            'message' => 'Error loading modules: ' . $e->getMessage()
        ], 500);
    }
})->name('api.packages.modules');

// API endpoint for modules by program (for modular enrollment)
Route::get('/api/programs/{programId}/modules', function ($programId) {
    try {
        Log::info('Program modules API called', [
            'program_id' => $programId
        ]);
        
        // Get modules for the specific program
        $modules = DB::table('modules')
            ->where('program_id', $programId)
            ->where('is_archived', false)
            ->orderBy('module_order', 'asc')
            ->select('modules_id', 'module_name', 'module_description', 'program_id')
            ->get();
        
        Log::info('Found modules for program', [
            'program_id' => $programId,
            'modules_count' => $modules->count(),
            'modules' => $modules->toArray()
        ]);
        
        if ($modules->isEmpty()) {
            return response()->json([
                'success' => true,
                'modules' => [],
                'message' => 'No modules found for this program',
                'debug_info' => [
                    'program_id' => $programId
                ]
            ]);
        }
        
        // Get courses for these modules
        $moduleIds = $modules->pluck('modules_id')->toArray();
        $courses = DB::table('courses')
            ->whereIn('module_id', $moduleIds)
            ->select('subject_id as course_id', 'subject_name as course_name', 'subject_description as description', 'module_id')
            ->get();

        $coursesByModule = [];
        foreach ($courses as $course) {
            $coursesByModule[$course->module_id][] = [
                'course_id' => $course->course_id,
                'course_name' => $course->course_name,
                'description' => $course->description,
            ];
        }

        // Get program info
        $program = DB::table('programs')
            ->where('program_id', $programId)
            ->select('program_id', 'program_name')
            ->first();

        $transformedModules = [];
        foreach ($modules as $module) {
            $transformedModules[] = [
                'module_id' => $module->modules_id,
                'module_name' => $module->module_name,
                'description' => $module->module_description,
                'program_id' => $module->program_id,
                'program_name' => $program ? $program->program_name : 'Unknown Program',
                'courses' => $coursesByModule[$module->modules_id] ?? [],
            ];
        }

        return response()->json([
            'success' => true,
            'modules' => $transformedModules,
            'program_name' => $program ? $program->program_name : 'Unknown Program'
        ]);
    } catch (\Exception $e) {
        Log::error('Error loading program modules:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false,
            'message' => 'Error loading modules: ' . $e->getMessage()
        ], 500);
    }
})->name('api.programs.modules');

/*
|--------------------------------------------------------------------------
| Student Enrollment
|--------------------------------------------------------------------------
*/
// Student enrollment form submission
Route::post('/student/enroll', [StudentRegistrationController::class, 'enroll'])
     ->name('student.enroll');

// Check enrollment status (AJAX)
Route::get('/student/enrollment-status', [StudentRegistrationController::class, 'checkEnrollmentStatus'])
     ->name('student.enrollment.status');

// OCR document processing
Route::post('/ocr/process', [StudentRegistrationController::class, 'processOcrDocument'])
     ->name('ocr.process');

/*
|--------------------------------------------------------------------------
| Admin Dashboard & Registration
|--------------------------------------------------------------------------
*/

// Payment routes
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/failure', [PaymentController::class, 'paymentFailure'])->name('payment.failure');
Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
Route::post('/upload-payment-proof', [PaymentController::class, 'uploadPaymentProof'])->name('payment.upload-proof');
Route::post('/student/payment/upload-proof', [StudentPaymentModalController::class, 'uploadPaymentProof'])->name('student.payment.upload-proof');
Route::get('/payment-methods/enabled', [AdminSettingsController::class, 'getEnabledPaymentMethods'])->name('payment-methods.enabled');

// Test route for modal functionality (no authentication required)
Route::get('/test/registration/{id}/details', [AdminController::class, 'getRegistrationDetailsJson'])
     ->name('test.registration.details');

// Test route for email functionality
Route::get('/test/email/{email}', function($email) {
    try {
        Log::info('Email test route accessed', ['email' => $email]);
        
        Mail::raw("Hello!\n\nThis is a test email from A.R.T.C system.\n\nIf you receive this, the email system is working correctly.\n\nTime sent: " . now()->format('Y-m-d H:i:s') . "\n\nBest regards,\nA.R.T.C Team", function ($message) use ($email) {
            $message->to($email)
                    ->subject('A.R.T.C - Email System Test');
        });
        
        Log::info('Email test sent successfully', ['email' => $email]);
        return response()->json(['status' => 'success', 'message' => 'Test email sent successfully to ' . $email]);
        
    } catch (\Exception $e) {
        Log::error('Email test failed', [
            'email' => $email,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['status' => 'error', 'message' => 'Email test failed: ' . $e->getMessage()], 500);
    }
})->name('test.email');

// Admin dashboard and admin routes with middleware
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])
         ->name('admin.dashboard');
    
    // Admin search route
    Route::get('/admin/search', [App\Http\Controllers\SearchController::class, 'adminSearch'])
         ->name('admin.search');

    // Admin approve/reject registration
    Route::get('/admin/modal-test', function() {
        return view('admin.modal-test');
    });
    Route::get('/admin/registration/{id}', [AdminController::class, 'showRegistration']);
    Route::get('/admin/registration/{id}/details', [AdminController::class, 'getRegistrationDetailsJson'])
         ->name('admin.registration.details');
    Route::post('/admin/registration/{id}/approve', [AdminController::class, 'approve'])
         ->name('admin.registration.approve');
    Route::post('/admin/registration/{id}/reject', [AdminController::class, 'reject'])
         ->name('admin.registration.reject');
    Route::post('/admin/registration/{id}/reject-with-reason', [AdminController::class, 'rejectWithReason'])
         ->name('admin.registration.reject.reason');
    Route::post('/admin/registration/{id}/reject-with-fields', [AdminController::class, 'rejectWithFields'])
         ->name('admin.registration.reject.fields');
    Route::post('/admin/registration/{id}/approve-resubmission', [AdminController::class, 'approveResubmission'])
         ->name('admin.registration.approve.resubmission');
    // Registration actions
    Route::post('/admin/registration/{id}/approve', [AdminController::class, 'approve'])
         ->name('admin.registration.approve');
    Route::post('/admin/registration/{id}/reject', [AdminController::class, 'rejectRegistration'])
         ->name('admin.registration.reject');
    Route::post('/admin/registration/{id}/update-rejection', [AdminController::class, 'updateRejection'])
         ->name('admin.registration.update.rejection');
    Route::get('/admin/registration/{id}/original-data', [AdminController::class, 'getOriginalRegistrationData'])
         ->name('admin.registration.original.data');

    // List student registrations
    Route::get('/admin-student-registration', [AdminController::class, 'studentRegistration'])
         ->name('admin.student.registration');
    Route::get('/admin-student-registration/pending', [AdminController::class, 'studentRegistration'])
         ->name('admin.student.registration.pending');
    Route::get('/admin-student-registration/rejected', [AdminController::class, 'studentRegistrationRejected'])
         ->name('admin.student.registration.rejected');
    Route::get('/admin-student-registration/resubmitted', [AdminController::class, 'studentRegistrationResubmitted'])
         ->name('admin.student.registration.resubmitted');
    Route::get('/admin-student-registration/history', [AdminController::class, 'studentRegistrationHistory'])
         ->name('admin.student.registration.history');

    // Student history actions
    Route::get('/admin/student/{id}/details', [AdminController::class, 'getStudentDetailsJson'])
         ->name('admin.student.details');
    Route::post('/admin/student/{id}/undo-approval', [AdminController::class, 'undoApproval'])
         ->name('admin.student.undo.approval');

    // Rejected registration actions
    Route::post('/admin/registration/{id}/undo-rejection', [AdminController::class, 'undoRejection'])
         ->name('admin.registration.undo.rejection');
    Route::post('/admin/registration/{id}/approve-rejected', [AdminController::class, 'approveRejectedRegistration'])
         ->name('admin.registration.approve.rejected');

    // Enrollment details for payment history
    Route::get('/admin/student/enrollment/{id}/details', [AdminController::class, 'getEnrollmentDetailsJson'])
         ->name('admin.enrollment.details');
    Route::get('/admin/enrollment/{id}/details', [AdminController::class, 'getEnrollmentDetailsJson'])
         ->name('admin.enrollment.details.json');

    // Enrollment assignment route  
    Route::post('/admin/enrollment/assign', [AdminController::class, 'assignEnrollment'])
         ->name('admin.enrollment.assign');

    // Payment management routes
    Route::get('/admin-student-registration/payment/pending', [AdminController::class, 'paymentPending'])
         ->name('admin.student.registration.payment.pending');
    Route::get('/admin-student-registration/payment/rejected', [AdminController::class, 'paymentRejected'])
         ->name('admin.student.registration.payment.rejected');
    Route::get('/admin-student-registration/payment/history', [AdminController::class, 'paymentHistory'])
         ->name('admin.student.registration.payment.history');

    // Mark enrollment as paid
    Route::post('/admin/enrollment/{id}/mark-paid', [AdminController::class, 'markAsPaid'])
         ->name('admin.enrollment.mark-paid');

    // View one student registration's details
    Route::get('/admin-student-registration/view/{id}', [AdminController::class, 'showRegistrationDetails'])
         ->name('admin.student.registration.view');

    // Add this route for payment details by enrollment ID
    Route::get('/admin/enrollment/{id}/payment-details', [AdminController::class, 'getPaymentDetailsByEnrollment'])
         ->name('admin.enrollment.payment-details');

    // Registration management routes (moved inside middleware group)
    Route::post('/admin/registrations/{id}/approve', [AdminController::class, 'approveRegistration'])
         ->name('admin.registrations.approve');
    Route::post('/admin/registrations/{id}/undo-approval', [AdminController::class, 'undoRegistrationApproval'])
         ->name('admin.registrations.undo-approval');
});

/*
|--------------------------------------------------------------------------
| Admin Programs
|--------------------------------------------------------------------------
*/
// Programs list
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/programs', [AdminProgramController::class, 'index'])->name('admin.programs.index');
    Route::get('/admin/programs/create', [AdminProgramController::class, 'create'])->name('admin.programs.create');
    Route::post('/admin/programs', [AdminProgramController::class, 'store'])->name('admin.programs.store');
    Route::post('/admin/programs/batch-store', [AdminProgramController::class, 'batchStore'])->name('admin.programs.batch-store');
    Route::delete('/admin/programs/{id}', [AdminProgramController::class, 'destroy'])->name('admin.programs.delete');
    Route::post('/admin/programs/{program}/toggle-archive', [AdminProgramController::class, 'toggleArchive'])->name('admin.programs.toggle-archive');
    Route::post('/admin/programs/{id}/archive', [AdminProgramController::class, 'archive'])->name('admin.programs.archive');
    Route::post('/admin/programs/batch-delete', [AdminProgramController::class, 'batchDelete'])->name('admin.programs.batch-delete');
    Route::get('/admin/programs/archived', [AdminProgramController::class, 'archived'])->name('admin.programs.archived');
    Route::get('/admin/programs/{id}/enrollments', [AdminProgramController::class, 'enrollments'])->name('admin.programs.enrollments');
    Route::post('/admin/programs/assign', [AdminProgramController::class, 'assignProgram'])->name('admin.programs.assign');
});

/*
|--------------------------------------------------------------------------
| Admin Announcements
|--------------------------------------------------------------------------
*/
// Announcements routes
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/announcements', [\App\Http\Controllers\Admin\AnnouncementController::class, 'index'])
         ->name('admin.announcements.index');
    Route::get('/admin/announcements/create', [\App\Http\Controllers\Admin\AnnouncementController::class, 'create'])
         ->name('admin.announcements.create');
    Route::post('/admin/announcements', [\App\Http\Controllers\Admin\AnnouncementController::class, 'store'])
         ->name('admin.announcements.store');
    Route::get('/admin/announcements/{id}', [\App\Http\Controllers\Admin\AnnouncementController::class, 'show'])
         ->name('admin.announcements.show');
    Route::get('/admin/announcements/{id}/edit', [\App\Http\Controllers\Admin\AnnouncementController::class, 'edit'])
         ->name('admin.announcements.edit');
    Route::put('/admin/announcements/{id}', [\App\Http\Controllers\Admin\AnnouncementController::class, 'update'])
         ->name('admin.announcements.update');
    Route::delete('/admin/announcements/{id}', [\App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])
         ->name('admin.announcements.destroy');
});


/*
|--------------------------------------------------------------------------
| Admin Modules
|--------------------------------------------------------------------------
*/
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/modules', [AdminModuleController::class, 'index'])->name('admin.modules.index');
    Route::post('/admin/modules', [AdminModuleController::class, 'store'])->name('admin.modules.store');
    Route::get('/admin/modules/{id}/edit', [AdminModuleController::class, 'edit'])->name('admin.modules.edit');
    Route::put('/admin/modules/{id}', [AdminModuleController::class, 'update'])->name('admin.modules.update');
    Route::post('/admin/modules/{id}/upload-video', [AdminModuleController::class, 'uploadVideo'])->name('admin.modules.upload-video');
    Route::post('/admin/modules/{id}/add-content', [AdminModuleController::class, 'addContent'])->name('admin.modules.add-content');
    Route::post('/admin/modules/batch', [AdminModuleController::class, 'batchStore'])->name('admin.modules.batch-store');
    Route::get('/admin/modules/course-content-upload', [AdminModuleController::class, 'showCourseContentUploadPage'])->name('admin.modules.course-content-upload');
    Route::post('/admin/modules/course-content-store', [AdminModuleController::class, 'courseContentStore'])->name('admin.modules.course-content-store');
    Route::patch('/admin/modules/{module:modules_id}/archive', [AdminModuleController::class, 'toggleArchive'])->name('admin.modules.toggle-archive');
    Route::delete('/admin/modules/batch-delete', [AdminModuleController::class, 'batchDelete'])->name('admin.modules.batch-delete');
    Route::get('/admin/modules/archived', [AdminModuleController::class, 'archived'])->name('admin.modules.archived');
    Route::delete('/admin/modules/{module:modules_id}', [AdminModuleController::class, 'destroy'])->name('admin.modules.destroy');
    Route::get('/admin/modules/by-program', [AdminModuleController::class, 'getModulesByProgram'])->name('admin.modules.by-program');
    Route::post('/admin/modules/update-order', [AdminModuleController::class, 'updateOrder'])->name('admin.modules.update-order');
    Route::post('/admin/modules/{id}/toggle-admin-override', [AdminModuleController::class, 'toggleAdminOverride'])->name('admin.modules.toggle-admin-override');
    Route::get('/admin/programs/{program}/batches', [AdminModuleController::class, 'getBatchesForProgram'])->name('admin.programs.batches');
    Route::get('/admin/programs/{program}/courses', [AdminModuleController::class, 'getCoursesForProgram'])->name('admin.programs.courses');
    Route::get('/admin/modules/{module}/courses', [AdminModuleController::class, 'getCoursesByModule'])->name('admin.modules.courses');
    Route::get('/admin/modules/batches/{programId}', [AdminModuleController::class, 'getBatchesByProgram'])->name('admin.modules.batches.by-program');
});

// Test upload route
Route::get('/test-upload', function() {
    return view('test_upload_form');
});

// Test endpoint
Route::get('/test-endpoint', function() {
    return response()->json(['message' => 'Test endpoint working', 'time' => now()]);
});

// Toggle archive status
Route::patch('/admin/modules/{module:modules_id}/archive', [AdminModuleController::class, 'toggleArchive'])
     ->name('admin.modules.toggle-archive');
     
// Batch delete modules (used only by archived modules view)
Route::delete('/admin/modules/batch-delete', [AdminModuleController::class, 'batchDelete'])
     ->name('admin.modules.batch-delete');

// View archived modules
Route::get('/admin/modules/archived', [AdminModuleController::class, 'archived'])
     ->name('admin.modules.archived');

// Delete a module (used only by archived modules view)
Route::delete('/admin/modules/{module:modules_id}', [AdminModuleController::class, 'destroy'])
     ->name('admin.modules.destroy');

// Get modules by program (AJAX)
Route::get('/admin/modules/by-program', [AdminModuleController::class, 'getModulesByProgram'])
     ->name('admin.modules.by-program');

// Update module order (drag and drop)
Route::post('/admin/modules/update-order', [AdminModuleController::class, 'updateOrder'])
     ->name('admin.modules.update-order');

// Toggle admin override for module
Route::post('/admin/modules/{id}/toggle-admin-override', [AdminModuleController::class, 'toggleAdminOverride'])
     ->name('admin.modules.toggle-admin-override');

// Get batches for a program (AJAX)
Route::get('/admin/programs/{program}/batches', [AdminModuleController::class, 'getBatchesForProgram'])
     ->name('admin.programs.batches');

// Get courses for a program (AJAX)
Route::get('/admin/programs/{program}/courses', [AdminModuleController::class, 'getCoursesForProgram'])
     ->name('admin.programs.courses');

// Get courses by module ID (AJAX)
Route::get('/admin/modules/{module}/courses', [AdminModuleController::class, 'getCoursesByModule'])
     ->name('admin.modules.courses');

// Get batches by program ID (AJAX)
Route::get('/admin/modules/batches/{programId}', [AdminModuleController::class, 'getBatchesByProgram'])
     ->name('admin.modules.batches.by-program');

// Archive a module
Route::post('/admin/modules/{id}/archive', [AdminModuleController::class, 'archive'])
     ->name('admin.modules.archive');

// Admin override settings
Route::get   ('/admin/modules/{id}/override', [AdminModuleController::class, 'getOverrideSettings'])
     ->name('admin.modules.get-override');
Route::patch ('/admin/modules/{id}/override', [AdminModuleController::class, 'updateOverride'])
     ->name('admin.modules.update-override');

// Admin side API routes
Route::get('admin/programs/{program}/batches',   [AdminModuleController::class, 'getBatchesForProgram']);
Route::get('admin/programs/{program}/courses',   [AdminModuleController::class, 'getCoursesForProgram']);


// Admin Courses Routes
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/courses', [AdminCourseController::class, 'index'])
         ->name('admin.courses.index');
    Route::post('/admin/courses', [AdminCourseController::class, 'store'])
         ->name('admin.courses.store');
    Route::get('/admin/courses/{id}', [AdminCourseController::class, 'show'])
         ->name('admin.courses.show');
    Route::put('/admin/courses/{id}', [AdminCourseController::class, 'update'])
         ->name('admin.courses.update');
    Route::delete('/admin/courses/{id}', [AdminCourseController::class, 'destroy'])
         ->name('admin.courses.destroy');
    Route::get('/admin/modules/{moduleId}/courses', [AdminCourseController::class, 'getModuleCourses'])
         ->name('admin.courses.by-module');
    Route::get('/admin/courses/{courseId}/content', [AdminCourseController::class, 'getCourseContent'])
         ->name('admin.courses.content');
    Route::post('/admin/courses/update-order', [AdminCourseController::class, 'updateOrder'])
         ->name('admin.courses.update-order');
    Route::post('/admin/courses/move', [AdminCourseController::class, 'moveCourse'])
         ->name('admin.courses.move');
});

// Admin Content Routes
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/modules/{id}', [AdminModuleController::class, 'getModule'])
         ->name('admin.modules.get');
    Route::get('/admin/modules/{moduleId}/content', [AdminModuleController::class, 'getModuleContent'])
         ->name('admin.modules.content');
    Route::get('/admin/modules/{moduleId}/courses/{courseId}/content', [AdminModuleController::class, 'getCourseContentItems'])
         ->name('admin.modules.courses.content');
    Route::get('/admin/content/{id}', [AdminModuleController::class, 'getContent'])
         ->name('admin.content.get');
    Route::delete('/admin/content/{id}', [AdminModuleController::class, 'deleteContent'])
         ->name('admin.content.delete');
    Route::put('/admin/content/{id}', [AdminModuleController::class, 'updateContent'])
         ->name('admin.content.update');
    Route::post('/admin/content/{id}', [AdminModuleController::class, 'updateContent'])
         ->name('admin.content.update.post');
    Route::post('/admin/content/update-order', [AdminModuleController::class, 'updateContentOrder'])
         ->name('admin.content.update-order');
    Route::post('/admin/content/move', [AdminModuleController::class, 'moveContent'])
         ->name('admin.content.move');
    Route::post('/admin/content/move-to-module', [AdminModuleController::class, 'moveContentToModule'])
         ->name('admin.content.move-to-module');
});

// Admin Packages Routes
Route::get('/admin/packages', [AdminPackageController::class, 'index'])
     ->name('admin.packages.index');
Route::post('/admin/packages', [AdminPackageController::class, 'store'])
     ->name('admin.packages.store');
Route::get('/admin/packages/{id}', [AdminPackageController::class, 'show'])
     ->name('admin.packages.show');
Route::get('/admin/packages/{id}/edit', [AdminPackageController::class, 'edit'])
     ->name('admin.packages.edit');
Route::put('/admin/packages/{id}', [AdminPackageController::class, 'update'])
     ->name('admin.packages.update');
Route::delete('/admin/packages/{id}', [AdminPackageController::class, 'destroy'])
     ->name('admin.packages.destroy');
Route::delete('/admin/packages/{id}/delete', [AdminPackageController::class, 'destroy'])
     ->name('admin.packages.delete');

// Additional Package Management Routes
Route::get('/admin/packages/program/{program_id}/modules', [AdminPackageController::class, 'getModules'])
     ->name('admin.packages.get-modules');
Route::get('/admin/get-program-modules', [AdminPackageController::class, 'getProgramModules'])
     ->name('admin.get-program-modules');  // Changed to avoid conflict
Route::get('/admin/get-module-courses', [AdminPackageController::class, 'getModuleCourses'])
     ->name('admin.get-module-courses');  // Changed to avoid conflict
Route::get('/get-package-details', [AdminPackageController::class, 'getPackageDetails'])
     ->name('get-package-details');
Route::post('/admin/packages/{id}/archive', [AdminPackageController::class, 'archive'])
     ->name('admin.packages.archive');
Route::post('/admin/packages/{id}/restore', [AdminPackageController::class, 'restore'])
     ->name('admin.packages.restore');

// Admin AI Quiz Generator
Route::get('/admin/quiz-generator', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'index'])
     ->name('admin.quiz-generator');
Route::post('/admin/quiz-generator/generate', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'generate'])
     ->name('admin.quiz-generator.generate');
Route::post('/admin/quiz-generator/save-quiz', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'save'])
     ->name('admin.quiz-generator.save');
Route::put('/admin/quiz-generator/update-quiz/{quizId}', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'update'])
     ->name('admin.quiz-generator.update');
Route::get('/admin/quiz-generator/modules/{programId}', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'getModulesByProgram'])
     ->name('admin.quiz-generator.modules');
Route::get('/admin/quiz-generator/courses/{moduleId}', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'getCoursesByModule'])
     ->name('admin.quiz-generator.courses');
Route::get('/admin/quiz-generator/contents/{courseId}', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'getContentsByCourse'])
     ->name('admin.quiz-generator.contents');
Route::post('/admin/quiz-generator/generate-ai-questions', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'generateAIQuestions'])
     ->name('admin.quiz-generator.generate-ai-questions');
Route::get('/admin/quiz-generator/quiz/{quizId}', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'getQuiz'])
     ->name('admin.quiz-generator.get-quiz');
Route::post('/admin/quiz-generator/{quizId}/publish', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'publish'])
     ->name('admin.quiz-generator.publish');
Route::post('/admin/quiz-generator/{quizId}/archive', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'archive'])
     ->name('admin.quiz-generator.archive');
Route::post('/admin/quiz-generator/{quizId}/draft', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'draft'])
     ->name('admin.quiz-generator.draft');
Route::delete('/admin/quiz-generator/{quizId}/delete', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'delete'])
     ->name('admin.quiz-generator.delete');
Route::get('/admin/quiz-generator/preview/{quizId}', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'preview'])
     ->name('admin.quiz-generator.preview');
Route::get('/admin/quiz-generator/api/questions/{quizId}', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'getQuestionsForModal'])
     ->name('admin.quiz-generator.api.questions');
Route::post('/admin/quiz-generator/get-question-options', [App\Http\Controllers\Admin\QuizGeneratorController::class, 'getQuestionOptions'])
     ->name('admin.quiz-generator.question-options');

// Chat routes
Route::get('/admin/chat', [AdminController::class, 'chatIndex'])->name('admin.chat.index');
Route::get('/admin/chat/room/{roomId}', [AdminController::class, 'chatRoom'])->name('admin.chat.room');

// FAQ Management routes
Route::get('/admin/faq', [AdminController::class, 'faqIndex'])->name('admin.faq.index');
Route::post('/admin/faq', [AdminController::class, 'storeFaq'])->name('admin.faq.store');
Route::put('/admin/faq/{id}', [AdminController::class, 'updateFaq'])->name('admin.faq.update');
Route::delete('/admin/faq/{id}', [AdminController::class, 'deleteFaq'])->name('admin.faq.delete');

/*
|--------------------------------------------------------------------------
| Admin Settings
|--------------------------------------------------------------------------
*/
// Settings page
Route::get('/admin/settings', [AdminSettingsController::class, 'index'])
     ->name('admin.settings.index');

// Update homepage settings
Route::post('/admin/settings/homepage', [AdminSettingsController::class, 'updateHomepage'])
     ->name('admin.settings.update.homepage');

// Update navbar settings
Route::post('/admin/settings/navbar', [AdminSettingsController::class, 'updateNavbar'])
     ->name('admin.settings.update.navbar');

// Update footer settings
Route::post('/admin/settings/footer', [AdminSettingsController::class, 'updateFooter'])
     ->name('admin.settings.update.footer');

// Update program cards settings
Route::post('/admin/settings/program-cards', [AdminSettingsController::class, 'updateProgramCards'])
     ->name('admin.settings.update.program-cards');

// Update enrollment settings
Route::post('/admin/settings/enrollment', [AdminSettingsController::class, 'updateEnrollment'])
     ->name('admin.settings.update.enrollment');

// Update button settings
Route::post('/admin/settings/buttons', [AdminSettingsController::class, 'updateButtons'])
     ->name('admin.settings.update.buttons');

// Update login page settings
Route::post('/admin/settings/login', [AdminSettingsController::class, 'updateLogin'])
     ->name('admin.settings.update.login');

// Update payment terms settings
Route::post('/admin/settings/payment-terms', [AdminSettingsController::class, 'updatePaymentTerms'])
     ->name('admin.settings.update.payment.terms');

// Update terms and conditions settings
Route::post('/admin/settings/terms-conditions', [AdminSettingsController::class, 'updateTermsConditions'])
     ->name('admin.settings.update.terms.conditions');

// Registration management routes (moved inside middleware group)

// Payment management routes
Route::post('/admin/payments/{id}/mark-paid', [AdminController::class, 'markPaymentAsPaid'])
     ->name('admin.payments.mark-paid');
Route::get('/admin/payments/{id}/details', [AdminController::class, 'viewPaymentDetails'])
     ->name('admin.payments.details');
Route::post('/admin/payments/{id}/reject', [AdminController::class, 'rejectPayment'])
     ->name('admin.payments.reject');

// Global Logo routes
Route::post('/admin/settings/global-logo', [AdminSettingsController::class, 'updateGlobalLogo'])
     ->name('admin.settings.global.logo');

Route::post('/admin/settings/remove-global-logo', [AdminSettingsController::class, 'removeGlobalLogo'])
     ->name('admin.settings.remove.global.logo');

Route::post('/admin/settings/remove-login-illustration', [AdminSettingsController::class, 'removeLoginIllustration'])
     ->name('admin.settings.remove.login.illustration');

// Remove background image or logo
Route::post('/admin/settings/remove-image', [AdminSettingsController::class, 'removeImage'])
     ->name('admin.settings.remove.image');

// Referral system settings
Route::post('/admin/settings/referral', [AdminSettingsController::class, 'saveReferralSettings'])
     ->name('admin.settings.referral');

// Meeting whitelist settings
Route::post('/admin/settings/meeting-whitelist', [AdminSettingsController::class, 'updateMeetingWhitelist'])
     ->name('admin.settings.meeting.whitelist');

// Module management whitelist settings
Route::post('/admin/settings/module-management-whitelist', [AdminSettingsController::class, 'updateModuleManagementWhitelist'])
     ->name('admin.settings.module.management.whitelist');

// Announcement management whitelist settings
Route::post('/admin/settings/announcement-management-whitelist', [AdminSettingsController::class, 'updateAnnouncementManagementWhitelist'])
     ->name('admin.settings.announcement.management.whitelist');

// Plan Management Routes (Learning Mode Configuration)
Route::prefix('admin/plans')->middleware(['admin.auth'])->group(function () {
    Route::get('/', [AdminSettingsController::class, 'planSettings'])->name('admin.plans.index');
    Route::post('/learning-modes', [AdminSettingsController::class, 'updateLearningModes'])->name('admin.plans.update-learning-modes');
});

// New settings routes for form requirements and UI customization
Route::get('/admin/settings/form-requirements', [AdminSettingsController::class, 'getFormRequirements']);
Route::post('/admin/settings/form-requirements', [AdminSettingsController::class, 'saveFormRequirements']);
Route::get('/admin/settings/student-portal', [AdminSettingsController::class, 'getStudentPortalSettings']);
Route::post('/admin/settings/student-portal', [AdminSettingsController::class, 'saveStudentPortalSettings']);
Route::post('/admin/settings/navbar', [AdminSettingsController::class, 'saveNavbarSettings']);
Route::get('/admin/settings/navbar', [AdminSettingsController::class, 'getNavbarSettings']);
Route::post('/admin/settings/footer', [AdminSettingsController::class, 'saveFooterSettings']);
Route::get('/admin/settings/footer', [AdminSettingsController::class, 'getFooterSettings']);

// Professor and Director Feature Management routes
Route::get('/admin/settings/professor-features', [AdminSettingsController::class, 'getProfessorFeatures']);
Route::post('/admin/settings/professor-features', [AdminSettingsController::class, 'updateProfessorFeatures']);
Route::get('/admin/settings/director-features', [AdminSettingsController::class, 'getDirectorFeatures']);
Route::post('/admin/settings/director-features', [AdminSettingsController::class, 'updateDirectorFeatures']);

// Payment Methods routes
Route::prefix('admin/settings/payment-methods')->middleware(['admin.auth'])->group(function () {
    Route::get('/', [AdminSettingsController::class, 'getPaymentMethods'])->name('admin.settings.payment-methods.index');
    Route::post('/', [AdminSettingsController::class, 'storePaymentMethod'])->name('admin.settings.payment-methods.store');
    Route::put('/{id}', [AdminSettingsController::class, 'updatePaymentMethod'])->name('admin.settings.payment-methods.update');
    Route::delete('/{id}', [AdminSettingsController::class, 'deletePaymentMethod'])->name('admin.settings.payment-methods.delete');
    Route::post('/reorder', [AdminSettingsController::class, 'updatePaymentMethodOrder'])->name('admin.settings.payment-methods.reorder');
});

// Public route for students to get enabled payment methods
Route::get('/payment-methods/enabled', [AdminSettingsController::class, 'getEnabledPaymentMethods'])->name('payment-methods.enabled');

// Education Levels routes
Route::prefix('admin/settings/education-levels')->middleware(['admin.auth'])->group(function () {
    Route::get('/', [EducationLevelController::class, 'index'])->name('admin.settings.education-levels.index');
    Route::post('/', [EducationLevelController::class, 'store'])->name('admin.settings.education-levels.store');
    Route::put('/{id}', [EducationLevelController::class, 'update'])->name('admin.settings.education-levels.update');
    Route::delete('/{id}', [EducationLevelController::class, 'destroy'])->name('admin.settings.education-levels.delete');
});

// Dynamic Field Synchronization routes
Route::prefix('admin/settings/dynamic-fields')->middleware(['admin.auth'])->group(function () {
    Route::post('/sync', [AdminSettingsController::class, 'syncDynamicFields'])->name('admin.settings.dynamic-fields.sync');
    Route::post('/add-column', [AdminSettingsController::class, 'addDynamicColumn'])->name('admin.settings.dynamic-fields.add-column');
    Route::get('/missing-columns', [AdminSettingsController::class, 'getMissingColumns'])->name('admin.settings.dynamic-fields.missing-columns');
});

// Chat functionality routes
Route::middleware(['session.auth'])->group(function () {
    Route::get('/chat/search-users', [ChatController::class, 'searchUsers'])->name('chat.search-users');
    Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/conversations', [ChatController::class, 'getConversations'])->name('chat.conversations');
    
    // API routes for session-based chat
    Route::get('/api/chat/session/users', [ChatController::class, 'getSessionUsers'])->name('api.chat.session.users');
    Route::get('/api/chat/session/search/professors', [ChatController::class, 'getSessionProfessorsAPI'])->name('api.chat.session.search.professors');
    Route::get('/api/chat/session/search/admins', [ChatController::class, 'getSessionAdminsAPI'])->name('api.chat.session.search.admins');
    Route::get('/api/chat/session/search/directors', [ChatController::class, 'getSessionDirectorsAPI'])->name('api.chat.session.search.directors');
    Route::get('/api/chat/session/search/users', [ChatController::class, 'getSessionUsers'])->name('api.chat.session.search.users');
    Route::post('/api/chat/session/send', [ChatController::class, 'sendSessionMessage'])->name('api.chat.session.send');
    Route::get('/api/chat/session/messages', [ChatController::class, 'getSessionMessages'])->name('api.chat.session.messages');
    Route::post('/api/chat/session/clear-history', [ChatController::class, 'clearSessionHistory'])->name('api.chat.session.clear-history');
    Route::get('/api/chat/session/programs', [ChatController::class, 'getSessionPrograms'])->name('api.chat.session.programs');
});

// Legacy chat routes for backwards compatibility
Route::get('/chat/enhanced-search', [ChatController::class, 'enhancedSearch'])->name('chat.enhanced-search');
Route::get('/chat/history/{user_id}', [ChatController::class, 'getChatHistory'])->name('chat.history');
Route::post('/chat/send-message', [ChatController::class, 'sendMessage'])->name('chat.send-message');
Route::post('/chat/save-message', [ChatController::class, 'saveChatMessage'])->name('chat.save-message');

// Enhanced Search functionality routes
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/ajax/search', [SearchController::class, 'search'])->name('ajax.search'); // AJAX-specific route

// Direct search route that completely bypasses middleware and session issues
Route::get('/direct-search', function(\Illuminate\Http\Request $request) {
    try {
        $query = $request->get('query', '');
        $type = $request->get('type', 'all');
        $limit = $request->get('limit', 10);
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query too short',
                'results' => []
            ]);
        }
        
        $results = [];
        
        // Direct database queries without any authentication checks
        if ($type === 'all' || $type === 'students') {
            $students = \Illuminate\Support\Facades\DB::table('students')
                ->select('student_id as id', 'firstname', 'lastname', 'email')
                ->where(function($q) use ($query) {
                    $q->where('firstname', 'LIKE', "%{$query}%")
                      ->orWhere('lastname', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->limit($limit)
                ->get();
                
            foreach ($students as $student) {
                $results[] = [
                    'type' => 'student',
                    'id' => $student->id,
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'email' => $student->email,
                    'url' => '/profile/user/' . $student->id
                ];
            }
        }
        
        if ($type === 'all' || $type === 'professors') {
            $professors = \Illuminate\Support\Facades\DB::table('professors')
                ->select('professor_id as id', 'professor_first_name', 'professor_last_name', 'professor_email')
                ->where(function($q) use ($query) {
                    $q->where('professor_first_name', 'LIKE', "%{$query}%")
                      ->orWhere('professor_last_name', 'LIKE', "%{$query}%")
                      ->orWhere('professor_email', 'LIKE', "%{$query}%");
                })
                ->limit($limit)
                ->get();
                
            foreach ($professors as $professor) {
                $results[] = [
                    'type' => 'professor',
                    'id' => $professor->id,
                    'name' => $professor->professor_first_name . ' ' . $professor->professor_last_name,
                    'email' => $professor->professor_email,
                    'url' => '/profile/professor/' . $professor->id
                ];
            }
        }
        
        if ($type === 'all' || $type === 'programs') {
            $programs = \Illuminate\Support\Facades\DB::table('programs')
                ->select('program_id as id', 'program_name', 'program_description')
                ->where('program_name', 'LIKE', "%{$query}%")
                ->limit($limit)
                ->get();
                
            foreach ($programs as $program) {
                $results[] = [
                    'type' => 'program',
                    'id' => $program->id,
                    'name' => $program->program_name,
                    'description' => $program->program_description,
                    'url' => '/profile/program/' . $program->id
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($results),
            'message' => count($results) > 0 ? 'Search completed successfully' : 'No results found'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Search error: ' . $e->getMessage(), 
            'results' => []
        ]);
    }
})->name('direct.search'); // Direct search route that bypasses all middleware

Route::get('/test-ajax-search', function(\Illuminate\Http\Request $request) {
    try {
        $controller = new \App\Http\Controllers\SearchController();
        return $controller->search($request);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Search error: ' . $e->getMessage(), 
            'results' => []
        ]);
    }
})->name('test.ajax.search'); // Test route that calls search controller directly

Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// New Universal Search System routes
Route::get('/search/universal', [SearchController::class, 'universalSearch'])->name('search.universal');
Route::get('/search/profile', [SearchController::class, 'getProfile'])->name('search.profile');

// Profile pages for search results - make them accessible with session auth
Route::get('/profile/user/{id}', [SearchController::class, 'showUserProfile'])->name('profile.user');
Route::get('/profile/professor/{id}', [SearchController::class, 'showProfessorProfile'])->name('profile.professor');
Route::get('/profile/program/{id}', [SearchController::class, 'showProgramProfile'])->name('profile.program');

// Simple test route to debug the issue
Route::get('/test-program/{id}', function($id) {
    $program = \App\Models\Program::findOrFail($id);
    return "Program: " . $program->program_name . " (ID: " . $id . ")";
})->name('test.program');

// API routes for AJAX search
Route::prefix('api/search')->group(function () {
    Route::get('/', [SearchController::class, 'universalSearch'])->name('api.search');
    Route::get('/profile', [SearchController::class, 'getProfile'])->name('api.search.profile');
    Route::get('/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');
});

// Homepage customization routes
Route::get('/admin/settings/homepage', [AdminSettingsController::class, 'getHomepageSettings']);
Route::post('/admin/settings/homepage', [AdminSettingsController::class, 'saveHomepageSettings']);

// Dynamic Field Management routes
Route::post('/admin/settings/form-requirements/toggle-active', [AdminSettingsController::class, 'toggleFieldActive'])
     ->name('admin.settings.form-requirements.toggle-active');
Route::post('/admin/settings/form-requirements/add-column', [AdminSettingsController::class, 'addDynamicColumn'])
     ->name('admin.settings.form-requirements.add-column');
Route::get('/admin/settings/form-requirements/preview/{programType}', [AdminSettingsController::class, 'previewForm'])
     ->name('admin.settings.form-requirements.preview');

// Form Requirements Database Sync routes
Route::get('/admin/settings/form-requirements-sync', [App\Http\Controllers\FormRequirementSyncController::class, 'index'])
     ->name('admin.settings.form-requirements-sync');
Route::post('/admin/settings/form-requirements-sync/sync', [App\Http\Controllers\FormRequirementSyncController::class, 'sync'])
     ->name('admin.settings.form-requirements-sync.sync');
Route::get('/admin/settings/form-requirements-sync/status', [App\Http\Controllers\FormRequirementSyncController::class, 'status'])
     ->name('admin.settings.form-requirements-sync.status');

// Plan Settings routes
Route::get('/admin/settings/plan-settings', [AdminSettingsController::class, 'getPlanSettings']);
Route::post('/admin/settings/plan-settings', [AdminSettingsController::class, 'savePlanSettings']);

// Module ordering routes
Route::post('/admin/modules/update-sort-order', [ModuleController::class, 'updateSortOrder'])
     ->name('admin.modules.updateOrder');
Route::get('/admin/modules/ordered', [ModuleController::class, 'getOrderedModules'])
     ->name('admin.modules.ordered');

// Sidebar settings route
Route::post('/admin/settings/sidebar', [AdminSettingsController::class, 'updateSidebar'])
     ->name('admin.settings.sidebar');

/*
|--------------------------------------------------------------------------
| Admin Analytics
|--------------------------------------------------------------------------
*/
// Analytics dashboard
Route::get('/admin/analytics', [AdminAnalyticsController::class, 'index'])
     ->name('admin.analytics.index');

// Analytics data endpoints
Route::get('/admin/analytics/data', [AdminAnalyticsController::class, 'getData'])
     ->name('admin.analytics.data');

Route::get('/admin/analytics/batches', [AdminAnalyticsController::class, 'getBatches'])
     ->name('admin.analytics.batches');

Route::get('/admin/analytics/subjects', [AdminAnalyticsController::class, 'getSubjects'])
     ->name('admin.analytics.subjects');

Route::get('/admin/analytics/programs', [AdminAnalyticsController::class, 'getPrograms'])
     ->name('admin.analytics.programs');

Route::get('/admin/analytics/student/{id}', [AdminAnalyticsController::class, 'getStudentDetail'])
     ->name('admin.analytics.student');

Route::get('/admin/analytics/subject/{id}', [AdminAnalyticsController::class, 'getSubjectDetail'])
     ->name('admin.analytics.subject.detail');

// Export routes
Route::get('/admin/analytics/export', [AdminAnalyticsController::class, 'export'])
     ->name('admin.analytics.export');

Route::get('/admin/analytics/subject-report', [AdminAnalyticsController::class, 'generateSubjectReport'])
     ->name('admin.analytics.subject-report');

// Board Passer Routes  
Route::get('/admin/analytics/students-list', [AdminAnalyticsController::class, 'getStudentsList'])
     ->name('admin.analytics.students-list');
Route::get('/admin/analytics/board-exams', [AdminAnalyticsController::class, 'getBoardExams'])
     ->name('admin.analytics.board-exams');
Route::post('/admin/analytics/upload-board-passers', [AdminAnalyticsController::class, 'uploadBoardPassers'])
     ->name('admin.analytics.upload-board-passers');
Route::post('/admin/analytics/add-board-passer', [AdminAnalyticsController::class, 'addBoardPasser'])
     ->name('admin.analytics.add-board-passer');
Route::get('/admin/analytics/download-template', [AdminAnalyticsController::class, 'downloadTemplate'])
     ->name('admin.analytics.download-template');
Route::get('/admin/analytics/board-passer-stats', [AdminAnalyticsController::class, 'getBoardPasserStats'])
     ->name('admin.analytics.board-passer-stats');

// Board Passers Management Routes
Route::prefix('admin/board-passers')->name('admin.board-passers.')->group(function () {
    Route::get('/', [App\Http\Controllers\BoardPassersController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\BoardPassersController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\BoardPassersController::class, 'show'])->name('show');
    Route::put('/{id}', [App\Http\Controllers\BoardPassersController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\BoardPassersController::class, 'destroy'])->name('destroy');
    Route::get('/api/stats', [App\Http\Controllers\BoardPassersController::class, 'getStats'])->name('stats');
    Route::get('/api/students', [App\Http\Controllers\BoardPassersController::class, 'getStudentsList'])->name('students-list');
    Route::get('/download/template', [App\Http\Controllers\BoardPassersController::class, 'downloadTemplate'])->name('download-template');
});

/*
|--------------------------------------------------------------------------
| Admin Directors
|--------------------------------------------------------------------------
*/
// Directors list
Route::get('/admin/directors', [AdminDirectorController::class, 'index'])
     ->name('admin.directors.index');

// View archived directors (must come before other dynamic routes)
Route::get('/admin/directors/archived', [AdminDirectorController::class, 'archived'])
     ->name('admin.directors.archived');

// Show "Add New Director" form
Route::get('/admin/directors/create', [AdminDirectorController::class, 'create'])
     ->name('admin.directors.create');

// Store new director
Route::post('/admin/directors', [AdminDirectorController::class, 'store'])
     ->name('admin.directors.store');

// Show director details
Route::get('/admin/directors/{director:directors_id}', [AdminDirectorController::class, 'show'])
     ->name('admin.directors.show');

// Show edit director form
Route::get('/admin/directors/{director:directors_id}/edit', [AdminDirectorController::class, 'edit'])
     ->name('admin.directors.edit');

// Update director
Route::put('/admin/directors/{director:directors_id}', [AdminDirectorController::class, 'update'])
     ->name('admin.directors.update');

// Archive director
Route::patch('/admin/directors/{director:directors_id}/archive', [AdminDirectorController::class, 'archive'])
     ->name('admin.directors.archive');

// Restore archived director
Route::patch('/admin/directors/{director:directors_id}/restore', [AdminDirectorController::class, 'restore'])
     ->name('admin.directors.restore');

// Delete director permanently
Route::delete('/admin/directors/{director:directors_id}', [AdminDirectorController::class, 'destroy'])
     ->name('admin.directors.destroy');

// Assign program to director
Route::post('/admin/directors/{director:directors_id}/assign-program', [AdminDirectorController::class, 'assignProgram'])
     ->name('admin.directors.assign-program');

// Unassign program from director
Route::post('/admin/directors/{director:directors_id}/unassign-program', [AdminDirectorController::class, 'unassignProgram'])
     ->name('admin.directors.unassign-program');

// Director Dashboard Routes
Route::get('/director/dashboard', function() {
    return redirect('/admin-dashboard');
})->name('director.dashboard');

Route::get('/director/profile', [DirectorController::class, 'profile'])
     ->name('director.profile');
Route::put('/director/profile', [DirectorController::class, 'updateProfile'])
     ->name('director.profile.update');

/*
|--------------------------------------------------------------------------
| Admin Students List
|--------------------------------------------------------------------------
*/
// Students list
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/students', [AdminStudentListController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/export', [AdminStudentListController::class, 'export'])->name('admin.students.export');
    Route::get('/admin/students/archived', [AdminStudentListController::class, 'archived'])->name('admin.students.archived');
    Route::post('/admin/students/{id}/archive', [AdminStudentListController::class, 'archive'])->name('admin.students.archive');
    Route::post('/admin/students/{id}/unarchive', [AdminStudentListController::class, 'unarchive'])->name('admin.students.unarchive');
    // Admin: Show a single student profile
    Route::get('/admin/students/{student}', [App\Http\Controllers\AdminStudentListController::class, 'show'])->name('admin.students.show');
    // Admin: Approve and disapprove students
    Route::patch('/admin/students/{student}/approve', [App\Http\Controllers\AdminStudentListController::class, 'approve'])->name('admin.students.approve');
    Route::patch('/admin/students/{student}/disapprove', [App\Http\Controllers\AdminStudentListController::class, 'disapprove'])->name('admin.students.disapprove');
    // Admin: Restore archived students
    Route::patch('/admin/students/{student}/restore', [App\Http\Controllers\AdminStudentListController::class, 'restore'])->name('admin.students.restore');
    
    // Debug routes for CSV export
    Route::get('/admin/students/debug-export', [App\Http\Controllers\DebugStudentExportController::class, 'debugExport'])->name('admin.students.debug-export');
    Route::get('/admin/students/test-csv', [App\Http\Controllers\DebugStudentExportController::class, 'testCsvDownload'])->name('admin.students.test-csv');
});

/*
|--------------------------------------------------------------------------
| Admin Professors
|--------------------------------------------------------------------------
*/
// Professor routes
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/professors', [AdminProfessorController::class, 'index'])->name('admin.professors.index');
    Route::get('/admin/professors/create', [AdminProfessorController::class, 'create'])->name('admin.professors.create');
    Route::post('/admin/professors', [AdminProfessorController::class, 'store'])->name('admin.professors.store');
    Route::get('/admin/professors/{id}', [AdminProfessorController::class, 'show'])->name('admin.professors.show');
    Route::put('/admin/professors/{id}', [AdminProfessorController::class, 'update'])->name('admin.professors.update');
    Route::delete('/admin/professors/{id}', [AdminProfessorController::class, 'destroy'])->name('admin.professors.delete');
    
    Route::get('/admin/professors/archived', [AdminProfessorController::class, 'archived'])
         ->name('admin.professors.archived');

    Route::get('/admin/professors/{professor}/edit', [AdminProfessorController::class, 'edit'])
         ->name('admin.professors.edit');

    Route::get('/admin/professors/{professor}/meetings', [AdminProfessorController::class, 'viewMeetings'])
         ->name('admin.professors.meetings');

    Route::put('/admin/professors/{professor}', [AdminProfessorController::class, 'update'])
         ->name('admin.professors.update');

    Route::patch('/admin/professors/{professor}/archive', [AdminProfessorController::class, 'archive'])
         ->name('admin.professors.archive');

    Route::patch('/admin/professors/{professor}/restore', [AdminProfessorController::class, 'restore'])
         ->name('admin.professors.restore');

    Route::delete('/admin/professors/{professor}', [AdminProfessorController::class, 'destroy'])
         ->name('admin.professors.destroy');

    Route::get('/admin/professors/{id}/profile', [AdminProfessorController::class, 'showProfile'])
         ->name('admin.professors.profile');

    Route::post('/admin/professors/{professor}/programs/{program}/video', [AdminProfessorController::class, 'updateVideoLink'])
         ->name('admin.professors.video.update');

    // Professor batch assignment routes
    Route::post('/admin/professors/{professor}/assign-batch', [AdminProfessorController::class, 'assignBatch'])
         ->name('admin.professors.assign-batch');

    Route::delete('/admin/professors/{professor}/unassign-batch/{batch}', [AdminProfessorController::class, 'unassignBatch'])
         ->name('admin.professors.unassign-batch');

    // Professor programs API route for meeting creation
    Route::get('/admin/professors/{professor}/programs', [AdminProfessorController::class, 'getProfessorPrograms'])
         ->name('admin.professors.programs');
    Route::get('/admin/professors/{professor}/videos', [AdminProfessorController::class, 'getVideos'])
         ->name('admin.professors.videos');
    
    // Professor meeting creation route
    Route::post('/admin/professors/{professor}/meetings', [AdminProfessorController::class, 'createMeeting'])
         ->name('admin.professors.createMeeting');
});

Route::post('/admin/settings/logo', [AdminSettingsController::class, 'updateGlobalLogo']);
Route::post('/admin/settings/favicon', [AdminSettingsController::class, 'updateFavicon']);
Route::get('/admin/settings/enrollment-form/{programType}', [AdminSettingsController::class, 'generateEnrollmentForm']);

// Temporary debug route for payment history (bypassing middleware)
Route::get('/debug-payment-history', function() {
    $controller = new AdminController();
    return $controller->paymentHistory();
});

// Payment management routes
Route::get('/admin-student-registration/payment/pending', [AdminController::class, 'paymentPending'])
     ->name('admin.student.registration.payment.pending');
Route::get('/admin-student-registration/payment/history', [AdminController::class, 'paymentHistory'])
     ->name('admin.student.registration.payment.history');

// Mark enrollment as paid
Route::post('/admin/enrollment/{id}/mark-paid', [AdminController::class, 'markAsPaid'])
     ->name('admin.enrollment.mark-paid');

// Approve enrollment
Route::post('/admin/enrollment/{id}/approve', [AdminController::class, 'approveEnrollment'])
     ->name('admin.enrollment.approve');

// Payment history details for modal
Route::get('/admin/payment-history/{id}/details', [App\Http\Controllers\AdminController::class, 'getPaymentHistoryDetailsJson'])->name('admin.payment-history.details');

// ... existing code ...
Route::post('/admin/enrollment/{enrollmentId}/undo-payment', [App\Http\Controllers\AdminController::class, 'undoPendingPayment'])->name('admin.enrollment.undo-payment');
// ... existing code ...

// ... existing code ...
Route::post('/admin/payment-history/{paymentHistoryId}/undo', [App\Http\Controllers\AdminController::class, 'undoPaymentHistory'])->name('admin.payment-history.undo');
// ... existing code ...

/*
|--------------------------------------------------------------------------
| Admin Assignment Submissions
|--------------------------------------------------------------------------
*/
// View assignment submissions
Route::get('/admin/submissions', [AdminController::class, 'viewSubmissions'])
     ->name('admin.submissions');

// Grade assignment submission
Route::post('/admin/submissions/{id}/grade', [AdminController::class, 'gradeSubmission'])
     ->name('admin.submissions.grade');

/*
|--------------------------------------------------------------------------
| Admin Certificates
|--------------------------------------------------------------------------
*/
// Complete Certificate Management Routes
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/certificates', [CertificateController::class, 'index'])->name('admin.certificates');
    Route::get('/admin/certificates/{student}/preview', [CertificateController::class, 'preview'])->name('admin.certificates.preview');
    Route::post('/admin/certificates/{student}/generate', [CertificateController::class, 'generate'])->name('admin.certificates.generate');
    Route::post('/admin/certificates/{student}/approve', [CertificateController::class, 'approve'])->name('admin.certificates.approve');
    Route::post('/admin/certificates/{student}/reject', [CertificateController::class, 'reject'])->name('admin.certificates.reject');
    Route::get('/admin/certificates/{student}/download', [CertificateController::class, 'adminDownload'])->name('admin.certificates.download');
    Route::post('/admin/certificates/bulk-approve', [CertificateController::class, 'bulkApprove'])->name('admin.certificates.bulk-approve');
});

/*
|--------------------------------------------------------------------------
| Professor Authentication Routes (Redirected to Unified Login)
|--------------------------------------------------------------------------
*/
// Redirect old professor login to unified login
Route::get('/professor/login', function() {
    return redirect()->route('login');
})->name('professor.login');

// Professor logout should use the unified logout
Route::post('/professor/logout', [UnifiedLoginController::class, 'logout'])
     ->name('professor.logout');

/*
|--------------------------------------------------------------------------
| Professor Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['professor.auth'])
    ->prefix('professor')
    ->name('professor.')
    ->group(function () {

    Route::get('/dashboard', [ProfessorDashboardController::class, 'index'])
         ->name('dashboard');
    
    Route::get('chat', [\App\Http\Controllers\Professor\ChatController::class,'index'])
        ->name('chat');

    Route::get('/programs', [ProfessorDashboardController::class, 'programs'])
         ->name('programs');

    Route::get('/programs/{program}', [ProfessorDashboardController::class, 'programDetails'])
         ->name('program.details');

    Route::post('/programs/{program}/video', [ProfessorDashboardController::class, 'updateVideo'])
         ->name('program.update-video');

    Route::get('reports/attendance', [ReportsController::class, 'attendance'])
        ->name('reports.attendance');

    Route::get('reports/grades', [ReportsController::class, 'grades'])
        ->name('reports.grades');
    
    // Profile Management
    Route::get('/profile', [ProfessorDashboardController::class, 'profile'])
         ->name('profile');
    Route::put('/profile', [ProfessorDashboardController::class, 'updateProfile'])
         ->name('profile.update');
    Route::post('/profile/photo', [ProfessorDashboardController::class, 'updateProfilePhoto'])
         ->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfessorDashboardController::class, 'removeProfilePhoto'])
         ->name('profile.photo.remove');
    
    // Student Management
    // My batches (only those this prof owns)
    Route::get(
        '/students/batches',
        [ProfessorDashboardController::class, 'studentBatches']
    )->name('students.batches');

    Route::get('/students', [ProfessorDashboardController::class, 'studentList'])
         ->name('students.index');
    Route::post('/students/{student}/grade', [ProfessorDashboardController::class, 'gradeStudent'])
         ->name('students.grade');
    
    // Professor-accessible profile routes (for search results)
    Route::get('/view/program/{id}', [SearchController::class, 'showProgramProfile'])->name('professor.view.program');
    Route::get('/view/student/{id}', [SearchController::class, 'showUserProfile'])->name('professor.view.student');
    Route::get('/view/professor/{id}', [SearchController::class, 'showProfessorProfile'])->name('professor.view.professor');
    
    // Meeting Management (replacing Attendance)
    Route::get('/attendance', [\App\Http\Controllers\ProfessorMeetingController::class, 'index'])
         ->name('attendance'); // Legacy route for backward compatibility
    Route::get('/meetings', [\App\Http\Controllers\ProfessorMeetingController::class, 'index'])
         ->name('meetings');
    Route::post('/meetings', [\App\Http\Controllers\ProfessorMeetingController::class, 'store'])
         ->name('meetings.store');
    Route::get('/meetings/{meeting}', [\App\Http\Controllers\ProfessorMeetingController::class, 'show'])
         ->name('meetings.show');
    Route::put('/meetings/{meeting}', [\App\Http\Controllers\ProfessorMeetingController::class, 'update'])
         ->name('meetings.update');
    Route::delete('/meetings/{meeting}', [\App\Http\Controllers\ProfessorMeetingController::class, 'destroy'])
         ->name('meetings.destroy');
    Route::post('/meetings/{meeting}/start', [\App\Http\Controllers\ProfessorMeetingController::class, 'start'])
         ->name('meetings.start');
    Route::post('/meetings/{meeting}/finish', [\App\Http\Controllers\ProfessorMeetingController::class, 'finish'])
         ->name('meetings.finish');
    Route::get('/meetings/{meeting}/stats', [\App\Http\Controllers\ProfessorMeetingController::class, 'stats'])
         ->name('meetings.stats');
    Route::get('/meetings/reports', [\App\Http\Controllers\ProfessorMeetingController::class, 'reports'])
         ->name('meetings.reports');
    Route::post('/meetings/{meeting}/start', [\App\Http\Controllers\ProfessorMeetingController::class, 'start'])
         ->name('meetings.start');
    Route::post('/meetings/{meeting}/finish', [\App\Http\Controllers\ProfessorMeetingController::class, 'finish'])
         ->name('meetings.finish');
    
    // Additional professor routes for meetings/settings
    Route::get('/settings', [ProfessorDashboardController::class, 'settings'])
         ->name('settings');
    Route::put('/settings', [ProfessorDashboardController::class, 'updateSettings'])
         ->name('settings.update');
    
    // Enhanced Grading Management
    Route::get('/grading', [\App\Http\Controllers\Professor\GradingController::class, 'index'])
         ->name('grading');
    Route::post('/grading', [\App\Http\Controllers\Professor\GradingController::class, 'store'])
         ->name('grading.store');
    Route::get('/grading/student/{student}', [\App\Http\Controllers\Professor\GradingController::class, 'studentDetails'])
         ->name('grading.student-details');
    Route::post('/grading/assignment/{student}/{assignment}', [\App\Http\Controllers\Professor\GradingController::class, 'gradeAssignment'])
         ->name('grading.assignment');
    Route::post('/grading/activity/{student}/{activity}', [\App\Http\Controllers\Professor\GradingController::class, 'gradeActivity'])
         ->name('grading.activity');
    Route::post('/grading/quiz/{student}/{quiz}', [\App\Http\Controllers\Professor\GradingController::class, 'gradeQuiz'])
         ->name('grading.quiz');
    Route::post('/assignments/create', [\App\Http\Controllers\Professor\GradingController::class, 'createAssignment'])
         ->name('assignments.create');
    Route::get('/assignments/create', [\App\Http\Controllers\Professor\GradingController::class, 'createAssignmentForm'])
         ->name('assignments.create');
    Route::post('/activities/create', [\App\Http\Controllers\Professor\GradingController::class, 'createActivity'])
         ->name('activities.create');
    Route::post('/grading/export', [\App\Http\Controllers\Professor\GradingController::class, 'exportGrades'])
         ->name('grading.export');
    
    // Chat routes
    Route::post('/chat/send', [\App\Http\Controllers\Professor\ChatController::class, 'sendMessage'])
         ->name('chat.send');
    
    // AI Quiz Generator
    Route::get('/quiz-generator', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'index'])
         ->name('quiz-generator');
    Route::post('/quiz-generator/generate', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'generate'])
         ->name('quiz-generator.generate');
    Route::get('/quiz-generator/preview/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'preview'])
         ->name('quiz-generator.preview');
    Route::get('/quiz-generator/export/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'export'])
         ->name('quiz-generator.export');
    Route::delete('/quiz-generator/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'delete'])
         ->name('quiz-generator.delete');
    Route::post('/quiz-generator/{quiz}/publish', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'publish'])
         ->name('quiz-generator.publish');
    Route::get('/quiz-generator/questions/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'viewQuestions'])
         ->name('quiz-generator.questions');
    
    // Quiz editor routes
    Route::get('/quiz-generator/{quiz}/edit', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'editQuestions'])
         ->name('quiz-generator.edit');
    Route::put('/quiz-generator/{quiz}/questions/{question}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'updateQuestion'])
         ->name('quiz-generator.question.update');
    Route::post('/quiz-generator/{quiz}/questions', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'addQuestion'])
         ->name('quiz-generator.question.add');
    Route::delete('/quiz-generator/{quiz}/questions/{question}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'deleteQuestion'])
         ->name('quiz-generator.question.delete');
    
    // Quiz generator question management routes
    Route::get('/quiz-generator/questions/{quiz}/modal-content', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'getModalQuestions'])
         ->name('quiz-generator.questions.modal');
    Route::get('/quiz-generator/api/questions/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'getQuestionsForModal'])
         ->name('quiz-generator.api.questions');
    Route::post('/quiz-generator/save', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'save'])
         ->name('quiz-generator.save');
    Route::put('/quiz-generator/questions/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'updateQuestions'])
         ->name('quiz-generator.questions.update');
    
    // Quiz generator AJAX routes
    Route::get('/quiz-generator/modules/{programId}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'getModulesByProgram'])
         ->name('quiz-generator.modules');
    Route::get('/quiz-generator/courses/{moduleId}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'getCoursesByModule'])
         ->name('quiz-generator.courses');
    Route::get('/quiz-generator/contents/{courseId}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'getContentByCourse'])
         ->name('quiz-generator.contents');

    // New overhauled quiz generator routes
    Route::post('/quiz-generator/generate-from-document', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'generateFromDocument'])
         ->name('quiz-generator.generate-from-document');
    Route::post('/quiz-generator/generate-ai-questions', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'generateAIQuestions'])
         ->name('quiz-generator.generate-ai-questions');
    Route::post('/quiz-generator/save-quiz', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'saveQuizWithQuestions'])
         ->name('quiz-generator.save-quiz');
    Route::put('/quiz-generator/update-quiz/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'updateQuizWithQuestions'])
         ->name('quiz-generator.update-quiz');
    
    // Quiz edit and preview routes
    Route::get('/quiz-generator/edit/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'editQuiz'])
         ->name('quiz-generator.edit');
    Route::get('/quiz-generator/preview/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'previewQuiz'])
         ->name('quiz-generator.preview');
    
    // Quiz status management routes
    Route::post('/quiz-generator/{quiz}/publish', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'publishQuiz'])
         ->name('quiz-generator.publish');
    Route::post('/quiz-generator/{quiz}/archive', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'archiveQuiz'])
         ->name('quiz-generator.archive');
    Route::post('/quiz-generator/{quiz}/restore', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'restoreQuiz'])
         ->name('quiz-generator.restore');
    Route::post('/quiz-generator/{quiz}/draft', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'moveToDraft'])
         ->name('quiz-generator.draft');
    Route::delete('/quiz-generator/{quiz}/delete', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'deleteQuiz'])
         ->name('quiz-generator.delete');
    Route::get('/quiz-generator/edit/{quiz}', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'getQuizForEdit'])
         ->name('quiz-generator.edit');
         
    // Temporary test route without authentication
    Route::post('/quiz-generator/test-generate', function(\Illuminate\Http\Request $request) {
        // Temporarily set session for testing
        session(['logged_in' => true, 'professor_id' => 8, 'user_role' => 'professor']);
        
        $geminiService = app(\App\Services\GeminiQuizService::class);
        $controller = new \App\Http\Controllers\Professor\QuizGeneratorController($geminiService);
        return $controller->generate($request);
    })->name('quiz-generator.test-generate');
    
    // Professor Module Management Routes
    Route::get('modules', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'index'])
         ->name('modules.index');
    Route::post('modules', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'store'])
         ->name('modules.store');
    Route::put('modules/{module}', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'update'])
         ->name('modules.update');
    Route::get('modules/{module}/edit', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'edit'])
         ->name('modules.edit');
    Route::post('modules/add-content', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'addContent'])
         ->name('modules.add-content');
    // Course content upload page - only need one route since we're inside the professor prefix group
    Route::get('modules/course-content-upload', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'showCourseContentUploadPage'])
         ->name('modules.course-content-upload');

    // Store new course content (for form action in Blade view)
    Route::post('modules/course-content', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'storeCourseContent'])
         ->name('modules.course-content-store');
    Route::post('modules/{module}/toggle-archive', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'toggleArchive'])
         ->name('modules.toggle-archive');
    Route::get('modules/archived', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'archived'])
         ->name('modules.archived');
    
    // Professor Module AJAX Routes
    // This route expects a 'program_id' query parameter, not 'module_id'.
    Route::get('modules/by-program', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'getModulesByProgram'])
         ->name('modules.by-program');
    Route::get('modules/batches', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'getBatchesByProgram'])
         ->name('modules.batches');
    Route::get('modules/courses', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'getCoursesByModule'])
         ->name('modules.courses');
    // Dynamic module-specific AJAX routes for courses and content
    Route::get('modules/{module}/courses', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'getCoursesByModule'])
         ->name('modules.courses.by-module');
    Route::get('modules/{module}/content', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'getModuleContent'])
         ->name('modules.content');
    // Course-specific content routes - maintain both URLs for compatibility
    Route::get('courses/{course}/content', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'getCourseContent'])
         ->name('courses.content');
    // This route is accessed from the front-end JavaScript
    Route::get('courses/{course}/edit', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'editCourse'])
         ->name('courses.edit');
    // Professor Courses Store Route (for Blade form action)
    Route::post('courses', [\App\Http\Controllers\Professor\ProfessorCourseController::class, 'store'])
         ->name('courses.store');
    
    // Content API routes
    Route::get('/content/{id}', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'getContent'])
         ->name('content.get');
    Route::put('/content/{id}', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'updateContent'])
         ->name('content.update');
    Route::delete('/content/{id}', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'deleteContent'])
         ->name('content.delete');
    
    // Archive routes for courses and content
    Route::post('/courses/{id}/archive', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'archiveCourse'])
         ->name('courses.archive');
    Route::post('/content/{id}/archive', [\App\Http\Controllers\Professor\ProfessorModuleController::class, 'archiveContent'])
         ->name('content.archive');
    
    // Test file extraction route
    Route::post('/quiz-generator/test-file-extraction', function(\Illuminate\Http\Request $request) {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['success' => false, 'message' => 'No file uploaded']);
            }
            
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $text = '';
            
            \Illuminate\Support\Facades\Log::info('Testing file extraction', [
                'filename' => $file->getClientOriginalName(),
                'extension' => $extension,
                'size' => $file->getSize()
            ]);
            
            // Handle image files (JPG, PNG)
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                try {
                    $tempPath = $file->storeAs('temp', uniqid().'.'.$extension);
                    $fullPath = storage_path('app/'.$tempPath);
                    
                    // Use Tesseract OCR for image text extraction
                    $text = shell_exec('tesseract "'.$fullPath.'" stdout 2>&1');
                    
                    // Clean up temp file
                    @unlink($fullPath);
                    
                    \Illuminate\Support\Facades\Log::info('Image OCR result', ['text_length' => strlen($text)]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Image OCR failed', ['error' => $e->getMessage()]);
                    $text = '';
                }
            } 
            // Handle PDF files
            else if ($extension === 'pdf') {
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($file->getRealPath());
                    $text = $pdf->getText();
                    
                    \Illuminate\Support\Facades\Log::info('PDF extraction result', ['text_length' => strlen($text)]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('PDF extraction failed', ['error' => $e->getMessage()]);
                    $text = '';
                }
            } 
            // Handle DOCX files
            else if ($extension === 'docx') {
                try {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getRealPath());
                    $text = '';
                    
                    foreach ($phpWord->getSections() as $section) {
                        foreach ($section->getElements() as $element) {
                            if (method_exists($element, 'getText')) {
                                $text .= $element->getText() . "\n";
                            }
                        }
                    }
                    
                    \Illuminate\Support\Facades\Log::info('DOCX extraction result', ['text_length' => strlen($text)]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('DOCX extraction failed', ['error' => $e->getMessage()]);
                    $text = '';
                }
            } 
            // Handle TXT files
            else if ($extension === 'txt') {
                try {
                    $text = file_get_contents($file->getRealPath());
                    
                    \Illuminate\Support\Facades\Log::info('TXT extraction result', ['text_length' => strlen($text)]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('TXT extraction failed', ['error' => $e->getMessage()]);
                    $text = '';
                }
            }
            
            $text = trim($text);
            
            return response()->json([
                'success' => true,
                'text_length' => strlen($text),
                'text_preview' => substr($text, 0, 500) . '...',
                'extension' => $extension
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('File extraction test failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('quiz-generator.test-file-extraction');
    
    // API route to fetch quizzes for testing
    Route::get('/api/test-quizzes', function() {
        $quizzes = \App\Models\Quiz::with('questions')->orderBy('created_at', 'desc')->get();
        return response()->json(['quizzes' => $quizzes]);
    });
    Route::put('/grading/{grade}', [\App\Http\Controllers\ProfessorGradingController::class, 'update'])
         ->name('grading.update');
    Route::delete('/grading/{grade}', [\App\Http\Controllers\ProfessorGradingController::class, 'destroy'])
         ->name('grading.destroy');
    Route::get('/grading/student/{student}', [\App\Http\Controllers\ProfessorGradingController::class, 'studentDetails'])
         ->name('grading.student');
    Route::post('/quiz-generator/save-manual', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'saveManualQuiz'])
         ->name('quiz-generator.save-manual');
    Route::post('/quiz-generator/question/options', [\App\Http\Controllers\Professor\QuizGeneratorController::class, 'getQuestionOptions'])
         ->name('quiz-generator.question.options');
    // ...existing professor routes...
    Route::post('/grading/auto-grade-quizzes', [\App\Http\Controllers\Professor\GradingController::class, 'autoGradeQuizzes'])->name('grading.auto-grade-quizzes');
    Route::get('/assignments/create', [\App\Http\Controllers\Professor\GradingController::class, 'createAssignmentForm'])->name('assignments.create');
    
    // Professor Announcements Routes
    Route::get('/announcements', [ProfessorAnnouncementController::class, 'index'])
         ->name('announcements.index');
    Route::get('/announcements/create', [ProfessorAnnouncementController::class, 'create'])
         ->name('announcements.create');
    Route::post('/announcements', [ProfessorAnnouncementController::class, 'store'])
         ->name('announcements.store');
    Route::get('/announcements/{announcement}', [ProfessorAnnouncementController::class, 'show'])
         ->name('announcements.show');
    Route::get('/announcements/{announcement}/edit', [ProfessorAnnouncementController::class, 'edit'])
         ->name('announcements.edit');
    Route::put('/announcements/{announcement}', [ProfessorAnnouncementController::class, 'update'])
         ->name('announcements.update');
    Route::delete('/announcements/{announcement}', [ProfessorAnnouncementController::class, 'destroy'])
         ->name('announcements.destroy');

    // Professor Submissions Routes
    Route::get('/submissions', [\App\Http\Controllers\Professor\SubmissionController::class, 'index'])
         ->name('submissions.index');
    Route::get('/submissions/{id}/details', [\App\Http\Controllers\Professor\SubmissionController::class, 'details'])
         ->name('submissions.details');
    Route::get('/submissions/{id}/download', [\App\Http\Controllers\Professor\SubmissionController::class, 'download'])
         ->name('submissions.download');
    Route::post('/submissions/{id}/grade', [\App\Http\Controllers\Professor\SubmissionController::class, 'gradeSubmission'])
         ->name('submissions.grade');
});

// API routes for student and professor data
Route::get('/api/student/enrolled-programs', function() {
    $userId = session('user_id');
    $userRole = session('user_role', 'guest');
    
    if (!$userId || $userRole !== 'student') {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    try {
        $programs = collect();
        
        if (class_exists('App\Models\Student')) {
            $student = \App\Models\Student::where('user_id', $userId)->first();
            
            if ($student) {
                $enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
                    ->with('program')
                    ->get();
                
                $programs = $enrollments->map(function($enrollment) {
                    return [
                        'program_id' => $enrollment->program->program_id,
                        'program_name' => $enrollment->program->program_name,
                        'program_description' => $enrollment->program->program_description
                    ];
                });
            }
        }
        
        return response()->json([
            'success' => true,
            'programs' => $programs->toArray()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to fetch enrolled programs',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/api/professor/assigned-programs', function() {
    $userId = session('user_id');
    $userRole = session('user_role', 'guest');
    
    if (!$userId || $userRole !== 'professor') {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    try {
        $programs = collect();
        
        if (class_exists('App\Models\Professor')) {
            $professor = \App\Models\Professor::where('user_id', $userId)->first();
            
            if ($professor) {
                $batches = \App\Models\Batch::where('professor_id', $professor->professor_id)
                    ->with('program')
                    ->get();
                
                $programs = $batches->map(function($batch) {
                    return [
                        'program_id' => $batch->program->program_id,
                        'program_name' => $batch->program->program_name,
                        'program_description' => $batch->program->program_description
                    ];
                })->unique('program_id');
            }
        }
        
        return response()->json([
            'success' => true,
            'programs' => $programs->values()->toArray()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to fetch assigned programs',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Chat test routes
Route::get('/test-chat-api', function() {
    return response()->json([
        'success' => true,
        'message' => 'Chat API is working!',
        'data' => [
            [
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'student'
            ],
            [
                'id' => 2,
                'name' => 'Test Professor',
                'email' => 'professor@example.com',
                'role' => 'professor'
            ]
        ]
     ]);
});

Route::post('/test-chat-send', function(Request $request) {
    return response()->json([
        'success' => true,
        'id' => rand(1, 1000),
        'message' => 'Message sent successfully',
        'data' => [
            'id' => rand(1, 1000),
            'sender_id' => 1,
            'receiver_id' => $request->input('receiver_id', 2),
            'message' => $request->input('message', 'Test message'),
            'sent_at' => now()->toISOString(),
            'sender_name' => 'Test User'
        ]
    ]);
});

Route::get('/test-chat-function', function() {
    return view('chat-function-test');
});

// Chat test route
Route::get('/test-chat', function () {
    return view('test-chat');
});

// Admin chat test route
Route::get('/admin/chat-test', function() {
    return view('admin.chat-test');
})->name('admin.chat.test');

// Temporary test routes for chat search (bypassing authentication)
Route::get('/test/chat/search/professors', function (Request $request) {
    $search = $request->get('search', '');
    
    try {
        $query = \App\Models\Professor::query();
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('professor_name', 'like', '%' . $search . '%')
                  ->orWhere('professor_email', 'like', '%' . $search . '%')
                  ->orWhere('professor_first_name', 'like', '%' . $search . '%')
                  ->orWhere('professor_last_name', 'like', '%' . $search . '%');
            });
        }
        
        $results = $query->select('professor_id as id', 'professor_name as name', 'professor_email as email', 'created_at')
                        ->orderBy('professor_name')
                        ->limit(20)
                        ->get();
        
        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count(),
            'search_term' => $search
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'search_term' => $search
        ]);
    }
});

Route::get('/test/chat/search/users', function (Request $request) {
    $search = $request->get('search', '');
    
    try {
        $query = \App\Models\User::query();
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        $results = $query->select('id', 'name', 'email', 'role', 'created_at')
                        ->orderBy('name')
                        ->limit(20)
                        ->get();
        
        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count(),
            'search_term' => $search
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'search_term' => $search
        ]);
    }
});


Route::get('/debug/tables', function () {
    try {
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $tableNames = array_map(function($table) {
            return array_values((array)$table)[0];
        }, $tables);
        
        return response()->json([
            'success' => true,
            'tables' => $tableNames
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Test route for final chat test
Route::get('/final-chat-test', function () {
    return view('final-chat-test');
});

// Route for final chat test HTML file
Route::get('/final-chat-test-html', function () {
    return response()->file(public_path('../final-chat-test.html'));
});

// Chat System Debug Route
Route::get('/chat-test-debug', function () {
    return view('chat-test-debug');
})->name('chat.test.debug');

// Test route for logout functionality
Route::get('/logout-test', function () {
    return view('logout-test');
})->name('logout.test');

// Debug route to check session state
Route::get('/debug/session', function () {
    return response()->json([
        'session_user_id' => session('user_id'),
        'session_user_role' => session('user_role'),
        'session_role' => session('role'),
        'session_logged_in' => session('logged_in'),
        'php_session_user_id' => $_SESSION['user_id'] ?? null,
        'php_session_user_type' => $_SESSION['user_type'] ?? null,
        'all_session_data' => session()->all(),
        'php_session_data' => $_SESSION ?? []
    ]);
});

// Test route to check database tables
Route::get('/debug/chat-tables', function () {
    try {
        $tables = [];
        
        // Check if required tables exist
        $checkTables = ['students', 'professors', 'admins', 'directors', 'messages'];
        
        foreach ($checkTables as $table) {
            try {
                $count = DB::table($table)->count();
                $tables[$table] = [
                    'exists' => true,
                    'count' => $count
                ];
            } catch (\Exception $e) {
                $tables[$table] = [
                    'exists' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'tables' => $tables
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
Route::get('/get-programs', [ProgramController::class, 'getPrograms'])->name('get.programs');

// Test route for form requirements
Route::get('/test-form-requirements', function () {
    try {
        $requirements = \App\Models\FormRequirement::all();
        
        $result = [
            'total_count' => $requirements->count(),
            'active_count' => $requirements->where('is_active', true)->count(),
            'modular_requirements' => \App\Models\FormRequirement::active()
                ->forProgram('modular')
                ->ordered()
                ->get()
                ->map(function($req) {
                    return [
                        'id' => $req->id,
                        'field_name' => $req->field_name,
                        'field_label' => $req->field_label,
                        'field_type' => $req->field_type,
                        'program_type' => $req->program_type,
                        'is_required' => $req->is_required,
                        'is_active' => $req->is_active,
                        'sort_order' => $req->sort_order,
                        'field_options' => $req->field_options
                    ];
                }),
            'all_requirements' => $requirements->take(10)->map(function($req) {
                return [
                    'id' => $req->id,
                    'field_name' => $req->field_name,
                    'field_label' => $req->field_label,
                    'field_type' => $req->field_type,
                    'program_type' => $req->program_type,
                    'is_required' => $req->is_required,
                    'is_active' => $req->is_active,
                    'sort_order' => $req->sort_order
                ];
            })
        ];
        
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Working chat test page
Route::get('/chat-test-working', function () {
    return response()->file(public_path('chat-test-working.html'));
})->name('chat.test.working');

// Fixed chat test page
Route::get('/chat-test-fixed', function () {
    return response()->file(public_path('chat-system-test-fixed.html'));
})->name('chat.test.fixed');

// Quick login test page
Route::get('/quick-login-test', function () {
    return response()->file(public_path('quick-login-test.html'));
})->name('quick.login.test');

// Debug route to test admin table structure
Route::get('/debug/admin-table', function () {
    try {
        // Get first admin to see structure
        $admin = DB::table('admins')->first();
        $columns = DB::select('SHOW COLUMNS FROM admins');
        
        return response()->json([
            'success' => true,
            'columns' => $columns,
            'sample_admin' => $admin,
            'admin_count' => DB::table('admins')->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Debug route to test messages table
Route::get('/debug/messages-table', function () {
    try {
        // Check if table exists
        $tables = DB::select("SHOW TABLES LIKE 'messages'");
        if (count($tables) > 0) {
            $columns = DB::select('SHOW COLUMNS FROM messages');
            return response()->json([
                'success' => true,
                'table_exists' => true,
                'columns' => $columns
            ]);
        } else {
            return response()->json([
                'success' => false,
                'table_exists' => false,
                'message' => 'Messages table does not exist'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});


// Test module content flow
Route::get('/test-module-content/{moduleId?}', function($moduleId = 45) {
    try {
        echo "<h2>Testing Data Flow for Module ID: {$moduleId}</h2>";
        
        // 1. Check if module exists
        $module = \App\Models\Module::find($moduleId);
        if (!$module) {
            echo "<p style='color: red;'>❌ Module {$moduleId} not found!</p>";
            return;
        }
        echo "<p style='color: green;'>✅ Module found: {$module->module_name}</p>";
        
        // 2. Check courses linked to this module
        $courses = \App\Models\Course::where('module_id', $moduleId)->get();
        echo "<h3>Courses for this module:</h3>";
        if ($courses->isEmpty()) {
            echo "<p style='color: red;'>❌ No courses found for module {$moduleId}</p>";
        } else {
            echo "<p style='color: green;'>✅ Found " . $courses->count() . " courses:</p>";
            foreach ($courses as $course) {
                echo "<ul>";
                echo "<li>Course ID: {$course->subject_id} - {$course->subject_name}</li>";
                
                // 3. Check lessons for this course
                $lessons = \App\Models\Lesson::where('course_id', $course->subject_id)->get();
                echo "<li>Lessons (" . $lessons->count() . "):</li>";
                if ($lessons->isEmpty()) {
                    echo "<ul><li style='color: orange;'>⚠️ No lessons found</li></ul>";
                } else {
                    echo "<ul>";
                    foreach ($lessons as $lesson) {
                        echo "<li>Lesson ID: {$lesson->lesson_id} - {$lesson->lesson_name}</li>";
                        
                        // 4. Check content items for this lesson
                        $contentItems = \App\Models\ContentItem::where('lesson_id', $lesson->lesson_id)->get();
                        echo "<ul>";
                        echo "<li>Content Items (" . $contentItems->count() . "):</li>";
                        if ($contentItems->isEmpty()) {
                            echo "<ul><li style='color: orange;'>⚠️ No content items found</li></ul>";
                        } else {
                            echo "<ul>";
                            foreach ($contentItems as $item) {
                                echo "<li>Content: {$item->content_title} (Type: {$item->content_type})</li>";
                                if ($item->attachment_path) {
                                    echo "<li>Attachment: {$item->attachment_path}</li>";
                                }
                            }
                            echo "</ul>";
                        }
                        echo "</ul>";
                    }
                    echo "</ul>";
                }
                echo "</ul>";
            }
        }
        
        // 5. Also check direct content items linked to courses (bypass lessons)
        echo "<h3>Direct Course Content Items:</h3>";
        foreach ($courses as $course) {
            $directContentItems = \App\Models\ContentItem::where('course_id', $course->subject_id)->whereNull('lesson_id')->get();
            if (!$directContentItems->isEmpty()) {
                echo "<p>Course {$course->subject_name} has " . $directContentItems->count() . " direct content items:</p>";
                echo "<ul>";
                foreach ($directContentItems as $item) {
                    echo "<li>{$item->content_title} (Type: {$item->content_type})</li>";
                }
                echo "</ul>";
            }
        }
        
        // 6. Show all content items for this course regardless of lesson
        echo "<h3>All Content Items for Module Courses:</h3>";
        foreach ($courses as $course) {
            $allContentItems = \App\Models\ContentItem::where('course_id', $course->subject_id)->get();
            if (!$allContentItems->isEmpty()) {
                echo "<p>Course {$course->subject_name} has " . $allContentItems->count() . " total content items:</p>";
                echo "<ul>";
                foreach ($allContentItems as $item) {
                    echo "<li>{$item->content_title} (Type: {$item->content_type}) - Lesson ID: " . ($item->lesson_id ?? 'None') . "</li>";
                }
                echo "</ul>";
            }
        }
        
        // 7. Test the controller method directly
        echo "<h3>Testing Controller Method:</h3>";
        $controller = new \App\Http\Controllers\StudentDashboardController();
        $result = $controller->getModuleCourses($moduleId);
        echo "<pre>" . json_encode($result->getData(), JSON_PRETTY_PRINT) . "</pre>";
        
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
})->name('test.module.content');

// Test student module view without authentication
Route::get('/test-student-module/{moduleId}', function($moduleId) {
    // Simulate being logged in as a student
    session([
        'logged_in' => true,
        'user_id' => 112, // Use a student user ID from the database
        'user_role' => 'student',
        'user_name' => 'Test Student'
    ]);
    
    $controller = new \App\Http\Controllers\StudentDashboardController();
    return $controller->module($moduleId);
})->name('test.student.module');

// Test API endpoint for module courses without authentication
Route::get('/test-api/module/{moduleId}/courses', function($moduleId) {
    // Simulate being logged in as a student
    session([
        'logged_in' => true,
        'user_id' => 112,
        'user_role' => 'student',
        'user_name' => 'Test Student'
    ]);
    
    $controller = new \App\Http\Controllers\StudentDashboardController();
    return $controller->getModuleCourses($moduleId);
})->name('test.api.module.courses');

// Package details for course selection
Route::get('/get-package-details', [StudentRegistrationController::class, 'getPackageDetails'])
    ->name('get.package.details');

// Test routes for course functionality
Route::prefix('test-api/test')->group(function () {
    Route::get('/database', function () {
        try {
            $chatCount = \App\Models\Chat::count();
            $userCount = \App\Models\User::count();
            
            return response()->json([
                'success' => true,
                'chat_count' => $chatCount,
                'user_count' => $userCount,
                'message' => 'Database connection successful'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    });

    Route::get('/course-access', [\App\Http\Controllers\CourseTestController::class, 'testCourseAccess']);
    Route::get('/course-enrollment', [\App\Http\Controllers\CourseTestController::class, 'testCreateCourseEnrollment']);
});

// Certificate viewing and downloading for students
Route::middleware(['web'])->group(function () {
    Route::get('/certificate', [App\Http\Controllers\CertificateController::class, 'show'])->name('certificate.show');
    Route::get('/certificate/download', [App\Http\Controllers\CertificateController::class, 'download'])->name('certificate.download');
});

// Test upload page for debugging
Route::get('/test-upload', function() {
    return view('test-upload');
});

Route::get('/test-direct-upload', function() {
    return view('test-direct-upload');
});

Route::post('/test-upload', function(\Illuminate\Http\Request $request) {
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $path = $file->storeAs('content', time() . '_' . $file->getClientOriginalName(), 'public');
        return ['success' => true, 'path' => $path, 'file_exists' => file_exists(storage_path('app/public/' . $path))];
    }
    return ['success' => false, 'message' => 'No file detected'];
});

// Admin Payment Management Routes
Route::middleware(['admin.auth'])->prefix('admin')->group(function () {
    // Payment management
    Route::get('/payments/pending', [\App\Http\Controllers\Admin\PaymentController::class, 'pending'])->name('admin.payments.pending');
    Route::get('/payments/history', [\App\Http\Controllers\Admin\PaymentController::class, 'history'])->name('admin.payments.history');
    Route::get('/payments/{id}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('admin.payments.show');
    Route::get('/payments/{id}/details', [\App\Http\Controllers\Admin\PaymentController::class, 'details'])->name('admin.payments.details');
    Route::get('/payments/{id}/original-data', [\App\Http\Controllers\Admin\PaymentController::class, 'originalData'])->name('admin.payments.original-data');
    Route::post('/payments/{id}/approve', [\App\Http\Controllers\Admin\PaymentController::class, 'approve'])->name('admin.payments.approve');
    Route::post('/payments/{id}/approve-resubmission', [\App\Http\Controllers\Admin\PaymentController::class, 'approveResubmission'])->name('admin.payments.approve-resubmission');
    Route::post('/payments/{id}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('admin.payments.reject');
    Route::post('/payments/{id}/undo-approval', [\App\Http\Controllers\Admin\PaymentController::class, 'undoApproval'])->name('admin.payments.undo-approval');
    Route::post('/payments/{id}/update-rejection', [\App\Http\Controllers\Admin\PaymentController::class, 'updateRejection'])->name('admin.payments.update-rejection');
    Route::get('/payments/{id}/download-proof', [\App\Http\Controllers\Admin\PaymentController::class, 'downloadProof'])->name('admin.payments.download-proof');
    Route::get('/payments/stats', [\App\Http\Controllers\Admin\PaymentController::class, 'getStats'])->name('admin.payments.stats');
    
    // Payment method field management
    Route::get('/payment-methods/settings', function() {
        return view('admin.payment-methods.settings');
    })->name('admin.payment-methods.settings');
    
    Route::get('/payment-methods/{methodId}/fields', [\App\Http\Controllers\Admin\PaymentMethodFieldController::class, 'apiIndex']);
    Route::post('/payment-methods/{methodId}/fields', [\App\Http\Controllers\Admin\PaymentMethodFieldController::class, 'store']);
    Route::delete('/payment-method-fields/{fieldId}', [\App\Http\Controllers\Admin\PaymentMethodFieldController::class, 'destroy']);
    Route::post('/payment-methods/{methodId}/toggle', [\App\Http\Controllers\Admin\PaymentMethodFieldController::class, 'toggleMethod']);
});

// Add this route for multi-program batch fetching
Route::get('/admin/modules/batches', [App\Http\Controllers\AdminModuleController::class, 'getBatchesForPrograms']);

// Public route for module courses (for modular enrollment page AJAX)
Route::get('/get-module-courses', [App\Http\Controllers\StudentRegistrationController::class, 'getModuleCourses']);

// Test routes for quiz generator debugging
Route::get('/test-auth', function() {
    echo "<h2>Authentication Test</h2>";
    
    $professorId = session('professor_id');
    echo "<p>Professor ID from session: " . ($professorId ?? 'not set') . "</p>";
    
    if ($professorId) {
        $professor = \App\Models\Professor::find($professorId);
        if ($professor) {
            echo "<p>✓ Professor found: " . $professor->professor_first_name . " " . $professor->professor_last_name . "</p>";
            echo "<p>✓ Professor email: " . $professor->professor_email . "</p>";
            
            // Test program assignment
            $programId = 35;
            $programAssignment = $professor->programs()->where('programs.program_id', $programId)->exists();
            echo "<p>" . ($programAssignment ? "✓" : "✗") . " Professor assigned to program $programId</p>";
            
        } else {
            echo "<p>✗ Professor not found in database</p>";
        }
    } else {
        echo "<p>✗ No professor logged in</p>";
        echo "<p>Available session data: " . json_encode(session()->all()) . "</p>";
    }
    
    echo "<hr>";
    echo "<h3>Test Quiz Generation Form</h3>";
    echo '<form action="/professor/quiz-generator/generate" method="POST" enctype="multipart/form-data">';
    echo csrf_field();
    echo '<input type="hidden" name="program_id" value="35">';
    echo '<input type="hidden" name="module_id" value="56">';
    echo '<input type="hidden" name="course_id" value="30">';
    echo '<input type="hidden" name="content_id" value="27">';
    echo '<input type="hidden" name="num_questions" value="5">';
    echo '<input type="hidden" name="quiz_type" value="multiple_choice">';
    echo '<input type="hidden" name="quiz_title" value="Test Quiz">';
    echo '<input type="hidden" name="instructions" value="Test instructions">';
    echo '<p>Upload a text file:</p>';
    echo '<input type="file" name="document" accept=".txt" required>';
    echo '<br><br>';
    echo '<button type="submit">Test Generate Quiz</button>';
    echo '</form>';
});

// Test route to manually generate a quiz
Route::post('/test-quiz-manual', function() {
    try {
        $professor = \App\Models\Professor::find(8); // Using professor ID from logs
        
        if (!$professor) {
            return response()->json(['error' => 'Professor not found'], 404);
        }
        
        // Create test quiz
        $quiz = \App\Models\Quiz::create([
            'professor_id' => $professor->professor_id,
            'program_id' => 35,
            'module_id' => 56,
            'course_id' => 30,
            'content_id' => 27,
            'quiz_title' => 'Manual Test Quiz',
            'instructions' => 'Test instructions',
            'randomize_order' => false,
            'tags' => ['test'],
            'is_draft' => false,
            'total_questions' => 1,
            'time_limit' => 60,
            'document_path' => 'test-path',
            'is_active' => true,
            'created_at' => now(),
        ]);
        
        // Create test question
        \App\Models\QuizQuestion::create([
            'quiz_id' => $quiz->quiz_id,
            'quiz_title' => $quiz->quiz_title,
            'program_id' => $quiz->program_id,
            'question_text' => 'What is 2 + 2?',
            'question_type' => 'multiple_choice',
            'options' => [
                'A' => '3',
                'B' => '4',
                'C' => '5',
                'D' => '6'
            ],
            'correct_answer' => 'B',
            'points' => 1,
            'is_active' => true,
            'created_by_professor' => $professor->professor_id,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Quiz created successfully',
            'quiz_id' => $quiz->quiz_id
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test routes for AI quiz generation
Route::get('/test-ai-connection', function() {
    try {
        $geminiService = new \App\Services\GeminiQuizService();
        $connection = $geminiService->testConnection();
        
        return response()->json([
            'success' => true,
            'connection' => $connection,
            'message' => $connection ? 'AI service connected successfully' : 'AI service connection failed'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Error testing AI connection'
        ]);
    }
});

Route::post('/test-ai-generate', function() {
    try {
        $geminiService = new \App\Services\GeminiQuizService();
        
        $testText = "Machine Design involves understanding stress analysis, factor of safety, and material properties. Key concepts include tension, compression, shear, and fatigue analysis.";
        
        $questions = $geminiService->generateQuizFromText($testText, ['num_questions' => 2]);
        
        return response()->json([
            'success' => true,
            'questions' => $questions,
            'count' => count($questions),
            'message' => 'AI quiz generation successful'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Error generating AI quiz'
        ]);
    }
});

// Test page for quiz generator
Route::get('/test-quiz-generator', function() {
    return view('test-quiz-generator');
});

// Test session setup
Route::post('/test-set-session', function(\Illuminate\Http\Request $request) {
    session([
        'logged_in' => $request->logged_in,
        'professor_id' => $request->professor_id,
        'user_role' => $request->user_role
    ]);
    
    return response()->json(['success' => true, 'message' => 'Session configured']);
});

// Quiz Debug Route
Route::get('/debug-quiz-system', function() {
    echo "<h1>Quiz System Debug</h1>";
    
    try {
        // 1. Check current session
        echo "<h2>1. Session Check</h2>";
        echo "<p>User ID: " . (session('user_id') ?? 'Not set') . "</p>";
        echo "<p>User Role: " . (session('user_role') ?? 'Not set') . "</p>";
        echo "<p>Logged in: " . (session('logged_in') ? 'Yes' : 'No') . "</p>";
        
        // 2. Check quiz routes
        echo "<h2>2. Quiz Routes Check</h2>";
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $quizRoutes = [];
        
        foreach ($routes as $route) {
            $uri = $route->uri();
            if (strpos($uri, 'quiz') !== false && strpos($uri, 'student') !== false) {
                $methods = implode('|', $route->methods());
                $name = $route->getName() ?? 'No name';
                $action = $route->getActionName();
                $quizRoutes[] = "[$methods] $uri → $action (name: $name)";
            }
        }
        
        if (empty($quizRoutes)) {
            echo "<p style='color: red;'>❌ No student quiz routes found</p>";
        } else {
            echo "<p>✅ Found " . count($quizRoutes) . " quiz routes:</p>";
            echo "<ul>";
            foreach ($quizRoutes as $route) {
                echo "<li>$route</li>";
            }
            echo "</ul>";
        }
        
        // 3. Check database tables
        echo "<h2>3. Database Tables Check</h2>";
        
        $tables = ['quizzes', 'quiz_questions', 'quiz_attempts', 'students', 'users'];
        foreach ($tables as $table) {
            try {
                $count = \Illuminate\Support\Facades\DB::table($table)->count();
                echo "<p>✅ Table '$table': $count records</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Table '$table': Error - " . $e->getMessage() . "</p>";
            }
        }
        
        // 4. Check quiz data
        echo "<h2>4. Quiz Data Check</h2>";
        
        try {
            $quizCount = \Illuminate\Support\Facades\DB::table('quizzes')->count();
            echo "<p>Total quizzes: $quizCount</p>";
            
            if ($quizCount > 0) {
                $sampleQuiz = \Illuminate\Support\Facades\DB::table('quizzes')->first();
                echo "<h3>Sample Quiz:</h3>";
                echo "<ul>";
                foreach ($sampleQuiz as $key => $value) {
                    echo "<li><strong>$key:</strong> " . (is_array($value) ? json_encode($value) : $value) . "</li>";
                }
                echo "</ul>";
                
                // Check questions for this quiz
                $questionCount = \Illuminate\Support\Facades\DB::table('quiz_questions')
                    ->where('quiz_id', $sampleQuiz->quiz_id)
                    ->count();
                echo "<p>Questions for this quiz: $questionCount</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error checking quiz data: " . $e->getMessage() . "</p>";
        }
        
        // 5. Check StudentDashboardController methods
        echo "<h2>5. Controller Methods Check</h2>";
        
        $controllerClass = new ReflectionClass(\App\Http\Controllers\StudentDashboardController::class);
        $methods = $controllerClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $quizMethods = [];
        
        foreach ($methods as $method) {
            if (strpos(strtolower($method->getName()), 'quiz') !== false) {
                $params = [];
                foreach ($method->getParameters() as $param) {
                    $paramStr = $param->getName();
                    if ($param->hasType()) {
                        $paramStr = $param->getType() . ' ' . $paramStr;
                    }
                    if ($param->isDefaultValueAvailable()) {
                        $paramStr .= ' = ' . var_export($param->getDefaultValue(), true);
                    }
                    $params[] = $paramStr;
                }
                $quizMethods[] = $method->getName() . '(' . implode(', ', $params) . ')';
            }
        }
        
        if (empty($quizMethods)) {
            echo "<p style='color: red;'>❌ No quiz methods found in StudentDashboardController</p>";
        } else {
            echo "<p>✅ Found quiz methods:</p>";
            echo "<ul>";
            foreach ($quizMethods as $method) {
                echo "<li>$method</li>";
            }
            echo "</ul>";
        }
        
        // 6. Check student authentication
        echo "<h2>6. Student Authentication Check</h2>";
        
        try {
            $student = \App\Models\Student::where('user_id', 1)->first();
            if ($student) {
                echo "<p>✅ Student found: ID {$student->student_id}</p>";
            } else {
                echo "<p style='color: red;'>❌ No student found for user_id 1</p>";
                
                // Try to find any student
                $anyStudent = \App\Models\Student::first();
                if ($anyStudent) {
                    echo "<p>Found sample student: ID {$anyStudent->student_id}, user_id: {$anyStudent->user_id}</p>";
                }
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error checking student: " . $e->getMessage() . "</p>";
        }
        
        // 7. Test route generation
        echo "<h2>7. Route Generation Test</h2>";
        
        try {
            $startRoute = route('student.quiz.start', ['quizId' => 1]);
            echo "<p>✅ Start route: $startRoute</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Start route error: " . $e->getMessage() . "</p>";
        }
        
        try {
            $takeRoute = route('student.quiz.take', ['attemptId' => 1]);
            echo "<p>✅ Take route: $takeRoute</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Take route error: " . $e->getMessage() . "</p>";
        }
        
        try {
            $submitRoute = route('student.quiz.submit', ['attemptId' => 1]);
            echo "<p>Submit route: $submitRoute</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Submit route error: " . $e->getMessage() . "</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Critical Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
    echo "<h2>8. Test Forms</h2>";
    echo "<h3>Test Quiz Start Route</h3>";
    echo "<form method='POST' action='/student/quiz/1/start'>";
    echo "<input type='hidden' name='_token' value='" . csrf_token() . "'>";
    echo "<button type='submit'>Test Quiz Start (POST)</button>";
    echo "</form>";
    
    echo "<h3>Test Quiz Take Route</h3>";
    echo "<p><a href='/student/quiz/take/1'>Test Quiz Take (GET)</a></p>";
    
    echo "<h2>9. Recommendations</h2>";
    echo "<ol>";
    echo "<li>Check if routes are properly defined for quiz submission with attemptId</li>";
    echo "<li>Verify controller method names match route definitions</li>";
    echo "<li>Ensure middleware allows student access to quiz routes</li>";
    echo "<li>Check if session authentication is working correctly</li>";
    echo "<li>Verify database relationships between users, students, quizzes, and attempts</li>";
    echo "</ol>";
});

// Complete quiz flow test route
Route::get('/test-quiz-flow', function() {
    include_once base_path('test_quiz_complete.php');
});

// Debug route to reset quiz attempt
Route::get('/debug-reset-attempt/{attemptId}', function($attemptId) {
    try {
        $attempt = \App\Models\QuizAttempt::find($attemptId);
        if ($attempt) {
            $attempt->update([
                'status' => 'in_progress',
                'completed_at' => null,
                'score' => null,
                'correct_answers' => 0,
                'answers' => []
            ]);
            return "Attempt {$attemptId} reset to in_progress";
        } else {
            return "Attempt {$attemptId} not found";
        }
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Admin Enrollments
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/enrollments', [AdminProgramController::class, 'enrollmentManagement'])->name('admin.enrollments.index');
    // ...add any other /admin/enrollments routes here...
});

// Admin Analytics
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics.index');
    // ...add any other /admin/analytics routes here...
});

// Admin Settings
Route::middleware(['admin.director.auth'])->group(function () {
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
    // ...add any other /admin/settings routes here...
});
