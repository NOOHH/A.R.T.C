<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return view('homepage'); // Make sure this matches your view name
});

Route::get('/enrollment', function () {
    return view('enrollment'); // Make sure this matches your view name
});


Route::get('/enrollment/full', function () {
    return view('registration.Full_enrollment'); // ✅ use dot notation
})->name('enrollment.full');


Route::post('/register', function (){
    return 'Successfully login';
});

Route::get('/student/register', function () {
    return view('student-register');
});

Route::post('/student/register', [StudentController::class, 'store'])->name('student.register');
