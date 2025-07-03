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
// use App\Http\Controllers\AdminProfessorController;  // TODO: Create this controller
use App\Http\Controllers\AdminPackageController;    // ← NEW
use App\Http\Controllers\AdminSettingsController;
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

// Delete a program
Route::delete('/admin/programs/{id}', [AdminProgramController::class, 'destroy'])
     ->name('admin.programs.delete');

// View enrollments for a program
Route::get('/admin/programs/{id}/enrollments', [AdminProgramController::class, 'enrollments'])
     ->name('admin.programs.enrollments');

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

// Update module
Route::put('/admin/modules/{module:modules_id}', [AdminModuleController::class, 'update'])
     ->name('admin.modules.update');

// Delete a module
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

// New simplified settings page
Route::get('/admin/settings/new', [AdminSettingsController::class, 'newIndex'])
     ->name('admin.settings.new.index');

// Fixed settings page
Route::get('/admin/settings/fixed', [AdminSettingsController::class, 'fixedIndex'])
     ->name('admin.settings.fixed.index');

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

/*
|--------------------------------------------------------------------------
| Admin Professors
|--------------------------------------------------------------------------
*/
// Professors list - TODO: Create AdminProfessorController
// Route::get('/admin/professors', [AdminProfessorController::class, 'index'])
//      ->name('admin.professors.index');

/*
|--------------------------------------------------------------------------
| Student Dashboard Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['student.auth'])->group(function () {
    // Student Dashboard
    Route::get('/student/dashboard', [StudentDashboardController::class, 'dashboard'])
         ->name('student.dashboard');
    
    // Student Settings
    Route::get('/student/settings', [StudentDashboardController::class, 'settings'])
         ->name('student.settings');
    Route::put('/student/settings', [StudentDashboardController::class, 'updateSettings'])
         ->name('student.settings.update');
    
    // Student Calendar
    Route::get('/student/calendar', [StudentDashboardController::class, 'calendar'])
         ->name('student.calendar');
    
    // Student Courses
    Route::get('/student/courses/calculus1', [StudentDashboardController::class, 'course'])
         ->defaults('courseId', 1)
         ->name('student.courses.calculus1');
         
    Route::get('/student/courses/calculus2', [StudentDashboardController::class, 'course'])
         ->defaults('courseId', 2)
         ->name('student.courses.calculus2');
});
