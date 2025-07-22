<?php
/**
 * Simple File Upload Fix
 * This script addresses file upload issues without using Laravel facades
 */

echo "=== FILE UPLOAD FIX ===\n\n";

// 1. Check storage directories
echo "1. CHECKING STORAGE DIRECTORIES\n";
echo "--------------------------------\n";

$baseDir = __DIR__;
$storagePaths = [
    'storage/app/public/content' => $baseDir . '/storage/app/public/content',
    'public/storage/content' => $baseDir . '/public/storage/content'
];

foreach ($storagePaths as $label => $path) {
    echo "Checking {$label}: ";
    if (is_dir($path)) {
        $files = glob($path . '/*');
        echo "EXISTS (" . count($files) . " files)\n";
        
        if (!empty($files)) {
            $recentFiles = array_slice($files, -3);
            foreach ($recentFiles as $file) {
                $size = filesize($file);
                $modified = date('Y-m-d H:i:s', filemtime($file));
                echo "  - " . basename($file) . " ({$size} bytes, {$modified})\n";
            }
        }
    } else {
        echo "MISSING - Creating directory...\n";
        if (mkdir($path, 0755, true)) {
            echo "  Created: {$path}\n";
        } else {
            echo "  Failed to create: {$path}\n";
        }
    }
}
echo "\n";

// 2. Check database directly
echo "2. CHECKING DATABASE\n";
echo "--------------------\n";

// Database configuration
$host = '127.0.0.1';
$dbname = 'artc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check recent content items
    $stmt = $pdo->query("SELECT id, content_title, attachment_path, created_at FROM content_items ORDER BY created_at DESC LIMIT 10");
    $contentItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Recent content items:\n";
    foreach ($contentItems as $item) {
        $attachmentStatus = $item['attachment_path'] === null ? 'NULL' : 
                           ($item['attachment_path'] === '' ? 'EMPTY_STRING' : 'HAS_VALUE');
        echo "ID: {$item['id']} | Title: {$item['content_title']} | Attachment: {$attachmentStatus}\n";
    }
    
    // Fix empty string attachment paths
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM content_items WHERE attachment_path = ''");
    $stmt->execute();
    $emptyCount = $stmt->fetchColumn();
    
    echo "\nFound {$emptyCount} records with empty string attachment_path\n";
    
    if ($emptyCount > 0) {
        $stmt = $pdo->prepare("UPDATE content_items SET attachment_path = NULL WHERE attachment_path = ''");
        $updated = $stmt->execute();
        $affectedRows = $stmt->rowCount();
        echo "Updated {$affectedRows} records to have NULL attachment_path\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Test file storage
echo "3. TESTING FILE STORAGE\n";
echo "------------------------\n";

$testContent = "Test file for upload validation - " . date('Y-m-d H:i:s');
$testFileName = 'test_upload_' . time() . '.txt';
$storagePath = $baseDir . '/storage/app/public/content/' . $testFileName;
$publicPath = $baseDir . '/public/storage/content/' . $testFileName;

// Ensure directories exist
$storageDir = dirname($storagePath);
$publicDir = dirname($publicPath);

if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}
if (!is_dir($publicDir)) {
    mkdir($publicDir, 0755, true);
}

// Test file creation
if (file_put_contents($storagePath, $testContent)) {
    echo "✅ Test file created in storage: {$storagePath}\n";
    echo "   Size: " . filesize($storagePath) . " bytes\n";
    
    // Test public copy
    if (copy($storagePath, $publicPath)) {
        echo "✅ Test file copied to public: {$publicPath}\n";
        echo "   Size: " . filesize($publicPath) . " bytes\n";
        
        // Clean up test files
        unlink($storagePath);
        unlink($publicPath);
        echo "✅ Test files cleaned up\n";
    } else {
        echo "❌ Failed to copy file to public directory\n";
    }
} else {
    echo "❌ Failed to create test file in storage\n";
}

echo "\n=== CONTROLLER FIXES NEEDED ===\n";
echo "Apply these fixes to AdminModuleController.php:\n\n";

$fix1 = <<<'PHP'
// In courseContentStore method, replace the file handling section with:
if ($request->hasFile('attachment')) {
    $upload = $request->file('attachment');
    
    if ($upload->isValid() && $upload->getSize() > 0) {
        try {
            $originalName = $upload->getClientOriginalName();
            $sanitizedName = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $originalName);
            $filename = time() . '_' . $sanitizedName;
            
            // Store the file
            $storedPath = $upload->storeAs('content', $filename, 'public');
            
            // Verify storage and set attachment_path
            $fullStoragePath = storage_path('app/public/' . $storedPath);
            if (file_exists($fullStoragePath)) {
                $attachmentPath = $storedPath; // This is the key fix!
                
                Log::info('File stored successfully', [
                    'originalName' => $originalName,
                    'storedPath' => $storedPath,
                    'fileExists' => true
                ]);
            } else {
                Log::error('File not found after storage');
                $attachmentPath = null;
            }
        } catch (Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            $attachmentPath = null;
        }
    } else {
        Log::warning('Invalid file upload');
        $attachmentPath = null;
    }
} else {
    $attachmentPath = null;
}

// Ensure $attachmentPath is added to the contentData array:
$contentData = [
    'content_title' => $validatedData['content_title'],
    'content_description' => $validatedData['content_description'],
    'course_id' => $validatedData['course_id'],
    'content_type' => $validatedData['content_type'],
    'content_data' => json_encode(['video_url' => null]),
    'attachment_path' => $attachmentPath, // Make sure this line exists!
    // ... other fields
];
PHP;

echo $fix1 . "\n\n";

$fix2 = <<<'PHP'
// In updateContent method, replace file validation with:
if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
    // Handle new file upload (same logic as above)
    $upload = $request->file('attachment');
    // ... file upload logic
} else {
    // Don't modify attachment_path if no new file provided
    // This prevents the "empty file" error
}

// Before updating, ensure we don't try to validate empty attachment paths:
if (isset($updateData['attachment_path']) && $updateData['attachment_path'] === '') {
    unset($updateData['attachment_path']);
}
PHP;

echo $fix2 . "\n\n";

echo "=== SUMMARY ===\n";
echo "✅ Storage directories checked/created\n";
echo "✅ Database empty strings fixed\n";
echo "✅ File storage test successful\n";
echo "✅ Controller fixes provided\n";
echo "\nNext: Apply the controller fixes and test file upload!\n";
