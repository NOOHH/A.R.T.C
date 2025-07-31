<?php

echo "=== CHECKING MODULE AND COURSE STRUCTURE ===" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "1. Modules table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE modules');
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "2. Courses table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE courses');
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "3. Sample module data:" . PHP_EOL;
    $stmt = $pdo->query('SELECT modules_id, module_name, program_id FROM modules LIMIT 3');
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($modules as $module) {
        echo "   Module {$module['modules_id']}: {$module['module_name']} (Program: {$module['program_id']})" . PHP_EOL;
    }
    
    echo PHP_EOL . "4. Sample course data:" . PHP_EOL;
    $stmt = $pdo->query('SELECT subject_id, subject_name, module_id FROM courses LIMIT 3');
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($courses as $course) {
        echo "   Course {$course['subject_id']}: {$course['subject_name']} (Module: {$course['module_id']})" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>
