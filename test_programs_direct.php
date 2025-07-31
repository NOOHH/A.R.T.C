<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Program;

echo "<h1>Direct Programs Test</h1>";

try {
    $programs = Program::select('program_id as id', 'program_name as name')
        ->where('is_archived', false)
        ->orderBy('program_name')
        ->get();
    
    echo "<h2>Programs Data:</h2>";
    echo "<pre>" . json_encode($programs, JSON_PRETTY_PRINT) . "</pre>";
    
    echo "<h2>For Dropdowns:</h2>";
    foreach ($programs as $program) {
        echo "<p>Value: '{$program->name}' - Text: '{$program->name} Board Exam'</p>";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
