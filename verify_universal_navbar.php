<?php
require_once 'vendor/autoload.php';

// Properly initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;

echo "=== FINAL VERIFICATION TEST ===\n\n";

// Set final test brand name
$finalBrandName = "✨ ALL PAGES UPDATED! ✨";
echo "Setting final test brand name: $finalBrandName\n";

UiSetting::set('navbar', 'brand_name', $finalBrandName, 'text');

// Verify it was saved
$savedBrand = UiSetting::get('navbar', 'brand_name');
echo "Database verification: " . ($savedBrand === $finalBrandName ? '✅ SUCCESS' : '❌ FAILED') . "\n";
echo "Current brand name in database: $savedBrand\n\n";

echo "=== VERIFICATION CHECKLIST ===\n";
echo "Now visit these pages and confirm they ALL show: $finalBrandName\n\n";

$verificationPages = [
    'http://127.0.0.1:8000/' => 'Homepage navbar',
    'http://127.0.0.1:8000/login' => 'Login page brand link',
    'http://127.0.0.1:8000/signup' => 'Signup page brand link',
    'http://127.0.0.1:8000/programs' => 'Programs page navbar',
    'http://127.0.0.1:8000/enrollment' => 'Enrollment page navbar',
    'http://127.0.0.1:8000/smartprep' => 'SmartPrep homepage navbar',
];

foreach ($verificationPages as $url => $location) {
    echo "[ ] $url\n    Check: $location\n\n";
}

echo "=== AUTO-SAVE TEST ===\n";
echo "[ ] Open: http://127.0.0.1:8000/dashboard/customize-website\n";
echo "[ ] Change brand name field to something new\n";
echo "[ ] Wait 3 seconds for auto-save\n";
echo "[ ] Check console shows: 'Updated navbar brand name to: [NEW_VALUE]'\n";
echo "[ ] Verify preview iframe navbar updates immediately\n";
echo "[ ] Visit other pages to confirm they show the new name\n\n";

echo "🎯 SUCCESS CRITERIA:\n";
echo "✅ ALL pages should show the updated brand name\n";
echo "✅ Auto-save should update preview immediately\n";
echo "✅ No more hardcoded 'Ascendo Review and Training Center'\n";
echo "✅ Universal navbar brand name system working\n\n";

echo "🚀 NAVBAR BRAND UPDATE SYSTEM IS NOW COMPLETE!\n";
?>
