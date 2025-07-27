<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Fixed Student Search Logic ===\n\n";

try {
    $query = 'nursing';
    
    echo "Testing search for: '{$query}'\n\n";
    
    // Test the FIXED query from searchStudentEnrolledOnly
    $programs = \App\Models\Program::where(function($programQuery) use ($query) {
            $programQuery->where('program_name', 'like', "%{$query}%")
                ->orWhere('program_description', 'like', "%{$query}%")
                ->orWhereHas('modules', function($moduleQuery) use ($query) {
                    $moduleQuery->where('module_name', 'like', "%{$query}%")
                        ->orWhere('module_description', 'like', "%{$query}%")
                        ->orWhereHas('courses', function($courseQuery) use ($query) {
                            $courseQuery->where('subject_name', 'like', "%{$query}%")
                                ->orWhere('subject_description', 'like', "%{$query}%");
                        });
                });
        })
        ->where('is_archived', false)
        ->where('is_active', true)
        ->with(['modules.courses', 'professors'])
        ->get();
        
    echo "Programs found with FIXED logic: " . $programs->count() . "\n";
    
    foreach ($programs as $program) {
        echo "- {$program->program_name} (ID: {$program->program_id})\n";
        echo "  Description: {$program->program_description}\n";
        echo "  Active: " . ($program->is_active ? 'Yes' : 'No') . "\n";
        echo "  Archived: " . ($program->is_archived ? 'Yes' : 'No') . "\n";
        echo "  Modules: " . $program->modules->count() . "\n";
        
        $coursesCount = $program->modules->sum(function($module) {
            return $module->courses->count();
        });
        echo "  Courses: " . $coursesCount . "\n";
        echo "  Professors: " . $program->professors->count() . "\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
