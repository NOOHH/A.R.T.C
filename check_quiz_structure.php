<?php
require __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
$result = $pdo->query("DESCRIBE quiz_questions");
echo "Quiz Questions Table Structure:\n";
foreach($result as $row) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
echo "\n";

$result = $pdo->query("DESCRIBE quizzes");
echo "Quizzes Table Structure:\n";
foreach($result as $row) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
