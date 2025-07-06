<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseTestController extends Controller
{
    public function checkStudentsSchema()
    {
        try {
            // Get the columns in the students table
            $columns = Schema::getColumnListing('students');
            
            // Also get detailed column information
            $columnDetails = DB::select("DESCRIBE students");
            
            return response()->json([
                'success' => true,
                'columns' => $columns,
                'details' => $columnDetails,
                'has_program_id' => in_array('program_id', $columns),
                'has_package_id' => in_array('package_id', $columns),
                'has_plan_id' => in_array('plan_id', $columns)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function testStudentInsert()
    {
        try {
            $testData = [
                'student_id' => 'TEST-' . date('Y-m-d-H-i-s'),
                'user_id' => 1,
                'firstname' => 'Test',
                'lastname' => 'Student',
                'student_school' => 'Test School',
                'street_address' => 'Test Address',
                'state_province' => 'Test State',
                'city' => 'Test City',
                'zipcode' => '12345',
                'contact_number' => '1234567890',
                'emergency_contact_number' => '0987654321',
                'Start_Date' => date('Y-m-d'),
                'email' => 'test@test.com',
                'program_id' => 1,
                'package_id' => 1,
                'plan_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $inserted = DB::table('students')->insert($testData);
            
            return response()->json([
                'success' => $inserted,
                'message' => 'Student inserted successfully',
                'data' => $testData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }
    }
    
    public function addMissingColumns()
    {
        try {
            // Check current columns
            $currentColumns = Schema::getColumnListing('students');
            $missingColumns = [];
            
            // Define required columns
            $requiredColumns = [
                'program_id' => 'INT NULL',
                'package_id' => 'INT NULL', 
                'plan_id' => 'INT NULL',
                'program_name' => 'VARCHAR(100) NULL',
                'package_name' => 'VARCHAR(100) NULL',
                'plan_name' => 'VARCHAR(50) NULL'
            ];
            
            foreach ($requiredColumns as $column => $definition) {
                if (!in_array($column, $currentColumns)) {
                    $sql = "ALTER TABLE students ADD COLUMN {$column} {$definition}";
                    DB::statement($sql);
                    $missingColumns[] = $column;
                }
            }
            
            // Get updated columns
            $updatedColumns = Schema::getColumnListing('students');
            
            return response()->json([
                'success' => true,
                'message' => 'Missing columns added successfully',
                'added_columns' => $missingColumns,
                'current_columns' => $updatedColumns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function removeColumnsFromStudents()
    {
        try {
            $columnsToRemove = ['program_id', 'package_id', 'plan_id', 'program_name', 'package_name', 'plan_name'];
            $removedColumns = [];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('students', $column)) {
                    DB::statement("ALTER TABLE students DROP COLUMN {$column}");
                    $removedColumns[] = $column;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Columns removed successfully',
                'removed_columns' => $removedColumns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
