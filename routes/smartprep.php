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
use App\Http\Controllers\Smartprep\Dashboard\ClientDashboardController;
use App\Http\Controllers\Smartprep\Dashboard\CustomizeWebsiteController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Public routes
Route::get('/', [HomepageController::class, 'welcome'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/email/verify', [VerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware('auth')
    ->name('verification.resend');

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])
    ->middleware('auth')
    ->name('password.confirm');
Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm'])
    ->middleware('auth')
    ->name('password.confirm.submit');

// Auth-protected routes
Route::middleware('auth')->group(function () {
    // Admin
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/website-requests', [WebsiteRequestsController::class, 'index'])->name('admin.website-requests');

    // Client dashboards
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/cache-test', [CustomizeWebsiteController::class, 'cacheTest'])->name('dashboard.cache-test');
    Route::get('/dashboard/customize-website', [CustomizeWebsiteController::class, 'current'])->name('dashboard.customize');
    Route::get('/dashboard/customize-website-old', [CustomizeWebsiteController::class, 'old'])->name('dashboard.customize-old');
    Route::get('/dashboard/customize-website-new', [CustomizeWebsiteController::class, 'new'])->name('dashboard.customize-new');
});

// Optional: simple DB check endpoint within SmartPrep scope for verification only
Route::get('/debug/which-db', function () {
    $db = DB::select('select database() as db');
    return ['connection' => config('database.default'), 'database' => $db[0]->db ?? null];
})->name('debug.db');

// TEMP: login test endpoint (local only)
Route::get('/debug/test-login', function (Request $request) {
    if (!app()->environment('local')) {
        abort(403);
    }
    $login = $request->query('email');
    $password = $request->query('password');
    $db = DB::select('select database() as db');
    $user = \App\Models\Smartprep\User::where('email', $login)
        ->orWhere(function($q) use ($login){ $q->whereNotNull('username')->where('username',$login); })
        ->first();
    $ok = $user && $password && Hash::check($password, $user->password);
    if ($ok) {
        Auth::guard('web')->login($user);
        $request->session()->regenerate();
    }
    return response()->json([
        'ok' => $ok,
        'user' => $user ? ['id'=>$user->id, 'email'=>$user->email] : null,
        'connection' => config('database.default'),
        'database' => $db[0]->db ?? null,
        'redirect' => $ok ? route('smartprep.dashboard') : null,
    ]);
})->name('debug.test-login');
