<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Smartprep\HomepageController;
use App\Http\Controllers\Smartprep\Auth\LoginController;
use App\Http\Controllers\Smartprep\Auth\RegisterController;
use App\Http\Controllers\Smartprep\Auth\VerificationController;
use App\Http\Controllers\Smartprep\Auth\ForgotPasswordController;
use App\Http\Controllers\Smartprep\Auth\ResetPasswordController;
use App\Http\Controllers\Smartprep\Auth\ConfirmPasswordController;
use App\Http\Controllers\Smartprep\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Smartprep\Admin\WebsiteRequestsController;
use App\Http\Controllers\Smartprep\Admin\AdminSettingsController;
use App\Http\Controllers\Smartprep\Admin\ClientsController;
use App\Http\Controllers\Smartprep\Dashboard\ClientDashboardController;
use App\Http\Controllers\Smartprep\Dashboard\CustomizeWebsiteController;
use Illuminate\Support\Facades\DB;

// Public routes
Route::get('/', [HomepageController::class, 'welcome'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/email/verify', [VerificationController::class, 'notice'])
    ->middleware('smartprep.auth')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['smartprep.auth', 'signed'])
    ->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware('smartprep.auth')
    ->name('verification.resend');

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])
    ->middleware('smartprep.auth')
    ->name('password.confirm');
Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm'])
    ->middleware('smartprep.auth')
    ->name('password.confirm.submit');

// Auth-protected routes
Route::middleware('smartprep.auth')->group(function () {
    // Admin
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/website-requests', [WebsiteRequestsController::class, 'index'])->name('admin.website-requests');
    Route::post('/admin/website-requests/{request}/approve', [WebsiteRequestsController::class, 'approve'])->name('admin.approve-request');
    Route::post('/admin/website-requests/{request}/reject', [WebsiteRequestsController::class, 'reject'])->name('admin.reject-request');
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings', [AdminSettingsController::class, 'save'])->name('admin.settings.save');
    Route::get('/admin/clients', [ClientsController::class, 'index'])->name('admin.clients');
    Route::get('/admin/clients/create', [ClientsController::class, 'create'])->name('admin.clients.create');
    Route::get('/admin/clients/{id}/edit', [ClientsController::class, 'edit'])->name('admin.clients.edit');
    Route::post('/admin/clients/{id}/archive', [ClientsController::class, 'archive'])->name('admin.clients.archive');
    Route::post('/admin/clients/{id}/unarchive', [ClientsController::class, 'unarchive'])->name('admin.clients.unarchive');
    Route::delete('/admin/clients/{id}', [ClientsController::class, 'destroy'])->name('admin.clients.destroy');

    // Client dashboards
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/cache-test', [CustomizeWebsiteController::class, 'cacheTest'])->name('dashboard.cache-test');
    Route::get('/dashboard/customize-website', [CustomizeWebsiteController::class, 'current'])->name('dashboard.customize');
    Route::post('/dashboard/submit-customized-website', [CustomizeWebsiteController::class, 'submitCustomization'])->name('dashboard.submit-customized-website');
    Route::get('/dashboard/customize-website-old', [CustomizeWebsiteController::class, 'old'])->name('dashboard.customize-old');
    Route::get('/dashboard/customize-website-new', [CustomizeWebsiteController::class, 'new'])->name('dashboard.customize-new');
});

// Optional: simple DB check endpoint within SmartPrep scope for verification only
Route::get('/debug/which-db', function () {
    $db = DB::select('select database() as db');
    return ['connection' => config('database.default'), 'database' => $db[0]->db ?? null];
})->name('debug.db');
