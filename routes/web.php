<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
use App\Http\Controllers\Admin\EducationLevelController;
use App\Http\Controllers\AdminProfessorController;
use App\Http\Controllers\AdminBatchController;
use App\Http\Controllers\AdminAnalyticsController;
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
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StudentModuleController;
use App\Http\Controllers\ProgramController;

// routes/web.php

use App\Http\Controllers\Api\ReferralController;

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
Route::get('/enrollment', [StudentRegistrationController::class, 'showEnrollmentSelection'])
     ->name('enrollment.selection');

// Full enrollment form (GET)
Route::get('/enrollment/full', [StudentRegistrationController::class, 'showRegistrationForm'])
     ->name('enrollment.full');

// Modular enrollment form (GET)
Route::get('/enrollment/modular', function () {
    $allPrograms = Program::where('is_archived', false)
        ->whereHas('packages') // Only show programs that have packages
        ->get();
    
    // Get only modular packages
    $packages = Package::with('program')
        ->where('package_type', 'modular')
        ->get();
    
    $programId = request('program_id');
    
    // Get form requirements for modular enrollment
    $formRequirements = \App\Models\FormRequirement::active()
        ->forProgram('modular')
        ->ordered()
        ->get();
    
    // Get education levels
    $educationLevels = \App\Models\EducationLevel::all();
    
    // Get plan data with learning mode settings
    $fullPlan = \App\Models\Plan::where('plan_id', 1)->first(); // Full Plan
    $modularPlan = \App\Models\Plan::where('plan_id', 2)->first(); // Modular Plan
    
    // Get existing student data if user is logged in
    $student = null;
    $enrolledProgramIds = [];
    
    if (session('user_id')) {
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        
        // Get already enrolled program IDs to exclude them from the dropdown
        if ($student) {
            $enrolledProgramIds = $student->enrollments()->pluck('program_id')->toArray();
        }
    }
    
    // Filter out programs the user is already enrolled in
    $programs = $allPrograms->reject(function ($program) use ($enrolledProgramIds) {
        return in_array($program->program_id, $enrolledProgramIds);
    });
    
    return view('registration.Modular_enrollment', compact('programs', 'packages', 'programId', 'formRequirements', 'educationLevels', 'student', 'fullPlan', 'modularPlan'));
})->name('enrollment.modular');

// Modular enrollment submission
Route::post('/enrollment/modular/submit', [StudentRegistrationController::class, 'submitModularEnrollment'])->name('enrollment.modular.submit');
Route::post('/enrollment/modular/store', [StudentRegistrationController::class, 'storeModular'])->name('enrollment.modular.store');

// API endpoints for modular enrollment
Route::get('/get-program-modules', [StudentRegistrationController::class, 'getProgramModules'])->name('get.program.modules');
Route::get('/get-module-courses', [StudentRegistrationController::class, 'getModuleCourses'])->name('get.module.courses');
Route::get('/get-program-batches', [StudentRegistrationController::class, 'getProgramBatches'])->name('get.program.batches');

