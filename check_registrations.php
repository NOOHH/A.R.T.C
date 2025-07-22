<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $stmt = $pdo->query('SELECT id, registration_id, firstname, lastname, status FROM registrations ORDER BY created_at DESC LIMIT 10');
    
    echo "Recent Registrations:\n";
    echo "DB ID | Registration ID | Name | Status\n";
    echo "------|----------------|------|--------\n";
    
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $reg) {
        echo sprintf(
            "%d | %s | %s %s | %s\n",
            $reg['id'],
            $reg['registration_id'] ?? 'NULL',
            $reg['firstname'] ?? '',
            $reg['lastname'] ?? '',
            $reg['status']
        );
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
