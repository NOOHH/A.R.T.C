<?php
// Debug file upload issue
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

echo "=== FILE UPLOAD DEBUG TEST ===\n";

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "✅ Laravel initialized\n";

// Test storage configuration
echo "\n📁 Storage Configuration:\n";
echo "Default disk: " . config('filesystems.default') . "\n";
echo "Public disk configured: " . (config('filesystems.disks.public') ? 'YES' : 'NO') . "\n";

$publicDisk = config('filesystems.disks.public');
echo "Public disk root: " . $publicDisk['root'] . "\n";
echo "Public disk URL: " . $publicDisk['url'] . "\n";

// Check directories
$contentDir = storage_path('app/public/content');
echo "\n📂 Directory Status:\n";
echo "Content directory: {$contentDir}\n";
echo "Exists: " . (is_dir($contentDir) ? 'YES' : 'NO') . "\n";
echo "Writable: " . (is_writable($contentDir) ? 'YES' : 'NO') . "\n";

// Test file upload simulation
echo "\n🧪 Testing File Upload Logic:\n";

// Simulate the controller logic
$attachmentPath = null;

// Create a test file
$testFilePath = storage_path('app/test_upload.txt');
file_put_contents($testFilePath, 'Test content for upload');

try {
    // Test the storeAs method directly
    $filename = time() . '_test_file.txt';
    
    // Copy the test file to simulate upload
    $targetPath = storage_path("app/public/content/{$filename}");
    if (copy($testFilePath, $targetPath)) {
        $attachmentPath = "content/{$filename}";
        echo "✅ File copy successful: {$attachmentPath}\n";
        
        // Test if accessible via storage disk
        if (Storage::disk('public')->exists($attachmentPath)) {
            echo "✅ File accessible via Storage facade\n";
        } else {
            echo "❌ File not accessible via Storage facade\n";
        }
    } else {
        echo "❌ File copy failed\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Check if the issue is with the storeAs method
echo "\n🔧 Testing Storage::putFileAs:\n";
try {
    $testContent = 'Another test file';
    $testFilename = time() . '_storage_test.txt';
    
    // Use Storage::put directly
    $storagePath = Storage::disk('public')->put("content/{$testFilename}", $testContent);
    
    if ($storagePath) {
        echo "✅ Storage::put successful: {$storagePath}\n";
    } else {
        echo "❌ Storage::put failed\n";
    }
} catch (Exception $e) {
    echo "❌ Storage error: " . $e->getMessage() . "\n";
}

// Clean up test files
unlink($testFilePath);
if (isset($targetPath) && file_exists($targetPath)) {
    unlink($targetPath);
}

echo "\n📋 Recommendations:\n";
echo "1. Check if the form is properly sending multipart/form-data\n";
echo "2. Verify the file input name is 'attachment'\n";
echo "3. Check PHP upload settings (upload_max_filesize, post_max_size)\n";
echo "4. Review Laravel logs during actual upload\n";

echo "\n✨ Debug test complete!\n";
?>
