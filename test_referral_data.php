<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Referrals Table Structure ===\n";
$structure = DB::select('DESCRIBE referrals');
foreach($structure as $column) {
    echo "Field: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}\n";
}

echo "\n=== Check if Directors and Professors exist ===\n";
$directors = DB::table('directors')->get();
$professors = DB::table('professors')->get();

echo "Directors count: " . $directors->count() . "\n";
if ($directors->count() > 0) {
    foreach($directors->take(2) as $director) {
        echo "Director: ID={$director->director_id}, Name={$director->first_name} {$director->last_name}, Code={$director->referral_code}\n";
    }
}

echo "\nProfessors count: " . $professors->count() . "\n";
if ($professors->count() > 0) {
    foreach($professors->take(2) as $professor) {
        echo "Professor: ID={$professor->professor_id}, Name={$professor->first_name} {$professor->last_name}, Code={$professor->referral_code}\n";
    }
}

// Let's add some test referral data
echo "\n=== Adding Test Referral Data ===\n";

if ($directors->count() > 0 && $professors->count() > 0) {
    $testReferrals = [
        [
            'referral_code' => $directors->first()->referral_code,
            'student_id' => 1,
            'referrer_type' => 'director',
            'referrer_id' => $directors->first()->director_id,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'referral_code' => $professors->first()->referral_code,
            'student_id' => 2,
            'referrer_type' => 'professor',
            'referrer_id' => $professors->first()->professor_id,
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    foreach($testReferrals as $referral) {
        try {
            // Check if already exists
            $exists = DB::table('referrals')->where('referral_code', $referral['referral_code'])->where('student_id', $referral['student_id'])->first();
            if (!$exists) {
                DB::table('referrals')->insert($referral);
                echo "Added referral: {$referral['referral_code']} for student {$referral['student_id']}\n";
            } else {
                echo "Referral already exists: {$referral['referral_code']} for student {$referral['student_id']}\n";
            }
        } catch (Exception $e) {
            echo "Error adding referral: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "No directors or professors found to create test data\n";
}
