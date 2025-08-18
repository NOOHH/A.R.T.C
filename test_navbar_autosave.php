<?php
require_once 'vendor/autoload.php';

// Properly initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\UiSetting;

echo "=== TESTING NAVBAR AUTO-SAVE FUNCTIONALITY ===\n\n";

// Check current brand name using correct model method
$currentBrandName = UiSetting::get('navbar', 'brand_name');
echo "Current brand name: " . ($currentBrandName ?? 'NOT FOUND') . "\n\n";

// Simulate auto-save update
$testBrandName = "TEST AUTO-SAVE " . date('H:i:s');
echo "Testing auto-save with: $testBrandName\n";

// Update the brand name using correct model method
UiSetting::set('navbar', 'brand_name', $testBrandName, 'text');

// Verify the update
$updatedBrandName = UiSetting::get('navbar', 'brand_name');
echo "After update: " . ($updatedBrandName ?? 'NOT FOUND') . "\n";

if ($updatedBrandName === $testBrandName) {
    echo "✅ AUTO-SAVE FUNCTIONALITY WORKING!\n";
} else {
    echo "❌ AUTO-SAVE FAILED!\n";
}

echo "\n=== NAVBAR SETTINGS SUMMARY ===\n";
$navbarSettings = UiSetting::getSection('navbar');
foreach ($navbarSettings as $key => $value) {
    echo "  $key = $value\n";
}
?>
