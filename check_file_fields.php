<?php
// Quick check of students table file fields
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Students table columns containing 'id', 'moral', 'birth', 'tor', 'diploma', 'form':\n";
    $result = $pdo->query('DESCRIBE students');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $field = $row['Field'];
        if (strpos($field, 'id') !== false || 
            strpos($field, 'moral') !== false || 
            strpos($field, 'birth') !== false || 
            strpos($field, 'tor') !== false || 
            strpos($field, 'diploma') !== false || 
            strpos($field, 'form') !== false) {
            echo "- $field\n";
        }
    }
    
    echo "\nRegistrations table columns containing file-related words:\n";
    $result = $pdo->query('DESCRIBE registrations');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $field = $row['Field'];
        if (strpos($field, 'id') !== false || 
            strpos($field, 'moral') !== false || 
            strpos($field, 'birth') !== false || 
            strpos($field, 'tor') !== false || 
            strpos($field, 'diploma') !== false || 
            strpos($field, 'form') !== false) {
            echo "- $field\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
