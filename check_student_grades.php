<?php

$pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');

echo "STUDENT_GRADES table:" . PHP_EOL;
$stmt = $pdo->query('DESCRIBE student_grades');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  - {$row['Field']} ({$row['Type']})" . PHP_EOL;
}

echo PHP_EOL . "Sample student_grades data:" . PHP_EOL;
$stmt = $pdo->query('SELECT * FROM student_grades LIMIT 3');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  " . json_encode($row) . PHP_EOL;
}
