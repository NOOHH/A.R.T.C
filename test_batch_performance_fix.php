<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

// Set admin session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';  
$_SESSION['logged_in'] = true;

echo "=== Testing Fixed getBatchPerformance Method ===\n\n";

// Test the analytics controller
$controller = new App\Http\Controllers\AdminAnalyticsController();

echo "Testing getBatchPerformance method:\n";
try {
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getBatchPerformance');
    $method->setAccessible(true);
    $result = $method->invoke($controller, []);
    
    echo "Batch Performance Data:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    
    if (empty($result)) {
        echo "✅ SUCCESS: Method now returns empty array when no students exist!\n";
    } else {
        echo "❌ ISSUE: Method still returns data:\n";
        foreach ($result as $batch) {
            echo "  - {$batch['name']}: {$batch['students']} students\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Also check if the web endpoint works
echo "\n=== Testing Web Analytics Endpoint ===\n";
try {
    $response = $controller->getAnalyticsData();
    $data = $response->getData(true);
    
    if (isset($data['batchPerformance'])) {
        echo "Batch Performance from web endpoint:\n";
        if (empty($data['batchPerformance'])) {
            echo "✅ SUCCESS: Web endpoint also returns empty batch performance!\n";
        } else {
            echo "❌ ISSUE: Web endpoint still shows batch data:\n";
            foreach ($data['batchPerformance'] as $batch) {
                echo "  - {$batch['name']}: {$batch['students']} students\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Error testing web endpoint: " . $e->getMessage() . "\n";
}

?>
