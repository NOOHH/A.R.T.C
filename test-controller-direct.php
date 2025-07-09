<?php

// Direct test of BatchEnrollmentController
require_once 'vendor/autoload.php';

use App\Http\Controllers\Admin\BatchEnrollmentController;
use App\Models\StudentBatch;
use App\Models\Program;

$app = require_once 'bootstrap/app.php';

// Start session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_type'] = 'admin';
$_SESSION['logged_in'] = true;

echo "Testing BatchEnrollmentController directly...\n";

try {
    // Create controller instance
    $controller = new BatchEnrollmentController();
    
    echo "Controller created successfully\n";
    
    // Test getting batches
    echo "Testing StudentBatch model...\n";
    $batchCount = StudentBatch::count();
    echo "Number of batches: $batchCount\n";
    
    // Test getting programs
    echo "Testing Program model...\n";
    $programCount = Program::where('is_archived', 0)->count();
    echo "Number of active programs: $programCount\n";
    
    echo "SUCCESS: All models and controller accessible!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    if ($e->getPrevious()) {
        echo "Previous error: " . $e->getPrevious()->getMessage() . "\n";
    }
}

echo "\nTest completed.\n";
