<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\DB;

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

// Route::get('/admin-dashboard', function () {
//     return view('admin.admin-dashboard');
// });


Route::get('/student/register', function () {
    return view('student-register');
});

use App\Http\Controllers\StudentRegistrationController;
Route::post('/register', [StudentRegistrationController::class, 'store'])->name('student.register');
use App\Http\Controllers\StudentLoginController;

Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login');

Route::get('/admin-dashboard', function () {
    return view('admin.admin-dashboard');
})->name('admin.dashboard');


Route::get('/admin-dashboard', function () {
    return view('admin.admin-dashboard');
})->name('admin.dashboard');


Route::get('/register/details/{user}', [StudentRegistrationController::class, 'showDetailsForm'])->name('register.details');
Route::post('/register/details/{user}', [StudentRegistrationController::class, 'submitDetails']);