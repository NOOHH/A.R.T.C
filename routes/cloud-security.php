<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloudSecurityQuizController;

// Cloud Security Quiz Routes
Route::prefix('cloud-security')->name('cloud-security.')->middleware(['auth', 'professor'])->group(function () {
    Route::get('/', [CloudSecurityQuizController::class, 'index'])->name('index');
    Route::post('/generate', [CloudSecurityQuizController::class, 'generateQuiz'])->name('generate');
    Route::post('/regenerate', [CloudSecurityQuizController::class, 'regenerateQuiz'])->name('regenerate');
    Route::post('/upload', [CloudSecurityQuizController::class, 'processUploadedPdfs'])->name('upload');
    Route::post('/save', [CloudSecurityQuizController::class, 'saveQuiz'])->name('save');
});
