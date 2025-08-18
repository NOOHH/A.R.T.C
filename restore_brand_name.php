<?php
require_once 'vendor/autoload.php';

// Properly initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;

// Restore original brand name
UiSetting::set('navbar', 'brand_name', '🚀 ADMIN FIXED & WORKING! 🚀', 'text');
echo 'Brand name restored to: ' . UiSetting::get('navbar', 'brand_name') . "\n";
?>
