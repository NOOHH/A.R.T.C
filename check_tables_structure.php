<?php

echo "CHECKING COMPLETION TABLES STRUCTURE" . PHP_EOL;
echo "====================================" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    // Check course_completions table
    echo "COURSE_COMPLETIONS table:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE course_completions');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} ({$row['Type']})" . PHP_EOL;
    }
    
    echo PHP_EOL . "CONTENT_COMPLETIONS table:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE content_completions');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} ({$row['Type']})" . PHP_EOL;
    }
    
    echo PHP_EOL . "MODULE_COMPLETIONS table:" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE module_completions');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} ({$row['Type']})" . PHP_EOL;
    }
    
    // Check data counts
    echo PHP_EOL . "DATA COUNTS:" . PHP_EOL;
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM course_completions');
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Course completions: {$count}" . PHP_EOL;
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM content_completions');
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Content completions: {$count}" . PHP_EOL;
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM module_completions');
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Module completions: {$count}" . PHP_EOL;
    
    // Sample data from course_completions
    echo PHP_EOL . "Sample course_completions data:" . PHP_EOL;
    $stmt = $pdo->query('SELECT * FROM course_completions LIMIT 3');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  " . json_encode($row) . PHP_EOL;
    }
    
    // Check if there are any quiz or assessment tables with scores
    echo PHP_EOL . "CHECKING FOR SCORE TABLES:" . PHP_EOL;
    $stmt = $pdo->query("SHOW TABLES LIKE '%quiz%'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "  Found table: {$row[0]}" . PHP_EOL;
    }
    
    $stmt = $pdo->query("SHOW TABLES LIKE '%assessment%'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "  Found table: {$row[0]}" . PHP_EOL;
    }
    
    $stmt = $pdo->query("SHOW TABLES LIKE '%score%'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "  Found table: {$row[0]}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
