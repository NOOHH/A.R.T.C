<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    echo "Messages table structure:\n";
    $result = $pdo->query('DESCRIBE messages');
    if ($result) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Key'] . "\n";
        }
    } else {
        echo "Table does not exist\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
