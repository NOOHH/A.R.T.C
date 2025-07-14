<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FormRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_name',
        'field_label', 
        'field_type',
        'program_type',
        'is_required',
        'is_active',
        'field_options',
        'validation_rules',
        'sort_order',
        'section_name',
        'is_bold'
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'is_bold' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProgram($query, $programType)
    {
        return $query->where(function($q) use ($programType) {
            $q->where('program_type', $programType)
              ->orWhere('program_type', 'both');
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Archive a field requirement (set as inactive)
     */
    public static function archiveField($fieldName)
    {
        return self::where('field_name', $fieldName)->update(['is_active' => false]);
    }
    
    /**
     * Restore a field requirement (set as active)
     */
    public static function restoreField($fieldName)
    {
        return self::where('field_name', $fieldName)->update(['is_active' => true]);
    }
    
    /**
     * Get archived fields
     */
    public function scopeArchived($query)
    {
        return $query->where('is_active', false);
    }
    
    /**
     * Check if a field is required and active
     */
    public static function isFieldRequiredAndActive($fieldName, $programType = 'both')
    {
        return self::where('field_name', $fieldName)
            ->where('is_active', true)
            ->where(function($q) use ($programType) {
                $q->where('program_type', $programType)
                  ->orWhere('program_type', 'both');
            })
            ->where('is_required', true)
            ->exists();
    }
    
    /**
     * Get all required field names for a program type
     */
    public static function getRequiredFieldNames($programType)
    {
        return self::active()
            ->forProgram($programType)
            ->where('is_required', true)
            ->where('field_type', '!=', 'section')
            ->pluck('field_name')
            ->toArray();
    }
    
    /**
     * Check if a field name exists and is active
     */
    public static function fieldExistsAndActive($fieldName, $programType = 'both')
    {
        return self::active()
            ->forProgram($programType)
            ->where('field_name', $fieldName)
            ->exists();
    }
    
    /**
     * Create database column for a new field in both registrations and students tables
     */
    public static function createDatabaseColumn($fieldName, $fieldType)
    {
        // Skip sections as they don't need database columns
        if ($fieldType === 'section') {
            Log::info("Skipping column creation for section field: {$fieldName}");
            return true;
        }
        
        // Skip special fields that shouldn't be database columns
        $skipFields = ['batch_id', 'program_id', 'package_id', 'user_id', 'enrollment_type', 'learning_mode'];
        if (in_array($fieldName, $skipFields)) {
            Log::info("Skipping column creation for special field: {$fieldName}");
            return true;
        }
        
        $success = true;
        $columnType = self::getColumnTypeForField($fieldType);
        
        Log::info("Creating database columns for field", [
            'field_name' => $fieldName,
            'field_type' => $fieldType,
            'column_type' => $columnType
        ]);
        
        // Create column in registrations table
        if (!self::columnExists($fieldName, 'registrations')) {
            try {
                Schema::table('registrations', function ($table) use ($fieldName, $columnType) {
                    switch ($columnType) {
                        case 'string':
                            $table->string($fieldName, 255)->nullable();
                            break;
                        case 'text':
                            $table->text($fieldName)->nullable();
                            break;
                        case 'integer':
                            $table->integer($fieldName)->nullable();
                            break;
                        case 'date':
                            $table->date($fieldName)->nullable();
                            break;
                        case 'datetime':
                            $table->dateTime($fieldName)->nullable();
                            break;
                        case 'boolean':
                            $table->boolean($fieldName)->nullable();
                            break;
                        case 'json':
                            $table->json($fieldName)->nullable();
                            break;
                        case 'decimal':
                            $table->decimal($fieldName, 8, 2)->nullable();
                            break;
                        default:
                            $table->string($fieldName, 255)->nullable();
                    }
                });
                Log::info("Successfully created column '{$fieldName}' in registrations table");
            } catch (\Exception $e) {
                Log::error("Failed to create column '{$fieldName}' in registrations table", [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $success = false;
            }
        } else {
            Log::info("Column '{$fieldName}' already exists in registrations table");
        }
        
        // Create column in students table
        if (!self::columnExists($fieldName, 'students')) {
            try {
                Schema::table('students', function ($table) use ($fieldName, $columnType) {
                    switch ($columnType) {
                        case 'string':
                            $table->string($fieldName, 255)->nullable();
                            break;
                        case 'text':
                            $table->text($fieldName)->nullable();
                            break;
                        case 'integer':
                            $table->integer($fieldName)->nullable();
                            break;
                        case 'date':
                            $table->date($fieldName)->nullable();
                            break;
                        case 'datetime':
                            $table->dateTime($fieldName)->nullable();
                            break;
                        case 'boolean':
                            $table->boolean($fieldName)->nullable();
                            break;
                        case 'json':
                            $table->json($fieldName)->nullable();
                            break;
                        case 'decimal':
                            $table->decimal($fieldName, 8, 2)->nullable();
                            break;
                        default:
                            $table->string($fieldName, 255)->nullable();
                    }
                });
                Log::info("Successfully created column '{$fieldName}' in students table");
            } catch (\Exception $e) {
                Log::error("Failed to create column '{$fieldName}' in students table", [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $success = false;
            }
        } else {
            Log::info("Column '{$fieldName}' already exists in students table");
        }
        
        return $success;
    }
    
    /**
     * Archive database column for a field (keep column and data, only mark form_requirement as inactive)
     */
    public static function archiveDatabaseColumn($fieldName)
    {
        // Don't delete columns! Just mark the form_requirement as inactive
        // Data stays in registrations and students tables for future restoration
        return self::archiveField($fieldName);
    }
    
    /**
     * Restore database column for a field (reactivate form_requirement and ensure columns exist)
     */
    public static function restoreDatabaseColumn($fieldName)
    {
        // First, try to reactivate existing form_requirement
        $existing = self::where('field_name', $fieldName)->first();
        if ($existing) {
            $existing->update(['is_active' => true]);
            Log::info("Reactivated existing form_requirement for field: {$fieldName}");
            
            // Ensure columns exist in both tables (they should already exist with data)
            self::createDatabaseColumn($fieldName, $existing->field_type);
            return true;
        }
        
        Log::warning("No form_requirement found for field: {$fieldName}");
        return false;
    }

    /**
     * Check if field has existing data in registrations or students tables
     */
    public static function hasExistingData($fieldName)
    {
        $hasDataInRegistrations = false;
        $hasDataInStudents = false;
        
        if (self::columnExists($fieldName, 'registrations')) {
            $hasDataInRegistrations = DB::table('registrations')
                ->whereNotNull($fieldName)
                ->where($fieldName, '!=', '')
                ->exists();
        }
        
        if (self::columnExists($fieldName, 'students')) {
            $hasDataInStudents = DB::table('students')
                ->whereNotNull($fieldName)
                ->where($fieldName, '!=', '')
                ->exists();
        }
        
        return [
            'registrations' => $hasDataInRegistrations,
            'students' => $hasDataInStudents,
            'has_any_data' => $hasDataInRegistrations || $hasDataInStudents
        ];
    }

    /**
     * Check if a column exists in the specified table
     */
    public static function columnExists($columnName, $tableName = 'registrations')
    {
        try {
            return Schema::hasColumn($tableName, $columnName);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Sync all existing form requirements with database columns
     * This ensures all active form requirements have corresponding database columns
     */
    public static function syncAllFormRequirementsWithDatabase()
    {
        Log::info('Starting sync of all form requirements with database columns');
        
        $formRequirements = self::active()->get();
        $successCount = 0;
        $failureCount = 0;
        
        foreach ($formRequirements as $formRequirement) {
            if ($formRequirement->field_type === 'section') {
                Log::info("Skipping section field: {$formRequirement->field_name}");
                continue;
            }
            
            try {
                $success = self::createDatabaseColumn($formRequirement->field_name, $formRequirement->field_type);
                if ($success) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            } catch (\Exception $e) {
                Log::error("Error syncing form requirement", [
                    'field_name' => $formRequirement->field_name,
                    'error' => $e->getMessage()
                ]);
                $failureCount++;
            }
        }
        
        Log::info('Completed sync of form requirements with database columns', [
            'total_processed' => $formRequirements->count(),
            'successful' => $successCount,
            'failed' => $failureCount
        ]);
        
        return [
            'total_processed' => $formRequirements->count(),
            'successful' => $successCount,
            'failed' => $failureCount
        ];
    }

    /**
     * Get all columns that exist in registrations table
     */
    public static function getRegistrationsTableColumns()
    {
        try {
            return Schema::getColumnListing('registrations');
        } catch (\Exception $e) {
            Log::error('Error getting registrations table columns: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all columns that exist in students table
     */
    public static function getStudentsTableColumns()
    {
        try {
            return Schema::getColumnListing('students');
        } catch (\Exception $e) {
            Log::error('Error getting students table columns: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get orphaned columns (columns that exist in tables but have no active form requirement)
     */
    public static function getOrphanedColumns()
    {
        $registrationsColumns = self::getRegistrationsTableColumns();
        $studentsColumns = self::getStudentsTableColumns();
        
        // Base columns that should be ignored (core table columns)
        $baseRegistrationsColumns = [
            'registration_id', 'user_id', 'program_id', 'package_id', 'enrollment_type', 
            'learning_mode', 'start_date', 'status', 'selected_modules', 'created_at', 
            'updated_at', 'id', 'deleted_at'
        ];
        
        $baseStudentsColumns = [
            'student_id', 'user_id', 'student_number', 'program_id', 'year_level', 
            'section', 'status', 'created_at', 'updated_at', 'id', 'deleted_at'
        ];
        
        // Get active form requirement field names
        $activeFieldNames = self::active()->pluck('field_name')->toArray();
        
        // Find orphaned columns
        $orphanedRegistrationsColumns = collect($registrationsColumns)
            ->diff($baseRegistrationsColumns)
            ->diff($activeFieldNames)
            ->values()
            ->toArray();
            
        $orphanedStudentsColumns = collect($studentsColumns)
            ->diff($baseStudentsColumns)
            ->diff($activeFieldNames)
            ->values()
            ->toArray();
        
        return [
            'registrations' => $orphanedRegistrationsColumns,
            'students' => $orphanedStudentsColumns,
            'total_orphaned' => count($orphanedRegistrationsColumns) + count($orphanedStudentsColumns)
        ];
    }

    /**
     * Get the appropriate database column type for a field type
     */
    private static function getColumnTypeForField($fieldType)
    {
        return match($fieldType) {
            'text', 'email', 'tel', 'url', 'password' => 'string',
            'textarea', 'longtext' => 'text',
            'number', 'integer' => 'integer',
            'decimal', 'float', 'money' => 'decimal',
            'date' => 'date',
            'datetime', 'datetime-local' => 'datetime',
            'checkbox', 'switch' => 'boolean',
            'file', 'image' => 'string', // Store file path
            'select', 'radio', 'dropdown' => 'string',
            'module_selection', 'multi_select', 'array' => 'json',
            'hidden' => 'string',
            default => 'string'
        };
    }
}
