<?php
/**
 * Clear all Laravel caches in production
 * Run this file directly on Sevalla to clear caches
 */

// Check if we're in CLI mode or web
$isCLI = php_sapi_name() === 'cli';

if (!$isCLI) {
    echo "<h1>Clearing Production Caches</h1><pre>";
}

echo "🚀 Starting cache clearing process...\n\n";

// Define the commands to run
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear', 
    'php artisan view:clear',
    'php artisan route:clear',
    'php artisan session:flush',
    'php artisan optimize:clear'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    
    // Execute the command and capture output
    $output = [];
    $return_var = 0;
    exec($command . ' 2>&1', $output, $return_var);
    
    if ($return_var === 0) {
        echo "✅ Success: " . implode("\n", $output) . "\n\n";
    } else {
        echo "❌ Error: " . implode("\n", $output) . "\n\n";
    }
}

echo "🎉 Cache clearing completed!\n";
echo "🔄 Please test your login again.\n\n";

if (!$isCLI) {
    echo "</pre>";
    echo '<p><a href="/login">Test Login Now</a></p>';
}
?>
