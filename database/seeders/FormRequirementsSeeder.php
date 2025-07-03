<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormRequirement;

class FormRequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing requirements
        FormRequirement::truncate();
        
        $requirements = [
            // Complete Program Requirements
            [
                'field_name' => 'religion',
                'field_label' => 'Religion',
                'field_type' => 'text',
                'program_type' => 'complete',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'field_name' => 'citizenship',
                'field_label' => 'Citizenship',
                'field_type' => 'text',
                'program_type' => 'complete',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'field_name' => 'civil_status',
                'field_label' => 'Civil Status',
                'field_type' => 'select',
                'field_options' => ['Single', 'Married', 'Divorced', 'Widowed'],
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'field_name' => 'birthdate',
                'field_label' => 'Date of Birth',
                'field_type' => 'date',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'field_name' => 'gender',
                'field_label' => 'Gender',
                'field_type' => 'radio',
                'field_options' => ['Male', 'Female', 'Other'],
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5
            ],
            
            // Modular Program Requirements
            [
                'field_name' => 'work_experience',
                'field_label' => 'Work Experience',
                'field_type' => 'textarea',
                'program_type' => 'modular',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'field_name' => 'preferred_schedule',
                'field_label' => 'Preferred Schedule',
                'field_type' => 'select',
                'field_options' => ['Morning', 'Afternoon', 'Evening', 'Weekend'],
                'program_type' => 'modular',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'field_name' => 'emergency_contact_relationship',
                'field_label' => 'Emergency Contact Relationship',
                'field_type' => 'text',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8
            ],
            [
                'field_name' => 'health_conditions',
                'field_label' => 'Any Health Conditions?',
                'field_type' => 'textarea',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 9
            ],
            [
                'field_name' => 'disability_support',
                'field_label' => 'Do you need disability support?',
                'field_type' => 'checkbox',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 10
            ],
            
            // Document Upload Fields
            [
                'field_name' => 'valid_id',
                'field_label' => 'Valid ID (Front and Back)',
                'field_type' => 'file',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 11
            ],
            [
                'field_name' => 'birth_certificate',
                'field_label' => 'Birth Certificate',
                'field_type' => 'file',
                'program_type' => 'complete',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 12
            ],
            [
                'field_name' => 'diploma_certificate',
                'field_label' => 'Diploma/Certificate of Completion',
                'field_type' => 'file',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 13
            ],
            [
                'field_name' => 'medical_certificate',
                'field_label' => 'Medical Certificate',
                'field_type' => 'file',
                'program_type' => 'complete',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 14
            ],
            [
                'field_name' => 'passport_photo',
                'field_label' => '2x2 Passport Photo',
                'field_type' => 'file',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 15
            ]
        ];
        
        foreach ($requirements as $requirement) {
            FormRequirement::create($requirement);
        }
    }
}
