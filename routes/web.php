<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UnifiedLoginController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\AdminProgramController;
use App\Http\Controllers\AdminModuleController;
use App\Http\Controllers\AdminDirectorController;
use App\Http\Controllers\AdminStudentListController;
use App\Http\Controllers\AdminPackageController;    // ← NEW
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminProfessorController;
use App\Http\Controllers\AdminBatchController;
use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\ProfessorDashboardController;
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

// Quick test to create sample programs
Route::get('/seed-programs', [TestController::class, 'seedPrograms']);

// Test database structure
Route::get('/test-db-structure', [TestController::class, 'testDatabaseConnection']);

/*
|--------------------------------------------------------------------------
| Batch Enrollment Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin/batches')->middleware(['admin.auth'])->group(function () {
    Route::get('/', [BatchEnrollmentController::class, 'index'])->name('admin.batches.index');
    Route::post('/', [BatchEnrollmentController::class, 'store'])->name('admin.batches.store');
    Route::get('/{id}', [BatchEnrollmentController::class, 'show'])->name('admin.batches.show');
    Route::put('/{id}', [BatchEnrollmentController::class, 'update'])->name('admin.batches.update');
    Route::post('/{id}/toggle-status', [BatchEnrollmentController::class, 'toggleStatus'])->name('admin.batches.toggle-status');
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
});

// Registration and document validation routes
Route::middleware(['session.auth'])->group(function () {
    Route::post('/registration/validate-document', [RegistrationController::class, 'validateDocument'])->name('registration.validate-document');
    Route::get('/api/batches/{programId}', [RegistrationController::class, 'getBatchesForProgram'])->name('api.batches.program');
    Route::post('/registration/batch-enrollment', [RegistrationController::class, 'saveBatchEnrollment'])->name('registration.batch-enrollment');
});

// OCR File validation routes
Route::post('/registration/validate-file', [RegistrationController::class, 'validateFileUpload'])->name('registration.validate-file');
Route::get('/registration/user-prefill', [RegistrationController::class, 'getUserPrefillData'])->name('registration.user-prefill');
Route::get('/registration/user-prefill-data', [RegistrationController::class, 'getUserPrefillData'])->name('registration.user-prefill-data');

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
    $allPrograms = Program::where('is_archived', false)->get();
    $packages = Package::all();
    $programId = request('program_id');
    
    // Get form requirements for modular enrollment
    $formRequirements = \App\Models\FormRequirement::active()
        ->forProgram('modular')
        ->ordered()
        ->get();
    
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
    
    // Get all modules for JavaScript (will be filtered by program on frontend)
    $allModules = \App\Models\Module::where('is_archived', false)
                                   ->orderBy('module_order')
                                   ->orderBy('module_name')
                                   ->get(['modules_id', 'module_name', 'module_description', 'program_id']);
    
    return view('registration.Modular_enrollment', compact('programs', 'packages', 'programId', 'formRequirements', 'student', 'allModules', 'fullPlan', 'modularPlan'));
})->name('enrollment.modular');

// Unified login page and authentication for all user types
Route::get('/login', [UnifiedLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UnifiedLoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [UnifiedLoginController::class, 'logout'])->name('logout');

// Signup page
Route::get('/signup', [App\Http\Controllers\SignupController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [App\Http\Controllers\SignupController::class, 'signup'])->name('user.signup');

// Legacy student authentication routes (now handled by UnifiedLoginController)
Route::post('/student/login', [UnifiedLoginController::class, 'login'])->name('student.login');
Route::post('/student/logout', [UnifiedLoginController::class, 'logout'])->name('student.logout');

// Student dashboard and related routes  
Route::middleware(['check.session', 'role.dashboard'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/settings', [StudentController::class, 'settings'])->name('student.settings');
    Route::put('/student/settings', [StudentController::class, 'updateSettings'])->name('student.settings.update');
    Route::get('/student/course/{courseId}', [StudentDashboardController::class, 'course'])->name('student.course');
    Route::get('/student/calendar', [StudentDashboardController::class, 'calendar'])->name('student.calendar');
    Route::get('/student/module/{moduleId}', [StudentDashboardController::class, 'module'])->name('student.module');
    
    // Module completion route
    Route::post('/student/module/{moduleId}/complete', [StudentDashboardController::class, 'completeModule'])->name('student.module.complete');
    
    // Assignment submission routes
    Route::post('/student/assignment/submit', [StudentDashboardController::class, 'submitAssignment'])->name('student.assignment.submit');
    
    // Quiz routes
    Route::get('/student/quiz/{moduleId}/start', [StudentDashboardController::class, 'startQuiz'])->name('student.quiz.start');
    Route::get('/student/quiz/{moduleId}/practice', [StudentDashboardController::class, 'practiceQuiz'])->name('student.quiz.practice');
    Route::post('/student/quiz/{moduleId}/submit', [StudentDashboardController::class, 'submitQuiz'])->name('student.quiz.submit');
    
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
// Admin dashboard and admin routes with middleware
Route::middleware(['check.session', 'role.dashboard'])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])
         ->name('admin.dashboard');

// Admin approve/reject registration
Route::get('/admin/registration/{id}', [AdminController::class, 'showRegistration']);
Route::post('/admin/registration/{id}/approve', [AdminController::class, 'approve'])
     ->name('admin.registration.approve');
Route::post('/admin/registration/{id}/reject', [AdminController::class, 'reject'])
     ->name('admin.registration.reject');

// List student registrations
Route::get('/admin-student-registration', [AdminController::class, 'studentRegistration'])
     ->name('admin.student.registration');
Route::get('/admin-student-registration/pending', [AdminController::class, 'studentRegistration'])
     ->name('admin.student.registration.pending');
Route::get('/admin-student-registration/history', [AdminController::class, 'studentRegistrationHistory'])
     ->name('admin.student.registration.history');

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

// Archive a module
Route::post('/admin/modules/{id}/archive', [AdminModuleController::class, 'archive'])
     ->name('admin.modules.archive');

// Admin override settings
Route::patch('/admin/modules/{id}/override', [AdminModuleController::class, 'updateOverride'])
     ->name('admin.modules.update-override');
Route::get('/admin/modules/{id}/override', [AdminModuleController::class, 'getOverrideSettings'])
     ->name('admin.modules.get-override');

// Admin Packages Routes
Route::get('/admin/packages', [AdminPackageController::class, 'index'])
     ->name('admin.packages.index');
Route::post('/admin/packages', [AdminPackageController::class, 'store'])
     ->name('admin.packages.store');
Route::get('/admin/packages/{id}/edit', [AdminPackageController::class, 'edit'])
     ->name('admin.packages.edit');
Route::put('/admin/packages/{id}', [AdminPackageController::class, 'update'])
     ->name('admin.packages.update');
Route::delete('/admin/packages/{id}', [AdminPackageController::class, 'destroy'])
     ->name('admin.packages.destroy');

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

// Chat functionality routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/search-users', [ChatController::class, 'searchUsers'])->name('chat.search-users');
    Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/conversations', [ChatController::class, 'getConversations'])->name('chat.conversations');
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

// Plan Settings routes
Route::get('/admin/settings/plan-settings', [AdminSettingsController::class, 'getPlanSettings']);
Route::post('/admin/settings/plan-settings', [AdminSettingsController::class, 'savePlanSettings']);

// Module ordering routes
Route::post('/admin/modules/update-sort-order', [ModuleController::class, 'updateSortOrder'])
     ->name('admin.modules.updateOrder');
Route::get('/admin/modules/ordered', [ModuleController::class, 'getOrderedModules'])
     ->name('admin.modules.ordered');

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

// View archived students (must come before dynamic routes)
Route::get('/admin/students/archived', [AdminStudentListController::class, 'archived'])
     ->name('admin.students.archived');

// Show student details
Route::get('/admin/students/{student:student_id}', [AdminStudentListController::class, 'show'])
     ->name('admin.students.show');

// Approve student
Route::patch('/admin/students/{student:student_id}/approve', [AdminStudentListController::class, 'approve'])
     ->name('admin.students.approve');

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

    Route::get('/programs', [ProfessorDashboardController::class, 'programs'])
         ->name('programs');

    Route::get('/programs/{program}', [ProfessorDashboardController::class, 'programDetails'])
         ->name('program.details');

    Route::post('/programs/{program}/video', [ProfessorDashboardController::class, 'updateVideo'])
         ->name('program.update-video');
    
    // Profile Management
    Route::get('/profile', [ProfessorDashboardController::class, 'profile'])
         ->name('profile');
    Route::put('/profile', [ProfessorDashboardController::class, 'updateProfile'])
         ->name('profile.update');
    
    // Student Management
    Route::get('/students', [ProfessorDashboardController::class, 'studentList'])
         ->name('students');
    Route::post('/students/{student}/grade', [ProfessorDashboardController::class, 'gradeStudent'])
         ->name('students.grade');
    
    // Attendance Management
    Route::get('/attendance', [\App\Http\Controllers\ProfessorAttendanceController::class, 'index'])
         ->name('attendance');
    Route::post('/attendance', [\App\Http\Controllers\ProfessorAttendanceController::class, 'store'])
         ->name('attendance.store');
    Route::get('/attendance/reports', [\App\Http\Controllers\ProfessorAttendanceController::class, 'reports'])
         ->name('attendance.reports');
    
    // Enhanced Grading Management
    Route::get('/grading', [\App\Http\Controllers\Professor\GradingController::class, 'index'])
         ->name('grading');
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

// Chat test route
Route::get('/chat-test', function () {
    return view('chat-test');
})->name('chat.test');
