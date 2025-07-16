<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Education levels count: " . DB::table('education_levels')->count() . "\n";

$levels = DB::table('education_levels')->get();
foreach ($levels as $level) {
    echo "- ID: {$level->id}, Name: {$level->level_name}\n";
}
