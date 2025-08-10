<?php

// Clear Laravel runtime caches
echo "Clearing Laravel caches...\n";

// Clear config cache
echo "Clearing config cache...\n";
system('php artisan config:clear');

// Clear application cache
echo "Clearing application cache...\n";
system('php artisan cache:clear');

// Clear route cache
echo "Clearing route cache...\n";
system('php artisan route:clear');

// Clear view cache
echo "Clearing view cache...\n";
system('php artisan view:clear');

// Clear compiled classes
echo "Clearing compiled classes...\n";
system('php artisan clear-compiled');

echo "All caches cleared successfully!\n";
