<?php

echo "=== CHECKING ENROLLMENTS TABLE STRUCTURE ===" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "1. Enrollments table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE enrollments');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "2. Sample enrollments data:" . PHP_EOL;
    $stmt = $pdo->query('SELECT * FROM enrollments LIMIT 3');
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($enrollments as $enrollment) {
        echo "   Enrollment: " . print_r($enrollment, true) . PHP_EOL;
    }
    
    echo "3. Checking courses table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE courses');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>
