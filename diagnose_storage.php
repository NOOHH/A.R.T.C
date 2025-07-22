<?php
echo "=== STORAGE SYSTEM DIAGNOSIS ===\n";

// Check storage directory
$storageDir = __DIR__ . '/storage/app/public/content';
$publicStorageDir = __DIR__ . '/public/storage/content';
$symlinkPath = __DIR__ . '/public/storage';

echo "1. Storage Directory Check:\n";
echo "   Path: $storageDir\n";
echo "   Exists: " . (is_dir($storageDir) ? 'YES' : 'NO') . "\n";
echo "   Writable: " . (is_writable($storageDir) ? 'YES' : 'NO') . "\n";

if (is_dir($storageDir)) {
    $files = scandir($storageDir);
    $fileCount = count($files) - 2; // subtract . and ..
    echo "   Files in directory: $fileCount\n";
    if ($fileCount > 0) {
        echo "   Recent files:\n";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $storageDir . '/' . $file;
                echo "     - $file (" . filesize($filePath) . " bytes)\n";
            }
        }
    }
}

echo "\n2. Public Storage Symlink Check:\n";
echo "   Symlink path: $symlinkPath\n";
echo "   Exists: " . (file_exists($symlinkPath) ? 'YES' : 'NO') . "\n";
echo "   Is link: " . (is_link($symlinkPath) ? 'YES' : 'NO') . "\n";
if (is_link($symlinkPath)) {
    echo "   Target: " . readlink($symlinkPath) . "\n";
} elseif (is_dir($symlinkPath)) {
    echo "   Is directory (not symlink): YES\n";
}

echo "\n3. Public Storage Directory Check:\n";
echo "   Path: $publicStorageDir\n";
echo "   Exists: " . (is_dir($publicStorageDir) ? 'YES' : 'NO') . "\n";
echo "   Writable: " . (is_writable($publicStorageDir) ? 'YES' : 'NO') . "\n";

echo "\n4. PHP File Upload Settings:\n";
echo "   file_uploads: " . (ini_get('file_uploads') ? 'ON' : 'OFF') . "\n";
echo "   upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   post_max_size: " . ini_get('post_max_size') . "\n";
echo "   max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "   memory_limit: " . ini_get('memory_limit') . "\n";

echo "\n5. Directory Permissions (if on Windows):\n";
if (PHP_OS_FAMILY === 'Windows') {
    echo "   OS: Windows\n";
    echo "   Current user: " . get_current_user() . "\n";
    echo "   Script owner: " . fileowner(__FILE__) . "\n";
} else {
    echo "   OS: " . PHP_OS_FAMILY . "\n";
    if (is_dir($storageDir)) {
        echo "   Storage dir permissions: " . substr(sprintf('%o', fileperms($storageDir)), -4) . "\n";
        echo "   Storage dir owner: " . fileowner($storageDir) . "\n";
    }
}

echo "\n6. Test File Creation:\n";
$testFile = $storageDir . '/permission_test.txt';
$testContent = 'Permission test - ' . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "   ✅ Can create files in storage directory\n";
    echo "   Test file size: " . filesize($testFile) . " bytes\n";
    unlink($testFile);
    echo "   ✅ Can delete files from storage directory\n";
} else {
    echo "   ❌ Cannot create files in storage directory\n";
    echo "   Last error: " . error_get_last()['message'] ?? 'No error info' . "\n";
}

echo "\n=== DIAGNOSIS COMPLETE ===\n";
?>
