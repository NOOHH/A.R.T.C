<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-navbar', function () {
    return view('debug-navbar');
})->name('debug.navbar');
