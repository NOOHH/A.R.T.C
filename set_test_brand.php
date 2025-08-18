<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;

UiSetting::set('navbar', 'brand_name', '🚀 REAL-TIME PREVIEW READY! 🚀', 'text');
echo 'Brand name set to: ' . UiSetting::get('navbar', 'brand_name') . "\n";
?>
