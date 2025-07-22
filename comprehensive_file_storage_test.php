<?php

// Comprehensive file upload and storage test
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Module;
use App\Models\ContentItem;

echo "=== COMPREHENSIVE FILE UPLOAD AND STORAGE TEST ===\n\n";

try {
    echo "1. TESTING STORAGE DIRECTORIES:\n";
    
    $storageAppPublic = storage_path('app/public/content');
    $publicStorage = public_path('storage/content');
    
    echo "   Storage app/public/content: " . ($storageAppPublic ? 'EXISTS' : 'NOT EXISTS') . "\n";
    echo "   Public storage/content: " . ($publicStorage ? 'EXISTS' : 'NOT EXISTS') . "\n";
    echo "   Storage writable: " . (is_writable($storageAppPublic) ? 'YES' : 'NO') . "\n";
    echo "   Public writable: " . (is_writable($publicStorage) ? 'YES' : 'NO') . "\n";
    
    // Create directories if they don't exist
    if (!is_dir($storageAppPublic)) {
        mkdir($storageAppPublic, 0755, true);
        echo "   Created storage/app/public/content directory\n";
    }
    
    if (!is_dir($publicStorage)) {
        mkdir($publicStorage, 0755, true);
        echo "   Created public/storage/content directory\n";
    }
    
    echo "\n2. TESTING FILE ACCESS:\n";
    
    // Test files that should exist
    $testFiles = [
        '1753101842_ARTC - DFD (1).pdf',
        '1753102728_ARTC - DFD (1).pdf',
        'test_module_1753044980.pdf'
    ];
    
    foreach ($testFiles as $filename) {
        $storagePath = $storageAppPublic . '/' . $filename;
        $publicPath = $publicStorage . '/' . $filename;
        $exists = file_exists($storagePath);
        $publicExists = file_exists($publicPath);
        
        echo "   File: {$filename}\n";
        echo "     Storage exists: " . ($exists ? 'YES' : 'NO') . "\n";
        echo "     Public exists: " . ($publicExists ? 'YES' : 'NO') . "\n";
        
        if ($exists && !$publicExists) {
            // Copy file to public
            if (copy($storagePath, $publicPath)) {
                echo "     ✅ Copied to public storage\n";
            } else {
                echo "     ❌ Failed to copy to public storage\n";
            }
        }
        echo "\n";
    }
    
    echo "3. TESTING DATABASE CONTENT ITEMS:\n";
    
    $contentItems = ContentItem::whereNotNull('attachment_path')
                              ->orderBy('created_at', 'desc')
                              ->limit(10)
                              ->get();
    
    foreach ($contentItems as $item) {
        echo "   Content ID: {$item->id}\n";
        echo "     Title: {$item->content_title}\n";
        echo "     Attachment: {$item->attachment_path}\n";
        
        if ($item->attachment_path) {
            $fullPath = storage_path('app/public/' . $item->attachment_path);
            $publicPath = public_path('storage/' . $item->attachment_path);
            
            echo "     Storage file exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
            echo "     Public file exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
            echo "     URL would be: " . asset('storage/' . $item->attachment_path) . "\n";
            
            // Copy to public if missing
            if (file_exists($fullPath) && !file_exists($publicPath)) {
                $publicDir = dirname($publicPath);
                if (!is_dir($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }
                if (copy($fullPath, $publicPath)) {
                    echo "     ✅ Copied to public storage\n";
                } else {
                    echo "     ❌ Failed to copy to public storage\n";
                }
            }
        }
        echo "\n";
    }
    
    echo "4. TESTING MODULES WITH ATTACHMENTS:\n";
    
    $modules = Module::whereNotNull('attachment')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
    
    foreach ($modules as $module) {
        echo "   Module ID: {$module->modules_id}\n";
        echo "     Name: {$module->module_name}\n";
        echo "     Attachment: {$module->attachment}\n";
        
        if ($module->attachment) {
            $fullPath = storage_path('app/public/' . $module->attachment);
            $publicPath = public_path('storage/' . $module->attachment);
            
            echo "     Storage file exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
            echo "     Public file exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
            echo "     URL would be: " . asset('storage/' . $module->attachment) . "\n";
            
            // Copy to public if missing
            if (file_exists($fullPath) && !file_exists($publicPath)) {
                $publicDir = dirname($publicPath);
                if (!is_dir($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }
                if (copy($fullPath, $publicPath)) {
                    echo "     ✅ Copied to public storage\n";
                } else {
                    echo "     ❌ Failed to copy to public storage\n";
                }
            }
        }
        echo "\n";
    }
    
    echo "5. CREATING TEST UPLOAD:\n";
    
    // Create a test file
    $testContent = "%PDF-1.4\nTest PDF content created at " . date('Y-m-d H:i:s') . "\n%%EOF";
    $testFileName = 'comprehensive_test_' . time() . '.pdf';
    
    // Store using Laravel Storage
    $stored = Storage::disk('public')->put('content/' . $testFileName, $testContent);
    
    if ($stored) {
        echo "   ✅ Test file created successfully\n";
        echo "   File: content/{$testFileName}\n";
        echo "   Storage path: " . storage_path('app/public/content/' . $testFileName) . "\n";
        echo "   Public URL: " . asset('storage/content/' . $testFileName) . "\n";
        
        // Copy to public storage
        $storagePath = storage_path('app/public/content/' . $testFileName);
        $publicPath = public_path('storage/content/' . $testFileName);
        
        if (copy($storagePath, $publicPath)) {
            echo "   ✅ Test file copied to public storage\n";
        } else {
            echo "   ❌ Failed to copy test file to public storage\n";
        }
    } else {
        echo "   ❌ Failed to create test file\n";
    }
    
    echo "\n=== TEST COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
