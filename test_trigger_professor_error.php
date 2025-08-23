<?php
require_once 'vendor/autoload.php';

echo "=== TRIGGERING PROFESSOR ROUTE TO IDENTIFY SOURCE ===\n\n";

// Clear the log file to see only new errors
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $currentSize = filesize($logFile);
    echo "Current log file size: $currentSize bytes\n";
}

echo "Triggering professor dashboard route...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/professor/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Response: HTTP $httpCode\n";

// Check for new log entries
if (file_exists($logFile)) {
    $newSize = filesize($logFile);
    echo "New log file size: $newSize bytes\n";
    
    if ($newSize > $currentSize) {
        echo "New log entries detected. Checking last 10 lines:\n";
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        $lastLines = array_slice($lines, -10);
        
        foreach ($lastLines as $line) {
            if (!empty($line)) {
                echo "LOG: $line\n";
            }
        }
    } else {
        echo "No new log entries\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
?>
