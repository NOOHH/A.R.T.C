<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage');
})->name('home');

Route::get('/enrollment', function () {
    return view('enrollment'); // Make sure this matches your view name
});

Route::get('/login', function () {
    return view('Login.login');
})->name('login');

// Route::get('/admin-dashboard', function () {
//     return view('admin.admin-dashboard');
// });


