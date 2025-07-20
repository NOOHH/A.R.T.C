<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\EducationLevel;

class DynamicColumnHandler
{
    /**
     * Tables that should have dynamic columns created
     */
    protected $targetTables = ['registrations', 'students', 'enrollments'];
    
    /**
     * Create column in all target tables for a document requirement
     */
    public function createColumnForDocument($documentType, $documentLabel = null)
    {
        $columnName = $this->sanitizeColumnName($documentType);
        
        Log::info('Creating dynamic column for document', [
            'original_type' => $documentType,
            'column_name' => $columnName,
            'label' => $documentLabel
        ]);
        
        foreach ($this->targetTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                try {
                    if (!Schema::hasColumn($tableName, $columnName)) {
                        Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                            $table->string($columnName, 255)->nullable();
                        });
                        
                        Log::info("Column {$columnName} created in table {$tableName}");
                    } else {
                        Log::info("Column {$columnName} already exists in table {$tableName}");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to create column {$columnName} in table {$tableName}: " . $e->getMessage());
                }
            }
        }
        
        return $columnName;
    }
    
    /**
     * Check if document requirements have changed and update columns accordingly
     */
    public function syncDocumentColumns()
    {
        try {
            $educationLevels = EducationLevel::where('is_active', true)->get();
            
            foreach ($educationLevels as $level) {
                $fileRequirements = $level->file_requirements ?? [];
                
                foreach ($fileRequirements as $requirement) {
                    $documentType = $requirement['document_type'] ?? null;
                    $documentLabel = $requirement['document_label'] ?? null;
                    
                    if ($documentType) {
                        $this->createColumnForDocument($documentType, $documentLabel);
                    }
                }
            }
            
            Log::info('Dynamic column sync completed successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Dynamic column sync failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sanitize document type to create valid column name
     */
    private function sanitizeColumnName($documentType)
    {
        // Convert to lowercase and replace spaces/special chars with underscores
        $columnName = strtolower($documentType);
        $columnName = preg_replace('/[^a-z0-9_]/', '_', $columnName);
        $columnName = preg_replace('/_+/', '_', $columnName); // Remove duplicate underscores
        $columnName = trim($columnName, '_'); // Remove leading/trailing underscores
        
        // Ensure it's not too long (MySQL limit is 64 characters)
        if (strlen($columnName) > 60) {
            $columnName = substr($columnName, 0, 60);
        }
        
        // Ensure it doesn't start with a number
        if (is_numeric(substr($columnName, 0, 1))) {
            $columnName = 'doc_' . $columnName;
        }
        
        return $columnName;
    }
    
    /**
     * Get all dynamic document columns for a table
     */
    public function getDocumentColumns($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            return [];
        }
        
        $columns = Schema::getColumnListing($tableName);
        
        // Common document-related column patterns
        $documentPatterns = [
            'certificate', 'cert_', 'diploma', 'transcript', 'tor', 'psa',
            'good_moral', 'birth_cert', 'photo', 'id_', 'school_id',
            'clearance', 'medical', 'valid_', 'document'
        ];
        
        $documentColumns = [];
        
        foreach ($columns as $column) {
            foreach ($documentPatterns as $pattern) {
                if (stripos($column, $pattern) !== false) {
                    $documentColumns[] = $column;
                    break;
                }
            }
        }
        
        return array_unique($documentColumns);
    }
    
    /**
     * Map document type to appropriate column name for existing data
     */
    public function mapDocumentTypeToColumn($documentType)
    {
        // Create mapping for common document types to existing columns
        $mappings = [
            'PSA' => 'PSA',
            'psa_birth_certificate' => 'PSA', 
            'birth_certificate' => 'PSA',
            'good_moral' => 'good_moral',
            'certificate_of_good_moral_character' => 'good_moral',
            'Course_Cert' => 'Course_Cert',
            'course_certificate' => 'Course_Cert',
            'TOR' => 'TOR',
            'transcript_of_records' => 'TOR',
            'Cert_of_Grad' => 'Cert_of_Grad',
            'diploma' => 'Cert_of_Grad',
            'diploma_certificate' => 'Cert_of_Grad',
            'photo_2x2' => 'photo_2x2',
            'passport_photo' => 'photo_2x2',
            'school_id' => 'school_id',
            'valid_school_identification' => 'school_id'
        ];
        
        // Check if there's a direct mapping
        if (isset($mappings[$documentType])) {
            return $mappings[$documentType];
        }
        
        // Try to find similar existing columns
        foreach ($this->targetTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                $columns = $this->getDocumentColumns($tableName);
                $sanitizedType = $this->sanitizeColumnName($documentType);
                
                // Check for exact match
                if (in_array($sanitizedType, $columns)) {
                    return $sanitizedType;
                }
                
                // Check for similar matches
                foreach ($columns as $column) {
                    $similarity = 0;
                    similar_text($sanitizedType, $column, $similarity);
                    if ($similarity > 80) { // 80% similarity
                        Log::info("Found similar column for {$documentType}: {$column} ({$similarity}% similarity)");
                        return $column;
                    }
                }
            }
        }
        
        // Return sanitized version if no mapping found
        return $this->sanitizeColumnName($documentType);
    }
    
    /**
     * Store file path in appropriate column for all tables
     */
    public function storeFileInTables($documentType, $filePath, $userId, $registrationId = null, $studentId = null)
    {
        $columnName = $this->mapDocumentTypeToColumn($documentType);
        
        // Ensure column exists first
        $this->createColumnForDocument($documentType);
        
        $updates = [];
        
        // Update registrations table
        if ($registrationId && Schema::hasTable('registrations') && Schema::hasColumn('registrations', $columnName)) {
            try {
                DB::table('registrations')
                    ->where('registration_id', $registrationId)
                    ->update([$columnName => $filePath]);
                $updates[] = "registrations (ID: {$registrationId})";
            } catch (\Exception $e) {
                Log::error("Failed to update registrations table: " . $e->getMessage());
            }
        }
        
        // Update students table
        if ($studentId && Schema::hasTable('students') && Schema::hasColumn('students', $columnName)) {
            try {
                DB::table('students')
                    ->where('student_id', $studentId)
                    ->update([$columnName => $filePath]);
                $updates[] = "students (ID: {$studentId})";
            } catch (\Exception $e) {
                Log::error("Failed to update students table: " . $e->getMessage());
            }
        }
        
        // Update enrollments table
        if ($userId && Schema::hasTable('enrollments') && Schema::hasColumn('enrollments', $columnName)) {
            try {
                DB::table('enrollments')
                    ->where('user_id', $userId)
                    ->update([$columnName => $filePath]);
                $updates[] = "enrollments (user_id: {$userId})";
            } catch (\Exception $e) {
                Log::error("Failed to update enrollments table: " . $e->getMessage());
            }
        }
        
        Log::info("File stored in dynamic column", [
            'document_type' => $documentType,
            'column_name' => $columnName,
            'file_path' => $filePath,
            'updated_tables' => $updates
        ]);
        
        return $columnName;
    }
}
