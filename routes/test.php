<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Test route for modules API
Route::get('/test-modules-api/{programId}', function ($programId) {
    try {
        $modules = \App\Models\Module::where('program_id', $programId)
                                     ->where('is_archived', false)
                                     ->orderBy('module_order', 'asc')
                                     ->get(['modules_id', 'module_name', 'module_description', 'program_id']);
        
        // Transform the data to ensure the id field is properly set
        $transformedModules = $modules->map(function ($module) {
            return [
                'id' => $module->modules_id,
                'module_name' => $module->module_name,
                'module_description' => $module->module_description,
                'program_id' => $module->program_id,
            ];
        });
        
        return response()->json([
            'success' => true,
            'modules' => $transformedModules
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading modules: ' . $e->getMessage()
        ], 500);
    }
})->name('test.modules.api');
