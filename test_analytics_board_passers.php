<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing board passers analytics...\n";
    
    // Set session data to simulate authenticated admin
    session(['user_type' => 'admin']);
    session(['user_name' => 'Administrator']);
    session(['user_id' => 1]);
    
    // Test the public API
    $controller = new \App\Http\Controllers\AdminAnalyticsController();
    
    // Test board passer stats
    $stats = $controller->getBoardPasserStats();
    echo "\nBoard Passer Stats:\n";
    echo $stats->getContent() . "\n";
    
    // Test the analytics data endpoint
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'year' => '2025',
        'month' => '7'
    ]);
    
    $data = $controller->getData($request);
    $dataArray = json_decode($data->getContent(), true);
    
    echo "\nAnalytics Data:\n";
    if (isset($dataArray['tables']['boardPassers'])) {
        echo "Board Passers Count: " . count($dataArray['tables']['boardPassers']) . "\n";
        if (count($dataArray['tables']['boardPassers']) > 0) {
            echo "First Board Passer:\n";
            print_r($dataArray['tables']['boardPassers'][0]);
        }
    } else {
        echo "No board passers data in response\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} 