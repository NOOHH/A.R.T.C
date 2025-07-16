<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Adding Test Referral Data ===\n";

// Get directors and professors
$directors = DB::table('directors')->whereNull('directors_archived')->orWhere('directors_archived', 0)->get();
$professors = DB::table('professors')->whereNull('professor_archived')->orWhere('professor_archived', 0)->get();

echo "Active Directors: " . $directors->count() . "\n";
echo "Active Professors: " . $professors->count() . "\n";

if ($directors->count() > 0 && $professors->count() > 0) {
    $director = $directors->first();
    $professor = $professors->first();
    
    // Create test referral data with proper registration_id (use a dummy value for now)
    $testReferrals = [
        [
            'referral_code' => $director->referral_code,
            'student_id' => 'STU001',
            'referrer_type' => 'director',
            'referrer_id' => $director->directors_id,
            'registration_id' => 1,
            'used_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'referral_code' => $professor->referral_code,
            'student_id' => 'STU002',
            'referrer_type' => 'professor',
            'referrer_id' => $professor->professor_id,
            'registration_id' => 2,
            'used_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'referral_code' => $director->referral_code,
            'student_id' => 'STU003',
            'referrer_type' => 'director',
            'referrer_id' => $director->directors_id,
            'registration_id' => 3,
            'used_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    foreach($testReferrals as $referral) {
        try {
            // Check if already exists
            $exists = DB::table('referrals')
                ->where('referral_code', $referral['referral_code'])
                ->where('student_id', $referral['student_id'])
                ->first();
                
            if (!$exists) {
                DB::table('referrals')->insert($referral);
                echo "✅ Added referral: {$referral['referral_code']} for student {$referral['student_id']} (type: {$referral['referrer_type']})\n";
            } else {
                echo "⚠️ Referral already exists: {$referral['referral_code']} for student {$referral['student_id']}\n";
            }
        } catch (Exception $e) {
            echo "❌ Error adding referral: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== Current Referrals Summary ===\n";
    $allReferrals = DB::table('referrals')->get();
    echo "Total referrals: " . $allReferrals->count() . "\n";
    
    $directorReferrals = DB::table('referrals')->where('referrer_type', 'director')->count();
    $professorReferrals = DB::table('referrals')->where('referrer_type', 'professor')->count();
    
    echo "Director referrals: $directorReferrals\n";
    echo "Professor referrals: $professorReferrals\n";
    
} else {
    echo "❌ No active directors or professors found to create test data\n";
}
