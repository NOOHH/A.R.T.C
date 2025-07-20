<?php

// Test direct module creation with file upload simulation
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Module;
use App\Models\Program;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

echo "=== DIRECT MODULE CREATION TEST ===\n\n";

try {
    // 1. Check if we have programs
    $programs = Program::limit(5)->get();
    echo "1. Available Programs:\n";
    foreach ($programs as $program) {
        echo "   - ID: {$program->program_id}, Name: {$program->program_name}\n";
    }
    
    if ($programs->isEmpty()) {
        echo "❌ No programs found! Cannot create module.\n";
        exit;
    }
    
    $firstProgram = $programs->first();
    
    // 2. Create test file in storage
    $testContent = "This is a test PDF content for module testing.\nCreated at: " . date('Y-m-d H:i:s');
    $fileName = 'test_module_' . time() . '.pdf';
    $filePath = 'content/' . $fileName;
    
    $stored = Storage::disk('public')->put($filePath, $testContent);
    
    if ($stored) {
        echo "2. Test file created: storage/app/public/{$filePath}\n";
        echo "   File URL: " . asset('storage/' . $filePath) . "\n";
    } else {
        echo "❌ Failed to create test file\n";
        exit;
    }
    
    // 3. Create module directly
    $moduleData = [
        'module_name' => 'TEST MODULE - ' . date('Y-m-d H:i:s'),
        'module_description' => 'This is a test module created via direct PHP script to test file upload functionality.',
        'program_id' => $firstProgram->program_id,
        'learning_mode' => 'Asynchronous',
        'attachment' => $filePath,
        'content_type' => 'module',
        'content_data' => [],
        'video_url' => null,
        'video_path' => null,
        'is_archived' => false,
    ];
    
    $module = Module::create($moduleData);
    
    if ($module) {
        echo "3. ✅ Module created successfully!\n";
        echo "   Module ID: {$module->modules_id}\n";
        echo "   Module Name: {$module->module_name}\n";
        echo "   Program: {$firstProgram->program_name}\n";
        echo "   Attachment: {$module->attachment}\n";
        echo "   Created at: {$module->created_at}\n";
    } else {
        echo "❌ Failed to create module\n";
    }
    
    // 4. Verify file exists and is accessible
    $fullPath = storage_path('app/public/' . $filePath);
    echo "4. File verification:\n";
    echo "   Full path: {$fullPath}\n";
    echo "   File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    echo "   File size: " . (file_exists($fullPath) ? filesize($fullPath) . ' bytes' : 'N/A') . "\n";
    echo "   File readable: " . (is_readable($fullPath) ? 'YES' : 'NO') . "\n";
    
    // 5. Test database retrieval
    $retrievedModule = Module::where('modules_id', $module->modules_id)->with('program')->first();
    if ($retrievedModule) {
        echo "5. ✅ Module retrieval successful!\n";
        echo "   Retrieved name: {$retrievedModule->module_name}\n";
        echo "   Retrieved attachment: {$retrievedModule->attachment}\n";
        echo "   Program name: {$retrievedModule->program->program_name}\n";
    } else {
        echo "❌ Failed to retrieve module from database\n";
    }
    
    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    echo "You can now check the admin modules page to see if the module appears with its attachment.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
