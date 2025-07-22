<?php
echo "=== RAW PHP FILE UPLOAD TEST ===\n";

// Create a test file to upload
$testContent = "Raw PHP upload test - " . date('Y-m-d H:i:s');
$tempFile = __DIR__ . '/temp_raw_test.txt';
file_put_contents($tempFile, $testContent);

echo "Created temporary test file: $tempFile\n";
echo "File size: " . filesize($tempFile) . " bytes\n";

// Simulate file upload data
$uploadData = [
    'name' => 'raw_test_upload.txt',
    'type' => 'text/plain',
    'tmp_name' => $tempFile,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($tempFile)
];

echo "\nSimulated upload data:\n";
print_r($uploadData);

// Test different storage methods
$storageDir = __DIR__ . '/storage/app/public/content';
$timestamp = time();

echo "\n=== TESTING STORAGE METHODS ===\n";

// Method 1: copy()
$copyDest = $storageDir . '/' . $timestamp . '_copy_test.txt';
echo "\n1. Testing copy() method:\n";
echo "   Source: {$uploadData['tmp_name']}\n";
echo "   Destination: $copyDest\n";
$copyResult = copy($uploadData['tmp_name'], $copyDest);
echo "   Result: " . ($copyResult ? 'SUCCESS' : 'FAILED') . "\n";
if ($copyResult) {
    echo "   File exists: " . (file_exists($copyDest) ? 'YES' : 'NO') . "\n";
    echo "   File size: " . filesize($copyDest) . " bytes\n";
    unlink($copyDest); // Clean up
}

// Method 2: file_get_contents + file_put_contents
$contentDest = $storageDir . '/' . $timestamp . '_content_test.txt';
echo "\n2. Testing file_get_contents/file_put_contents method:\n";
echo "   Source: {$uploadData['tmp_name']}\n";
echo "   Destination: $contentDest\n";
$content = file_get_contents($uploadData['tmp_name']);
$contentResult = file_put_contents($contentDest, $content);
echo "   Result: " . ($contentResult !== false ? 'SUCCESS' : 'FAILED') . "\n";
if ($contentResult !== false) {
    echo "   File exists: " . (file_exists($contentDest) ? 'YES' : 'NO') . "\n";
    echo "   File size: " . filesize($contentDest) . " bytes\n";
    unlink($contentDest); // Clean up
}

// Method 3: rename() (simulating move_uploaded_file)
$renameDest = $storageDir . '/' . $timestamp . '_rename_test.txt';
$tempCopy = $tempFile . '_copy_for_rename';
copy($tempFile, $tempCopy); // Create copy since rename moves the file

echo "\n3. Testing rename() method (simulating move_uploaded_file):\n";
echo "   Source: $tempCopy\n";
echo "   Destination: $renameDest\n";
$renameResult = rename($tempCopy, $renameDest);
echo "   Result: " . ($renameResult ? 'SUCCESS' : 'FAILED') . "\n";
if ($renameResult) {
    echo "   File exists: " . (file_exists($renameDest) ? 'YES' : 'NO') . "\n";
    echo "   File size: " . filesize($renameDest) . " bytes\n";
    echo "   Source still exists: " . (file_exists($tempCopy) ? 'YES' : 'NO') . "\n";
    unlink($renameDest); // Clean up
}

// Test Laravel-style path generation
echo "\n=== TESTING LARAVEL-STYLE PATH GENERATION ===\n";
$filename = $timestamp . '_laravel_style_test.txt';
$relativePath = 'content/' . $filename;
$absolutePath = $storageDir . '/' . $filename;

echo "Filename: $filename\n";
echo "Relative path: $relativePath\n";
echo "Absolute path: $absolutePath\n";

$testResult = file_put_contents($absolutePath, $testContent);
if ($testResult) {
    echo "✅ Laravel-style path creation: SUCCESS\n";
    echo "File size: " . filesize($absolutePath) . " bytes\n";
    
    // Test public access path
    $publicPath = __DIR__ . '/public/storage/' . $relativePath;
    $publicDir = dirname($publicPath);
    
    echo "Public path: $publicPath\n";
    echo "Public dir exists: " . (is_dir($publicDir) ? 'YES' : 'NO') . "\n";
    
    if (!is_dir($publicDir)) {
        $mkdirResult = mkdir($publicDir, 0755, true);
        echo "Created public dir: " . ($mkdirResult ? 'SUCCESS' : 'FAILED') . "\n";
    }
    
    if (is_dir($publicDir)) {
        $publicCopyResult = copy($absolutePath, $publicPath);
        echo "Copy to public: " . ($publicCopyResult ? 'SUCCESS' : 'FAILED') . "\n";
        if ($publicCopyResult) {
            echo "Public file accessible: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
            unlink($publicPath);
        }
    }
    
    unlink($absolutePath);
} else {
    echo "❌ Laravel-style path creation: FAILED\n";
}

// Clean up
unlink($tempFile);

echo "\n=== RAW PHP TEST COMPLETE ===\n";
?>
