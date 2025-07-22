<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $stmt = $pdo->query('DESCRIBE registrations');
    
    echo "Registration table structure:\n";
    echo "Field | Type | Null | Key\n";
    echo "------|------|------|-----\n";
    
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        echo sprintf(
            "%s | %s | %s | %s\n",
            $col['Field'],
            $col['Type'],
            $col['Null'],
            $col['Key']
        );
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
