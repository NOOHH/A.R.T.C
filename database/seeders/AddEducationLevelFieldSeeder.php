<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormRequirement;

class AddEducationLevelFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if education_level field already exists
        $existingField = FormRequirement::where('field_name', 'education_level')->first();
        
        if (!$existingField) {
            // Add education level field to form requirements
            FormRequirement::create([
                'field_name' => 'education_level',
                'field_label' => 'Education Level',
                'field_type' => 'select',
                'field_options' => ['Undergraduate', 'Graduate'],
                'program_type' => 'both',
                'is_required' => true,
                'is_active' => true,
                'is_bold' => true,
                'sort_order' => 16, // Place after existing fields
                'section_name' => 'Education Information'
            ]);
            
            echo "Education level field added to form requirements.\n";
        } else {
            echo "Education level field already exists in form requirements.\n";
        }
        
        // Update sort order for other fields to maintain proper ordering
        $this->updateSortOrders();
    }
    
    /**
     * Update sort orders to maintain proper field ordering
     */
    private function updateSortOrders()
    {
        // Get all fields ordered by current sort_order
        $fields = FormRequirement::orderBy('sort_order')->get();
        
        $newOrder = 1;
        foreach ($fields as $field) {
            if ($field->field_name !== 'education_level') {
                $field->update(['sort_order' => $newOrder]);
                $newOrder++;
            }
        }
        
        // Set education level to come before document upload fields
        $educationField = FormRequirement::where('field_name', 'education_level')->first();
        if ($educationField) {
            $documentStartOrder = FormRequirement::whereIn('field_name', [
                'valid_id', 'birth_certificate', 'diploma_certificate', 
                'medical_certificate', 'passport_photo'
            ])->min('sort_order');
            
            if ($documentStartOrder) {
                $educationField->update(['sort_order' => $documentStartOrder - 1]);
                
                // Adjust document field orders
                FormRequirement::whereIn('field_name', [
                    'valid_id', 'birth_certificate', 'diploma_certificate', 
                    'medical_certificate', 'passport_photo'
                ])->increment('sort_order');
            }
        }
    }
}
