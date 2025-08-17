<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Available tables in the smartprep database:\n";
$tables = DB::select('SHOW TABLES');
foreach($tables as $table) {
    echo "- " . array_values((array)$table)[0] . "\n";
}

echo "\nChecking if ui_settings table exists:\n";
try {
    $uiSettings = DB::select('SELECT * FROM ui_settings LIMIT 1');
    echo "ui_settings table exists with " . count($uiSettings) . " records\n";
} catch (Exception $e) {
    echo "ui_settings table does not exist or error: " . $e->getMessage() . "\n";
}

echo "\nChecking if website_requests table exists:\n";
try {
    $websiteRequests = DB::select('SELECT * FROM website_requests LIMIT 1');
    echo "website_requests table exists with " . count($websiteRequests) . " records\n";
} catch (Exception $e) {
    echo "website_requests table does not exist or error: " . $e->getMessage() . "\n";
}
