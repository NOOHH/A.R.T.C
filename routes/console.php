<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('debug:tenant-db', function () {
    $client = \App\Models\Client::find(9);
    $tenant = \App\Models\Tenant::where('slug', $client->slug)->first();
    
    $this->info('Client DB: ' . $client->db_name);
    $this->info('Tenant DB: ' . $tenant->database_name);
    
    // Test TenantService
    $tenantService = app(\App\Services\TenantService::class);
    
    $this->info('Switching to tenant...');
    $tenantService->switchToTenant($tenant);
    
    // Check what database we're connected to
    $currentDB = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
    $this->info('Current DB after switch: ' . $currentDB);
    
    // Check settings
    try {
        $settings = \App\Models\Setting::getGroup('navbar');
        if ($settings) {
            $this->info('Navbar brand_name: ' . ($settings['brand_name'] ?? 'NOT SET'));
        } else {
            $this->info('No navbar settings found');
        }
    } catch (\Exception $e) {
        $this->error('Error getting settings: ' . $e->getMessage());
    }
    
    $tenantService->switchToMain();
    $this->info('Switched back to main DB');
})->purpose('Debug tenant database switching');
