<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminModuleController extends Controller
{
    /**
     * Display a listing of modules.
     */
    public function index(Request $request)
    {
        Log::info('AdminModuleController::index called', [
            'program_id' => $request->input('program_id'),
            'request_all' => $request->all()
        ]);
        
        $programs = Program::all();
        $modules = collect();
        
        if ($request->has('program_id') && $request->program_id != '') {
            $modules = Module::where('program_id', $request->program_id)
                           ->where('is_archived', false)
                           ->with('program')
                           ->orderBy('module_order', 'asc') // Sort by order if available
                           ->orderBy('created_at', 'asc')   // Otherwise by creation date
                           ->get();
                           
            // Group modules by content type for better organization
            $modulesByType = $modules->groupBy('content_type');
        }

        return view('admin.admin-modules.admin-modules', compact('programs', 'modules', 'selectedProgram', 'modulesByType'));
    }

    /**
     * Show the form for creating a new module.
     */
    public function create()
    {
        $programs = Program::all();
        return view('admin.admin-modules.create', compact('programs'));
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(Request $request)
    {
        // Add debugging
        Log::info('AdminModuleController::store called', [
            'request_data' => $request->all(),
            'files' => $request->hasFile('attachment') ? 'yes' : 'no'
        ]);
        
        try {
            $request->validate([
                'module_name' => 'required|string|max:255',
                'module_description' => 'nullable|string',
                'program_id' => 'required|exists:programs,program_id',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg|max:10240',
                'content_type' => 'nullable|string|in:module,assignment,quiz,test,link,file',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in AdminModuleController::store', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            try {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('modules', $filename, 'public');
            } catch (\Exception $e) {
                Log::error('File upload failed in AdminModuleController::store', ['error' => $e->getMessage()]);
                return back()->with('error', 'File upload failed: ' . $e->getMessage())->withInput();
            }
        }

        // Prepare content-specific data
        $contentData = [];
        $contentType = $request->input('content_type', 'module');

        // Extract content-specific fields based on type
        switch ($contentType) {
            case 'assignment':
                $contentData = [
                    'assignment_title' => $request->input('assignment_title'),
                    'assignment_instructions' => $request->input('assignment_instructions'),
                    'due_date' => $request->input('due_date'),
                    'max_points' => $request->input('max_points'),
                ];
                break;
            case 'quiz':
                $contentData = [
                    'quiz_title' => $request->input('quiz_title'),
                    'quiz_description' => $request->input('quiz_description'),
                    'time_limit' => $request->input('time_limit'),
                    'question_count' => $request->input('question_count'),
                ];
                break;
            case 'test':
                $contentData = [
                    'test_title' => $request->input('test_title'),
                    'test_description' => $request->input('test_description'),
                    'test_date' => $request->input('test_date'),
                    'duration' => $request->input('duration'),
                    'total_marks' => $request->input('total_marks'),
                ];
                break;
            case 'link':
                $contentData = [
                    'external_url' => $request->input('external_url'),
                    'link_title' => $request->input('link_title'),
                    'link_description' => $request->input('link_description'),
                    'link_type' => $request->input('link_type', 'other'),
                ];
                break;
            case 'file':
                // File uploads are handled through the attachment field
                $contentData = [
                    'file_description' => $request->input('module_description'),
                ];
                break;
        }

        try {
            $module = Module::create([
                'module_name' => $request->module_name,
                'module_description' => $request->module_description,
                'program_id' => $request->program_id,
                'attachment' => $attachmentPath,
                'content_type' => $contentType,
                'content_data' => $contentData,
                'is_archived' => false,
            ]);
            
            Log::info('Module created successfully', ['module_id' => $module->modules_id]);

            return redirect()->route('admin.modules.index', ['program_id' => $request->program_id])
                            ->with('success', ucfirst($contentType) . ' created successfully!');
        } catch (\Exception $e) {
            Log::error('Module creation failed in AdminModuleController::store', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Failed to create module: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified module.
     */
    public function show(Module $module)
    {
        return view('admin.admin-modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module)
    {
        $programs = Program::all();
        return view('admin.admin-modules.edit', compact('module', 'programs'));
    }

    /**
     * Update the specified module in storage.
     */
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'module_name' => 'required|string|max:255',
            'module_description' => 'nullable|string',
            'program_id' => 'required|exists:programs,program_id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
        ]);

        $updateData = [
            'module_name' => $request->module_name,
            'module_description' => $request->module_description,
            'program_id' => $request->program_id,
        ];

        // Handle file upload for update
        if ($request->hasFile('attachment')) {
            // Delete old attachment if it exists
            if ($module->attachment && Storage::disk('public')->exists($module->attachment)) {
                Storage::disk('public')->delete($module->attachment);
            }

            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('modules', $filename, 'public');
            $updateData['attachment'] = $attachmentPath;
        }

        $module->update($updateData);

        return redirect()->route('admin.modules.index', ['program_id' => $request->program_id])
                        ->with('success', 'Module updated successfully!');
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Module $module)
    {
        $programId = $module->program_id;
        
        // Delete attachment file if it exists
        if ($module->attachment && Storage::disk('public')->exists($module->attachment)) {
            Storage::disk('public')->delete($module->attachment);
        }
        
        $module->delete();

        return redirect()->route('admin.modules.index', ['program_id' => $programId])
                        ->with('success', 'Module deleted successfully!');
    }

    /**
     * Store multiple modules in storage (batch upload).
     */
    public function batchStore(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'modules' => 'required|array|min:1',
            'modules.*.module_name' => 'required|string|max:255',
            'modules.*.module_description' => 'nullable|string',
            'modules.*.attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg|max:10240',
            'modules.*.content_type' => 'required|in:module,assignment,quiz,test,link',
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($request->modules as $index => $moduleData) {
            try {
                $attachmentPath = null;
                
                // Handle file upload if present
                if ($request->hasFile("modules.{$index}.attachment")) {
                    $file = $request->file("modules.{$index}.attachment");
                    $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $attachmentPath = $file->storeAs('modules', $filename, 'public');
                }

                // Prepare content-specific data
                $contentData = [];
                $contentType = $moduleData['content_type'];
                $allData = $request->input("modules.{$index}");
                
                switch ($contentType) {
                    case 'assignment':
                        $contentData = [
                            'assignment_title' => $allData['assignment_title'] ?? null,
                            'assignment_instructions' => $allData['assignment_instructions'] ?? null,
                            'due_date' => $allData['due_date'] ?? null,
                            'max_points' => $allData['max_points'] ?? null,
                        ];
                        break;
                    case 'quiz':
                        $contentData = [
                            'quiz_title' => $allData['quiz_title'] ?? null,
                            'quiz_description' => $allData['quiz_description'] ?? null,
                            'time_limit' => $allData['time_limit'] ?? null,
                            'question_count' => $allData['question_count'] ?? null,
                        ];
                        break;
                    case 'test':
                        $contentData = [
                            'test_title' => $allData['test_title'] ?? null,
                            'test_description' => $allData['test_description'] ?? null,
                            'test_date' => $allData['test_date'] ?? null,
                            'duration' => $allData['duration'] ?? null,
                            'total_marks' => $allData['total_marks'] ?? null,
                        ];
                        break;
                    case 'link':
                        $contentData = [
                            'external_url' => $allData['external_url'] ?? null,
                            'link_title' => $allData['link_title'] ?? null,
                            'link_description' => $allData['link_description'] ?? null,
                            'link_type' => $allData['link_type'] ?? 'other',
                        ];
                        break;
                }

                Module::create([
                    'module_name' => $moduleData['module_name'],
                    'module_description' => $moduleData['module_description'] ?? null,
                    'program_id' => $request->program_id,
                    'attachment' => $attachmentPath,
                    'content_type' => $contentType,
                    'content_data' => $contentData,
                    'is_archived' => false,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to create module '{$moduleData['module_name']}': " . $e->getMessage();
            }
        }

        $message = "Successfully created {$successCount} module(s).";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()->route('admin.modules.index', ['program_id' => $request->program_id])
                        ->with('success', $message);
    }

    /**
     * Toggle archive status of a module.
     */
    public function toggleArchive(Request $request, Module $module)
    {
        $request->validate([
            'is_archived' => 'required|boolean'
        ]);

        $module->update(['is_archived' => $request->is_archived]);

        $status = $request->is_archived ? 'archived' : 'unarchived';
        
        return response()->json([
            'success' => true,
            'message' => "Module {$status} successfully!"
        ]);
    }

    /**
     * Batch delete modules.
     */
    public function batchDelete(Request $request)
    {
        $request->validate([
            'module_ids' => 'required|array|min:1',
            'module_ids.*' => 'exists:modules,modules_id'
        ]);

        $modules = Module::whereIn('modules_id', $request->module_ids)->get();
        
        try {
            foreach ($modules as $module) {
                // Delete file if exists
                if ($module->attachment && Storage::disk('public')->exists($module->attachment)) {
                    Storage::disk('public')->delete($module->attachment);
                }
                $module->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => count($modules) . ' modules deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show archived modules.
     */
    public function archived(Request $request)
    {
        $programs = Program::all();
        $modules = collect();
        
        if ($request->has('program_id') && $request->program_id != '') {
            $modules = Module::where('program_id', $request->program_id)
                           ->where('is_archived', true)
                           ->with('program')
                           ->orderBy('updated_at', 'desc')
                           ->get();
        }
        
        return view('admin.admin-modules.admin-modules-archived', compact('programs', 'modules'));
    }
}
