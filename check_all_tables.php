<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking All Table Structures\n";
echo "=============================\n";

echo "Programs table structure:\n";
$programs = \Illuminate\Support\Facades\DB::select("DESCRIBE programs");
foreach($programs as $column) {
    echo "- " . $column->Field . " (" . $column->Type . ")\n";
}

echo "\nProfessor_program table structure:\n";
$pp = \Illuminate\Support\Facades\DB::select("DESCRIBE professor_program");
foreach($pp as $column) {
    echo "- " . $column->Field . " (" . $column->Type . ")\n";
}

echo "\nSample data from programs:\n";
$samplePrograms = \Illuminate\Support\Facades\DB::select("SELECT * FROM programs LIMIT 5");
foreach($samplePrograms as $program) {
    echo "Program: ";
    foreach($program as $key => $value) {
        echo "$key: $value | ";
    }
    echo "\n";
}

echo "\nSample data from professor_program:\n";
$samplePP = \Illuminate\Support\Facades\DB::select("SELECT * FROM professor_program WHERE professor_id = 8");
foreach($samplePP as $pp) {
    echo "Professor-Program: ";
    foreach($pp as $key => $value) {
        echo "$key: $value | ";
    }
    echo "\n";
}

echo "\nTrying to find program 40:\n";
// Try different possible primary key names
$possibleKeys = ['id', 'program_id', 'programs_id'];
foreach($possibleKeys as $key) {
    try {
        $program = \Illuminate\Support\Facades\DB::select("SELECT * FROM programs WHERE $key = 40");
        if (!empty($program)) {
            echo "Found program 40 using key '$key':\n";
            foreach($program[0] as $field => $value) {
                echo "- $field: $value\n";
            }
            break;
        }
    } catch(\Exception $e) {
        echo "Key '$key' doesn't exist\n";
    }
}

echo "\nComplete analysis!\n";
