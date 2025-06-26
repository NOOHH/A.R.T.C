<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home'); // Make sure this matches your view name
});


Route::post('/register', function (){
    return 'Successfully login';
});
