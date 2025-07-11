<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\OcrService;

// Test if OcrService can be instantiated
try {
    $ocrService = new OcrService();
    echo "OcrService instantiated successfully\n";
    
    // Test suggestPrograms method
    $result = $ocrService->suggestPrograms("sample text");
    echo "suggestPrograms method called successfully\n";
    var_dump($result);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
