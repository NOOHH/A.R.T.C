<?php
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    echo "Available databases:\n";
    $stmt = $pdo->query('SHOW DATABASES');
    while ($row = $stmt->fetch()) {
        echo "  {$row[0]}\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
