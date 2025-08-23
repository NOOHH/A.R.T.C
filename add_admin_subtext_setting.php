<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ADD ADMIN PORTAL SUBTEXT CUSTOMIZATION ===\n\n";

try {
    // Switch to tenant database
    config(['database.connections.tenant.database' => 'smartprep_artc']);
    DB::purge('tenant');
    config(['database.default' => 'tenant']);
    DB::purge('mysql');
    
    echo "1. Adding admin portal subtext setting:\n";
    
    $existingSubtext = DB::table('ui_settings')->where('section', 'navbar')->where('setting_key', 'admin_subtext')->first();
    
    if (!$existingSubtext) {
        DB::table('ui_settings')->insert([
            'section' => 'navbar',
            'setting_key' => 'admin_subtext',
            'setting_value' => 'Learning Portal', // Custom subtext
            'setting_type' => 'text',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added admin_subtext setting: 'Learning Portal'\n";
    } else {
        echo "   - admin_subtext already exists with value: {$existingSubtext->setting_value}\n";
    }
    
    // Switch back to main
    config(['database.default' => 'mysql']);
    DB::purge('tenant');
    
    echo "2. Testing Setting::getGroup after adding subtext:\n";
    
    // Switch back to tenant to test
    config(['database.connections.tenant.database' => 'smartprep_artc']);
    DB::purge('tenant');
    config(['database.default' => 'tenant']);
    DB::purge('mysql');
    
    $navbarGroup = \App\Models\Setting::getGroup('navbar');
    if (isset($navbarGroup['admin_subtext'])) {
        echo "   ✅ admin_subtext now available: '{$navbarGroup['admin_subtext']}'\n";
    } else {
        echo "   ❌ admin_subtext still not available\n";
    }
    
    // Switch back to main
    config(['database.default' => 'mysql']);
    DB::purge('tenant');
    
    echo "\n✅ Admin subtext customization has been added!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Complete ===\n";
