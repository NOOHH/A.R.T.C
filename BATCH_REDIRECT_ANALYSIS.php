<?php
echo "🔍 BATCH UPLOAD BUTTON REDIRECTION ANALYSIS\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// The issue: Button redirects to wrong URL
echo "❌ Current (Wrong): http://127.0.0.1:8000/admin/modules/course-content-upload\n";
echo "✅ Expected (Correct): http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload?website=1\n\n";

// Let's find what's causing the redirect
echo "🔍 Step 1: Analyzing the batch upload button behavior\n";

$moduleViewPath = 'resources/views/admin/admin-modules/admin-modules.blade.php';
$content = file_get_contents($moduleViewPath);

// Search for JavaScript that might be handling the showBatchModal click
echo "Searching for JavaScript handling showBatchModal...\n";

// Look for patterns that might cause redirection
$redirectPatterns = [
    'location.href' => '/location\.href\s*=\s*[\'"]([^\'"]+)[\'"]/i',
    'window.location' => '/window\.location\s*=\s*[\'"]([^\'"]+)[\'"]/i',
    'redirect' => '/redirect\s*\(\s*[\'"]([^\'"]+)[\'"]/i',
    'route(' => '/route\s*\(\s*[\'"]([^\'"]+)[\'"]/i',
    'url(' => '/url\s*\(\s*[\'"]([^\'"]+)[\'"]/i',
];

$foundRedirects = [];

foreach ($redirectPatterns as $type => $pattern) {
    if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            if (strpos($match[1], 'course-content-upload') !== false || 
                strpos($match[1], 'admin/modules') !== false) {
                $foundRedirects[] = [
                    'type' => $type,
                    'full_match' => $match[0],
                    'url' => $match[1],
                    'line' => 'unknown'
                ];
                echo "🎯 Found $type redirect: {$match[1]}\n";
            }
        }
    }
}

if (empty($foundRedirects)) {
    echo "⚠️  No direct URL redirects found in JavaScript\n";
    echo "   The button might be:\n";
    echo "   1. Triggering a form submission\n";
    echo "   2. Using a hidden link\n";
    echo "   3. Handled by external JavaScript\n";
    echo "   4. Using Laravel route() helper that resolves to wrong route\n";
}

echo "\n🔍 Step 2: Checking for forms or hidden elements\n";

// Look for forms that might be submitted
if (preg_match('/showBatchModal.*?form/si', $content)) {
    echo "✅ Found form associated with showBatchModal\n";
} else {
    echo "❌ No form found associated with showBatchModal\n";
}

// Look for any route references to course-content-upload
$routePattern = '/route\s*\(\s*[\'"]([^\'"]*(course|upload|batch)[^\'"]*)[\'"]/i';
if (preg_match_all($routePattern, $content, $matches, PREG_SET_ORDER)) {
    echo "🎯 Found route references:\n";
    foreach ($matches as $match) {
        echo "   - {$match[1]}\n";
    }
} else {
    echo "❌ No route references found\n";
}

echo "\n🔍 Step 3: Checking JavaScript event handlers\n";

// Look for event handlers on showBatchModal
$eventPattern = '/showBatchModal.*?addEventListener|onclick.*showBatchModal/i';
if (preg_match($eventPattern, $content, $match)) {
    echo "✅ Found event handler: " . trim($match[0]) . "\n";
} else {
    echo "❌ No event handler found\n";
}

echo "\n🎯 Next: Examining the exact JavaScript behavior...\n";
?>
