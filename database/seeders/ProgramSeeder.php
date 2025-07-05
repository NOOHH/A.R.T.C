<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        // Create sample programs for testing
        $programs = [
            [
                'program_name' => 'Nursing Board Review',
                'program_description' => 'Comprehensive review program for nursing board examination',
                'is_archived' => false,
            ],
            [
                'program_name' => 'Medical Technology Review',
                'program_description' => 'Complete preparation for medical technology board exam',
                'is_archived' => false,
            ],
            [
                'program_name' => 'Physical Therapy Review',
                'program_description' => 'Intensive review for physical therapy board examination',
                'is_archived' => false,
            ],
            [
                'program_name' => 'Pharmacy Review',
                'program_description' => 'Board review program for pharmacy graduates',
                'is_archived' => false,
            ],
            [
                'program_name' => 'Medical Laboratory Science',
                'program_description' => 'Specialized review for medical laboratory science professionals',
                'is_archived' => false,
            ]
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}
