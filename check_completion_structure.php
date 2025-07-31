<?php

echo "=== CHECKING COMPLETION TABLES STRUCTURE ===" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "1. Module_completions table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE module_completions');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "2. Course_completions table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE course_completions');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "3. Content_completions table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE content_completions');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "4. Sample data from completion tables:" . PHP_EOL;
    
    echo "   Module completions for student 2025-07-00001:" . PHP_EOL;
    $stmt = $pdo->query("SELECT * FROM module_completions WHERE student_id = '2025-07-00001' LIMIT 3");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   " . print_r($row, true) . PHP_EOL;
    }
    
    echo "   Course completions for student 2025-07-00001:" . PHP_EOL;
    $stmt = $pdo->query("SELECT * FROM course_completions WHERE student_id = '2025-07-00001' LIMIT 3");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   " . print_r($row, true) . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>
