<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $result = $pdo->query('SHOW TABLES LIKE "payment_method_fields"');
    if ($result->rowCount() > 0) {
        echo 'Table payment_method_fields exists' . PHP_EOL;
    } else {
        echo 'Table payment_method_fields does not exist' . PHP_EOL;
    }
    
    // Also check payment_methods table
    $result2 = $pdo->query('SHOW TABLES LIKE "payment_methods"');
    if ($result2->rowCount() > 0) {
        echo 'Table payment_methods exists' . PHP_EOL;
    } else {
        echo 'Table payment_methods does not exist' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
