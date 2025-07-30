<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap the Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test basic class loading
try {
    echo "Testing professor module controller loading...\n";
    
    // Check if controller class exists
    if (class_exists('App\Http\Controllers\Professor\ProfessorModuleController')) {
        echo "✓ ProfessorModuleController class loaded successfully\n";
    } else {
        echo "✗ ProfessorModuleController class not found\n";
    }
    
    // Check if required models exist
    $models = [
        'App\Models\Module',
        'App\Models\Course', 
        'App\Models\ContentItem',
        'App\Models\StudentBatch',
        'App\Models\AdminSetting'
    ];
    
    foreach ($models as $model) {
        if (class_exists($model)) {
            echo "✓ Model $model loaded successfully\n";
        } else {
            echo "✗ Model $model not found\n";
        }
    }
    
    echo "All classes loaded successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
