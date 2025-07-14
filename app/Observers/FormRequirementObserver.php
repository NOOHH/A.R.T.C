<?php

namespace App\Observers;

use App\Models\FormRequirement;
use Illuminate\Support\Facades\Log;

class FormRequirementObserver
{
    /**
     * Handle the FormRequirement "created" event.
     */
    public function created(FormRequirement $formRequirement)
    {
        Log::info('FormRequirement created, adding database columns', [
            'field_name' => $formRequirement->field_name,
            'field_type' => $formRequirement->field_type
        ]);
        
        // Automatically create database columns when a new form requirement is created
        FormRequirement::createDatabaseColumn($formRequirement->field_name, $formRequirement->field_type);
    }

    /**
     * Handle the FormRequirement "updated" event.
     */
    public function updated(FormRequirement $formRequirement)
    {
        // If field_name or field_type changed, we might need to create new columns
        if ($formRequirement->wasChanged('field_name') || $formRequirement->wasChanged('field_type')) {
            Log::info('FormRequirement field_name or field_type changed, updating database columns', [
                'old_field_name' => $formRequirement->getOriginal('field_name'),
                'new_field_name' => $formRequirement->field_name,
                'old_field_type' => $formRequirement->getOriginal('field_type'),
                'new_field_type' => $formRequirement->field_type
            ]);
            
            // Create column for new field_name and field_type
            FormRequirement::createDatabaseColumn($formRequirement->field_name, $formRequirement->field_type);
            
            // Note: We don't delete the old column to preserve data
            if ($formRequirement->wasChanged('field_name')) {
                $oldFieldName = $formRequirement->getOriginal('field_name');
                Log::info("Old column '{$oldFieldName}' preserved with data. New column '{$formRequirement->field_name}' created.");
            }
        }
        
        // If is_active changed to false, log archival (but don't delete columns)
        if ($formRequirement->wasChanged('is_active') && !$formRequirement->is_active) {
            Log::info('FormRequirement archived, preserving database columns and data', [
                'field_name' => $formRequirement->field_name
            ]);
        }
        
        // If is_active changed to true, ensure columns exist
        if ($formRequirement->wasChanged('is_active') && $formRequirement->is_active) {
            Log::info('FormRequirement restored, ensuring database columns exist', [
                'field_name' => $formRequirement->field_name
            ]);
            FormRequirement::createDatabaseColumn($formRequirement->field_name, $formRequirement->field_type);
        }
    }

    /**
     * Handle the FormRequirement "deleted" event.
     */
    public function deleted(FormRequirement $formRequirement)
    {
        Log::info('FormRequirement deleted, preserving database columns and data', [
            'field_name' => $formRequirement->field_name,
            'preservation_note' => 'Database columns and existing data are preserved for potential future restoration'
        ]);
        
        // Check if there's existing data
        $dataStatus = FormRequirement::hasExistingData($formRequirement->field_name);
        if ($dataStatus['has_any_data']) {
            Log::warning('FormRequirement deleted but data exists in database', [
                'field_name' => $formRequirement->field_name,
                'data_in_registrations' => $dataStatus['registrations'],
                'data_in_students' => $dataStatus['students'],
                'action' => 'Data preserved for future restoration'
            ]);
        }
        
        // Do NOT delete columns - preserve all data for potential restoration
    }

    /**
     * Handle the FormRequirement "restored" event.
     */
    public function restored(FormRequirement $formRequirement)
    {
        Log::info('FormRequirement restored, ensuring database columns exist', [
            'field_name' => $formRequirement->field_name
        ]);
        
        // Ensure columns exist when form requirement is restored
        FormRequirement::createDatabaseColumn($formRequirement->field_name, $formRequirement->field_type);
    }
}
