<?php
/**
 * Script to add director dashboard access settings
 * Using the correct key-value structure for admin_settings table
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 ADDING DIRECTOR DASHBOARD ACCESS SETTINGS...\n\n";

try {
    // Settings to add/update for director dashboard access
    $directorSettings = [
        'director_dashboard_access' => 'true',
        'director_can_view_all_pages' => 'true', 
        'director_full_admin_access' => 'true',
        'enable_director_mode' => 'true'
    ];
    
    echo "📊 CURRENT DIRECTOR SETTINGS:\n";
    $currentSettings = DB::table('admin_settings')->get();
    foreach ($currentSettings as $setting) {
        echo "   - {$setting->setting_key}: {$setting->setting_value}\n";
    }
    
    echo "\n🔄 ADDING/UPDATING DIRECTOR ACCESS SETTINGS...\n";
    
    foreach ($directorSettings as $key => $value) {
        // Check if setting exists
        $exists = DB::table('admin_settings')
            ->where('setting_key', $key)
            ->exists();
        
        if ($exists) {
            // Update existing setting
            DB::table('admin_settings')
                ->where('setting_key', $key)
                ->update([
                    'setting_value' => $value,
                    'is_active' => 1,
                    'updated_at' => now()
                ]);
            echo "   ✅ UPDATED: $key = $value\n";
        } else {
            // Insert new setting
            DB::table('admin_settings')->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✅ CREATED: $key = $value\n";
        }
    }
    
    echo "\n📋 FINAL DIRECTOR SETTINGS:\n";
    $finalSettings = DB::table('admin_settings')
        ->where('setting_key', 'like', 'director_%')
        ->orWhere('setting_key', 'like', 'enable_director_%')
        ->get();
    
    foreach ($finalSettings as $setting) {
        echo "   ✅ {$setting->setting_key}: {$setting->setting_value}\n";
    }
    
    echo "\n🎯 DIRECTOR DASHBOARD ACCESS CONFIGURED!\n";
    echo "   The director should now have full access to:\n";
    echo "   - Main director dashboard\n";
    echo "   - All admin pages and features\n";
    echo "   - Complete system functionality\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📍 FILE: " . $e->getFile() . "\n";
    echo "📍 LINE: " . $e->getLine() . "\n";
}

echo "\n✅ DIRECTOR ACCESS CONFIGURATION COMPLETE!\n";
