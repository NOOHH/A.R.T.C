<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING DATABASE CONNECTION CONTEXT ===\n\n";

// Test the exact same code that the navbar template uses
echo "1. Simulating navbar template code:\n";

$settings = \App\Helpers\SettingsHelper::getSettings();
$navbarSettings = \App\Models\UiSetting::getSection('navbar');
$navbar = $navbarSettings ? $navbarSettings->toArray() : [];

echo "   \$navbarSettings result: " . ($navbarSettings ? 'Found' : 'Empty/Null') . "\n";
echo "   \$navbar array: " . json_encode($navbar) . "\n";
echo "   brand_name value: '" . ($navbar['brand_name'] ?? 'NOT_FOUND') . "'\n";

// Test what would be displayed
$displayName = $navbar['brand_name'] ?? 'Ascendo Review and Training Center';
echo "   Final display value: '{$displayName}'\n";

// Check current database connection
echo "\n2. Database connection info:\n";
echo "   Current connection: " . config('database.default') . "\n";
echo "   Database name: " . config('database.connections.mysql.database') . "\n";

echo "\n=== END TEST ===\n";
