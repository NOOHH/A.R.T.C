<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING EDUCATION LEVEL API ===\n";

// Test 1: Direct controller call
echo "1. Testing EducationLevelController::index()...\n";
try {
    $controller = new \App\Http\Controllers\Admin\EducationLevelController();
    $response = $controller->index();
    $data = $response->getData(true);
    echo "✅ Controller works. Found " . count($data['data']) . " education levels\n";
    echo "Sample: " . json_encode($data['data'][0] ?? 'No data', JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "❌ Controller error: " . $e->getMessage() . "\n\n";
}

// Test 2: Test getForPlan method
echo "2. Testing EducationLevelController::getForPlan('professional')...\n";
try {
    $controller = new \App\Http\Controllers\Admin\EducationLevelController();
    $response = $controller->getForPlan('professional');
    $data = $response->getData(true);
    echo "✅ getForPlan works. Found " . count($data['data']) . " education levels for professional plan\n";
    echo "Sample: " . json_encode($data['data'][0] ?? 'No data', JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "❌ getForPlan error: " . $e->getMessage() . "\n\n";
}

// Test 3: Test file requirements format
echo "3. Testing file requirements format...\n";
try {
    $educationLevel = \App\Models\EducationLevel::first();
    if ($educationLevel) {
        $requirements = $educationLevel->getFileRequirementsForPlan('professional');
        echo "✅ File requirements method works\n";
        echo "Requirements: " . json_encode($requirements, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "❌ No education levels found\n";
    }
} catch (\Exception $e) {
    echo "❌ File requirements error: " . $e->getMessage() . "\n";
}
