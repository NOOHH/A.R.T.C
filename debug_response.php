<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debug getCourseContent Response\n";
echo "===============================\n";

// Set up session for testing
session(['logged_in' => true, 'professor_id' => 8, 'user_role' => 'professor']);

$controller = new \App\Http\Controllers\Professor\ProfessorModuleController();

try {
    $response = $controller->getCourseContent(48);
    
    echo "Response type: " . get_class($response) . "\n";
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        echo "Response data:\n";
        print_r($data);
    } else {
        echo "Unexpected response type\n";
        var_dump($response);
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

// Check Laravel logs
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $recentLogs = substr($logs, -2000); // Last 2000 characters
    echo "\nRecent Laravel logs:\n";
    echo $recentLogs;
}

echo "\nDebug complete!\n";
