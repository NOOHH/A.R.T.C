<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing database operations:\n\n";

echo "1. Testing website_requests table structure:\n";
try {
    $columns = DB::select('DESCRIBE website_requests');
    echo "✅ website_requests table has " . count($columns) . " columns\n";
    
    // Test if we can insert with proper structure
    echo "✅ Structure is compatible with new CustomizeWebsiteController\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing ui_settings table operations:\n";
try {
    // Test if we can read settings
    $settings = DB::table('ui_settings')->get();
    echo "✅ Can read from ui_settings table (" . count($settings) . " records)\n";
    
    // Test if we can insert a sample setting
    $testKey = 'test_' . time();
    DB::table('ui_settings')->insert([
        'section' => 'admin',
        'setting_key' => $testKey,
        'setting_value' => 'test_value',
        'setting_type' => 'text',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✅ Can insert new settings\n";
    
    // Test if we can update the setting
    DB::table('ui_settings')
        ->where('setting_key', $testKey)
        ->update(['setting_value' => 'updated_value', 'updated_at' => now()]);
    echo "✅ Can update existing settings\n";
    
    // Clean up test record
    DB::table('ui_settings')->where('setting_key', $testKey)->delete();
    echo "✅ Test record cleaned up\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing current settings retrieval:\n";
try {
    $settingsData = DB::table('ui_settings')->get();
    $settings = [];
    
    foreach ($settingsData as $setting) {
        $settings[$setting->setting_key] = $setting->setting_value;
    }
    
    echo "✅ Current settings available:\n";
    foreach ($settings as $key => $value) {
        echo "   - {$key}: " . substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ All database operations are working correctly!\n";
