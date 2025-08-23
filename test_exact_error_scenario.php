<?php
require_once 'vendor/autoload.php';

echo "=== TESTING EXACT ERROR SCENARIO ===\n\n";

// Based on the error context, the failing request was:
// http://127.0.0.1:8000/professor/announcements
// With referer: http://127.0.0.1:8000/t/draft/test1/professor/dashboard?website=15&preview=true&t=1755929903003

echo "Testing the exact scenario that was failing...\n";

// First, set up the session context by visiting the preview dashboard
echo "1. Setting up preview context by visiting dashboard...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/t/draft/test1/professor/dashboard?website=15&preview=true');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Dashboard setup result: HTTP $httpCode\n";

// Now test the announcements page with the same session
echo "\n2. Testing announcements page with preview session context...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/professor/announcements');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Referer: http://127.0.0.1:8000/t/draft/test1/professor/dashboard?website=15&preview=true&t=1755929903003'
]);

$startTime = microtime(true);
$response = curl_exec($ch);
$endTime = microtime(true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$duration = round(($endTime - $startTime) * 1000, 2);

echo "Announcements result: HTTP $httpCode (${duration}ms)\n";

// Check for database errors
if (str_contains(strtolower($response), 'professors') && str_contains(strtolower($response), 'table') && str_contains(strtolower($response), 'exist')) {
    echo "❌ FAILED: Still getting professors table error\n";
    echo "Error snippet: " . substr($response, 0, 500) . "...\n";
} elseif (str_contains(strtolower($response), 'sqlstate')) {
    echo "❌ FAILED: SQL error detected\n";
    echo "Error snippet: " . substr($response, 0, 500) . "...\n";
} elseif ($httpCode === 200) {
    echo "✅ PASSED: No database errors - announcement page loading successfully\n";
} elseif ($httpCode >= 300 && $httpCode < 400) {
    echo "✅ PASSED: Redirect response (expected for auth handling)\n";
} else {
    echo "⚠️  HTTP $httpCode - needs investigation\n";
    echo "Response snippet: " . substr($response, 0, 300) . "...\n";
}

// Clean up
if (file_exists('test_cookies.txt')) {
    unlink('test_cookies.txt');
}

echo "\n=== SCENARIO TEST COMPLETE ===\n";
?>