// Enrollment-specific OTP and validation routes
Route::post('/enrollment/send-otp', [StudentRegistrationController::class, 'sendEnrollmentOTP'])->name('enrollment.send-otp');
Route::post('/enrollment/verify-otp', [StudentRegistrationController::class, 'verifyEnrollmentOTP'])->name('enrollment.verify-otp');
Route::post('/enrollment/validate-referral', [StudentRegistrationController::class, 'validateEnrollmentReferral'])->name('enrollment.validate-referral');
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
    
    Route::get('/student/course/{courseId}', [StudentDashboardController::class, 'course'])->name('student.course');
    Route::get('/student/meetings', [\App\Http\Controllers\ClassMeetingController::class, 'studentMeetings'])->name('student.meetings');
    Route::get('/student/meetings/upcoming', [\App\Http\Controllers\ClassMeetingController::class, 'studentUpcomingMeetings'])
        ->name('student.meetings.upcoming');
    Route::post('/student/meetings/{id}/access', [\App\Http\Controllers\ClassMeetingController::class, 'logStudentAccess'])->name('student.meetings.access');
    Route::get('/student/calendar', [StudentDashboardController::class, 'calendar'])->name('student.calendar');
    Route::get('/student/module/{moduleId}', [StudentDashboardController::class, 'module'])->name('student.module');
    
    // Paywall route
    Route::get('/student/paywall', [StudentDashboardController::class, 'paywall'])->name('student.paywall');
    
    // Module completion route
    Route::post('/student/module/{moduleId}/complete', [StudentDashboardController::class, 'completeModule'])->name('student.module.complete');
    
    // Get module courses with lessons and content
    Route::get('/student/module/{moduleId}/courses', [StudentDashboardController::class, 'getModuleCourses'])->name('student.module.courses');
    
    // Assignment submission routes
    Route::post('/student/assignment/submit', [StudentDashboardController::class, 'submitAssignment'])->name('student.assignment.submit');
    
    // Assignment submission routes
    Route::post('/student/assignment/submit', [StudentDashboardController::class, 'submitAssignment'])->name('student.assignment.submit');
    Route::post('/student/submit-assignment', [StudentDashboardController::class, 'submitAssignmentFile'])->name('student.submit-assignment');
    Route::get('/student/content/{contentId}/submission-info', [StudentDashboardController::class, 'getSubmissionInfo'])->name('student.submission-info');
    Route::get('/student/content/{contentId}', [StudentDashboardController::class, 'getContent'])->name('student.content');
    
    // Quiz routes
    Route::get('/student/quiz/{moduleId}/start', [StudentDashboardController::class, 'startQuiz'])->name('student.quiz.start');
    Route::get('/student/quiz/{moduleId}/practice', [StudentDashboardController::class, 'practiceQuiz'])->name('student.quiz.practice');
    Route::post('/student/quiz/{moduleId}/submit', [StudentDashboardController::class, 'submitQuiz'])->name('student.quiz.submit');
    
    // AI-generated quiz routes
    Route::get('/student/ai-quiz/{quizId}/start', [StudentDashboardController::class, 'startAiQuiz'])->name('student.ai-quiz.start');
    Route::post('/student/ai-quiz/{quizId}/submit', [StudentDashboardController::class, 'submitAiQuiz'])->name('student.ai-quiz.submit');
    
    // Payment routes
    Route::post('/student/payment/process', [App\Http\Controllers\StudentPaymentController::class, 'processPayment'])->name('student.payment.process');
    Route::get('/student/payment/history', [App\Http\Controllers\StudentPaymentController::class, 'paymentHistory'])->name('student.payment.history');
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
        // Use raw database query to bypass Model accessors
        $modules = DB::table('modules')
                    ->where('program_id', $programId)
                    ->where('is_archived', false)
                    ->orderBy('module_order', 'asc')
                    ->select('modules_id', 'module_name', 'module_description', 'program_id')
                    ->get();
        
        // Transform the data to ensure the id field is properly set
        $transformedModules = [];
        foreach ($modules as $module) {
            $transformedModules[] = [
                'id' => $module->modules_id,
                'module_name' => $module->module_name,
                'module_description' => $module->module_description,
                'program_id' => $module->program_id,
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
Route::get('/payment-methods/enabled', [AdminSettingsController::class, 'getEnabledPaymentMethods'])->name('payment-methods.enabled');

// Admin dashboard and admin routes with middleware
Route::middleware(['check.session', 'role.dashboard'])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])
         ->name('admin.dashboard');

// Admin approve/reject registration
Route::get('/admin/registration/{id}', [AdminController::class, 'showRegistration']);
Route::get('/admin/registration/{id}/details', [AdminController::class, 'getRegistrationDetailsJson']);
Route::post('/admin/registration/{id}/approve', [AdminController::class, 'approve'])
     ->name('admin.registration.approve');
Route::post('/admin/registration/{id}/reject', [AdminController::class, 'reject'])
     ->name('admin.registration.reject');
Route::post('/admin/registration/{id}/reject-with-reason', [AdminController::class, 'rejectWithReason'])
     ->name('admin.registration.reject.reason');

// Get registration details
Route::get('/admin/registration/{id}/details', [AdminController::class, 'getRegistrationDetailsJson'])
     ->name('admin.registration.details');

// List student registrations
Route::get('/admin-student-registration', [AdminController::class, 'studentRegistration'])
     ->name('admin.student.registration');
Route::get('/admin-student-registration/pending', [AdminController::class, 'studentRegistration'])
     ->name('admin.student.registration.pending');
Route::get('/admin-student-registration/history', [AdminController::class, 'studentRegistrationHistory'])
     ->name('admin.student.registration.history');

// Student history actions
Route::get('/admin/student/{id}/details', [AdminController::class, 'getStudentDetailsJson'])
     ->name('admin.student.details');
Route::post('/admin/student/{id}/undo-approval', [AdminController::class, 'undoApproval'])
     ->name('admin.student.undo.approval');

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
Route::get('/admin-student-registration/payment/history', [AdminController::class, 'paymentHistory'])
     ->name('admin.student.registration.payment.history');

