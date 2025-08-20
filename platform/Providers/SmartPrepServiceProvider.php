<?php

namespace Platform\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SmartPrepServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Future: bind platform-wide services (tenant provisioning, billing, etc.)
    }

    public function boot(): void
    {
        // Register platform routes with different prefix to avoid conflicts
        Route::get('/platform/health', function() {
            return response()->json(['status' => 'ok', 'app' => 'SmartPrep Platform - New Architecture']);
        })->name('platform.health');
        
        // Debug route to confirm this provider is loading
        Route::get('/platform/debug', function() {
            return 'Platform service provider is working! New multi-tenant architecture loaded.';
        })->name('platform.debug');
    }
}
