<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->exec('UPDATE education_levels SET is_active = 1 WHERE id = 2');
    echo 'Graduate level activated successfully';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
