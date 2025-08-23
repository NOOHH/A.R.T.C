<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ADD MISSING BRAND SETTINGS TO TENANT DATABASE ===\n\n";

try {
    // Switch to tenant database
    config(['database.connections.tenant.database' => 'smartprep_artc']);
    DB::purge('tenant');
    config(['database.default' => 'tenant']);
    DB::purge('mysql');
    
    $currentDb = DB::select('SELECT DATABASE() as db')[0]->db;
    echo "1. Connected to database: $currentDb\n";
    
    echo "2. Checking existing brand settings:\n";
    $existingBrandName = DB::table('ui_settings')->where('section', 'navbar')->where('setting_key', 'brand_name')->first();
    $existingBrandLogo = DB::table('ui_settings')->where('section', 'navbar')->where('setting_key', 'brand_logo')->first();
    
    echo "   - brand_name exists: " . ($existingBrandName ? 'YES' : 'NO') . "\n";
    echo "   - brand_logo exists: " . ($existingBrandLogo ? 'YES' : 'NO') . "\n";
    
    echo "3. Adding missing brand settings:\n";
    
    if (!$existingBrandName) {
        DB::table('ui_settings')->insert([
            'section' => 'navbar',
            'setting_key' => 'brand_name',
            'setting_value' => 'SmartPrep Learning Center',  // Custom brand name for ARTC
            'setting_type' => 'text',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added brand_name setting\n";
    } else {
        echo "   - brand_name already exists with value: {$existingBrandName->setting_value}\n";
    }
    
    if (!$existingBrandLogo) {
        DB::table('ui_settings')->insert([
            'section' => 'navbar',
            'setting_key' => 'brand_logo',
            'setting_value' => '', // Empty for now, can be set later
            'setting_type' => 'file',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added brand_logo setting\n";
    } else {
        echo "   - brand_logo already exists with value: {$existingBrandLogo->setting_value}\n";
    }
    
    echo "4. Testing Setting::getGroup after adding brand settings:\n";
    $navbarGroup = \App\Models\Setting::getGroup('navbar');
    if (isset($navbarGroup['brand_name'])) {
        echo "   ✅ brand_name now available: '{$navbarGroup['brand_name']}'\n";
    } else {
        echo "   ❌ brand_name still not available\n";
    }
    
    echo "5. Testing specific brand_name value:\n";
    $brandName = \App\Models\Setting::get('navbar', 'brand_name', 'DEFAULT');
    echo "   Brand name: '$brandName'\n";
    
    echo "6. Verifying all navbar settings:\n";
    $allNavbarSettings = DB::table('ui_settings')->where('section', 'navbar')->get();
    echo "   Total navbar settings: " . $allNavbarSettings->count() . "\n";
    foreach ($allNavbarSettings as $setting) {
        if (in_array($setting->setting_key, ['brand_name', 'brand_logo'])) {
            echo "   - {$setting->setting_key}: {$setting->setting_value}\n";
        }
    }
    
    // Switch back to main
    config(['database.default' => 'mysql']);
    DB::purge('tenant');
    
    echo "\n✅ Brand settings have been added to tenant database!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Complete ===\n";
