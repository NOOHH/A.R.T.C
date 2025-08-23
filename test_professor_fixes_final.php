<?php
require_once 'vendor/autoload.php';

echo "=== TESTING PROFESSOR PREVIEW ROUTES AFTER FIXES ===\n\n";

$testRoutes = [
    'Professor Dashboard' => 'http://127.0.0.1:8000/t/draft/test1/professor/dashboard?preview=true&website=15',
    'Professor Programs' => 'http://127.0.0.1:8000/t/draft/test1/professor/programs?preview=true&website=15',
    'Professor Modules' => 'http://127.0.0.1:8000/t/draft/test1/professor/modules?preview=true&website=15',
    'Professor Meetings' => 'http://127.0.0.1:8000/t/draft/test1/professor/meetings?preview=true&website=15',
    'Professor Grading' => 'http://127.0.0.1:8000/t/draft/test1/professor/grading?preview=true&website=15'
];

$passCount = 0;
$totalCount = count($testRoutes);

foreach ($testRoutes as $routeName => $url) {
    echo "Testing: $routeName\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
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
    
    if ($httpCode === 200) {
        echo "✅ PASSED: HTTP 200 (${duration}ms)\n";
        $passCount++;
    } else {
        echo "❌ FAILED: HTTP $httpCode (${duration}ms)\n";
    }
    
    echo str_repeat("-", 50) . "\n";
}

echo "\n=== RESULTS ===\n";
echo "Passed: $passCount/$totalCount professor preview routes\n";

if ($passCount === $totalCount) {
    echo "🎉 ALL PROFESSOR PREVIEW ROUTES WORKING!\n";
} else {
    echo "⚠️  Some routes failed - may need investigation\n";
}

echo "\n=== FINAL VERIFICATION ===\n";
echo "Testing search functionality again:\n";

// Test search with professors type to ensure no database errors
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/search-now?query=sarah&type=professors');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && !str_contains(strtolower($response), 'sqlstate')) {
    echo "✅ Search route working without database errors\n";
} else {
    echo "❌ Search route has issues\n";
}

echo "\n=== COMPREHENSIVE FIX COMPLETE ===\n";
?>
