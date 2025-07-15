<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\OcrService;

// Create test images directory if it doesn't exist
$testDir = __DIR__ . '/test-images';
if (!is_dir($testDir)) {
    mkdir($testDir, 0755, true);
}

echo "=== OCR Service Test with Sample Images ===\n\n";

try {
    $ocrService = new OcrService();
    echo "✅ OcrService instantiated successfully\n\n";
    
    // Test images (you'll need to save the provided images to test-images directory)
    $testImages = [
        'student-id.jpg' => 'Student ID Card (LPU)',
        'graduation-cert.jpg' => 'Graduation Certificate', 
        'diploma.jpg' => 'High School Diploma'
    ];
    
    foreach ($testImages as $filename => $description) {
        $imagePath = $testDir . '/' . $filename;
        
        echo "Testing: $description\n";
        echo "File: $filename\n";
        
        if (file_exists($imagePath)) {
            echo "✅ Image file found\n";
            
            // Test basic text extraction
            echo "--- Basic Text Extraction ---\n";
            $basicText = $ocrService->extractText($imagePath);
            echo "Extracted text: " . substr($basicText, 0, 200) . "...\n\n";
            
            // Test with different file types for optimization
            echo "--- Document Type Specific Extraction ---\n";
            $idText = $ocrService->extractText($imagePath, 'id_card');
            echo "ID optimized text: " . substr($idText, 0, 200) . "...\n\n";
            
            // Test program suggestion
            echo "--- Program Suggestion ---\n";
            $suggestions = $ocrService->suggestPrograms($basicText);
            echo "Program suggestions: " . json_encode($suggestions, JSON_PRETTY_PRINT) . "\n\n";
            
        } else {
            echo "❌ Image file not found at: $imagePath\n";
            echo "Please save the provided images to the test-images directory\n\n";
        }
        
        echo str_repeat('-', 80) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Instructions ===\n";
echo "1. Save the provided images to: $testDir/\n";
echo "2. Name them: student-id.jpg, graduation-cert.jpg, diploma.jpg\n";
echo "3. Run this test again to see OCR results\n";
echo "4. The OCR service now includes:\n";
echo "   - Image preprocessing for better text recognition\n";
echo "   - Cursive/stylized font optimization\n";
echo "   - Multiple Tesseract engine modes\n";
echo "   - Enhanced confidence scoring\n";
