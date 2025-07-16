<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check current referral settings
$settings = DB::table('admin_settings')->whereIn('setting_key', ['referral_enabled', 'referral_required'])->get();
echo "Current referral settings:\n";
foreach($settings as $setting) {
    echo "{$setting->setting_key}: {$setting->setting_value}\n";
}

// Test if signup page will show referral field
$enabled = DB::table('admin_settings')->where('setting_key', 'referral_enabled')->value('setting_value');
$required = DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value');

echo "\nSignup form should " . ($enabled === '1' ? 'SHOW' : 'HIDE') . " referral field\n";
echo "Referral field should be " . ($required === '1' ? 'REQUIRED' : 'OPTIONAL') . "\n";

// Let's also check if we have any test data for analytics
echo "\n=== Referral Analytics Data ===\n";
$referrals = DB::table('referrals')->get();
echo "Total referrals in database: " . $referrals->count() . "\n";

if ($referrals->count() > 0) {
    foreach($referrals->take(3) as $referral) {
        echo "ID: {$referral->id}, Code: {$referral->referral_code}, Student: {$referral->student_id}, Type: {$referral->referrer_type}\n";
    }
}
