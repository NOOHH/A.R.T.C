<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== QUICK FILE UPLOAD TEST ===\n\n";

// Find valid course-module-program combination
$validData = DB::table('courses')
    ->join('modules', 'courses.module_id', '=', 'modules.modules_id')
    ->join('programs', 'modules.program_id', '=', 'programs.program_id')
    ->select('courses.*', 'modules.module_name', 'modules.modules_id as module_id', 'programs.program_name', 'programs.program_id')
    ->first();

if (!$validData) {
    echo "❌ No valid data combination found\n";
    exit(1);
}

echo "Using valid combination:\n";
echo "- Program: {$validData->program_name} (ID: {$validData->program_id})\n";
echo "- Module: {$validData->module_name} (ID: {$validData->module_id})\n";
echo "- Course: {$validData->subject_name} (ID: {$validData->subject_id})\n\n";

// Create test file and simulate upload
$testFileName = 'upload_debug_' . time() . '.pdf';
$testContent = '%PDF-1.4
Test PDF content for debugging
%%EOF';

// Test 1: File storage process
echo "1. Testing file storage...\n";

$storageDir = storage_path('app/public/content');
$publicDir = public_path('storage/content');

if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);
if (!is_dir($publicDir)) mkdir($publicDir, 0755, true);

$storagePath = $storageDir . DIRECTORY_SEPARATOR . $testFileName;
$publicPath = $publicDir . DIRECTORY_SEPARATOR . $testFileName;

// Create files
file_put_contents($storagePath, $testContent);
copy($storagePath, $publicPath);

echo "✅ Files created successfully\n";
echo "Storage: " . filesize($storagePath) . " bytes\n";
echo "Public: " . filesize($publicPath) . " bytes\n\n";

// Test 2: Database insertion with attachment_path
echo "2. Testing database insertion...\n";

$attachmentPath = "content/{$testFileName}";
$contentData = [
    'content_title' => 'Test Upload Debug',
    'content_description' => 'Testing why attachment_path is null',
    'course_id' => $validData->subject_id,
    'content_type' => 'document',
    'content_data' => '{}',
    'attachment_path' => $attachmentPath,
    'max_points' => 0,
    'is_required' => 1,
    'is_active' => 1,
    'enable_submission' => 0,
    'max_file_size' => 10,
    'created_at' => now(),
    'updated_at' => now()
];

echo "Inserting with attachment_path: $attachmentPath\n";

$contentId = DB::table('content_items')->insertGetId($contentData);
echo "✅ Inserted with ID: $contentId\n";

// Verify
$inserted = DB::table('content_items')->find($contentId);
echo "Verification:\n";
echo "- ID: {$inserted->id}\n";
echo "- Title: {$inserted->content_title}\n";
echo "- Attachment Path: " . ($inserted->attachment_path ?: 'NULL') . "\n";

if ($inserted->attachment_path === $attachmentPath) {
    echo "✅ SUCCESS: Attachment path saved correctly!\n";
} else {
    echo "❌ FAILED: Attachment path is NULL or incorrect\n";
    echo "Expected: $attachmentPath\n";
    echo "Got: " . ($inserted->attachment_path ?: 'NULL') . "\n";
}

// Test 3: Check recent uploads in logs
echo "\n3. Checking recent upload logs...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logLines = file($logFile);
    $recentLogs = array_slice($logLines, -20);
    
    foreach ($recentLogs as $line) {
        if (stripos($line, 'attachment_path') !== false || stripos($line, 'courseContentStore') !== false) {
            echo trim($line) . "\n";
        }
    }
}

// Clean up
unlink($storagePath);
unlink($publicPath);
DB::table('content_items')->where('id', $contentId)->delete();

echo "\n✅ Test completed and cleaned up\n";

?>
