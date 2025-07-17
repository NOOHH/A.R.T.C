<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Module;
use App\Models\Program;

echo "=== CHECKING MODULES DATABASE ===\n";

// Get all modules
$modules = Module::all();
echo "Total modules found: " . $modules->count() . "\n\n";

// Check modules with attachments or content
$modulesWithContent = Module::where(function($query) {
    $query->whereNotNull('attachment')
          ->orWhereNotNull('content_data')
          ->orWhereNotNull('additional_content')
          ->orWhereNotNull('video_path');
})->get();

echo "Modules with content: " . $modulesWithContent->count() . "\n\n";

// Show details of first few modules
$exampleModules = Module::take(5)->get();
foreach ($exampleModules as $module) {
    echo "Module ID: " . $module->modules_id . "\n";
    echo "Name: " . $module->module_name . "\n";
    echo "Program ID: " . $module->program_id . "\n";
    echo "Attachment: " . ($module->attachment ?? 'NULL') . "\n";
    echo "Content Type: " . ($module->content_type ?? 'NULL') . "\n";
    echo "Has Content Data: " . (empty($module->content_data) ? 'NO' : 'YES') . "\n";
    echo "Has Additional Content: " . (empty($module->additional_content) ? 'NO' : 'YES') . "\n";
    echo "Video Path: " . ($module->video_path ?? 'NULL') . "\n";
    
    // Check if program exists
    $program = Program::find($module->program_id);
    echo "Program exists: " . ($program ? 'YES (' . $program->program_name . ')' : 'NO') . "\n";
    echo "---\n";
}

// Check if the specific module from the screenshot exists
$culinaryModule = Module::where('module_name', 'LIKE', '%Creation of Food%')
    ->orWhere('module_name', 'LIKE', '%Module 1%')
    ->first();
    
if ($culinaryModule) {
    echo "\n=== CULINARY MODULE FOUND ===\n";
    echo "Module ID: " . $culinaryModule->modules_id . "\n";
    echo "Name: " . $culinaryModule->module_name . "\n";
    echo "Description: " . ($culinaryModule->module_description ?? 'NULL') . "\n";
    echo "Attachment: " . ($culinaryModule->attachment ?? 'NULL') . "\n";
    echo "Content Data: " . json_encode($culinaryModule->content_data) . "\n";
} else {
    echo "\n=== NO CULINARY MODULE FOUND ===\n";
}

echo "\n=== COMPLETE ===\n";
?>
