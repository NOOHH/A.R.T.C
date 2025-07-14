<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormRequirement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class FormRequirementSyncController extends Controller
{
    /**
     * Show the sync status page
     */
    public function index()
    {
        // Get sync status information
        $orphanedColumns = FormRequirement::getOrphanedColumns();
        $formRequirements = FormRequirement::active()->get();
        
        // Check which form requirements need database columns
        $needsSync = [];
        foreach ($formRequirements as $formRequirement) {
            if ($formRequirement->field_type === 'section') {
                continue;
            }
            
            $regExists = FormRequirement::columnExists($formRequirement->field_name, 'registrations');
            $studExists = FormRequirement::columnExists($formRequirement->field_name, 'students');
            
            if (!$regExists || !$studExists) {
                $needsSync[] = [
                    'field_name' => $formRequirement->field_name,
                    'field_type' => $formRequirement->field_type,
                    'needs_registrations' => !$regExists,
                    'needs_students' => !$studExists
                ];
            }
        }
        
        return view('admin.form-requirements-sync', compact('orphanedColumns', 'needsSync', 'formRequirements'));
    }
    
    /**
     * Perform the sync via AJAX
     */
    public function sync(Request $request)
    {
        try {
            Log::info('Manual form requirements sync initiated by admin');
            
            $result = FormRequirement::syncAllFormRequirementsWithDatabase();
            
            return response()->json([
                'success' => true,
                'message' => 'Form requirements sync completed successfully!',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Form requirements sync failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get sync status via AJAX
     */
    public function status()
    {
        try {
            $orphanedColumns = FormRequirement::getOrphanedColumns();
            $formRequirements = FormRequirement::active()->get();
            
            $needsSync = [];
            foreach ($formRequirements as $formRequirement) {
                if ($formRequirement->field_type === 'section') {
                    continue;
                }
                
                $regExists = FormRequirement::columnExists($formRequirement->field_name, 'registrations');
                $studExists = FormRequirement::columnExists($formRequirement->field_name, 'students');
                
                if (!$regExists || !$studExists) {
                    $needsSync[] = [
                        'field_name' => $formRequirement->field_name,
                        'field_type' => $formRequirement->field_type,
                        'needs_registrations' => !$regExists,
                        'needs_students' => !$studExists
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'orphaned_columns' => $orphanedColumns,
                    'needs_sync' => $needsSync,
                    'total_form_requirements' => $formRequirements->count(),
                    'needs_sync_count' => count($needsSync)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting form requirements sync status', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting sync status: ' . $e->getMessage()
            ], 500);
        }
    }
}
