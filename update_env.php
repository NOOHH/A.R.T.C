<?php

// Read the current .env file
$envContent = file_get_contents('.env');

// Update the necessary values
$envContent = str_replace('APP_URL=http://localhost', 'APP_URL=http://localhost:8000', $envContent);
$envContent = str_replace('DB_DATABASE=laravel', 'DB_DATABASE=artc', $envContent);
$envContent = str_replace('APP_ENV=local', 'APP_ENV=local', $envContent);
$envContent = str_replace('APP_DEBUG=true', 'APP_DEBUG=true', $envContent);

// Write back to .env file
file_put_contents('.env', $envContent);

echo "Updated .env file successfully!\n";
echo "APP_URL set to: http://localhost:8000\n";
echo "DB_DATABASE set to: artc\n";
