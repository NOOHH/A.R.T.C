<?php
// Test QR code upload functionality
require_once 'vendor/autoload.php';

try {
    // Simulate a QR code file upload
    $testImagePath = storage_path('app/test_qr.png');
    $testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
    file_put_contents($testImagePath, $testImageContent);
    
    // Create a test image file
    $uploadedFile = new \Illuminate\Http\UploadedFile(
        $testImagePath,
        'test_qr.png',
        'image/png',
        strlen($testImageContent),
        0,
        true
    );
    
    // Test the storage functionality
    $storedPath = $uploadedFile->store('payment_qr_codes', 'public');
    
    if ($storedPath) {
        echo "✅ QR code upload test successful!\n";
        echo "Stored path: {$storedPath}\n";
        
        // Check if file exists in both locations
        $fullStoragePath = storage_path("app/public/{$storedPath}");
        $publicPath = public_path("storage/{$storedPath}");
        
        echo "Storage path exists: " . (file_exists($fullStoragePath) ? "✅ Yes" : "❌ No") . "\n";
        echo "Public path accessible: " . (is_readable($publicPath) ? "✅ Yes" : "❌ No") . "\n";
        
        // Test web accessibility
        $webUrl = "http://127.0.0.1:8000/storage/{$storedPath}";
        echo "Web URL: {$webUrl}\n";
        
    } else {
        echo "❌ QR code upload test failed!\n";
    }
    
    // Cleanup
    if (file_exists($testImagePath)) {
        unlink($testImagePath);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
