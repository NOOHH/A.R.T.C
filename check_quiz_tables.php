<?php

$pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');

echo "CHECKING QUIZ TABLES FOR SCORES" . PHP_EOL;
echo "===============================" . PHP_EOL;

// Check quiz_attempts table
echo "QUIZ_ATTEMPTS table:" . PHP_EOL;
$stmt = $pdo->query('DESCRIBE quiz_attempts');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  - {$row['Field']} ({$row['Type']})" . PHP_EOL;
}

echo PHP_EOL . "Sample quiz_attempts data:" . PHP_EOL;
$stmt = $pdo->query('SELECT * FROM quiz_attempts LIMIT 3');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  " . json_encode($row) . PHP_EOL;
}

// Check quizzes table
echo PHP_EOL . "QUIZZES table:" . PHP_EOL;
$stmt = $pdo->query('DESCRIBE quizzes');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  - {$row['Field']} ({$row['Type']})" . PHP_EOL;
}

// Check if there are any other score-related tables
echo PHP_EOL . "ALL TABLES:" . PHP_EOL;
$stmt = $pdo->query('SHOW TABLES');
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $table = $row[0];
    if (stripos($table, 'score') !== false || stripos($table, 'grade') !== false || stripos($table, 'result') !== false || stripos($table, 'attempt') !== false) {
        echo "Found related table: $table" . PHP_EOL;
    }
}
