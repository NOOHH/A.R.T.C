<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\EducationLevelController;
use App\Models\EducationLevel;
use Illuminate\Http\Request;

echo "=== TESTING EDUCATION LEVEL CONTROLLER ===\n";

try {
    // Test direct model access
    echo "Testing EducationLevel model...\n";
    $levels = EducationLevel::all();
    echo "Found " . $levels->count() . " education levels\n";
    
    foreach ($levels as $level) {
        echo "- ID: {$level->id}, Name: {$level->level_name}\n";
        echo "  File Requirements: " . (is_string($level->file_requirements) ? $level->file_requirements : json_encode($level->file_requirements)) . "\n";
    }
    
    // Test controller
    echo "\nTesting EducationLevelController...\n";
    $controller = new EducationLevelController();
    
    // Create a mock request
    $request = new Request();
    
    echo "Calling index method...\n";
    $response = $controller->index();
    
    $responseData = $response->getData(true);
    echo "Response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
