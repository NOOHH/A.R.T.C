<?php
// Debug program profile

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $programId = $_GET['id'] ?? 40;
    
    echo "<h1>Debug Program Profile - ID: $programId</h1>";
    
    $program = App\Models\Program::with(['modules.courses', 'professors', 'students.user'])->find($programId);
    
    if (!$program) {
        echo "<p style='color: red;'>Program not found!</p>";
        exit;
    }
    
    echo "<h2>Program Data:</h2>";
    echo "<pre>";
    echo "ID: " . $program->program_id . "\n";
    echo "Name: " . $program->program_name . "\n";
    echo "Description: " . ($program->program_description ?: 'No description') . "\n";
    echo "Active: " . ($program->is_active ? 'Yes' : 'No') . "\n";
    echo "Archived: " . ($program->is_archived ? 'Yes' : 'No') . "\n";
    echo "Created: " . $program->created_at . "\n";
    echo "Modules count: " . $program->modules->count() . "\n";
    echo "Professors count: " . $program->professors->count() . "\n";
    echo "Students count: " . $program->students->count() . "\n";
    echo "</pre>";
    
    echo "<h2>Modules:</h2>";
    foreach ($program->modules as $module) {
        echo "<h3>" . $module->module_name . "</h3>";
        echo "<p>Courses: " . $module->courses->count() . "</p>";
        foreach ($module->courses as $course) {
            echo "<li>" . $course->subject_name . "</li>";
        }
    }
    
    echo "<h2>Professors:</h2>";
    foreach ($program->professors as $professor) {
        echo "<li>" . ($professor->professor_first_name . ' ' . $professor->professor_last_name) . " (" . $professor->professor_email . ")</li>";
    }
    
    echo "<h2>Students:</h2>";
    foreach ($program->students as $student) {
        $user = $student->user;
        if ($user) {
            echo "<li>" . ($user->user_firstname . ' ' . $user->user_lastname) . " (" . $user->email . ")</li>";
        }
    }
    
} catch (Exception $e) {
    echo "<h1 style='color: red;'>Error:</h1>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}
?>
