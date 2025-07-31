<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/test-db-comprehensive', function () {
    try {
        // Check which tables exist
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(function($table) {
            return array_values((array)$table)[0];
        }, $tables);
        
        // Check specific table structures
        $structures = [];
        
        // Check student_progress table
        if (in_array('student_progress', $tableNames)) {
            $structures['student_progress'] = DB::select('DESCRIBE student_progress');
        }
        
        // Check content_completions table
        if (in_array('content_completions', $tableNames)) {
            $structures['content_completions'] = DB::select('DESCRIBE content_completions');
        }
        
        // Check assignment_submissions table
        if (in_array('assignments_submissions', $tableNames)) {
            $structures['assignments_submissions'] = DB::select('DESCRIBE assignments_submissions');
        }
        
        // Check content_items table
        if (in_array('content_items', $tableNames)) {
            $structures['content_items'] = DB::select('DESCRIBE content_items');
        }
        
        // Check courses table
        if (in_array('courses', $tableNames)) {
            $structures['courses'] = DB::select('DESCRIBE courses');
        }
        
        // Check for sample data
        $sampleData = [];
        
        if (in_array('content_items', $tableNames)) {
            $sampleData['content_items'] = DB::table('content_items')->take(3)->get();
        }
        
        if (in_array('courses', $tableNames)) {
            $sampleData['courses'] = DB::table('courses')->take(3)->get();
        }
        
        return response()->json([
            'success' => true,
            'total_tables' => count($tableNames),
            'tables' => $tableNames,
            'table_structures' => $structures,
            'sample_data' => $sampleData
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});
