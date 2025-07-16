<?php
$pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
$stmt = $pdo->query('DESCRIBE registrations');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['Field'] . PHP_EOL;
}
?>
