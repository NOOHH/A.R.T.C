<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Client;
use App\Models\Tenant;
use App\Models\Setting;
use App\Models\UiSetting;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FIXING TENANT DATABASE SETTINGS ===\n\n";

try {
    $client = Client::where('id', 9)->first();
    if (!$client) {
        echo "❌ Client ID 9 not found\n";
        exit(1);
    }
    
    $tenant = Tenant::where('slug', $client->slug)->first();
    if (!$tenant) {
        echo "❌ Tenant not found\n";
        exit(1);
    }
    
    echo "Working with tenant: {$tenant->slug} (DB: {$tenant->database_name})\n\n";
    
    $tenantService = app(TenantService::class);
    
    // 1. Switch to tenant database
    echo "1. Switching to tenant database...\n";
    $tenantService->switchToTenant($tenant);
    
    // 2. Run migration to create settings table
    echo "2. Creating settings table...\n";
    try {
        // Set the database connection for migrations
        config(['database.connections.tenant.database' => $tenant->database_name]);
        
        // Run the migration manually
        DB::statement("CREATE TABLE IF NOT EXISTS settings (
            id bigint unsigned NOT NULL AUTO_INCREMENT,
            `group` varchar(100) NOT NULL,
            `key` varchar(100) NOT NULL,
            `value` text,
            `type` varchar(50) DEFAULT 'text',
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY settings_group_key_unique (`group`,`key`),
            KEY settings_group_index (`group`),
            KEY settings_key_index (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        echo "✅ Settings table created\n";
    } catch (\Exception $e) {
        echo "⚠️  Settings table might already exist: " . $e->getMessage() . "\n";
    }
    
    // 3. Switch back to main database to get admin settings
    echo "\n3. Copying admin settings to tenant...\n";
    $tenantService->switchToMain();
    
    // Get all admin settings
    $adminSettings = [
        'general' => UiSetting::getSection('general')->toArray(),
        'navbar' => UiSetting::getSection('navbar')->toArray(),
        'branding' => UiSetting::getSection('branding')->toArray(),
        'homepage' => UiSetting::getSection('homepage')->toArray(),
        'student_portal' => UiSetting::getSection('student_portal')->toArray(),
        'professor_panel' => UiSetting::getSection('professor_panel')->toArray(),
        'admin_panel' => UiSetting::getSection('admin_panel')->toArray(),
        'student_sidebar' => UiSetting::getSection('student_sidebar')->toArray(),
        'professor_sidebar' => UiSetting::getSection('professor_sidebar')->toArray(),
        'admin_sidebar' => UiSetting::getSection('admin_sidebar')->toArray(),
        'advanced' => UiSetting::getSection('advanced')->toArray(),
    ];
    
    echo "Found admin settings sections: " . implode(', ', array_keys($adminSettings)) . "\n";
    
    // 4. Switch back to tenant and copy settings
    $tenantService->switchToTenant($tenant);
    
    $totalSettings = 0;
    foreach ($adminSettings as $section => $settings) {
        echo "Copying {$section}: ";
        $count = 0;
        foreach ($settings as $key => $value) {
            Setting::set($section, $key, $value);
            $count++;
            $totalSettings++;
        }
        echo "{$count} settings\n";
    }
    
    echo "✅ Copied {$totalSettings} total settings\n\n";
    
    // 5. Test navbar settings
    echo "4. Testing navbar settings...\n";
    $navbarSettings = Setting::getGroup('navbar');
    echo "Navbar settings in tenant DB:\n";
    foreach ($navbarSettings as $key => $value) {
        echo "  - {$key}: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
    }
    
    // 6. Switch back to main
    $tenantService->switchToMain();
    echo "\n✅ Back to main database\n";
    
    echo "\n=== TENANT DATABASE FIXED ===\n";
    echo "Now try updating navbar settings again:\n";
    echo "http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=9\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    try {
        app(TenantService::class)->switchToMain();
    } catch (\Exception $switchError) {
        echo "❌ Failed to switch back to main database\n";
    }
}
