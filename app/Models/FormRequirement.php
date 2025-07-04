<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

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
     * Create database column for a new field
     */
    public static function createDatabaseColumn($fieldName, $fieldType)
    {
        // Skip if column already exists
        if (self::columnExists($fieldName)) {
            return true;
        }
        
        // Skip sections as they don't need database columns
        if ($fieldType === 'section') {
            return true;
        }
        
        try {
            $columnType = self::getColumnTypeForField($fieldType);
            
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
                    case 'boolean':
                        $table->boolean($fieldName)->nullable();
                        break;
                    case 'json':
                        $table->json($fieldName)->nullable();
                        break;
                    default:
                        $table->string($fieldName, 255)->nullable();
                }
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to create column {$fieldName}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Archive database column for a field (rename it)
     */
    public static function archiveDatabaseColumn($fieldName)
    {
        if (!self::columnExists($fieldName)) {
            return true;
        }
        
        try {
            $archivedName = "archived_{$fieldName}_" . time();
            
            Schema::table('registrations', function ($table) use ($fieldName, $archivedName) {
                $table->renameColumn($fieldName, $archivedName);
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to archive column {$fieldName}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Restore database column for a field (rename it back)
     */
    public static function restoreDatabaseColumn($fieldName)
    {
        // Find the archived column
        $columns = Schema::getColumnListing('registrations');
        $archivedColumn = null;
        
        foreach ($columns as $column) {
            if (preg_match("/^archived_{$fieldName}_\d+$/", $column)) {
                $archivedColumn = $column;
                break;
            }
        }
        
        if (!$archivedColumn) {
            // Column doesn't exist, create it
            $requirement = self::where('field_name', $fieldName)->first();
            if ($requirement) {
                return self::createDatabaseColumn($fieldName, $requirement->field_type);
            }
            return false;
        }
        
        try {
            Schema::table('registrations', function ($table) use ($archivedColumn, $fieldName) {
                $table->renameColumn($archivedColumn, $fieldName);
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to restore column {$fieldName}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a column exists in registrations table
     */
    public static function columnExists($columnName)
    {
        try {
            return Schema::hasColumn('registrations', $columnName);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get the appropriate database column type for a field type
     */
    private static function getColumnTypeForField($fieldType)
    {
        return match($fieldType) {
            'text', 'email', 'tel' => 'string',
            'textarea' => 'text',
            'number' => 'integer',
            'date' => 'date',
            'checkbox' => 'boolean',
            'file' => 'string', // Store file path
            'select', 'radio' => 'string',
            'module_selection' => 'json',
            default => 'string'
        };
    }
}
