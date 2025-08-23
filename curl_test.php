<?php
/**
 * Laravel Artisan Command Test for Archived Content
 */

echo "Testing archived content method through artisan...\n";

// Test the URL directly with verbose output
$output = shell_exec('curl -v "http://127.0.0.1:8000/t/draft/test11/admin/archived" 2>&1');

echo "Full curl output:\n";
echo "================\n";
echo $output;
echo "\n\n";

// Check for redirects in the verbose output
if (strpos($output, '302') !== false || strpos($output, 'Location:') !== false) {
    echo "❌ REDIRECT DETECTED\n";
    echo "The request is being redirected, likely by middleware\n";
} else {
    echo "✅ No redirect detected\n";
}

// Check what we actually got
if (strpos($output, 'Login') !== false) {
    echo "❌ Got login page\n";
} else if (strpos($output, 'Archived Content') !== false) {
    echo "✅ Got archived content page\n";
} else {
    echo "⚠️  Got unknown response\n";
}
?>
