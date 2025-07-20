<?php

// Test script to verify column mappings are correct
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing database column mappings...\n\n";

try {
    // Test modules table
    echo "1. Testing modules table:\n";
    $modules = DB::table('modules')->select('modules_id', 'module_name')->limit(3)->get();
    foreach ($modules as $module) {
        echo "   - Module ID: {$module->modules_id}, Name: {$module->module_name}\n";
    }
    echo "   ✓ modules table columns are correct\n\n";
    
    // Test courses table  
    echo "2. Testing courses table:\n";
    $courses = DB::table('courses')->select('subject_id', 'subject_name', 'module_id')->limit(3)->get();
    foreach ($courses as $course) {
        echo "   - Course ID: {$course->subject_id}, Name: {$course->subject_name}, Module ID: {$course->module_id}\n";
    }
    echo "   ✓ courses table columns are correct\n\n";
    
    // Test content_items table
    echo "3. Testing content_items table:\n";
    $content = DB::table('content_items')->select('id', 'content_title', 'course_id')->limit(3)->get();
    foreach ($content as $item) {
        echo "   - Content ID: {$item->id}, Title: {$item->content_title}, Course ID: {$item->course_id}\n";
    }
    echo "   ✓ content_items table columns are correct\n\n";
    
    // Test join relationships
    echo "4. Testing table joins:\n";
    $joined = DB::table('courses')
        ->join('modules', 'courses.module_id', '=', 'modules.modules_id')
        ->select('courses.subject_id', 'courses.subject_name', 'modules.module_name')
        ->limit(2)
        ->get();
    
    foreach ($joined as $row) {
        echo "   - Course: {$row->subject_name} (ID: {$row->subject_id}) in Module: {$row->module_name}\n";
    }
    echo "   ✓ courses-modules join is working\n\n";
    
    $contentJoined = DB::table('content_items')
        ->join('courses', 'content_items.course_id', '=', 'courses.subject_id')
        ->select('content_items.id', 'content_items.content_title', 'courses.subject_name')
        ->limit(2)
        ->get();
        
    foreach ($contentJoined as $row) {
        echo "   - Content: {$row->content_title} (ID: {$row->id}) in Course: {$row->subject_name}\n";
    }
    echo "   ✓ content_items-courses join is working\n\n";
    
    echo "✅ All database column mappings are correct!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
