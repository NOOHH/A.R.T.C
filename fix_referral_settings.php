<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::table('admin_settings')->updateOrInsert(
    ['setting_key' => 'referral_required'],
    ['setting_value' => '0']
);

echo "referral_required setting added/updated\n";

$settings = DB::table('admin_settings')->whereIn('setting_key', ['referral_enabled', 'referral_required'])->get();
foreach($settings as $setting) {
    echo "{$setting->setting_key}: {$setting->setting_value}\n";
}
