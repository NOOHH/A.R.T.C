<?php

echo "=== CHECKING PROGRAM AND PACKAGE STRUCTURE ===" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "1. Programs table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE programs');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "2. Packages table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE packages');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "3. Modules table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE modules');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "4. Content table structure:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE content');
    while($row = $stmt->fetch()) {
        echo "   {$row['Field']} - {$row['Type']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "5. Sample programs:" . PHP_EOL;
    $stmt = $pdo->query('SELECT * FROM programs LIMIT 3');
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($programs as $program) {
        echo "   Program {$program['program_id']}: {$program['program_name']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "6. Sample packages:" . PHP_EOL;
    $stmt = $pdo->query('SELECT * FROM packages LIMIT 3');
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($packages as $package) {
        echo "   Package {$package['package_id']}: {$package['package_name']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "7. Checking relationship between programs/packages and modules:" . PHP_EOL;
    $stmt = $pdo->query("
        SELECT p.program_id, p.program_name, COUNT(m.module_id) as module_count
        FROM programs p
        LEFT JOIN modules m ON p.program_id = m.program_id
        GROUP BY p.program_id, p.program_name
        LIMIT 5
    ");
    $programModules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($programModules as $pm) {
        echo "   Program {$pm['program_id']} ({$pm['program_name']}): {$pm['module_count']} modules" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>
