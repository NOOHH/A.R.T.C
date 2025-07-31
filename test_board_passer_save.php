<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing board passer save...\n";
    
    // Set session data to simulate authenticated admin
    session(['user_type' => 'admin']);
    session(['user_name' => 'Administrator']);
    session(['user_id' => 1]);
    
    // Test with a known student ID from our earlier test
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'student_id' => '2025-07-00005',  // Use the student ID we found earlier
        'board_exam' => 'CPA',
        'exam_date' => '2024-05-15',
        'result' => 'PASS',
        'notes' => 'Test entry'
    ]);
    
    // Manually call the controller method
    $controller = new \App\Http\Controllers\AdminAnalyticsController();
    $response = $controller->addBoardPasser($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
