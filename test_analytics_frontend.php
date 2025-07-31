<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing analytics frontend data...\n";
    
    // Set session data to simulate authenticated admin
    session(['user_type' => 'admin']);
    session(['user_name' => 'Administrator']);
    session(['user_id' => 1]);
    
    // Test the analytics data endpoint (same as frontend)
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'year' => '2025',
        'month' => '7'
    ]);
    
    $controller = new \App\Http\Controllers\AdminAnalyticsController();
    $response = $controller->getData($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    
    $data = json_decode($response->getContent(), true);
    
    echo "\nAnalytics Data Structure:\n";
    echo "Keys in data: " . implode(', ', array_keys($data)) . "\n";
    
    if (isset($data['tables'])) {
        echo "Keys in tables: " . implode(', ', array_keys($data['tables'])) . "\n";
        
        if (isset($data['tables']['boardPassers'])) {
            echo "Board Passers Count: " . count($data['tables']['boardPassers']) . "\n";
            
            if (count($data['tables']['boardPassers']) > 0) {
                echo "First Board Passer Data:\n";
                print_r($data['tables']['boardPassers'][0]);
            }
        } else {
            echo "No boardPassers key in tables data\n";
        }
    } else {
        echo "No tables key in data\n";
    }
    
    // Test board passer stats endpoint
    $statsResponse = $controller->getBoardPasserStats();
    echo "\nBoard Passer Stats Response:\n";
    echo $statsResponse->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} 