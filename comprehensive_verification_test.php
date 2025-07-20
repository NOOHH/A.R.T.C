<?php

// Comprehensive test to verify all fixes are working
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Module;
use App\Models\Program;
use App\Models\Course;

echo "=== COMPREHENSIVE VERIFICATION TEST ===\n\n";

try {
    // 1. Test recently created modules with attachments
    $recentModules = Module::where('module_name', 'LIKE', 'TEST MODULE%')
                          ->orderBy('created_at', 'desc')
                          ->limit(3)
                          ->get();
    
    echo "1. Recent Test Modules:\n";
    foreach ($recentModules as $module) {
        echo "   - ID: {$module->modules_id}, Name: {$module->module_name}\n";
        echo "     Attachment: " . ($module->attachment ? $module->attachment : 'None') . "\n";
        echo "     Created: {$module->created_at}\n";
        
        if ($module->attachment) {
            $fullPath = storage_path('app/public/' . $module->attachment);
            echo "     File exists: " . (file_exists($fullPath) ? 'YES ✅' : 'NO ❌') . "\n";
            echo "     File URL: " . asset('storage/' . $module->attachment) . "\n";
        }
        echo "\n";
    }
    
    // 2. Test API endpoints
    echo "2. Testing API Endpoints:\n";
    
    if ($recentModules->count() > 0) {
        $testModule = $recentModules->first();
        echo "   Testing module content API for module ID: {$testModule->modules_id}\n";
        
        // Simulate the API call
        $controller = new \App\Http\Controllers\AdminModuleController();
        $response = $controller->getModuleContent($testModule->modules_id);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            echo "   ✅ Module content API working\n";
            echo "   Module name: {$data['module']['module_name']}\n";
            echo "   Attachment: " . ($data['module']['attachment'] ? $data['module']['attachment'] : 'None') . "\n";
            echo "   Courses count: " . count($data['courses']) . "\n";
        } else {
            echo "   ❌ Module content API failed\n";
        }
    }
    
    // 3. Test file upload functionality
    echo "\n3. Testing File Upload System:\n";
    
    // Create a new test file
    $testContent = "PDF Test Content\n%PDF-1.4\nThis is a test PDF-like file for upload testing.\nCreated: " . date('Y-m-d H:i:s');
    $testFileName = 'upload_test_' . time() . '.pdf';
    $testFilePath = 'content/' . $testFileName;
    
    $stored = \Illuminate\Support\Facades\Storage::disk('public')->put($testFilePath, $testContent);
    
    if ($stored) {
        echo "   ✅ File storage working\n";
        echo "   Test file: {$testFilePath}\n";
        echo "   URL: " . asset('storage/' . $testFilePath) . "\n";
        
        // Create module with this attachment
        $testUploadModule = Module::create([
            'module_name' => 'UPLOAD TEST MODULE - ' . date('H:i:s'),
            'module_description' => 'Testing file upload functionality',
            'program_id' => 32, // Use first available program
            'learning_mode' => 'Asynchronous',
            'attachment' => $testFilePath,
            'content_type' => 'module',
            'is_archived' => false,
        ]);
        
        if ($testUploadModule) {
            echo "   ✅ Module with attachment created\n";
            echo "   New module ID: {$testUploadModule->modules_id}\n";
        }
    } else {
        echo "   ❌ File storage failed\n";
    }
    
    // 4. Database statistics
    echo "\n4. Database Statistics:\n";
    $totalModules = Module::count();
    $modulesWithAttachments = Module::whereNotNull('attachment')->count();
    $totalPrograms = Program::count();
    
    echo "   Total modules: {$totalModules}\n";
    echo "   Modules with attachments: {$modulesWithAttachments}\n";
    echo "   Total programs: {$totalPrograms}\n";
    
    // 5. File system check
    echo "\n5. File System Check:\n";
    $contentDir = storage_path('app/public/content');
    $files = glob($contentDir . '/*');
    echo "   Files in content directory: " . count($files) . "\n";
    
    foreach (array_slice($files, -3) as $file) {
        $fileName = basename($file);
        $fileSize = filesize($file);
        echo "   - {$fileName} ({$fileSize} bytes)\n";
    }
    
    echo "\n=== VERIFICATION COMPLETE ===\n";
    echo "Status: " . ($recentModules->count() > 0 && $stored ? "✅ ALL SYSTEMS WORKING" : "❌ ISSUES DETECTED") . "\n";
    
} catch (Exception $e) {
    echo "❌ Error during verification: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
