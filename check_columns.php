<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Package table columns: " . implode(', ', Schema::getColumnListing('packages')) . "\n";
echo "Module table columns: " . implode(', ', Schema::getColumnListing('modules')) . "\n";
echo "Course table columns: " . implode(', ', Schema::getColumnListing('courses')) . "\n";
