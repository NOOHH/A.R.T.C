<?php

// Simple test file to check the OCR and program suggestion system
require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel framework
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OcrService;
use App\Models\Program;

echo "=== OCR and Program Suggestion Test ===\n";

try {
    // Test 1: Check if programs exist
    $programs = Program::where('is_archived', 0)->get();
    echo "Available programs: " . $programs->count() . "\n";
    
    foreach ($programs->take(5) as $program) {
        echo "- {$program->program_name}\n";
    }
    
    // Test 2: Test OCR service
    $ocrService = new OcrService();
    
    // Test with sample text that should match nursing
    $sampleText = "CERTIFICATE OF NURSING\nThis is to certify that JUANITA REÑO has satisfactorily completed the prescribed course of study and internship in Nursing";
    
    echo "\n=== Testing Program Suggestions ===\n";
    echo "Sample text: " . substr($sampleText, 0, 100) . "...\n";
    
    $suggestions = $ocrService->suggestPrograms($sampleText);
    echo "Suggestions found: " . count($suggestions) . "\n";
    
    foreach ($suggestions as $suggestion) {
        echo "- Program: {$suggestion['program_name']} (Score: {$suggestion['score']})\n";
        echo "  Keywords: " . implode(', ', $suggestion['matching_keywords']) . "\n";
    }
    
    // Test 3: Test name validation
    echo "\n=== Testing Name Validation ===\n";
    $nameValid = $ocrService->validateUserName($sampleText, "Juanita", "Reño");
    echo "Name validation for 'Juanita Reño': " . ($nameValid ? "PASSED" : "FAILED") . "\n";
    
    $nameValid2 = $ocrService->validateUserName($sampleText, "John", "Doe");
    echo "Name validation for 'John Doe': " . ($nameValid2 ? "PASSED" : "FAILED") . "\n";
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
