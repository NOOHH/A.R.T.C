<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleFormRequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $requirements = [
            [
                'field_name' => 'section_1',
                'field_label' => 'Personal Information',
                'field_type' => 'section',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 1,
                'section_name' => 'Personal Information'
            ],
            [
                'field_name' => 'phone_number',
                'field_label' => 'Phone Number',
                'field_type' => 'tel',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'section_name' => 'Personal Information'
            ],
            [
                'field_name' => 'emergency_contact',
                'field_label' => 'Emergency Contact',
                'field_type' => 'tel',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'section_name' => 'Personal Information'
            ],
            [
                'field_name' => 'section_2',
                'field_label' => 'Educational Background',
                'field_type' => 'section',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 4,
                'section_name' => 'Educational Background'
            ],
            [
                'field_name' => 'highest_education',
                'field_label' => 'Highest Educational Attainment',
                'field_type' => 'select',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'field_options' => ['High School', 'Bachelor Degree', 'Master Degree', 'Doctorate'],
                'sort_order' => 5,
                'section_name' => 'Educational Background'
            ],
            [
                'field_name' => 'section_3',
                'field_label' => 'Documents',
                'field_type' => 'section',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 6,
                'section_name' => 'Documents'
            ],
            [
                'field_name' => 'tor_document',
                'field_label' => 'Transcript of Records (TOR)',
                'field_type' => 'file',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'section_name' => 'Documents'
            ],
            [
                'field_name' => 'diploma_document',
                'field_label' => 'Diploma/Certificate',
                'field_type' => 'file',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
                'section_name' => 'Documents'
            ]
        ];

        foreach ($requirements as $requirement) {
            \App\Models\FormRequirement::create($requirement);
        }
    }
}
