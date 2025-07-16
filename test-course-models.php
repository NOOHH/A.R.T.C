<?php
// Test course functionality
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

try {
    // Test 1: Check if models exist
    echo "=== Testing Course Models ===" . PHP_EOL;
    
    $moduleClass = new ReflectionClass('App\Models\Module');
    $courseClass = new ReflectionClass('App\Models\Course');
    $lessonClass = new ReflectionClass('App\Models\Lesson');
    $contentClass = new ReflectionClass('App\Models\ContentItem');
    
    echo "✓ Module model exists" . PHP_EOL;
    echo "✓ Course model exists" . PHP_EOL;
    echo "✓ Lesson model exists" . PHP_EOL;
    echo "✓ ContentItem model exists" . PHP_EOL;
    
    // Test 2: Check database connectivity
    echo "\n=== Testing Database Connection ===" . PHP_EOL;
    
    $modules = \App\Models\Module::count();
    echo "✓ Database connected - Found {$modules} modules" . PHP_EOL;
    
    // Test 3: Test relationships
    echo "\n=== Testing Model Relationships ===" . PHP_EOL;
    
    $module = \App\Models\Module::first();
    if ($module) {
        echo "✓ Found test module: {$module->module_name}" . PHP_EOL;
        
        $courses = $module->courses;
        echo "✓ Module->courses relationship works - Found " . count($courses) . " courses" . PHP_EOL;
        
        if (count($courses) > 0) {
            $course = $courses[0];
            $lessons = $course->lessons;
            echo "✓ Course->lessons relationship works - Found " . count($lessons) . " lessons" . PHP_EOL;
            
            if (count($lessons) > 0) {
                $lesson = $lessons[0];
                $contentItems = $lesson->contentItems;
                echo "✓ Lesson->contentItems relationship works - Found " . count($contentItems) . " content items" . PHP_EOL;
            }
        }
    }
    
    // Test 4: Test controller methods
    echo "\n=== Testing Controller Methods ===" . PHP_EOL;
    
    $controller = new \App\Http\Controllers\AdminCourseController();
    $reflection = new ReflectionClass($controller);
    
    $methods = ['index', 'store', 'show', 'update', 'destroy', 'getModuleCourses', 'getCourseContent'];
    foreach ($methods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "✓ {$method} method exists" . PHP_EOL;
        } else {
            echo "✗ {$method} method missing" . PHP_EOL;
        }
    }
    
    echo "\n=== Test Complete ===" . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
?>
