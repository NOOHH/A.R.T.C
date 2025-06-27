<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('homepage');
})->name('home');

Route::get('/enrollment', function () {
    return view('enrollment'); // Make sure this matches your view name
});


Route::get('/enrollment/full', function () {
    return view('registration.Full_enrollment'); // ✅ use dot notation
})->name('enrollment.full');

Route::get('/enrollment/modular', function () {
    return view('registration.Modular_enrollment'); // ✅ use dot notation
})->name('enrollment.modular');


Route::get('/login', function () {
    return view('Login.login');
})->name('login');

// Admin Dashboard and Registration Management
Route::get('/admin-dashboard', [AdminController::class, 'dashboard']);
Route::get('/admin/registration/{id}', [AdminController::class, 'showRegistration']);
Route::post('/admin/registration/{id}/approve', [AdminController::class, 'approve']);
Route::post('/admin/registration/{id}/reject', [AdminController::class, 'reject']);


Route::get('/student/register', function () {
    return view('student-register');
});

Route::post('/student/register', [StudentController::class, 'store'])->name('student.register');
