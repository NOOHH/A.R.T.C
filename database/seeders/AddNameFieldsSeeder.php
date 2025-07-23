<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormRequirement;

class AddNameFieldsSeeder extends Seeder
{
    public function run()
    {
        // Check if firstname field exists
        $firstnameField = FormRequirement::where('field_name', 'firstname')->first();
        if (!$firstnameField) {
            FormRequirement::create([
                'field_name' => 'firstname',
                'field_label' => 'First Name',
                'field_type' => 'text',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'is_bold' => false
            ]);
            echo "Created firstname field\n";
        } else {
            // Ensure it's active
            $firstnameField->update(['is_active' => true]);
            echo "Firstname field already exists, ensured it's active\n";
        }

        // Check if lastname field exists
        $lastnameField = FormRequirement::where('field_name', 'lastname')->first();
        if (!$lastnameField) {
            FormRequirement::create([
                'field_name' => 'lastname',
                'field_label' => 'Last Name',
                'field_type' => 'text',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'is_bold' => false
            ]);
            echo "Created lastname field\n";
        } else {
            // Ensure it's active
            $lastnameField->update(['is_active' => true]);
            echo "Lastname field already exists, ensured it's active\n";
        }

        // Also check for alternate field names (first_name, last_name)
        $firstNameField = FormRequirement::where('field_name', 'first_name')->first();
        if (!$firstNameField) {
            FormRequirement::create([
                'field_name' => 'first_name',
                'field_label' => 'First Name',
                'field_type' => 'text',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'is_bold' => false
            ]);
            echo "Created first_name field\n";
        }

        $lastNameField = FormRequirement::where('field_name', 'last_name')->first();
        if (!$lastNameField) {
            FormRequirement::create([
                'field_name' => 'last_name',
                'field_label' => 'Last Name',
                'field_type' => 'text',
                'section_name' => 'Personal Information',
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'is_bold' => false
            ]);
            echo "Created last_name field\n";
        }
    }
}
