<?php
echo "🔍 BATCH MODAL INVESTIGATION\n";
echo "=" . str_repeat("=", 35) . "\n\n";

$moduleViewPath = 'resources/views/admin/admin-modules/admin-modules.blade.php';
$content = file_get_contents($moduleViewPath);

echo "📄 Searching for modal elements in admin-modules.blade.php:\n";

// Search for various modal-related patterns
$modalPatterns = [
    'batchModalBg' => '/id=["\']batchModalBg["\']/',
    'batch modal div' => '/<div[^>]*batch[^>]*modal[^>]*>/',
    'modal background' => '/<div[^>]*modal[^>]*background[^>]*>/',
    'modal wrapper' => '/<div[^>]*modal[^>]*wrapper[^>]*>/',
    'upload modal' => '/<div[^>]*upload[^>]*modal[^>]*>/',
];

$foundElements = [];

foreach ($modalPatterns as $name => $pattern) {
    if (preg_match($pattern, $content, $matches)) {
        echo "✅ Found $name: " . trim($matches[0]) . "\n";
        $foundElements[] = $name;
    } else {
        echo "❌ Not found: $name\n";
    }
}

if (empty($foundElements)) {
    echo "\n⚠️  No modal elements found! The modal HTML might be missing.\n";
    echo "This explains why the button doesn't work - there's no modal to show.\n";
} else {
    echo "\n✅ Found modal elements, investigating further...\n";
}

// Check if the button is redirecting instead of opening modal
echo "\n🔍 Checking for any redirect behavior in JavaScript:\n";

$jsRedirectPatterns = [
    'window.location' => '/window\.location\s*=/',
    'location.href' => '/location\.href\s*=/',
    'redirect' => '/redirect\s*\(/',
    'navigate' => '/navigate\s*\(/',
];

foreach ($jsRedirectPatterns as $name => $pattern) {
    if (preg_match($pattern, $content)) {
        echo "⚠️  Found $name pattern - might be causing redirect\n";
    } else {
        echo "✅ No $name pattern found\n";
    }
}

echo "\n🎯 Next steps:\n";
if (empty($foundElements)) {
    echo "1. Modal HTML is missing - need to add the batch upload modal\n";
    echo "2. JavaScript expects modal but it doesn't exist\n";
    echo "3. Button click handler tries to show non-existent modal\n";
} else {
    echo "1. Modal HTML exists - check JavaScript event handling\n";
    echo "2. Verify modal CSS is not preventing display\n";
    echo "3. Check for JavaScript errors in browser console\n";
}

echo "\n📋 Analysis complete.\n";
?>
