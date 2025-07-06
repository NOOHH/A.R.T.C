<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentLoginController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\AdminProgramController;
use App\Http\Controllers\AdminModuleController;
use App\Http\Controllers\AdminDirectorController;
use App\Http\Controllers\AdminStudentListController;
use App\Http\Controllers\AdminPackageController;    // ← NEW
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminProfessorController;
use App\Http\Controllers\ProfessorDashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\FormRequirementController;
use App\Models\Program;
use App\Http\Controllers\TestController;
use App\Models\Package;
use App\Http\Controllers\DatabaseTestController;

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
    
    return view('registration.Modular_enrollment', compact('programs', 'packages', 'programId', 'formRequirements', 'student', 'allModules'));
})->name('enrollment.modular');

// Login page
Route::get('/login', fn() => view('Login.login'))->name('login');

// Student authentication routes
Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login');
Route::post('/student/logout', [StudentLoginController::class, 'logout'])->name('student.logout');

// Student dashboard and related routes  
Route::middleware(['student.auth'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/settings', [StudentController::class, 'settings'])->name('student.settings');
    Route::get('/student/course/{courseId}', [StudentDashboardController::class, 'course'])->name('student.course');
    Route::get('/student/calendar', [StudentDashboardController::class, 'calendar'])->name('student.calendar');
    Route::get('/student/module/{moduleId}', [StudentDashboardController::class, 'module'])->name('student.module');
});

/*
|--------------------------------------------------------------------------
| Student Actions
|--------------------------------------------------------------------------
*/
// Student register POST
Route::post('/student/register', [StudentRegistrationController::class, 'store'])
     ->name('student.register');

// Registration success page
Route::get('/registration/success', function() {
    return view('registration.success');
})->name('registration.success');

// Test registration form
Route::get('/test-registration', function () {
    $formRequirements = App\Models\FormRequirement::active()->forProgram('complete')->get();
    return view('test-registration', compact('formRequirements'));
})->name('test.registration');

// Check if email exists
Route::post('/check-email', [StudentRegistrationController::class, 'checkEmail'])
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

/*
|--------------------------------------------------------------------------
| Admin Dashboard & Registration
|--------------------------------------------------------------------------
*/
// Admin dashboard
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

Route::post('/admin/settings/logo', [AdminSettingsController::class, 'updateGlobalLogo']);
Route::post('/admin/settings/favicon', [AdminSettingsController::class, 'updateFavicon']);
Route::get('/admin/settings/enrollment-form/{programType}', [AdminSettingsController::class, 'generateEnrollmentForm']);

/*
|--------------------------------------------------------------------------
| Professor Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:professor'])
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
         ->name('program.video.update');
});

/*
|--------------------------------------------------------------------------
| Test UI and Form Requirements (Development Only - Remove in Production)
|--------------------------------------------------------------------------
*/
// Route::get('/test-ui', function () {
//     $formRequirements = App\Models\FormRequirement::active()
//         ->forProgram('complete')
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
