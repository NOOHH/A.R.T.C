<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentLoginController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\AdminProgramController;
use App\Http\Controllers\AdminPackageController;
use App\Models\Program;

// ✅ DB test route
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Connected to DB successfully!";
    } catch (\Exception $e) {
        return "❌ DB connection failed: " . $e->getMessage();
    }
});

// ✅ Homepage
Route::get('/', function () {
    return view('homepage');
})->name('home');

// ✅ Enrollment selection page
Route::get('/enrollment', [StudentRegistrationController::class, 'showEnrollmentSelection'])
     ->name('enrollment.selection');

// ✅ Full enrollment form (GET)
Route::get('/enrollment/full', [StudentRegistrationController::class, 'showRegistrationForm'])
     ->name('enrollment.full');

// ✅ Modular enrollment form (GET)
Route::get('/enrollment/modular', function () {
    $programs  = Program::all();
    $programId = request('program_id');
    return view('registration.Modular_enrollment', compact('programs','programId'));
})->name('enrollment.modular');

// ✅ Login page
Route::get('/login', function () {
    return view('Login.login');
})->name('login');

// ✅ Admin dashboard
Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])
     ->name('admin.dashboard');

// ✅ Admin registration detail fetch
Route::get('/admin/registration/{id}', [AdminController::class, 'showRegistration']);

// ✅ Admin registration approve/reject
Route::post('/admin/registration/{id}/approve', [AdminController::class, 'approve'])
     ->name('admin.registration.approve');
Route::post('/admin/registration/{id}/reject', [AdminController::class, 'reject'])
     ->name('admin.registration.reject');

// ✅ Student register POST
Route::post('/student/register', [StudentRegistrationController::class, 'store'])
     ->name('student.register');

// ✅ Student login POST
Route::post('/student/login', [StudentLoginController::class, 'login'])
     ->name('student.login');

// ✅ Future extra registration details
Route::get('/register/details/{user}', [StudentRegistrationController::class, 'showDetailsForm'])
     ->name('register.details');
Route::post('/register/details/{user}', [StudentRegistrationController::class, 'submitDetails']);

// ✅ Admin student registration list
Route::get('/admin-student-registration', [AdminController::class, 'studentRegistration'])
     ->name('admin.student.registration');
Route::get('/admin-student-registration/pending', [AdminController::class, 'studentRegistration'])
     ->name('admin.student.registration.pending');
Route::get('/admin-student-registration/history', [AdminController::class, 'studentRegistrationHistory'])
     ->name('admin.student.registration.history');

// ✅ Admin view student registration details
Route::get('/admin-student-registration/view/{id}', [AdminController::class, 'showRegistrationDetails'])
     ->name('admin.student.registration.view');

<<<<<<< HEAD
// ✅ Admin programs
Route::get('/admin/programs', [AdminProgramController::class, 'index'])
     ->name('admin.programs.index');
Route::post('/admin/programs', [AdminProgramController::class, 'store'])
     ->name('admin.programs.store');
Route::delete('/admin/programs/{id}', [AdminProgramController::class, 'destroy'])
     ->name('admin.programs.delete');
Route::get('/admin/programs/{id}/enrollments', [AdminProgramController::class, 'enrollments'])
     ->name('admin.programs.enrollments');
=======
// Admin Programs
Route::get('/admin/programs', [App\Http\Controllers\AdminProgramController::class, 'index'])->name('admin.programs.index');
Route::post('/admin/programs', [App\Http\Controllers\AdminProgramController::class, 'store'])->name('admin.programs.store');
Route::delete('/admin/programs/{id}', [App\Http\Controllers\AdminProgramController::class, 'destroy'])->name('admin.programs.delete');
Route::get('/admin/programs/{id}/enrollments', [AdminProgramController::class, 'enrollments'])->name('admin.programs.enrollments');

// Admin Packages
Route::get('/admin/packages', [App\Http\Controllers\Admin\PackageController::class, 'index'])->name('admin.packages.index');
Route::post('/admin/packages', [App\Http\Controllers\Admin\PackageController::class, 'store'])->name('admin.packages.store');
Route::delete('/admin/packages/{id}', [App\Http\Controllers\Admin\PackageController::class, 'destroy'])->name('admin.packages.delete');
>>>>>>> main
