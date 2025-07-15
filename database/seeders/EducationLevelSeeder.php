<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EducationLevel;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $educationLevels = [
            [
                'level_name' => 'Undergraduate',
                'file_requirements' => json_encode([
                    'School ID' => ['required' => true, 'type' => 'image', 'description' => 'Valid school identification'],
                    'TOR file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'Transcript of Records'],
                    'Good Moral file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'Certificate of Good Moral Character'],
                    'PSA file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'PSA Birth Certificate']
                ]),
                'available_for_general' => true,
                'available_for_professional' => true,
                'available_for_review' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_name' => 'Graduate',
                'file_requirements' => json_encode([
                    'School ID' => ['required' => true, 'type' => 'image', 'description' => 'Valid school identification'],
                    'Diploma' => ['required' => true, 'type' => 'pdf', 'description' => 'College Diploma'],
                    'TOR file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'Transcript of Records'],
                    'PSA file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'PSA Birth Certificate']
                ]),
                'available_for_general' => true,
                'available_for_professional' => true,
                'available_for_review' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_name' => 'Post-Graduate',
                'file_requirements' => json_encode([
                    'School ID' => ['required' => true, 'type' => 'image', 'description' => 'Valid school identification'],
                    'Masters Diploma' => ['required' => true, 'type' => 'pdf', 'description' => 'Masters Degree Diploma'],
                    'TOR file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'Graduate Transcript of Records'],
                    'PSA file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'PSA Birth Certificate']
                ]),
                'available_for_general' => true,
                'available_for_professional' => true,
                'available_for_review' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($educationLevels as $level) {
            EducationLevel::updateOrCreate(
                ['level_name' => $level['level_name']],
                $level
            );
        }
    }
}
