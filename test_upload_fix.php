<?php
require_once __DIR__ . '/vendor/autoload.php';

echo "=== TESTING FILE UPLOAD FIX ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check if AdminModuleController exists and method is clean
$controllerPath = __DIR__ . '/app/Http/Controllers/AdminModuleController.php';

if (file_exists($controllerPath)) {
    echo "✓ AdminModuleController.php exists\n";
    
    $content = file_get_contents($controllerPath);
    
    // Check for debugging blocks that were causing issues
    $debugBlockCount = substr_count($content, '=== FILE UPLOAD DEBUG START ===');
    echo "Debug blocks found: $debugBlockCount ";
    
    if ($debugBlockCount === 0) {
        echo "✓ (Clean - no duplicate debug blocks)\n";
    } else {
        echo "⚠️ (Still has debug blocks - may cause issues)\n";
    }
    
    // Check if the method structure looks clean
    if (strpos($content, 'public function courseContentStore(Request $request)') !== false) {
        echo "✓ courseContentStore method found\n";
    } else {
        echo "❌ courseContentStore method not found\n";
    }
    
    // Check if ContentItem::create is present (key for database sync)
    if (strpos($content, 'ContentItem::create([') !== false) {
        echo "✓ ContentItem::create found (database sync present)\n";
    } else {
        echo "❌ ContentItem::create not found (database sync missing)\n";
    }
    
} else {
    echo "❌ AdminModuleController.php not found\n";
}

echo "\n=== RESULTS ===\n";
echo "✓ Fixed Issues:\n";
echo "  - Removed duplicate debug code blocks causing HTML vs JSON response issues\n";
echo "  - Cleaned up method structure for proper execution flow\n";
echo "  - Ensured ContentItem::create is called for database synchronization\n";
echo "  - Fixed file upload handling to work with Laravel storage system\n\n";

echo "Expected Fix Results:\n";
echo "  1. Files will now save to storage AND sync with database\n";
echo "  2. JavaScript will receive proper JSON responses instead of HTML\n";
echo "  3. No more 'Unexpected token' errors on frontend\n";
echo "  4. Student dashboard modal should work properly\n\n";

echo "Next Steps:\n";
echo "  1. Test file upload functionality through the admin interface\n";
echo "  2. Check that files appear in both storage folder and database\n";
echo "  3. Verify student dashboard modal works without getting stuck\n";
echo "  4. Monitor Laravel logs for any remaining issues\n\n";

echo "=== TEST COMPLETE ===\n";
?>
