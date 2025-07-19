<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo 'Programs:' . PHP_EOL;
foreach (\App\Models\Program::all() as $program) {
    echo '- ID: ' . $program->program_id . ', Name: ' . $program->program_name . PHP_EOL;
}

echo PHP_EOL . 'Packages:' . PHP_EOL;
foreach (\App\Models\Package::all() as $package) {
    echo '- ID: ' . $package->package_id . ', Name: ' . $package->package_name . PHP_EOL;
}

echo PHP_EOL . 'Education Levels:' . PHP_EOL;
foreach (\App\Models\EducationLevel::all() as $level) {
    echo '- ID: ' . $level->id . ', Name: ' . $level->level_name . PHP_EOL;
}
