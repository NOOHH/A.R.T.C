<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Check tenant settings for test1
$tenantService = app(\App\Services\TenantService::class);

try {
    // Switch to main database first
    $tenantService->switchToMain();
    
    // Find test1 tenant
    $tenant = \App\Models\Tenant::where('slug', 'test1')->first();
    
    if ($tenant) {
        echo "Found tenant: {$tenant->name} (slug: {$tenant->slug})\n";
        echo "Database name: {$tenant->database_name}\n\n";
        
        // Switch to tenant database
        $tenantService->switchToTenant($tenant);
        
        // Check if settings table exists
        if (\Schema::hasTable('settings')) {
            echo "Settings table exists in tenant database\n";
            
            // Get navbar settings
            $navbarSettings = \App\Models\Setting::where('group', 'navbar')->get();
            echo "Navbar settings:\n";
            foreach ($navbarSettings as $setting) {
                echo "  {$setting->key}: {$setting->value}\n";
            }
            
            // Get professor panel settings
            $professorSettings = \App\Models\Setting::where('group', 'professor_panel')->get();
            echo "\nProfessor panel settings:\n";
            foreach ($professorSettings as $setting) {
                echo "  {$setting->key}: {$setting->value}\n";
            }
            
        } else {
            echo "Settings table does NOT exist in tenant database\n";
        }
        
    } else {
        echo "Tenant 'test1' not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    // Switch back to main
    $tenantService->switchToMain();
}