// Mark enrollment as paid
Route::post('/admin/enrollment/{id}/mark-paid', [AdminController::class, 'markAsPaid'])
     ->name('admin.enrollment.mark-paid');

// View one student registration’s details
Route::get('/admin-student-registration/view/{id}', [AdminController::class, 'showRegistrationDetails'])
     ->name('admin.student.registration.view');

/*
|--------------------------------------------------------------------------
| Admin Programs
|--------------------------------------------------------------------------
*/
// Programs list
Route::get('/admin/programs', [AdminProgramController::class, 'index'])
     ->name('admin.programs.index');

// Show “Add New Program” form
Route::get('/admin/programs/create', [AdminProgramController::class, 'create'])
     ->name('admin.programs.create');

// Store new program
Route::post('/admin/programs', [AdminProgramController::class, 'store'])
     ->name('admin.programs.store');

// Batch store programs
Route::post('/admin/programs/batch-store', [AdminProgramController::class, 'batchStore'])
     ->name('admin.programs.batch-store');

// Delete a program (used only by archived programs view)
Route::delete('/admin/programs/{id}', [AdminProgramController::class, 'destroy'])
     ->name('admin.programs.delete');

// Toggle archive status
Route::post('/admin/programs/{program}/toggle-archive', [AdminProgramController::class, 'toggleArchive'])
     ->name('admin.programs.toggle-archive');

// Archive a program (used by main programs view)
Route::post('/admin/programs/{id}/archive', [AdminProgramController::class, 'archive'])
     ->name('admin.programs.archive');

// Batch delete programs (used only by archived programs view)
Route::post('/admin/programs/batch-delete', [AdminProgramController::class, 'batchDelete'])
     ->name('admin.programs.batch-delete');

// View archived programs
Route::get('/admin/programs/archived', [AdminProgramController::class, 'archived'])
     ->name('admin.programs.archived');

// View enrollments for a program
Route::get('/admin/programs/{id}/enrollments', [AdminProgramController::class, 'enrollments'])
     ->name('admin.programs.enrollments');

// Assign program to student
Route::post('/admin/programs/assign', [AdminProgramController::class, 'assignProgram'])
     ->name('admin.programs.assign');

// Enrollment management page
Route::get('/admin/enrollments', [AdminProgramController::class, 'enrollmentManagement'])
     ->name('admin.enrollments.index');


/*
|--------------------------------------------------------------------------
| Admin Modules
|--------------------------------------------------------------------------
*/
// Modules list
Route::get('/admin/modules', [AdminModuleController::class, 'index'])
     ->name('admin.modules.index');

// Store new module
Route::post('/admin/modules', [AdminModuleController::class, 'store'])
     ->name('admin.modules.store');

// Edit module
Route::get('/admin/modules/{id}/edit', [AdminModuleController::class, 'edit'])
     ->name('admin.modules.edit');

// Update module
Route::put('/admin/modules/{id}', [AdminModuleController::class, 'update'])
     ->name('admin.modules.update');

// Upload video for module
Route::post('/admin/modules/{id}/upload-video', [AdminModuleController::class, 'uploadVideo'])
     ->name('admin.modules.upload-video');

// Add content to module
Route::post('/admin/modules/{id}/add-content', [AdminModuleController::class, 'addContent'])
     ->name('admin.modules.add-content');

// Batch store modules
Route::post('/admin/modules/batch', [AdminModuleController::class, 'batchStore'])
     ->name('admin.modules.batch-store');

// Store course content
Route::post('/admin/modules/course-content-store', [AdminModuleController::class, 'courseContentStore'])
     ->name('admin.modules.course-content-store');

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

// Admin override settings (legacy - keeping for compatibility)
Route::get   ('/admin/modules/{id}/override', [AdminModuleController::class, 'getOverrideSettings'])
     ->name('admin.modules.get-override');
Route::patch ('/admin/modules/{id}/override', [AdminModuleController::class, 'updateOverride'])
     ->name('admin.modules.update-override');

