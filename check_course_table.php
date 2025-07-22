<?php
$pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
$stmt = $pdo->query('DESCRIBE courses');
echo "Course table structure:\n";
while($row = $stmt->fetch()) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . PHP_EOL;
}
?>
