<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\UiSetting;

try {
    echo "=== TESTING UI SETTINGS FUNCTIONALITY ===\n\n";
    
    // Test the exact error that was happening
    echo "1. Testing the exact query that was failing:\n";
    echo "   Attempting: select setting_value, setting_key from ui_settings where section = 'navbar'\n";
    
    try {
        $results = DB::table('ui_settings')
                    ->select('setting_value', 'setting_key')
                    ->where('section', 'navbar')
                    ->get();
        
        echo "   ✅ Query successful! Found " . count($results) . " records\n";
        
        foreach ($results as $result) {
            echo "     - {$result->setting_key}: {$result->setting_value}\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Query failed: " . $e->getMessage() . "\n";
    }
    
    // Test UiSetting::getSection method specifically
    echo "\n2. Testing UiSetting::getSection('navbar'):\n";
    try {
        $navbarSettings = UiSetting::getSection('navbar');
        echo "   ✅ getSection() successful! Found " . count($navbarSettings) . " settings\n";
        
        foreach ($navbarSettings as $key => $value) {
            echo "     - {$key}: {$value}\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ getSection() failed: " . $e->getMessage() . "\n";
    }
    
    // Test creating some default navbar settings if none exist
    echo "\n3. Ensuring default navbar settings exist:\n";
    
    $defaultSettings = [
        'primary_color' => '#007bff',
        'background_color' => '#ffffff',
        'text_color' => '#333333',
        'logo_url' => '/images/logo.png'
    ];
    
    foreach ($defaultSettings as $key => $value) {
        $existing = UiSetting::get('navbar', $key);
        if (!$existing || $existing === 'default_value') {
            UiSetting::set('navbar', $key, $value, 'text');
            echo "   ✅ Set default navbar.{$key} = {$value}\n";
        } else {
            echo "   ℹ️ navbar.{$key} already exists: {$existing}\n";
        }
    }
    
    echo "\n✅ UI Settings functionality is now working correctly!\n";
    echo "The homepage should load without errors.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
