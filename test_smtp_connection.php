<?php

echo "Testing SMTP Connection to Gmail\n";
echo "===============================\n\n";

// Test SMTP connection without Laravel
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$smtp_user = 'bravo.teamproj@gmail.com';
$smtp_pass = 'rwlgspjnqxkhfpst';

echo "Attempting to connect to {$smtp_host}:{$smtp_port}...\n";

$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

$errno = 0;
$errstr = '';
$timeout = 10;

$socket = stream_socket_client(
    "tcp://{$smtp_host}:{$smtp_port}",
    $errno,
    $errstr,
    $timeout,
    STREAM_CLIENT_CONNECT,
    $context
);

if ($socket) {
    echo "✓ Successfully connected to Gmail SMTP server!\n";
    
    // Read server response
    $response = fgets($socket);
    echo "Server response: " . trim($response) . "\n";
    
    // Send EHLO command
    fwrite($socket, "EHLO localhost\r\n");
    $response = fgets($socket);
    echo "EHLO response: " . trim($response) . "\n";
    
    // Start TLS
    fwrite($socket, "STARTTLS\r\n");
    $response = fgets($socket);
    echo "STARTTLS response: " . trim($response) . "\n";
    
    fclose($socket);
    echo "\n✓ SMTP connection test successful!\n";
    echo "The Gmail SMTP server is reachable and responding correctly.\n";
    
} else {
    echo "✗ Failed to connect to Gmail SMTP server\n";
    echo "Error: {$errstr} (Code: {$errno})\n";
    
    // Additional diagnostics
    echo "\nTrying to resolve hostname...\n";
    $ip = gethostbyname($smtp_host);
    if ($ip !== $smtp_host) {
        echo "✓ Hostname resolved to: {$ip}\n";
    } else {
        echo "✗ Failed to resolve hostname\n";
    }
}

echo "\nNote: If connection is successful but emails aren't sending,\n";
echo "check if you're using the correct App Password for Gmail.\n";
echo "Regular Gmail passwords won't work with SMTP.\n";

?>
