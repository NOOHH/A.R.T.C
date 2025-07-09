<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\FormRequirement;

class FormRequirementController extends Controller
{
    /**
     * Display the form requirements management page
     */
    public function index()
    {
        $requirements = FormRequirement::orderBy('sort_order')->get();
        return view('admin.form-requirements', compact('requirements'));
    }

    /**
     * Store a new form requirement
     */
    public function store(Request $request)
    {
        $request->validate([
            'field_name' => 'required|string|max:255|unique:form_requirements,field_name',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,email,tel,date,file,select,textarea,checkbox,radio,number,section,module_selection',
            'program_type' => 'required|string|in:both,modular,full',
            'section_name' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
        ]);

        $maxSortOrder = FormRequirement::max('sort_order') ?? 0;

        // Create the form requirement
        $requirement = FormRequirement::create([
            'field_name' => $request->field_name,
            'field_label' => $request->field_label,
            'field_type' => $request->field_type,
            'program_type' => $request->program_type,
            'section_name' => $request->section_name,
            'is_required' => $request->boolean('is_required'),
            'is_active' => true,
            'sort_order' => $maxSortOrder + 1,
        ]);

        // Automatically create database column for the new field
        $columnCreated = FormRequirement::createDatabaseColumn($request->field_name, $request->field_type);
        
        if (!$columnCreated) {
            // If column creation failed, still return success but log the issue
            Log::warning("Form requirement created but database column creation failed for field: {$request->field_name}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Form requirement added successfully' . ($columnCreated ? ' with database column' : ' (column creation failed)'),
            'requirement' => $requirement
        ]);
    }

    /**
     * Archive a form requirement
     */
    public function archive(Request $request)
    {
        $request->validate([
            'field_name' => 'required|string|exists:form_requirements,field_name'
        ]);

        $updated = FormRequirement::archiveField($request->field_name);
        
        // Also archive the database column
        $columnArchived = FormRequirement::archiveDatabaseColumn($request->field_name);

        return response()->json([
            'success' => $updated > 0,
            'message' => $updated > 0 ? 'Field archived successfully' . ($columnArchived ? ' with database column' : ' (column archiving failed)') : 'Field not found'
        ]);
    }

    /**
     * Restore a form requirement
     */
    public function restore(Request $request)
    {
        $request->validate([
            'field_name' => 'required|string|exists:form_requirements,field_name'
        ]);

        $updated = FormRequirement::restoreField($request->field_name);
        
        // Also restore the database column
        $columnRestored = FormRequirement::restoreDatabaseColumn($request->field_name);

        return response()->json([
            'success' => $updated > 0,
            'message' => $updated > 0 ? 'Field restored successfully' . ($columnRestored ? ' with database column' : ' (column restoration failed)') : 'Field not found'
        ]);
    }

    /**
     * Update form requirement
     */
    public function update(Request $request, $id)
    {
        $requirement = FormRequirement::findOrFail($id);

        $request->validate([
            'field_name' => 'required|string|max:255|unique:form_requirements,field_name,' . $id,
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,email,tel,date,file,select,textarea,checkbox,radio,number,section,module_selection',
            'program_type' => 'required|string|in:both,modular,full',
            'section_name' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
        ]);

        $requirement->update([
            'field_name' => $request->field_name,
            'field_label' => $request->field_label,
            'field_type' => $request->field_type,
            'program_type' => $request->program_type,
            'section_name' => $request->section_name,
            'is_required' => $request->boolean('is_required'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Form requirement updated successfully',
            'requirement' => $requirement
        ]);
    }

    /**
     * Delete a form requirement
     */
    public function destroy($id)
    {
        $requirement = FormRequirement::findOrFail($id);
        $requirement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form requirement deleted successfully'
        ]);
    }
}
