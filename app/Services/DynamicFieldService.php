<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\FormRequirement;

class DynamicFieldService
{
    /**
     * Synchronize dynamic fields between registrations and students tables
     */
    public function synchronizeDynamicFields()
    {
        try {
            // Get all dynamic fields from form_requirements
            $dynamicFields = FormRequirement::where('entity_type', 'student')
                ->where('is_active', true)
                ->where('field_type', '!=', 'section')
                ->get();

            // Get existing columns from both tables
            $registrationColumns = $this->getTableColumns('registrations');
            $studentColumns = $this->getTableColumns('students');

            foreach ($dynamicFields as $field) {
                $columnName = $field->field_name;
                
                // Skip if column already exists in students table
                if (in_array($columnName, $studentColumns)) {
                    continue;
                }

                // Check if column exists in registrations table
                if (in_array($columnName, $registrationColumns)) {
                    $this->addColumnToStudentsTable($columnName, $field->field_type);
                    $this->migrateDataToStudentsTable($columnName);
                }
            }

            Log::info('Dynamic field synchronization completed successfully');
            return true;

        } catch (\Exception $e) {
            Log::error('Error synchronizing dynamic fields: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all column names from a table
     */
    private function getTableColumns($tableName)
    {
        $columns = DB::select("DESCRIBE {$tableName}");
        return array_map(function($column) {
            return $column->Field;
        }, $columns);
    }

    /**
     * Add a new column to the students table
     */
    private function addColumnToStudentsTable($columnName, $fieldType)
    {
        try {
            Schema::table('students', function($table) use ($columnName, $fieldType) {
                switch ($fieldType) {
                    case 'text':
                    case 'email':
                        $table->string($columnName, 255)->nullable();
                        break;
                    case 'textarea':
                        $table->text($columnName)->nullable();
                        break;
                    case 'number':
                        $table->decimal($columnName, 10, 2)->nullable();
                        break;
                    case 'date':
                        $table->date($columnName)->nullable();
                        break;
                    case 'file':
                        $table->string($columnName, 255)->nullable();
                        break;
                    case 'select':
                    case 'checkbox':
                    case 'radio':
                    case 'multiple_selection':
                        $table->text($columnName)->nullable(); // Store as JSON for multiple values
                        break;
                    default:
                        $table->string($columnName, 255)->nullable();
                        break;
                }
            });

            Log::info("Added column '{$columnName}' to students table");
            return true;

        } catch (\Exception $e) {
            Log::error("Error adding column '{$columnName}' to students table: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Migrate data from registrations to students table for a specific column
     */
    private function migrateDataToStudentsTable($columnName)
    {
        try {
            // Get all approved registrations with data in this column
            $registrations = DB::table('registrations')
                ->join('students', 'registrations.user_id', '=', 'students.user_id')
                ->whereNotNull("registrations.{$columnName}")
                ->where("registrations.{$columnName}", '!=', '')
                ->where('registrations.status', 'approved')
                ->select('students.student_id', "registrations.{$columnName} as field_value")
                ->get();

            foreach ($registrations as $registration) {
                DB::table('students')
                    ->where('student_id', $registration->student_id)
                    ->update([$columnName => $registration->field_value]);
            }

            Log::info("Migrated data for column '{$columnName}' to students table ({$registrations->count()} records)");
            return true;

        } catch (\Exception $e) {
            Log::error("Error migrating data for column '{$columnName}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add a new dynamic field and sync it to students table
     */
    public function addDynamicField($fieldName, $fieldType, $fieldLabel = null)
    {
        try {
            // Add to registrations table first if it doesn't exist
            $registrationColumns = $this->getTableColumns('registrations');
            
            if (!in_array($fieldName, $registrationColumns)) {
                Schema::table('registrations', function($table) use ($fieldName, $fieldType) {
                    switch ($fieldType) {
                        case 'text':
                        case 'email':
                            $table->string($fieldName, 255)->nullable();
                            break;
                        case 'textarea':
                            $table->text($fieldName)->nullable();
                            break;
                        case 'number':
                            $table->decimal($fieldName, 10, 2)->nullable();
                            break;
                        case 'date':
                            $table->date($fieldName)->nullable();
                            break;
                        case 'file':
                            $table->string($fieldName, 255)->nullable();
                            break;
                        case 'select':
                        case 'checkbox':
                        case 'radio':
                        case 'multiple_selection':
                            $table->text($fieldName)->nullable();
                            break;
                        default:
                            $table->string($fieldName, 255)->nullable();
                            break;
                    }
                });
                Log::info("Added column '{$fieldName}' to registrations table");
            }

            // Add to students table
            $this->addColumnToStudentsTable($fieldName, $fieldType);

            // Create form requirement entry
            FormRequirement::create([
                'field_name' => $fieldName,
                'field_label' => $fieldLabel ?: ucwords(str_replace('_', ' ', $fieldName)),
                'field_type' => $fieldType,
                'entity_type' => 'student',
                'program_type' => 'both',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => FormRequirement::max('sort_order') + 1
            ]);

            Log::info("Successfully added dynamic field '{$fieldName}'");
            return true;

        } catch (\Exception $e) {
            Log::error("Error adding dynamic field '{$fieldName}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync a specific student's data from registration to student record
     */
    public function syncStudentData($userId)
    {
        try {
            // Get the approved registration for this user
            $registration = DB::table('registrations')
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->first();

            if (!$registration) {
                return false;
            }

            // Get the student record
            $student = DB::table('students')
                ->where('user_id', $userId)
                ->first();

            if (!$student) {
                return false;
            }

            // Get all dynamic fields
            $dynamicFields = FormRequirement::where('entity_type', 'student')
                ->where('is_active', true)
                ->where('field_type', '!=', 'section')
                ->pluck('field_name');

            $updateData = [];
            $registrationArray = (array) $registration;

            foreach ($dynamicFields as $fieldName) {
                if (isset($registrationArray[$fieldName]) && !empty($registrationArray[$fieldName])) {
                    $updateData[$fieldName] = $registrationArray[$fieldName];
                }
            }

            if (!empty($updateData)) {
                DB::table('students')
                    ->where('student_id', $student->student_id)
                    ->update($updateData);

                Log::info("Synced data for student {$student->student_id}", ['fields' => array_keys($updateData)]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Error syncing student data for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get missing columns in students table compared to registrations
     */
    public function getMissingColumnsInStudents()
    {
        $registrationColumns = $this->getTableColumns('registrations');
        $studentColumns = $this->getTableColumns('students');
        
        // Get dynamic fields
        $dynamicFields = FormRequirement::where('entity_type', 'student')
            ->where('is_active', true)
            ->where('field_type', '!=', 'section')
            ->pluck('field_name')
            ->toArray();

        // Find missing columns that are also dynamic fields
        $missingColumns = array_intersect(
            array_diff($registrationColumns, $studentColumns),
            $dynamicFields
        );

        return $missingColumns;
    }
}
