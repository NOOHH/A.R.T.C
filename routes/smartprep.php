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
use Illuminate\Support\Facades\Auth;

// Public routes (ensure session is SmartPrep-scoped even on homepage)
Route::middleware(['web'])->group(function () {
    // Final route name will be 'smartprep.home' due to RouteServiceProvider 'as("smartprep.")'
    Route::get('/', [HomepageController::class, 'welcome'])->name('home');
});

// API endpoint for UI settings (for live preview)
Route::get('/api/ui-settings', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Helpers\UiSettingsHelper::getAll()
    ]);
})->name('api.ui-settings');

// API endpoint for programs (needed by frontend JavaScript)
Route::get('/api/programs', function () {
    $programs = \App\Models\Program::where('is_archived', false)
                                   ->select('program_id', 'program_name', 'program_description')
                                   ->get();
    
    return response()->json($programs);
})->name('smartprep.api.programs');

// API endpoint for sidebar settings (for student/professor/admin dashboards)
Route::get('/api/sidebar-settings', [ClientDashboardController::class, 'getSidebarSettings'])
    ->middleware('smartprep.auth')
    ->name('smartprep.api.sidebar-settings');

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
Route::middleware(['smartprep.auth', 'debug.smartprep'])->group(function () {
    // Admin
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/website-requests', [WebsiteRequestsController::class, 'index'])->name('admin.website-requests');
    Route::post('/admin/website-requests/purge-ghosts', [WebsiteRequestsController::class, 'purgeGhosts'])->name('admin.website-requests.purge');
    Route::post('/admin/website-requests/{request}/approve', [WebsiteRequestsController::class, 'approve'])->name('admin.approve-request');
    Route::post('/admin/website-requests/{request}/reject', [WebsiteRequestsController::class, 'reject'])->name('admin.reject-request');
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings', [AdminSettingsController::class, 'save'])->name('admin.settings.save');
    Route::post('/admin/settings/general', [AdminSettingsController::class, 'updateGeneral'])->name('admin.settings.update.general');
    Route::post('/admin/settings/navbar', [AdminSettingsController::class, 'updateNavbar'])->name('admin.settings.update.navbar');
    Route::post('/admin/settings/homepage', [AdminSettingsController::class, 'updateHomepage'])->name('admin.settings.update.homepage');
    Route::post('/admin/settings/branding', [AdminSettingsController::class, 'updateBranding'])->name('admin.settings.update.branding');
    Route::post('/admin/settings/student', [AdminSettingsController::class, 'updateStudent'])->name('admin.settings.update.student');
    Route::post('/admin/settings/professor', [AdminSettingsController::class, 'updateProfessor'])->name('admin.settings.update.professor');
    Route::post('/admin/settings/admin', [AdminSettingsController::class, 'updateAdmin'])->name('admin.settings.update.admin');
    Route::post('/admin/settings/sidebar', [AdminSettingsController::class, 'updateSidebar'])->name('admin.settings.update.sidebar');
    Route::get('/admin/clients', [ClientsController::class, 'index'])->name('admin.clients');
    Route::get('/admin/clients/create', [ClientsController::class, 'create'])->name('admin.clients.create');
    Route::get('/admin/clients/{id}/edit', [ClientsController::class, 'edit'])->name('admin.clients.edit');
    Route::patch('/admin/clients/{id}/archive', [ClientsController::class, 'archive'])->name('admin.clients.archive');
    Route::patch('/admin/clients/{id}/unarchive', [ClientsController::class, 'unarchive'])->name('admin.clients.unarchive');
    Route::delete('/admin/clients/{id}', [ClientsController::class, 'destroy'])->name('admin.clients.destroy');

    // --- Temporary debug utilities for tenant troubleshooting ---
    Route::get('/admin/tenants', function() {
        return response()->json([
            'tenants' => \App\Models\Tenant::select('id','slug','database_name','status')->orderBy('id','desc')->get()
        ]);
    })->name('admin.tenants.debug-list');

    Route::match(['get','post'],'/admin/tenants/ensure/{slug}', function($slug) {
        $client = \App\Models\Client::where('slug',$slug)->first();
        if(!$client) {
            return response()->json(['error' => 'Client not found for slug '.$slug], 404);
        }
        $dbName = $client->db_name;
        if(!$dbName) {
            $keyword = preg_replace('/^smartprep-/', '', $client->slug);
            $dbName = 'smartprep_' . \Illuminate\Support\Str::slug($keyword, '_');
        }
        $tenant = \App\Models\Tenant::updateOrCreate(
            ['slug' => $client->slug],
            [
                'name' => $client->name,
                'database_name' => $dbName,
                'domain' => $client->domain,
                'status' => $client->status ?? 'draft',
                'settings' => ['client_id' => $client->id]
            ]
        );
        return response()->json(['created' => true, 'tenant' => $tenant]);
    })->name('admin.tenants.ensure');
    // --- End debug utilities ---

    // Client dashboards
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/cache-test', [CustomizeWebsiteController::class, 'cacheTest'])->name('dashboard.cache-test');
    Route::get('/dashboard/customize-website', [CustomizeWebsiteController::class, 'current'])->name('dashboard.customize');
    // User-managed website deletion (non-admin) – allows a client to delete their own draft/ inactive site
    Route::delete('/dashboard/websites/{id}', [CustomizeWebsiteController::class, 'destroy'])
        ->name('dashboard.websites.destroy');
    Route::post('/dashboard/submit-customized-website', [CustomizeWebsiteController::class, 'submitCustomization'])->name('dashboard.submit-customized-website');
    Route::get('/dashboard/customize-website-old', [CustomizeWebsiteController::class, 'old'])->name('dashboard.customize-old');
    Route::get('/dashboard/customize-website-new', [CustomizeWebsiteController::class, 'new'])->name('dashboard.customize-new');
    // Website management (create / update / delete handled in CustomizeWebsiteController)
    Route::post('/dashboard/websites', [CustomizeWebsiteController::class, 'store'])->name('dashboard.websites.store');
    Route::patch('/dashboard/websites/{id}', [CustomizeWebsiteController::class, 'update'])->name('dashboard.websites.update');
});

// Optional: simple DB check endpoint within SmartPrep scope for verification only
Route::get('/debug/which-db', function () {
    $db = DB::select('select database() as db');
    return ['connection' => config('database.default'), 'database' => $db[0]->db ?? null];
})->name('debug.db');
