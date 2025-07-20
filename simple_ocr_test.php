#!/usr/bin/env php
<?php
/*
 * Simple OCR Test
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OcrService;

echo "=== SIMPLE OCR TEST ===\n";

$samplePath = base_path('resources/images/OCR Images/sample5.png');
echo "File path: $samplePath\n";
echo "File exists: " . (file_exists($samplePath) ? 'Yes' : 'No') . "\n";

if (file_exists($samplePath)) {
    echo "File size: " . filesize($samplePath) . " bytes\n";
    
    try {
        $ocrService = new OcrService();
        echo "OCR Service created successfully\n";
        
        $extractedText = $ocrService->extractText($samplePath);
        echo "Text extracted: " . strlen($extractedText) . " characters\n";
        echo "Preview: " . substr($extractedText, 0, 100) . "\n";
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
    }
}
echo "=== TEST DONE ===\n";
