<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Database Connection Debug Test ===\n\n";

try {
    echo "1. Testing current default database connection:\n";
    $defaultConnection = config('database.default');
    echo "   Default connection: $defaultConnection\n";
    
    echo "2. Testing direct database query:\n";
    $currentDb = DB::select('SELECT DATABASE() as db')[0]->db;
    echo "   Current database: $currentDb\n";
    
    echo "3. Testing available databases:\n";
    $databases = DB::select('SHOW DATABASES');
    echo "   Available databases:\n";
    foreach ($databases as $db) {
        echo "   - " . $db->Database . "\n";
    }
    
    echo "4. Testing tenant service:\n";
    echo "   Checking available clients:\n";
    try {
        $clients = \App\Models\Client::all();
        echo "   Found " . $clients->count() . " clients\n";
        foreach ($clients as $client) {
            echo "   - ID: {$client->id}, Slug: {$client->slug}\n";
        }
        
        if ($clients->count() > 0) {
            $client = $clients->first();
            echo "   Using client: {$client->slug}\n";
        
        $tenantObj = \App\Models\Tenant::where('slug', $client->slug)->first();
        if ($tenantObj) {
            echo "   Tenant found: {$tenantObj->database_name}\n";
            
            $tenantService = app(\App\Services\TenantService::class);
            echo "   Before switch - Current DB: " . (DB::select('SELECT DATABASE() as db')[0]->db ?? 'NULL') . "\n";
            
            echo "   Switching to tenant...\n";
            $tenantService->switchToTenant($tenantObj);
            
            echo "   After switch - Current DB: " . (DB::select('SELECT DATABASE() as db')[0]->db ?? 'NULL') . "\n";
            echo "   Default connection: " . config('database.default') . "\n";
            
            echo "5. Testing Settings table access:\n";
            try {
                $schema = DB::getSchemaBuilder();
                echo "   Checking for tables:\n";
                
                if ($schema->hasTable('settings')) {
                    echo "   - settings table exists\n";
                    $settingsCount = DB::table('settings')->count();
                    echo "   - settings count: $settingsCount\n";
                } else {
                    echo "   - settings table does not exist\n";
                }
                
                if ($schema->hasTable('ui_settings')) {
                    echo "   - ui_settings table exists\n";
                    $uiSettingsCount = DB::table('ui_settings')->count();
                    echo "   - ui_settings count: $uiSettingsCount\n";
                } else {
                    echo "   - ui_settings table does not exist\n";
                }
                
                if ($schema->hasTable('admin_settings')) {
                    echo "   - admin_settings table exists\n";
                    $adminSettingsCount = DB::table('admin_settings')->count();
                    echo "   - admin_settings count: $adminSettingsCount\n";
                } else {
                    echo "   - admin_settings table does not exist\n";
                }
                
            } catch (\Exception $e) {
                echo "   ERROR accessing tables: " . $e->getMessage() . "\n";
            }
            
            echo "6. Testing Setting::getGroup method:\n";
            try {
                $navbarSettings = \App\Models\Setting::getGroup('navbar');
                echo "   Navbar settings loaded successfully\n";
                echo "   Navbar settings type: " . gettype($navbarSettings) . "\n";
                if (is_object($navbarSettings) && method_exists($navbarSettings, 'toArray')) {
                    echo "   Navbar settings: " . json_encode($navbarSettings->toArray()) . "\n";
                } else {
                    echo "   Navbar settings: " . json_encode($navbarSettings) . "\n";
                }
            } catch (\Exception $e) {
                echo "   ERROR loading navbar settings: " . $e->getMessage() . "\n";
            }
            
            echo "7. Switching back to main:\n";
            $tenantService->switchToMain();
            echo "   After switch back - Current DB: " . (DB::select('SELECT DATABASE() as db')[0]->db ?? 'NULL') . "\n";
            
        } else {
            echo "   ERROR: No clients found in database\n";
        }
    } catch (\Exception $e) {
        echo "   ERROR getting clients: " . $e->getMessage() . "\n";
        
        // Try direct tenant approach
        echo "   Trying direct tenant approach:\n";
        $tenants = \App\Models\Tenant::all();
        echo "   Found " . $tenants->count() . " tenants\n";
        foreach ($tenants as $tenant) {
            echo "   - Name: {$tenant->name}, DB: {$tenant->database_name}\n";
        }
        
        if ($tenants->count() > 0) {
            $tenantObj = $tenants->first();
            echo "   Using tenant: {$tenantObj->database_name}\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
