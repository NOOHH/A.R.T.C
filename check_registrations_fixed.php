<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $stmt = $pdo->query('SELECT registration_id, firstname, lastname, status FROM registrations ORDER BY created_at DESC LIMIT 10');
    
    echo "Recent Registrations:\n";
    echo "Registration ID | Name | Status\n";
    echo "---------------|------|--------\n";
    
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $reg) {
        echo sprintf(
            "%d | %s %s | %s\n",
            $reg['registration_id'],
            $reg['firstname'] ?? '',
            $reg['lastname'] ?? '',
            $reg['status']
        );
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
