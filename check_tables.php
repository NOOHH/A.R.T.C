<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
$result = $pdo->query('SHOW TABLES');
echo "Database Tables:\n";
foreach($result as $row) {
    echo "- " . $row[0] . "\n";
}
