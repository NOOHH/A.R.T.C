<?php
header('Content-Type: text/plain');
echo "Graduate Requirements Raw Data Check\n";
echo "===================================\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $result = $pdo->query("SELECT file_requirements FROM education_levels WHERE id = 2");
    $row = $result->fetch();
    
    echo "Graduate file_requirements raw data:\n";
    echo "Length: " . strlen($row['file_requirements']) . " characters\n\n";
    echo "Raw content:\n";
    echo $row['file_requirements'];
    echo "\n\nHex dump of first 200 characters:\n";
    echo bin2hex(substr($row['file_requirements'], 0, 200));
    
    echo "\n\nJSON validation:\n";
    $decoded = json_decode($row['file_requirements'], true);
    echo "JSON Error: " . json_last_error_msg() . "\n";
    
    if ($decoded !== null) {
        echo "Successfully decoded!\n";
        print_r($decoded);
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
