<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
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
use App\Models\Program;
use App\Models\Package;

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
Route::get('/', fn() => view('homepage'))->name('home');

// Enrollment selection
Route::get('/enrollment', [StudentRegistrationController::class, 'showEnrollmentSelection'])
     ->name('enrollment.selection');

// Full enrollment form (GET)
Route::get('/enrollment/full', [StudentRegistrationController::class, 'showRegistrationForm'])
     ->name('enrollment.full');

// Modular enrollment form (GET)
Route::get('/enrollment/modular', function () {
    $programs  = Program::all();
    $packages  = Package::all(); // Add this line to fix the undefined variable error
    $programId = request('program_id');
    return view('registration.Modular_enrollment', compact('programs', 'packages', 'programId'));
})->name('enrollment.modular');

// Login page
Route::get('/login', fn() => view('Login.login'))->name('login');

/*
|--------------------------------------------------------------------------
| Student Actions
|--------------------------------------------------------------------------
*/
// Student register POST
Route::post('/student/register', [StudentRegistrationController::class, 'store'])
     ->name('student.register');

// Check if email exists
Route::post('/check-email', [StudentRegistrationController::class, 'checkEmail'])
     ->name('check.email');

// Student login POST
Route::post('/student/login', [StudentLoginController::class, 'login'])
     ->name('student.login');

// Student logout
Route::post('/student/logout', [StudentLoginController::class, 'logout'])
     ->name('student.logout');

// Extra registration details
Route::get('/register/details/{user}', [StudentRegistrationController::class, 'showDetailsForm'])
     ->name('register.details');
Route::post('/register/details/{user}', [StudentRegistrationController::class, 'submitDetails']);

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
     ->name('admin.modules.destroy')
     ->middleware(['admin.auth']);

Route::get('/admin/modules/by-program', [AdminModuleController::class, 'getModulesByProgram'])
    ->name('admin.modules.by-program')
    ->middleware(['admin.auth']);
    
// New routes for enhanced LMS functionality
Route::post('/admin/modules/update-order', [AdminModuleController::class, 'updateOrder'])
    ->name('admin.modules.updateOrder')
    ->middleware(['admin.auth']);
    
Route::get('/admin/modules/{module:modules_id}/preview', [AdminModuleController::class, 'preview'])
    ->name('admin.modules.preview')
    ->middleware(['admin.auth']);

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
| Professor Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:professor'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard', [ProfessorDashboardController::class, 'index'])
         ->name('dashboard');
    
    Route::get('/programs', [ProfessorDashboardController::class, 'programs'])
         ->name('programs');
    
    // Student Calendar
    Route::get('/student/calendar', [StudentDashboardController::class, 'calendar'])
         ->name('student.calendar');
    
    // Student Courses - Dynamic route for any course
    Route::get('/student/course/{courseId}', [StudentDashboardController::class, 'course'])
         ->name('student.course');
         
    // Legacy routes - keep for backward compatibility
    Route::get('/student/courses/calculus1', [StudentDashboardController::class, 'course'])
         ->defaults('courseId', 1)
         ->name('student.courses.calculus1');
         
    Route::get('/student/courses/calculus2', [StudentDashboardController::class, 'course'])
         ->defaults('courseId', 2)
         ->name('student.courses.calculus2');
    
    // New route for viewing individual modules
    Route::get('/student/module/{moduleId}', [StudentDashboardController::class, 'module'])
         ->name('student.module');
         
    // Route for marking a module as complete via AJAX
    Route::post('/student/module/{moduleId}/complete', [StudentDashboardController::class, 'markModuleComplete'])
         ->name('student.module.complete');
});

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
