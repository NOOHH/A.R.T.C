<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Admin Settings Status ===\n";
    $settings = DB::table('admin_settings')->whereIn('setting_key', ['referral_enabled', 'referral_required'])->get();
    
    foreach($settings as $setting) {
        echo "{$setting->setting_key}: {$setting->setting_value}\n";
    }
    
    if($settings->isEmpty()) {
        echo "No referral settings found. Inserting defaults...\n";
        DB::table('admin_settings')->insertOrIgnore([
            ['setting_key' => 'referral_enabled', 'setting_value' => '1'],
            ['setting_key' => 'referral_required', 'setting_value' => '0']
        ]);
        echo "Default settings inserted.\n";
        
        // Verify insertion
        $settings = DB::table('admin_settings')->whereIn('setting_key', ['referral_enabled', 'referral_required'])->get();
        echo "\nAfter insertion:\n";
        foreach($settings as $setting) {
            echo "{$setting->setting_key}: {$setting->setting_value}\n";
        }
    }
    
    echo "\n=== Admin Settings Table Structure ===\n";
    $allSettings = DB::table('admin_settings')->get();
    echo "Total settings count: " . $allSettings->count() . "\n";
    foreach($allSettings as $setting) {
        echo "{$setting->setting_key}: {$setting->setting_value}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
