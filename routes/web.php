<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentLoginController;
use App\Http\Controllers\StudentRegistrationController;

// Optional: DB test route
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Connected to DB successfully!";
    } catch (\Exception $e) {
        return "❌ DB connection failed: " . $e->getMessage();
    }
});

Route::get('/', function () {
    return view('homepage');
})->name('home');

Route::get('/enrollment', [App\Http\Controllers\StudentRegistrationController::class, 'showEnrollmentSelection'])->name('enrollment.selection');

Route::get('/enrollment/full', [App\Http\Controllers\StudentRegistrationController::class, 'showRegistrationForm'])->name('enrollment.full');

Route::get('/enrollment/modular', function () {
    return view('registration.Modular_enrollment');
})->name('enrollment.modular');

Route::get('/login', function () {
    return view('Login.login');
})->name('login');

// Admin Dashboard and Registration Management
Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/registration/{id}', [AdminController::class, 'showRegistration']);
Route::post('/admin/registration/{id}/approve', [AdminController::class, 'approve']);
Route::post('/admin/registration/{id}/reject', [AdminController::class, 'reject']);

// Student registration/login
Route::post('/student/register', [StudentRegistrationController::class, 'store'])->name('student.register');
Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login');

// Optional: future modular support
Route::get('/register/details/{user}', [StudentRegistrationController::class, 'showDetailsForm'])->name('register.details');
Route::post('/register/details/{user}', [StudentRegistrationController::class, 'submitDetails']);

// Admin student registration view
Route::get('/admin-student-registration', [AdminController::class, 'studentRegistration'])->name('admin.student.registration');
Route::get('/admin-student-registration/pending', [AdminController::class, 'studentRegistration'])->name('admin.student.registration.pending');
Route::get('/admin-student-registration/history', [AdminController::class, 'studentRegistrationHistory'])->name('admin.student.registration.history');

// View specific student registration submission details
Route::get('/admin-student-registration/view/{id}', [AdminController::class, 'showRegistrationDetails'])->name('admin.student.registration.view');
