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
use App\Http\Controllers\ProfessorDashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\FormRequirementController;
use App\Models\Program;
use App\Http\Controllers\TestController;
use App\Models\Package;
use App\Http\Controllers\DatabaseTestController;
use App\Http\Controllers\Admin\BatchEnrollmentController;
use App\Http\Controllers\RegistrationController;

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

// Update module
Route::put('/admin/modules/{module:modules_id}', [AdminModuleController::class, 'update'])
     ->name('admin.modules.update');

// Delete a module (used only by archived modules view)
Route::delete('/admin/modules/{module:modules_id}', [AdminModuleController::class, 'destroy'])
     ->name('admin.modules.destroy');

// Get modules by program (AJAX)
Route::get('/admin/modules/by-program', [AdminModuleController::class, 'getModulesByProgram'])
     ->name('admin.modules.by-program');

/*
|--------------------------------------------------------------------------
| Admin Packages
|--------------------------------------------------------------------------
*/
// Packages list
Route::get('/admin/packages', [AdminPackageController::class, 'index'])
     ->name('admin.packages.index');

// Show "Add New Package" form
Route::get('/admin/packages/create', [AdminPackageController::class, 'create'])
     ->name('admin.packages.create');

// Store new package
Route::post('/admin/packages', [AdminPackageController::class, 'store'])
     ->name('admin.packages.store');

// Update a package
Route::put('/admin/packages/{id}', [AdminPackageController::class, 'update'])
     ->name('admin.packages.update');

// Delete a package
Route::delete('/admin/packages/{id}', [AdminPackageController::class, 'destroy'])
     ->name('admin.packages.delete');

/*
|--------------------------------------------------------------------------
| Admin Batches
|--------------------------------------------------------------------------
*/
// Batch management
Route::get('/admin/batches', [AdminBatchController::class, 'index'])
     ->name('admin.batches.index');

// Create batch form
Route::get('/admin/batches/create', [AdminBatchController::class, 'create'])
     ->name('admin.batches.create');

// Store new batch
Route::post('/admin/batches', [AdminBatchController::class, 'store'])
     ->name('admin.batches.store');

// Update batch
Route::put('/admin/batches/{batch}', [AdminBatchController::class, 'update'])
     ->name('admin.batches.update');

// Delete batch
Route::delete('/admin/batches/{batch}', [AdminBatchController::class, 'destroy'])
     ->name('admin.batches.destroy');

// Toggle batch status
Route::patch('/admin/batches/{batch}/toggle-status', [AdminBatchController::class, 'toggleStatus'])
     ->name('admin.batches.toggle-status');

// Move student between batches
Route::post('/admin/batches/move-student', [AdminBatchController::class, 'moveStudent'])
     ->name('admin.batches.move-student');

// Batch enrollment management
Route::get('/admin/student-enrollment/batch-enroll', [AdminBatchController::class, 'batchEnroll'])
     ->name('admin.student.enrollment.batch');

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

/*
|--------------------------------------------------------------------------
| Test UI and Form Requirements (Development Only - Remove in Production)
|--------------------------------------------------------------------------
*/
// Route::get('/test-ui', function () {
//     $formRequirements = App\Models\FormRequirement::active()
//         ->forProgram('full')
//         ->ordered()
//         ->get();
//     
//     $navbarSettings = App\Models\UiSetting::getSection('navbar');
//     
//     return view('test-ui', compact('formRequirements', 'navbarSettings'));
// });

// Test dynamic registration system
Route::get('/test-dynamic-registration', function () {
    $activeFields = App\Models\FormRequirement::active()->get();
    $inactiveFields = App\Models\FormRequirement::where('is_active', false)->get();
    
    return view('test-dynamic-registration', compact('activeFields', 'inactiveFields'));
})->name('test.dynamic.registration');

// Form Requirements Management Routes
Route::get('/admin/form-requirements', [FormRequirementController::class, 'index'])->name('admin.form-requirements');
Route::post('/admin/form-requirements', [FormRequirementController::class, 'store'])->name('admin.form-requirements.store');
Route::post('/admin/form-requirements/archive', [FormRequirementController::class, 'archive'])->name('admin.form-requirements.archive');
Route::post('/admin/form-requirements/restore', [FormRequirementController::class, 'restore'])->name('admin.form-requirements.restore');
Route::put('/admin/form-requirements/{id}', [FormRequirementController::class, 'update'])->name('admin.form-requirements.update');
Route::delete('/admin/form-requirements/{id}', [FormRequirementController::class, 'destroy'])->name('admin.form-requirements.destroy');

// Database testing routes
Route::get('/test/db/students-schema', [DatabaseTestController::class, 'checkStudentsSchema']);
Route::get('/test/db/student-insert', [DatabaseTestController::class, 'testStudentInsert']);
Route::get('/test/db/add-missing-columns', [DatabaseTestController::class, 'addMissingColumns']);

// Professor Features Settings
Route::post('/admin/settings/professor-features', [AdminSettingsController::class, 'updateProfessorFeatures'])
     ->name('admin.settings.professor-features');
Route::get('/admin/settings/professor-features', [AdminSettingsController::class, 'getProfessorFeatures'])
     ->name('admin.settings.professor-features.get');

// Batch Enrollment Management Routes
Route::prefix('admin')->middleware(['web'])->group(function () {
    Route::get('/batches', [BatchEnrollmentController::class, 'index'])->name('admin.batches.index');
    Route::post('/batches', [BatchEnrollmentController::class, 'store'])->name('admin.batches.store');
    Route::put('/batches/{id}', [BatchEnrollmentController::class, 'update'])->name('admin.batches.update');
    Route::delete('/batches/{id}', [BatchEnrollmentController::class, 'delete'])->name('admin.batches.delete');
    
    // Student management within batches
    Route::get('/batches/{id}/students', [BatchEnrollmentController::class, 'students'])->name('admin.batches.students');
    Route::post('/batches/{batchId}/enrollments/{enrollmentId}/add-to-batch', [BatchEnrollmentController::class, 'addStudentToBatch'])->name('admin.batches.add-student');
    Route::delete('/batches/{batchId}/students/{studentId}', [BatchEnrollmentController::class, 'removeStudentFromBatch'])->name('admin.batches.remove-student');
    Route::get('/batches/{id}/export', [BatchEnrollmentController::class, 'exportBatchEnrollments'])->name('admin.batches.export');
    Route::post('/batches/{id}/toggle-status', [BatchEnrollmentController::class, 'toggleStatus'])->name('admin.batches.toggle-status');
    
    // Move student between pending/current (for visual drag-and-drop with actual status updates when moving to current)
    Route::post('/batches/{batchId}/enrollments/{enrollmentId}/move-to-current', [BatchEnrollmentController::class, 'moveStudentToCurrent'])->name('admin.batches.move-to-current');
    Route::post('/batches/{batchId}/enrollments/{enrollmentId}/move-to-pending', [BatchEnrollmentController::class, 'moveStudentToPending'])->name('admin.batches.move-to-pending');
    Route::post('/batches/{batchId}/enrollments/{enrollmentId}/remove-from-batch', [BatchEnrollmentController::class, 'removeStudentFromBatchCompletely'])->name('admin.batches.remove-from-batch');
});

Route::get('/test-user-creation', function() { 
    return view('test-user-creation'); 
});
