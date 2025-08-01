<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Current timezone: " . config('app.timezone') . PHP_EOL;
echo "Current time: " . now()->format('Y-m-d H:i:s T') . PHP_EOL;
echo "PHP default timezone: " . date_default_timezone_get() . PHP_EOL;
echo "PHP current time: " . date('Y-m-d H:i:s T') . PHP_EOL;
