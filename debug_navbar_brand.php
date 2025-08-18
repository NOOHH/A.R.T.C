<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING NAVBAR BRAND NAME ===\n\n";

try {
    // Check what UiSetting::getSection('navbar') returns
    $navbarSettings = \App\Models\UiSetting::getSection('navbar');
    echo "UiSetting::getSection('navbar') result: ";
    if ($navbarSettings) {
        echo "Found " . count($navbarSettings) . " settings\n";
        foreach ($navbarSettings as $key => $value) {
            echo "  $key = $value\n";
        }
    } else {
        echo "NULL or empty\n";
    }
    
    // Check what's actually in the database
    echo "\n=== DATABASE CHECK ===\n";
    $dbSettings = \Illuminate\Support\Facades\DB::table('ui_settings')
        ->where('section', 'navbar')
        ->get();
    
    echo "Found " . $dbSettings->count() . " navbar settings in database:\n";
    foreach ($dbSettings as $setting) {
        echo "  $setting->setting_key = $setting->setting_value\n";
    }
    
    // Check specifically for brand_name
    echo "\n=== BRAND NAME CHECK ===\n";
    $brandName = \App\Models\UiSetting::get('navbar', 'brand_name', 'NOT_FOUND');
    echo "UiSetting::get result: $brandName\n";
    
    // Check the most recent brand_name setting
    echo "\n=== RECENT BRAND NAME UPDATES ===\n";
    $recentBrandName = \Illuminate\Support\Facades\DB::table('ui_settings')
        ->where('section', 'navbar')
        ->where('setting_key', 'brand_name')
        ->orderBy('updated_at', 'desc')
        ->first();
    
    if ($recentBrandName) {
        echo "Most recent brand_name: $recentBrandName->setting_value\n";
        echo "Last updated: $recentBrandName->updated_at\n";
    } else {
        echo "No brand_name setting found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
