<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentLoginController;
use App\Http\Controllers\StudentRegistrationController;


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

Route::get('/enrollment', function () {
    return view('enrollment');
});

Route::get('/enrollment/full', function () {
    return view('registration.Full_enrollment');
})->name('enrollment.full');

Route::get('/enrollment/modular', function () {
    return view('registration.Modular_enrollment');
})->name('enrollment.modular');

Route::get('/login', function () {
    return view('Login.login');
})->name('login');

Route::get('/admin-dashboard', function () {
    return view('admin.admin-dashboard');
})->name('admin.dashboard');

// ✅ Form routes
Route::post('/student/register', [StudentRegistrationController::class, 'store'])->name('student.register');
Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login');

// Optional: future modular support
Route::get('/register/details/{user}', [StudentRegistrationController::class, 'showDetailsForm'])->name('register.details');
Route::post('/register/details/{user}', [StudentRegistrationController::class, 'submitDetails']);
