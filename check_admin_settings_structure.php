<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check table structure
$structure = DB::select('DESCRIBE admin_settings');
echo "=== Admin Settings Table Structure ===\n";
foreach($structure as $column) {
    echo "Field: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}, Extra: {$column->Extra}\n";
}

// Get existing data
echo "\n=== Current Data ===\n";
$settings = DB::table('admin_settings')->get();
foreach($settings as $setting) {
    echo "ID: {$setting->setting_id}, Key: {$setting->setting_key}, Value: {$setting->setting_value}\n";
}

// Find max ID
$maxId = DB::table('admin_settings')->max('setting_id') ?: 0;
echo "\nMax ID: {$maxId}\n";

// Insert with explicit ID
$newId = $maxId + 1;
try {
    DB::table('admin_settings')->insert([
        'setting_id' => $newId,
        'setting_key' => 'referral_required',
        'setting_value' => '0'
    ]);
    echo "Successfully inserted referral_required setting with ID: {$newId}\n";
} catch (Exception $e) {
    echo "Insert failed: " . $e->getMessage() . "\n";
    // Try update instead
    $existing = DB::table('admin_settings')->where('setting_key', 'referral_required')->first();
    if ($existing) {
        echo "Setting already exists, updating...\n";
        DB::table('admin_settings')->where('setting_key', 'referral_required')->update(['setting_value' => '0']);
        echo "Updated successfully\n";
    }
}
