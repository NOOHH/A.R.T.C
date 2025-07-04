<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModuleController extends Controller
{
    /**
     * Update module sort order
     */
    public function updateSortOrder(Request $request)
    {
        try {
            $moduleIds = $request->input('module_ids', []);
            
            if (empty($moduleIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No module IDs provided'
                ], 400);
            }

            // Update each module's sort order
            foreach ($moduleIds as $index => $moduleId) {
                Module::where('modules_id', $moduleId)
                    ->update(['module_order' => $index + 1]);
            }

            Log::info('Module sort order updated', [
                'module_ids' => $moduleIds,
                'admin_id' => auth()->user()->id ?? 'unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Module order updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating module sort order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating module order'
            ], 500);
        }
    }

    /**
     * Get modules ordered by sort order
     */
    public function getOrderedModules(Request $request)
    {
        try {
            $programId = $request->input('program_id');
            
            $query = Module::query()->ordered();
            
            if ($programId) {
                $query->where('program_id', $programId);
            }

            $modules = $query->get();

            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting ordered modules', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching modules'
            ], 500);
        }
    }
}
