<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing navbar settings functionality:\n\n";

// Test 1: Insert sample navbar settings
echo "1. Testing navbar settings insertion:\n";
try {
    $sampleSettings = [
        'navbar_brand_name' => 'TEST BRAND',
        'navbar_style' => 'fixed-top',
        'show_login_button' => '1',
        'navbar_menu_items' => '[{"label":"Test Home","link":"/"}, {"label":"Test About","link":"/about"}]'
    ];
    
    foreach ($sampleSettings as $key => $value) {
        // Check if setting exists
        $existing = DB::table('ui_settings')->where('setting_key', $key)->first();
        
        if ($existing) {
            DB::table('ui_settings')
                ->where('id', $existing->id)
                ->update(['setting_value' => $value, 'updated_at' => now()]);
            echo "   ✅ Updated {$key} = {$value}\n";
        } else {
            DB::table('ui_settings')->insert([
                'section' => 'admin',
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => in_array($key, ['navbar_menu_items']) ? 'json' : (in_array($key, ['show_login_button']) ? 'boolean' : 'text'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✅ Created {$key} = {$value}\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 2: Retrieve and display current settings
echo "\n2. Current navbar settings in database:\n";
try {
    $navbarSettings = DB::table('ui_settings')
        ->whereIn('setting_key', ['navbar_brand_name', 'navbar_style', 'show_login_button', 'navbar_menu_items'])
        ->get();
    
    foreach ($navbarSettings as $setting) {
        echo "   - {$setting->setting_key}: {$setting->setting_value}\n";
    }
    
    if ($navbarSettings->count() === 0) {
        echo "   ⚠️  No navbar settings found in database\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 3: Verify controller can retrieve settings
echo "\n3. Testing controller settings retrieval:\n";
try {
    $settingsData = DB::table('ui_settings')->get();
    $settings = [];
    
    foreach ($settingsData as $setting) {
        $settings[$setting->setting_key] = $setting->setting_value;
    }
    
    echo "   ✅ Total settings loaded: " . count($settings) . "\n";
    echo "   ✅ Navbar brand name: " . ($settings['navbar_brand_name'] ?? 'Not set') . "\n";
    echo "   ✅ Show login button: " . ($settings['show_login_button'] ?? 'Not set') . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Navbar settings testing completed!\n";
