<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $stmt = $pdo->query('SELECT * FROM payment_methods');
    
    echo "Payment Methods:\n";
    echo "ID | Name | Type | QR Path | Enabled\n";
    echo "---|------|------|---------|--------\n";
    
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $method) {
        echo sprintf(
            "%d | %s | %s | %s | %s\n",
            $method['payment_method_id'],
            $method['method_name'],
            $method['method_type'],
            $method['qr_code_path'] ?? 'NULL',
            $method['is_enabled'] ? 'Yes' : 'No'
        );
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
