<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage'); // Make sure this matches your view name
});

Route::get('/enrollment', function () {
    return view('enrollment'); // Make sure this matches your view name
});

Route::post('/register', function (){
    return 'Successfully login';
});

