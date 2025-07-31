<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing analytics with filters...\n";
    
    // Set session data to simulate authenticated admin
    session(['user_type' => 'admin']);
    session(['user_name' => 'Administrator']);
    session(['user_id' => 1]);
    
    // Test with filters
    $request = new \Illuminate\Http\Request([
        'year' => '2022',
        'month' => '4',
        'program' => '',
        'batch' => '',
        'subject' => ''
    ]);
    
    // Manually call the controller method
    $controller = new \App\Http\Controllers\AdminAnalyticsController();
    $response = $controller->getData($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['tables'])) {
        echo "Tables data found:\n";
        foreach ($data['tables'] as $key => $value) {
            echo "- $key: " . (is_array($value) ? count($value) . " items" : "data present") . "\n";
        }
    } else {
        echo "No tables data found\n";
    }
    
    if (isset($data['error'])) {
        echo "Error: " . $data['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
