<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormRequirement;

class FormRequirementSeeder extends Seeder
{
    public function run()
    {
        // Clear existing form requirements
        FormRequirement::truncate();
        
        $requirements = [
            [
                'field_name' => 'personal_info_section',
                'field_label' => 'Personal Information',
                'field_type' => 'section',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'field_name' => 'firstname',
                'field_label' => 'First Name',
                'field_type' => 'text',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'field_name' => 'middlename',
                'field_label' => 'Middle Name',
                'field_type' => 'text',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'field_name' => 'lastname',
                'field_label' => 'Last Name',
                'field_type' => 'text',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'field_name' => 'school_name',
                'field_label' => 'School Name',
                'field_type' => 'text',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'field_name' => 'contact_info_section',
                'field_label' => 'Contact Information',
                'field_type' => 'section',
                'section_name' => 'Contact Information',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'field_name' => 'street_address',
                'field_label' => 'Street Address',
                'field_type' => 'text',
                'section_name' => 'Contact Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'field_name' => 'city',
                'field_label' => 'City',
                'field_type' => 'text',
                'section_name' => 'Contact Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8
            ],
            [
                'field_name' => 'state_province',
                'field_label' => 'State/Province',
                'field_type' => 'text',
                'section_name' => 'Contact Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 9
            ],
            [
                'field_name' => 'zipcode',
                'field_label' => 'Zip Code',
                'field_type' => 'text',
                'section_name' => 'Contact Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'field_name' => 'contact_number',
                'field_label' => 'Contact Number',
                'field_type' => 'tel',
                'section_name' => 'Contact Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 11
            ],
            [
                'field_name' => 'emergency_contact_number',
                'field_label' => 'Emergency Contact Number',
                'field_type' => 'tel',
                'section_name' => 'Contact Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 12
            ],
            [
                'field_name' => 'documents_section',
                'field_label' => 'Required Documents',
                'field_type' => 'section',
                'section_name' => 'Required Documents',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 13
            ],
            [
                'field_name' => 'transcript_of_records',
                'field_label' => 'Transcript of Records',
                'field_type' => 'file',
                'section_name' => 'Required Documents',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 14
            ],
            [
                'field_name' => 'good_moral',
                'field_label' => 'Good Moral Certificate',
                'field_type' => 'file',
                'section_name' => 'Required Documents',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 15
            ],
            [
                'field_name' => 'photo_2x2',
                'field_label' => '2x2 Photo',
                'field_type' => 'file',
                'section_name' => 'Required Documents',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 16
            ],
            [
                'field_name' => 'selected_modules',
                'field_label' => 'Select Modules',
                'field_type' => 'module_selection',
                'section_name' => 'Program Selection',
                'program_type' => 'modular',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 17
            ],
        ];
        
        foreach ($requirements as $requirement) {
            FormRequirement::create($requirement);
        }
    }
}
