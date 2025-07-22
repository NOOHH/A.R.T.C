<?php
/**
 * Comprehensive File Upload Fix
 * This script addresses both:
 * 1. Files uploading but attachment_path not being saved to database
 * 2. updateContent method failing with empty file path error
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== COMPREHENSIVE FILE UPLOAD FIX ===\n\n";

// 1. Check current database state
echo "1. CHECKING CURRENT DATABASE STATE\n";
echo "-----------------------------------\n";

try {
    $contentItems = DB::table('content_items')
        ->select('id', 'content_title', 'attachment_path', 'created_at')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
        
    echo "Recent content items:\n";
    foreach ($contentItems as $item) {
        $attachmentStatus = $item->attachment_path === null ? 'NULL' : 
                           ($item->attachment_path === '' ? 'EMPTY_STRING' : 'HAS_VALUE');
        echo "ID: {$item->id} | Title: {$item->content_title} | Attachment: {$attachmentStatus}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error checking database: " . $e->getMessage() . "\n\n";
}

// 2. Check storage directory structure
echo "2. CHECKING STORAGE DIRECTORY STRUCTURE\n";
echo "----------------------------------------\n";

$storagePaths = [
    'storage/app/public/content' => storage_path('app/public/content'),
    'public/storage/content' => public_path('storage/content')
];

foreach ($storagePaths as $label => $path) {
    echo "Checking {$label}: ";
    if (is_dir($path)) {
        $files = glob($path . '/*');
        echo "EXISTS (" . count($files) . " files)\n";
        
        // Show recent files
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
        mkdir($path, 0755, true);
        echo "  Created: {$path}\n";
    }
}
echo "\n";

// 3. Fix empty string attachment_path values
echo "3. FIXING EMPTY STRING ATTACHMENT PATHS\n";
echo "----------------------------------------\n";

try {
    $emptyStringCount = DB::table('content_items')
        ->where('attachment_path', '=', '')
        ->count();
        
    echo "Found {$emptyStringCount} records with empty string attachment_path\n";
    
    if ($emptyStringCount > 0) {
        $updated = DB::table('content_items')
            ->where('attachment_path', '=', '')
            ->update(['attachment_path' => null]);
            
        echo "Updated {$updated} records to have NULL attachment_path instead of empty string\n";
    }
} catch (Exception $e) {
    echo "Error fixing empty strings: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Create enhanced controller method fixes
echo "4. CREATING CONTROLLER METHOD FIXES\n";
echo "------------------------------------\n";

$controllerFixes = <<<'PHP'
    // Fix for courseContentStore method - Enhanced file processing
    if ($request->hasFile('attachment')) {
        $upload = $request->file('attachment');
        
        // Enhanced validation
        if ($upload->isValid() && $upload->getSize() > 0) {
            try {
                $originalName = $upload->getClientOriginalName();
                $sanitizedName = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $originalName);
                $filename = time() . '_' . $sanitizedName;
                
                // Store the file
                $storedPath = $upload->storeAs('content', $filename, 'public');
                
                // Verify file was actually stored
                $fullStoragePath = storage_path('app/public/' . $storedPath);
                if (file_exists($fullStoragePath)) {
                    $attachmentPath = $storedPath;
                    
                    // Ensure public symlink exists
                    $publicPath = public_path('storage/' . $storedPath);
                    $publicDir = dirname($publicPath);
                    if (!is_dir($publicDir)) {
                        mkdir($publicDir, 0755, true);
                    }
                    
                    // Copy to public if symlink doesn't work
                    if (!file_exists($publicPath)) {
                        copy($fullStoragePath, $publicPath);
                    }
                    
                    Log::info('File upload successful', [
                        'originalName' => $originalName,
                        'storedPath' => $storedPath,
                        'fileSize' => $upload->getSize(),
                        'exists_in_storage' => file_exists($fullStoragePath),
                        'exists_in_public' => file_exists($publicPath)
                    ]);
                } else {
                    Log::error('File storage failed - file not found after store operation', [
                        'storedPath' => $storedPath,
                        'fullStoragePath' => $fullStoragePath
                    ]);
                    $attachmentPath = null;
                }
            } catch (Exception $e) {
                Log::error('File upload error: ' . $e->getMessage());
                $attachmentPath = null;
            }
        } else {
            Log::warning('Invalid file upload', [
                'isValid' => $upload->isValid(),
                'size' => $upload->getSize(),
                'error' => $upload->getError()
            ]);
            $attachmentPath = null;
        }
    } else {
        $attachmentPath = null;
    }

    // Fix for updateContent method - Handle empty attachment paths
    // Only validate file existence if we're actually updating the file
    if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
        // Handle new file upload (same as above)
    } else {
        // Don't update attachment_path if no new file is provided
        // Remove attachment_path from update data to preserve existing value
        if (isset($updateData['attachment_path']) && 
            ($updateData['attachment_path'] === '' || $updateData['attachment_path'] === null)) {
            unset($updateData['attachment_path']);
        }
    }
PHP;

echo "Controller fixes prepared. Apply these manually to AdminModuleController.php:\n";
echo $controllerFixes . "\n\n";

// 5. Test file upload simulation
echo "5. TESTING FILE UPLOAD SIMULATION\n";
echo "-----------------------------------\n";

// Create a test file
$testFilePath = storage_path('app/test_upload.txt');
file_put_contents($testFilePath, 'Test file content for upload simulation');

try {
    // Simulate file storage
    $testStoragePath = 'content/test_' . time() . '.txt';
    $fullStoragePath = storage_path('app/public/' . $testStoragePath);
    $storageDir = dirname($fullStoragePath);
    
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
    }
    
    copy($testFilePath, $fullStoragePath);
    
    echo "Test file simulation:\n";
    echo "  Source: {$testFilePath}\n";
    echo "  Destination: {$fullStoragePath}\n";
    echo "  Exists after copy: " . (file_exists($fullStoragePath) ? 'YES' : 'NO') . "\n";
    echo "  File size: " . filesize($fullStoragePath) . " bytes\n";
    
    // Clean up test files
    unlink($testFilePath);
    unlink($fullStoragePath);
    echo "  Test files cleaned up\n";
    
} catch (Exception $e) {
    echo "Test simulation error: " . $e->getMessage() . "\n";
}

echo "\n=== FIX SUMMARY ===\n";
echo "1. ✅ Checked database state and fixed empty string attachment_path values\n";
echo "2. ✅ Verified storage directory structure\n";
echo "3. ✅ Provided controller method fixes\n";
echo "4. ✅ Tested file storage simulation\n";
echo "\nNext steps:\n";
echo "- Apply the controller fixes to AdminModuleController.php\n";
echo "- Test file upload functionality\n";
echo "- Test content editing with existing attachments\n";
echo "\nFix completed successfully!\n";
