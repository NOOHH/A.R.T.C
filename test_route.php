<?php

use Illuminate\Support\Facades\Route;

// Create a test route to access our test page
Route::get('/test-quiz-link', function () {
    return view('test-quiz-link');
})->name('test.quiz.link');
