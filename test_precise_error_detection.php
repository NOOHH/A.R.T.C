<?php
require_once 'vendor/autoload.php';

echo "=== PRECISE ERROR DETECTION TEST ===\n\n";

// Test for exact SQLSTATE error pattern
$testUrl = 'http://127.0.0.1:8000/professor/announcements';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "URL: $testUrl\n";
echo "HTTP Code: $httpCode\n";

// More precise error detection
$hasSqlstateError = str_contains($response, 'SQLSTATE[42S02]');
$hasProfessorsTableError = str_contains($response, "Table 'smartprep.professors' doesn't exist");
$hasIgnitionError = str_contains($response, 'Illuminate\\Database\\QueryException');

echo "Has SQLSTATE error: " . ($hasSqlstateError ? 'YES' : 'NO') . "\n";
echo "Has professors table error: " . ($hasProfessorsTableError ? 'YES' : 'NO') . "\n";
echo "Has Laravel DB exception: " . ($hasIgnitionError ? 'YES' : 'NO') . "\n";

if ($hasSqlstateError || $hasProfessorsTableError || $hasIgnitionError) {
    echo "\n❌ FAILED: Database error detected in response\n";
    echo "Error location in response:\n";
    
    // Find the exact error location
    if ($hasSqlstateError) {
        $pos = strpos($response, 'SQLSTATE[42S02]');
        echo "SQLSTATE error at position: $pos\n";
        echo "Context: " . substr($response, max(0, $pos-100), 200) . "\n";
    }
} else {
    echo "\n✅ PASSED: No database errors detected\n";
    echo "Response appears to be normal HTML content\n";
}

echo "\n" . str_repeat("-", 60) . "\n";

// Test log file for new errors since our fix
echo "Checking log for new errors since timestamp: 06:25:00\n";
$logContent = file_get_contents('storage/logs/laravel.log');
$lines = explode("\n", $logContent);

$newErrors = [];
foreach ($lines as $line) {
    if (str_contains($line, '[2025-08-23 06:25:') || str_contains($line, '[2025-08-23 06:26:')) {
        if (str_contains($line, 'professors') && str_contains($line, 'table') && str_contains($line, 'exist')) {
            $newErrors[] = $line;
        }
    }
}

if (empty($newErrors)) {
    echo "✅ No new professors table errors in recent logs\n";
} else {
    echo "❌ Found " . count($newErrors) . " new errors:\n";
    foreach ($newErrors as $error) {
        echo "- " . substr($error, 0, 150) . "...\n";
    }
}

echo "\n=== PRECISE TEST COMPLETE ===\n";
?>
