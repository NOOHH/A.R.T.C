<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$programs = App\Models\Program::all();
echo "Found " . $programs->count() . " programs:\n";
foreach($programs as $p) {
    echo 'ID: ' . $p->program_id . ' - Name: ' . $p->program_name . "\n";
}
