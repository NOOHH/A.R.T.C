<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    echo "Creating payment_method_fields table...\n";
    
    $sql = "
    CREATE TABLE IF NOT EXISTS payment_method_fields (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        payment_method_id bigint(20) unsigned NOT NULL,
        field_name varchar(255) NOT NULL,
        field_label varchar(255) NOT NULL,
        field_type enum('text','number','date','file','textarea','select') NOT NULL,
        field_options json DEFAULT NULL,
        is_required tinyint(1) NOT NULL DEFAULT 1,
        sort_order int(11) NOT NULL DEFAULT 0,
        created_at timestamp NULL DEFAULT NULL,
        updated_at timestamp NULL DEFAULT NULL,
        PRIMARY KEY (id),
        KEY payment_method_fields_payment_method_id_foreign (payment_method_id),
        CONSTRAINT payment_method_fields_payment_method_id_foreign 
            FOREIGN KEY (payment_method_id) 
            REFERENCES payment_methods (payment_method_id) 
            ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "✓ Table created successfully\n";
    
    // Now run our test
    include 'test-dynamic-payment-system.php';
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