// New Admin Override System Routes
Route::middleware(['admin.auth'])->prefix('admin/overrides')->group(function () {
    Route::get('/status/{type}/{id}', [App\Http\Controllers\AdminOverrideController::class, 'getStatus'])
         ->name('admin.overrides.status');
    Route::get('/prerequisites', [App\Http\Controllers\AdminOverrideController::class, 'getPrerequisites'])
         ->name('admin.overrides.prerequisites');
    Route::post('/clear-schedule', [App\Http\Controllers\AdminOverrideController::class, 'clearSchedule'])
         ->name('admin.overrides.clear-schedule');
    Route::post('/toggle-lock', [App\Http\Controllers\AdminOverrideController::class, 'toggleLock'])
         ->name('admin.overrides.toggle-lock');
    Route::post('/set-schedule', [App\Http\Controllers\AdminOverrideController::class, 'setSchedule'])
         ->name('admin.overrides.set-schedule');
    Route::post('/set-prerequisite', [App\Http\Controllers\AdminOverrideController::class, 'setPrerequisite'])
         ->name('admin.overrides.set-prerequisite');
    Route::post('/bulk-lock', [App\Http\Controllers\AdminOverrideController::class, 'bulkLock'])
         ->name('admin.overrides.bulk-lock');
    Route::post('/bulk-unlock', [App\Http\Controllers\AdminOverrideController::class, 'bulkUnlock'])
         ->name('admin.overrides.bulk-unlock');
    Route::get('/list/{programId}', [App\Http\Controllers\AdminOverrideController::class, 'getItemOverrides'])
         ->name('admin.overrides.list');
});

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
Route::get('/admin/quiz-generator', [AdminModuleController::class, 'adminQuizGenerator'])
     ->name('admin.quiz-generator');
Route::post('/admin/quiz-generator/generate', [AdminModuleController::class, 'generateAdminAiQuiz'])
     ->name('admin.quiz-generator.generate');

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
Route::get('/search/advanced', [SearchController::class, 'advancedSearch'])->name('search.advanced');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

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
Route::post('/analytics/upload-board-passers', [AdminAnalyticsController::class, 'uploadBoardPassers']);
Route::post('/analytics/add-board-passer', [AdminAnalyticsController::class, 'addBoardPasser']);
Route::get('/analytics/download-template', [AdminAnalyticsController::class, 'downloadTemplate']);
Route::get('/analytics/board-passer-stats', [AdminAnalyticsController::class, 'getBoardPasserStats']);
Route::get('/analytics/students-list', [AdminAnalyticsController::class, 'getStudentsList']);

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
Route::get('/director/dashboard', [DirectorController::class, 'dashboard'])
     ->name('director.dashboard');
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
Route::get('/admin/students', [AdminStudentListController::class, 'index'])
     ->name('admin.students.index');

// Export students to CSV
Route::get('/admin/students/export', [AdminStudentListController::class, 'export'])
     ->name('admin.students.export');

// View archived students (must come before dynamic routes)
Route::get('/admin/students/archived', [AdminStudentListController::class, 'archived'])
     ->name('admin.students.archived');

// Show student details
Route::get('/admin/students/{student:student_id}', [AdminStudentListController::class, 'show'])
     ->name('admin.students.show');

// Approve student
Route::patch('/admin/students/{student:student_id}/approve', [AdminStudentListController::class, 'approve'])
     ->name('admin.students.approve');

// Disapprove student 
Route::patch('/admin/students/{student:student_id}/disapprove', [AdminStudentListController::class, 'disapprove'])
     ->name('admin.students.disapprove');

// Archive student
Route::patch('/admin/students/{student:student_id}/archive', [AdminStudentListController::class, 'archive'])
     ->name('admin.students.archive');

// Restore archived student
Route::patch('/admin/students/{student:student_id}/restore', [AdminStudentListController::class, 'restore'])
     ->name('admin.students.restore');

// Delete student permanently
Route::delete('/admin/students/{student:student_id}', [AdminStudentListController::class, 'destroy'])
     ->name('admin.students.destroy');

/*
|--------------------------------------------------------------------------
| Admin Professors
|--------------------------------------------------------------------------
*/
// Professor routes
Route::get('/admin/professors', [AdminProfessorController::class, 'index'])
     ->name('admin.professors.index');

Route::get('/admin/professors/archived', [AdminProfessorController::class, 'archived'])
     ->name('admin.professors.archived');

Route::post('/admin/professors', [AdminProfessorController::class, 'store'])
     ->name('admin.professors.store');

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

}); // End of admin middleware group

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
    Route::post('/activities/create', [\App\Http\Controllers\Professor\GradingController::class, 'createActivity'])
         ->name('activities.create');
    
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
    Route::put('/grading/{grade}', [\App\Http\Controllers\ProfessorGradingController::class, 'update'])
         ->name('grading.update');
    Route::delete('/grading/{grade}', [\App\Http\Controllers\ProfessorGradingController::class, 'destroy'])
         ->name('grading.destroy');
    Route::get('/grading/student/{student}', [\App\Http\Controllers\ProfessorGradingController::class, 'studentDetails'])
         ->name('grading.student');
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
