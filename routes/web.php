<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentLoginController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\AdminProgramController;
use App\Http\Controllers\AdminProfessorController;
use App\Http\Controllers\AdminPackageController;    // ← NEW
use App\Models\Program;

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
    $programs = Program::all();
    $packages = \App\Models\Package::all();
    $enrollmentType = 'modular';
    return view('registration.Modular_enrollment', compact('programs', 'packages', 'enrollmentType'));
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
Route::get(
    '/admin/programs/{program}/enrollments',
    [AdminProgramController::class, 'enrollments']
)->name('admin.programs.enrollments');

/*
|--------------------------------------------------------------------------
| Admin Modules
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AdminModuleController;

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

// Show “Add New Package” form
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
| Admin Professors
|--------------------------------------------------------------------------
*/
// Professors list
Route::get('/admin/professors', [AdminProfessorController::class, 'index'])
     ->name('admin.professors.index');
