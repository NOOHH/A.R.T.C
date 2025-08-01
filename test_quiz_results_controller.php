<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\StudentDashboardController;
use Illuminate\Http\Request;

echo "=== TESTING QUIZ RESULTS CONTROLLER ===" . PHP_EOL;

// Simulate session
session(['user_id' => 1]);

try {
    $controller = new StudentDashboardController();
    
    // Test the showQuizResults method
    $response = $controller->showQuizResults(3);
    
    if ($response instanceof \Illuminate\View\View) {
        echo "✅ Controller method executed successfully" . PHP_EOL;
        echo "✅ View returned: " . $response->getName() . PHP_EOL;
        
        // Check the data passed to the view
        $viewData = $response->getData();
        
        if (isset($viewData['attempt'])) {
            echo "✅ Attempt data passed to view" . PHP_EOL;
            echo "  - Attempt ID: " . $viewData['attempt']->attempt_id . PHP_EOL;
        }
        
        if (isset($viewData['quiz'])) {
            echo "✅ Quiz data passed to view" . PHP_EOL;
            echo "  - Quiz Title: " . $viewData['quiz']->quiz_title . PHP_EOL;
        }
        
        if (isset($viewData['student'])) {
            echo "✅ Student data passed to view" . PHP_EOL;
            echo "  - Student Name: " . $viewData['student']->firstname . PHP_EOL;
        }
        
        if (isset($viewData['results'])) {
            echo "✅ Results data passed to view" . PHP_EOL;
            echo "  - Number of questions: " . count($viewData['results']) . PHP_EOL;
        }
        
    } else {
        echo "❌ Controller returned redirect instead of view" . PHP_EOL;
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            echo "  - Redirect to: " . $response->getTargetUrl() . PHP_EOL;
            if ($response->getSession()->has('error')) {
                echo "  - Error message: " . $response->getSession()->get('error') . PHP_EOL;
            }
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Controller method failed with error:" . PHP_EOL;
    echo "  " . $e->getMessage() . PHP_EOL;
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}

echo PHP_EOL . "=== TEST COMPLETE ===" . PHP_EOL;
