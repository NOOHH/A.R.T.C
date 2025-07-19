<?php
header('Content-Type: text/plain');
echo "Database Tables Check\n";
echo "====================\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Tables in ARTC database:\n";
    $result = $pdo->query('SHOW TABLES');
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        echo "- {$row[0]}\n";
    }
    
    echo "\nEducation Levels table structure:\n";
    $result = $pdo->query('DESCRIBE education_levels');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Key']}\n";
    }
    
    echo "\nEducation Levels data:\n";
    $result = $pdo->query('SELECT * FROM education_levels');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Name: {$row['level_name']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
