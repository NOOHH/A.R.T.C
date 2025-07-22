<?php
/**
 * Simulate the exact file upload process to debug attachment_path issue
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// Simulate Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATTACHMENT PATH DEBUG TEST ===\n\n";

try {
    // 1. Check if we have test data
    echo "1. Setting up test data...\n";
    
    $testCourse = DB::table('courses')->first();
    if (!$testCourse) {
        echo "❌ No courses found for testing\n";
        exit(1);
    }
    echo "✅ Using test course: ID {$testCourse->subject_id}, Name: {$testCourse->subject_name}\n";
    
    $testModule = DB::table('modules')->where('modules_id', $testCourse->module_id)->first();
    if (!$testModule) {
        echo "❌ No module found for course\n";
        exit(1);
    }
    echo "✅ Using test module: ID {$testModule->modules_id}, Name: {$testModule->module_name}\n";
    
    $testProgram = DB::table('programs')->where('program_id', $testModule->program_id)->first();
    if (!$testProgram) {
        echo "❌ No program found for module\n";
        exit(1);
    }
    echo "✅ Using test program: ID {$testProgram->program_id}, Name: {$testProgram->program_name}\n";
    
    // 2. Create a test file
    echo "\n2. Creating test file...\n";
    $testFileName = 'debug_test_' . time() . '.pdf';
    $testContent = '%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj
2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj
3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
>>
endobj
xref
0 4
0000000000 65535 f 
0000000010 00000 n 
0000000079 00000 n 
0000000173 00000 n 
trailer
<<
/Size 4
/Root 1 0 R
>>
startxref
301
%%EOF';
    
    // Create test file in temp location
    $tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $testFileName;
    file_put_contents($tempFilePath, $testContent);
    echo "✅ Test file created: $tempFilePath (" . filesize($tempFilePath) . " bytes)\n";
    
    // 3. Simulate the file storage process step by step
    echo "\n3. Simulating file storage process...\n";
    
    // Storage path - same as in the controller
    $filename = time() . '_' . $testFileName;
    $attachmentPath = 'content/' . $filename;
    
    echo "Generated filename: $filename\n";
    echo "Generated attachment path: $attachmentPath\n";
    
    // Check storage directories
    $storageDir = storage_path('app/public/content');
    $publicDir = public_path('storage/content');
    
    echo "Storage directory: $storageDir\n";
    echo "Storage dir exists: " . (is_dir($storageDir) ? '✅ Yes' : '❌ No') . "\n";
    echo "Storage dir writable: " . (is_writable($storageDir) ? '✅ Yes' : '❌ No') . "\n";
    
    echo "Public directory: $publicDir\n";
    echo "Public dir exists: " . (is_dir($publicDir) ? '✅ Yes' : '❌ No') . "\n";
    echo "Public dir writable: " . (is_writable($publicDir) ? '✅ Yes' : '❌ No') . "\n";
    
    // Create directories if they don't exist
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
        echo "✅ Created storage directory\n";
    }
    if (!is_dir($publicDir)) {
        mkdir($publicDir, 0755, true);
        echo "✅ Created public directory\n";
    }
    
    // Copy file to storage
    $finalStoragePath = $storageDir . DIRECTORY_SEPARATOR . $filename;
    $finalPublicPath = $publicDir . DIRECTORY_SEPARATOR . $filename;
    
    if (copy($tempFilePath, $finalStoragePath)) {
        echo "✅ File copied to storage: $finalStoragePath\n";
        
        if (copy($finalStoragePath, $finalPublicPath)) {
            echo "✅ File copied to public: $finalPublicPath\n";
        } else {
            echo "❌ Failed to copy to public directory\n";
        }
    } else {
        echo "❌ Failed to copy to storage directory\n";
    }
    
    // 4. Test database insertion with exact data structure
    echo "\n4. Testing database insertion...\n";
    
    $contentItemData = [
        'content_title' => 'Debug Test Upload',
        'content_description' => 'Testing attachment path storage',
        'course_id' => $testCourse->subject_id,
        'content_type' => 'document',
        'content_data' => json_encode(['document_url' => null]),
        'attachment_path' => $attachmentPath,
        'max_points' => 0,
        'due_date' => null,
        'time_limit' => null,
        'is_required' => true,
        'is_active' => true,
        'enable_submission' => false,
        'allowed_file_types' => null,
        'max_file_size' => 10,
        'submission_instructions' => null,
        'content_url' => null,
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    echo "Content item data to insert:\n";
    foreach ($contentItemData as $key => $value) {
        $valueStr = is_null($value) ? 'NULL' : (is_string($value) ? "\"$value\"" : $value);
        echo "- $key: $valueStr\n";
    }
    
    // Insert using direct DB query
    $contentId = DB::table('content_items')->insertGetId($contentItemData);
    echo "✅ Content item inserted with ID: $contentId\n";
    
    // Verify the insertion
    $insertedItem = DB::table('content_items')->find($contentId);
    if ($insertedItem) {
        echo "✅ Insertion verified:\n";
        echo "- ID: {$insertedItem->id}\n";
        echo "- Title: {$insertedItem->content_title}\n";
        echo "- Course ID: {$insertedItem->course_id}\n";
        echo "- Attachment Path: " . ($insertedItem->attachment_path ?: 'NULL') . "\n";
        echo "- Content Type: {$insertedItem->content_type}\n";
        
        if ($insertedItem->attachment_path === $attachmentPath) {
            echo "✅ Attachment path correctly saved!\n";
        } else {
            echo "❌ Attachment path mismatch!\n";
            echo "  Expected: $attachmentPath\n";
            echo "  Actual: " . ($insertedItem->attachment_path ?: 'NULL') . "\n";
        }
    } else {
        echo "❌ Failed to retrieve inserted item\n";
    }
    
    // 5. Test using Eloquent model (same as controller)
    echo "\n5. Testing with Eloquent model...\n";
    
    try {
        $eloquentItem = \App\Models\ContentItem::create($contentItemData);
        echo "✅ Eloquent model creation successful:\n";
        echo "- ID: {$eloquentItem->id}\n";
        echo "- Title: {$eloquentItem->content_title}\n";
        echo "- Course ID: {$eloquentItem->course_id}\n";
        echo "- Attachment Path: " . ($eloquentItem->attachment_path ?: 'NULL') . "\n";
        
        if ($eloquentItem->attachment_path === $attachmentPath) {
            echo "✅ Eloquent attachment path correctly saved!\n";
        } else {
            echo "❌ Eloquent attachment path mismatch!\n";
            echo "  Expected: $attachmentPath\n";
            echo "  Actual: " . ($eloquentItem->attachment_path ?: 'NULL') . "\n";
        }
        
        // Clean up Eloquent test
        $eloquentItem->delete();
        echo "✅ Eloquent test item cleaned up\n";
        
    } catch (Exception $e) {
        echo "❌ Eloquent model creation failed: " . $e->getMessage() . "\n";
    }
    
    // 6. Test file access
    echo "\n6. Testing file access...\n";
    
    $webUrl = "http://localhost:8000/storage/$attachmentPath";
    echo "Web URL: $webUrl\n";
    
    if (file_exists($finalStoragePath) && file_exists($finalPublicPath)) {
        echo "✅ Both storage and public files exist\n";
        echo "Storage size: " . filesize($finalStoragePath) . " bytes\n";
        echo "Public size: " . filesize($finalPublicPath) . " bytes\n";
    } else {
        echo "❌ File access issue\n";
        echo "Storage exists: " . (file_exists($finalStoragePath) ? 'Yes' : 'No') . "\n";
        echo "Public exists: " . (file_exists($finalPublicPath) ? 'Yes' : 'No') . "\n";
    }
    
    // Clean up
    echo "\n7. Cleaning up...\n";
    
    // Remove test files
    if (file_exists($tempFilePath)) unlink($tempFilePath);
    if (file_exists($finalStoragePath)) unlink($finalStoragePath);
    if (file_exists($finalPublicPath)) unlink($finalPublicPath);
    
    // Remove test database record
    DB::table('content_items')->where('id', $contentId)->delete();
    
    echo "✅ Cleanup completed\n";
    
    echo "\n=== SUMMARY ===\n";
    echo "The attachment path storage process appears to work correctly.\n";
    echo "If files are uploading but attachment_path is NULL in your actual uploads,\n";
    echo "the issue might be:\n";
    echo "1. File upload is failing silently\n";
    echo "2. Validation is failing\n";
    echo "3. Exception is thrown after file storage but before database save\n";
    echo "4. The attachment_path variable is being reset somewhere\n";
    echo "\nCheck the Laravel logs during actual upload to see the exact flow.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
