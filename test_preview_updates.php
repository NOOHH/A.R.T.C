<?php
require_once 'vendor/autoload.php';

// Properly initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;

echo "=== NAVBAR BRAND NAME PREVIEW UPDATE TEST ===\n\n";

// Get current brand name
$currentBrandName = UiSetting::get('navbar', 'brand_name', 'Ascendo Review and Training Center');
echo "Current navbar brand name: $currentBrandName\n\n";

// Test different brand names
$testNames = [
    "🔥 REAL-TIME PREVIEW TEST 🔥",
    "✨ Auto-Save Working! ✨", 
    "⚡ Instant Updates ⚡"
];

echo "Testing navbar brand name updates:\n";
foreach ($testNames as $i => $testName) {
    echo sprintf("%d. Testing: %s\n", $i + 1, $testName);
    
    // Save the test name
    UiSetting::set('navbar', 'brand_name', $testName, 'text');
    
    // Verify it was saved
    $saved = UiSetting::get('navbar', 'brand_name');
    echo sprintf("   Saved: %s\n", $saved === $testName ? '✅ SUCCESS' : '❌ FAILED');
    echo sprintf("   Database value: %s\n\n", $saved);
    
    // Wait a moment
    usleep(500000); // 0.5 seconds
}

// Restore original name
UiSetting::set('navbar', 'brand_name', $currentBrandName, 'text');
echo "Restored original brand name: $currentBrandName\n";

echo "\n=== TESTING INSTRUCTIONS ===\n";
echo "1. Open: http://127.0.0.1:8000/dashboard/customize-website\n";
echo "2. Change the 'Brand Name' field value\n";
echo "3. Wait 3 seconds (auto-save delay)\n";
echo "4. Look at the preview iframe - the navbar should update instantly!\n";
echo "5. Check browser console for: 'Updated navbar brand name to: [NEW_VALUE]'\n";
echo "\n✨ The preview should now update in real-time without full page refresh! ✨\n";
?>
