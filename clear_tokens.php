<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
$pdo->exec('DELETE FROM password_resets WHERE email = "bmjustimbaste2003@gmail.com"');
echo "Old tokens cleared for bmjustimbaste2003@gmail.com\n";
?>
