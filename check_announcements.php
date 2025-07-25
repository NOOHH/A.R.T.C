<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking announcements table structure:\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('announcements');
foreach ($columns as $column) {
    echo "- $column\n";
}
