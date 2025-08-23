<?php
require_once 'vendor/autoload.php';

echo "=== COMPREHENSIVE PROFESSOR DATABASE ERROR FIX TEST ===\n\n";

$tests = [
    'Search Route Test' => [
        'url' => 'http://127.0.0.1:8000/search-now?query=test&type=professors',
        'description' => 'Testing search route that directly queries professors table'
    ],
    'Search Route Preview Test' => [
        'url' => 'http://127.0.0.1:8000/search-now?query=test&type=professors&preview=true',
        'description' => 'Testing search route in preview mode'
    ],
    'Professor Dashboard Test' => [
        'url' => 'http://127.0.0.1:8000/professor/dashboard',
        'description' => 'Testing regular professor dashboard (should redirect or handle gracefully)'
    ],
    'Professor Preview Dashboard Test' => [
        'url' => 'http://127.0.0.1:8000/t/draft/test1/professor/dashboard?preview=true&website=15',
        'description' => 'Testing professor preview dashboard'
    ]
];

foreach ($tests as $testName => $test) {
    echo "Running: $testName\n";
    echo "URL: {$test['url']}\n";
    echo "Description: {$test['description']}\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test['url']);
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
        echo "❌ FAILED: Response contains professors table error\n";
        echo "Error snippet: " . substr($response, 0, 200) . "...\n";
    } elseif ($httpCode >= 200 && $httpCode < 400) {
        echo "✅ PASSED: No professors table errors detected\n";
    } elseif ($httpCode >= 300 && $httpCode < 400) {
        echo "✅ PASSED: Redirect (expected for auth-protected routes)\n";
    } else {
        echo "⚠️  WARNING: HTTP $httpCode - may need investigation\n";
    }
    
    echo str_repeat("-", 60) . "\n\n";
}

echo "=== CHECKING RECENT LARAVEL LOGS ===\n\n";

// Check for recent professor table errors in logs
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -100); // Get last 100 lines
    
    $errorCount = 0;
    foreach ($recentLines as $line) {
        if (str_contains($line, 'professors') && str_contains($line, 'table') && str_contains($line, 'exist')) {
            $errorCount++;
        }
    }
    
    if ($errorCount > 0) {
        echo "❌ Found $errorCount recent professors table errors in logs\n";
    } else {
        echo "✅ No recent professors table errors found in logs\n";
    }
} else {
    echo "⚠️  Laravel log file not found\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "Summary: Applied fixes to:\n";
echo "1. TenantContextHelper::detectProfessorTenant() - added preview mode protection\n";
echo "2. routes/web.php search route - improved preview mode detection\n";
echo "3. CheckProfessorAuth middleware - enhanced preview mode detection\n";
echo "\nThese should prevent SQLSTATE[42S02] professors table errors.\n";
?>
