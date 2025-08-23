<?php
require_once 'vendor/autoload.php';

echo "=== TESTING PROFESSOR ANNOUNCEMENT CONTROLLER FIX ===\n\n";

// Test the announcement route that was causing the database error
$testUrl = 'http://127.0.0.1:8000/professor/announcements';

echo "Testing URL: $testUrl\n";
echo "This should either:\n";
echo "1. Redirect if not authenticated (expected)\n";
echo "2. Show announcements if authenticated\n";
echo "3. NOT produce database errors\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, false);

$startTime = microtime(true);
$response = curl_exec($ch);
$endTime = microtime(true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$duration = round(($endTime - $startTime) * 1000, 2);

echo "Result: HTTP $httpCode (${duration}ms)\n";

// Check for database errors in response
if (str_contains(strtolower($response), 'professors') && str_contains(strtolower($response), 'table') && str_contains(strtolower($response), 'exist')) {
    echo "❌ FAILED: Response still contains professors table error\n";
    echo "Error snippet: " . substr($response, 0, 500) . "...\n";
} elseif (str_contains(strtolower($response), 'sqlstate')) {
    echo "❌ FAILED: Response contains SQL error\n";
    echo "Error snippet: " . substr($response, 0, 500) . "...\n";
} elseif ($httpCode >= 200 && $httpCode < 400) {
    echo "✅ PASSED: No database errors detected\n";
} else {
    echo "⚠️  Result: HTTP $httpCode - checking if it's an expected redirect or error\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// Now test the preview announcement route
echo "Testing Professor Announcement Preview Route:\n";
$previewUrl = 'http://127.0.0.1:8000/t/draft/test1/professor/announcements?preview=true&website=15';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $previewUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, false);

$startTime = microtime(true);
$response = curl_exec($ch);
$endTime = microtime(true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$duration = round(($endTime - $startTime) * 1000, 2);

echo "Preview URL: $previewUrl\n";
echo "Result: HTTP $httpCode (${duration}ms)\n";

if ($httpCode === 200) {
    echo "✅ PASSED: Preview route working\n";
} else {
    echo "❌ FAILED: Preview route returned HTTP $httpCode\n";
}

echo "\n=== CHECKING FOR NEW ERRORS IN LOG ===\n";

// Check for new professors table errors in logs
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -20); // Get last 20 lines
    
    $newErrorCount = 0;
    foreach ($recentLines as $line) {
        if (str_contains($line, '[2025-08-23') && str_contains($line, 'professors') && str_contains($line, 'table') && str_contains($line, 'exist')) {
            $newErrorCount++;
            echo "NEW ERROR: " . substr($line, 0, 150) . "...\n";
        }
    }
    
    if ($newErrorCount === 0) {
        echo "✅ No new professors table errors found in recent logs\n";
    } else {
        echo "❌ Found $newErrorCount new professors table errors\n";
    }
} else {
    echo "⚠️  Laravel log file not found\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
