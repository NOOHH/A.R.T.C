<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EducationLevelController extends Controller
{
    /**
     * Get all education levels
     */
    public function index()
    {
        try {
            $educationLevels = EducationLevel::orderBy('level_name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $educationLevels
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading education levels', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load education levels'
            ], 500);
        }
    }

    /**
     * Store a new education level
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'level_name' => 'required|string|max:255|unique:education_levels,level_name',
                'level_description' => 'nullable|string',
                'level_order' => 'nullable|integer|min:1',
                'available_full_plan' => 'boolean',
                'available_modular_plan' => 'boolean',
                'file_requirements' => 'nullable', // Accept any format
                'is_active' => 'nullable|boolean'
            ]);

            // Handle file requirements - convert to string if it's an array
            if (isset($validated['file_requirements'])) {
                if (is_array($validated['file_requirements'])) {
                    $validated['file_requirements'] = json_encode($validated['file_requirements']);
                } elseif ($validated['file_requirements']) {
                    // If it's a string, validate it's valid JSON
                    $fileRequirements = json_decode($validated['file_requirements'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Invalid file requirements format'
                        ], 400);
                    }
                }
            }

            // Set default order if not provided
            if (!isset($validated['level_order'])) {
                $validated['level_order'] = EducationLevel::max('level_order') + 1;
            }

            // Set default is_active if not provided
            if (!isset($validated['is_active'])) {
                $validated['is_active'] = true;
            }

            $educationLevel = EducationLevel::create($validated);

            Log::info('Education level created', [
                'education_level_id' => $educationLevel->id,
                'level_name' => $educationLevel->level_name,
                'created_by' => session('user_id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Education level created successfully',
                'educationLevel' => $educationLevel
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating education level', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to create education level'
            ], 500);
        }
    }

    /**
     * Update an education level
     */
    public function update(Request $request, $id)
    {
        try {
            $educationLevel = EducationLevel::findOrFail($id);

            $validated = $request->validate([
                'level_name' => 'required|string|max:255|unique:education_levels,level_name,' . $id,
                'level_description' => 'nullable|string',
                'level_order' => 'nullable|integer|min:1',
                'available_full_plan' => 'boolean',
                'available_modular_plan' => 'boolean',
                'file_requirements' => 'nullable', // Accept any format
                'is_active' => 'nullable|boolean'
            ]);

            // Handle file requirements - convert to string if it's an array
            if (isset($validated['file_requirements'])) {
                if (is_array($validated['file_requirements'])) {
                    $validated['file_requirements'] = json_encode($validated['file_requirements']);
                } elseif ($validated['file_requirements']) {
                    // If it's a string, validate it's valid JSON
                    $fileRequirements = json_decode($validated['file_requirements'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Invalid file requirements format'
                        ], 400);
                    }
                }
            }

            // Set default is_active if not provided
            if (!isset($validated['is_active'])) {
                $validated['is_active'] = true;
            }

            $educationLevel->update($validated);

            Log::info('Education level updated', [
                'education_level_id' => $educationLevel->id,
                'level_name' => $educationLevel->level_name,
                'updated_by' => session('user_id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Education level updated successfully',
                'educationLevel' => $educationLevel
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating education level', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'education_level_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update education level'
            ], 500);
        }
    }

    /**
     * Delete an education level
     */
    public function destroy($id)
    {
        try {
            $educationLevel = EducationLevel::findOrFail($id);
            $levelName = $educationLevel->level_name;
            
            $educationLevel->delete();

            Log::info('Education level deleted', [
                'education_level_id' => $id,
                'level_name' => $levelName,
                'deleted_by' => session('user_id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Education level deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting education level', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'education_level_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to delete education level'
            ], 500);
        }
    }

    /**
     * Get education levels for a specific plan
     */
    public function getForPlan($plan = null)
    {
        try {
            if ($plan) {
                $educationLevels = EducationLevel::forPlan($plan)->get();
            } else {
                $educationLevels = EducationLevel::all();
            }
            
            return response()->json([
                'success' => true,
                'data' => $educationLevels
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get education levels for plan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get education levels',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
