<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Program;
use App\Models\Course;
use App\Models\ContentItem;
use App\Models\StudentBatch;
use App\Models\AdminSetting;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfessorModuleController extends Controller
{
    /**
     * Check if professor module management is enabled and if professor is whitelisted
     */
    private function checkModulePermission()
    {
        try {
            // Use session-based authentication instead of Auth guard
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: Not authenticated as professor via session');
                abort(403, 'You are not authenticated as a professor.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Professor authenticated via session', ['professor_id' => $professorId]);

            // Check if feature is globally enabled
            $isEnabled = AdminSetting::getValue('professor_module_management_enabled', '0') === '1';
            Log::info('ProfessorModuleController: Module management enabled check', ['enabled' => $isEnabled]);

            // Check whitelist
            $whitelist = AdminSetting::getValue('professor_module_management_whitelist', '');
            Log::info('ProfessorModuleController: Whitelist check', ['whitelist' => $whitelist]);

            // If feature is globally enabled
            if ($isEnabled) {
                // If whitelist is empty, allow all professors
                if (empty($whitelist) || trim($whitelist) === '') {
                    Log::info('ProfessorModuleController: Feature enabled, whitelist empty - allowing all professors', ['professor_id' => $professorId]);
                    return;
                }
                
                // If whitelist has IDs, check if professor is in whitelist
                $whitelistedIds = array_filter(array_map('trim', explode(',', $whitelist)), function($id) {
                    return !empty($id) && $id !== '';
                });
                
                if (!empty($whitelistedIds) && !in_array((string)$professorId, $whitelistedIds)) {
                    Log::warning('ProfessorModuleController: Feature enabled but professor not in whitelist', [
                        'professor_id' => $professorId,
                        'whitelist' => $whitelistedIds
                    ]);
                    abort(403, 'You are not authorized to manage modules.');
                }
                
                Log::info('ProfessorModuleController: Feature enabled and professor in whitelist', ['professor_id' => $professorId]);
                return;
            }
            
            // If feature is globally disabled, check if professor is specifically whitelisted
            if (!empty($whitelist) && trim($whitelist) !== '') {
                $whitelistedIds = array_filter(array_map('trim', explode(',', $whitelist)), function($id) {
                    return !empty($id) && $id !== '';
                });
                
                if (!empty($whitelistedIds) && in_array((string)$professorId, $whitelistedIds)) {
                    Log::info('ProfessorModuleController: Feature disabled but professor whitelisted - allowing access', ['professor_id' => $professorId]);
                    return;
                }
            }
            
            // Feature is disabled and professor is not whitelisted
            Log::warning('ProfessorModuleController: Module management not enabled and professor not whitelisted');
            abort(403, 'Module management is not enabled for professors.');

        } catch (\Exception $e) {
            Log::error('ProfessorModuleController checkModulePermission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Display a listing of modules for assigned programs only.
     */
    public function index(Request $request)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in index');
                return redirect()->route('login')->with('error', 'Please log in as a professor to access this page.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in index', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in index', ['professor_id' => $professorId]);
                return redirect()->route('login')->with('error', 'Professor not found.');
            }
            
            Log::info('ProfessorModuleController: Professor found in index', [
                'professor_id' => $professor->professor_id,
                'professor_name' => $professor->professor_name
            ]);
            
            // Get only programs assigned to this professor
            $programs = $professor->assignedPrograms()->get();
            $modules = collect();
            
            if ($request->has('program_id') && $request->program_id != '') {
                // Check if professor is assigned to this program
                $assignedProgram = $programs->where('program_id', $request->program_id)->first();
                if (!$assignedProgram) {
                    return redirect()->route('professor.modules.index')
                                   ->with('error', 'You are not assigned to this program.');
                }
                
                $modules = Module::where('program_id', $request->program_id)
                               ->where('is_archived', false)
                               ->with(['program', 'batch'])
                               ->orderBy('module_order', 'asc')
                               ->orderBy('created_at', 'desc')
                               ->get();
            }

            return view('professor.modules.index', compact('programs', 'modules'));
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('error', 'An error occurred while loading the page.');
        }
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(Request $request)
    {
        $this->checkModulePermission();

        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'module_name' => 'required|string|max:255',
            'module_description' => 'nullable|string',
            'batch_id' => 'nullable|exists:student_batches,batch_id',
            'learning_mode' => 'required|in:synchronous,asynchronous'
        ]);

        $professor = Auth::guard('professor')->user();
        
        // Check if professor is assigned to this program
        $assignedProgram = $professor->assignedPrograms()->where('program_id', $request->program_id)->first();
        if (!$assignedProgram) {
            return response()->json(['error' => 'You are not assigned to this program.'], 403);
        }

        // Get the next module order
        $maxOrder = Module::where('program_id', $request->program_id)->max('module_order') ?? 0;

        $module = Module::create([
            'program_id' => $request->program_id,
            'module_name' => $request->module_name,
            'module_description' => $request->module_description,
            'batch_id' => $request->batch_id,
            'learning_mode' => $request->learning_mode,
            'module_order' => $maxOrder + 1,
            'created_by' => 'professor', // Track who created it
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Module created successfully.',
            'module' => $module->load(['program', 'batch'])
        ]);
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module)
    {
        try {
            $this->checkModulePermission();
            
            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in edit');
                return redirect()->route('login')->with('error', 'Please log in as a professor to access this page.');
            }
            
            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in edit', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in edit', ['professor_id' => $professorId]);
                return redirect()->route('login')->with('error', 'Professor not found.');
            }
            
            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                Log::warning('ProfessorModuleController: Professor not assigned to program in edit', [
                    'professor_id' => $professorId, 
                    'program_id' => $module->program_id
                ]);
                return redirect()->route('professor.modules.index')
                               ->with('error', 'You are not assigned to this program.');
            }
            
            // Get programs assigned to this professor for the dropdown
            $programs = $professor->assignedPrograms()->get();
            
            // Get batches for the current program
            $batches = StudentBatch::where('program_id', $module->program_id)->get();
            
            return response()->json([
                'success' => true,
                'module' => $module->load(['program', 'batch']),
                'programs' => $programs,
                'batches' => $batches
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController edit error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'module_id' => $module->modules_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified module in storage.
     */
    public function update(Request $request, Module $module)
    {
        $this->checkModulePermission();

        $professor = Auth::guard('professor')->user();
        
        // Check if professor is assigned to this program
        $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
        if (!$assignedProgram) {
            return response()->json(['error' => 'You are not assigned to this program.'], 403);
        }

        $request->validate([
            'module_name' => 'required|string|max:255',
            'module_description' => 'nullable|string',
            'batch_id' => 'nullable|exists:student_batches,batch_id',
            'learning_mode' => 'required|in:synchronous,asynchronous'
        ]);

        $module->update([
            'module_name' => $request->module_name,
            'module_description' => $request->module_description,
            'batch_id' => $request->batch_id,
            'learning_mode' => $request->learning_mode,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Module updated successfully.',
            'module' => $module->load(['program', 'batch'])
        ]);
    }

    /**
     * Add content to a module.
     */
    public function addContent(Request $request)
    {
        $this->checkModulePermission();

        $request->validate([
            'module_id' => 'required|exists:modules,module_id',
            'content_type' => 'required|in:lesson,video,assignment,quiz,test,link',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string',
            'external_link' => 'nullable|url',
            'order_index' => 'nullable|integer|min:1'
        ]);

        $module = Module::findOrFail($request->module_id);
        $professor = Auth::guard('professor')->user();
        
        // Check if professor is assigned to this program
        $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
        if (!$assignedProgram) {
            return response()->json(['error' => 'You are not assigned to this program.'], 403);
        }

        // Get the next order index if not provided
        if (!$request->order_index) {
            $maxOrder = ContentItem::where('module_id', $request->module_id)->max('order_index') ?? 0;
            $orderIndex = $maxOrder + 1;
        } else {
            $orderIndex = $request->order_index;
        }

        $contentItem = ContentItem::create([
            'module_id' => $request->module_id,
            'content_type' => $request->content_type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $request->file_path,
            'external_link' => $request->external_link,
            'order_index' => $orderIndex,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content item added successfully.',
            'content_item' => $contentItem
        ]);
    }

    /**
     * Archive/unarchive a module.
     */
    public function toggleArchive(Module $module)
    {
        $this->checkModulePermission();

        $professor = Auth::guard('professor')->user();
        
        // Check if professor is assigned to this program
        $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
        if (!$assignedProgram) {
            return response()->json(['error' => 'You are not assigned to this program.'], 403);
        }

        $module->update(['is_archived' => !$module->is_archived]);

        return response()->json([
            'success' => true,
            'message' => $module->is_archived ? 'Module archived successfully.' : 'Module restored successfully.',
            'is_archived' => $module->is_archived
        ]);
    }

    /**
     * Get modules by program for AJAX requests.
     */
    public function getModulesByProgram(Request $request)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in getModulesByProgram');
                return response()->json(['error' => 'Authentication failed'], 401);
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in getModulesByProgram', ['professor_id' => $professorId]);
                return response()->json(['error' => 'Professor not found'], 404);
            }
            
            Log::info('ProfessorModuleController: Professor found', [
                'professor_id' => $professor->professor_id,
                'professor_name' => $professor->professor_name
            ]);

            $programId = $request->input('program_id');
            $moduleId = $request->input('module_id');
            
            // If module_id is provided but program_id isn't, look up the program_id
            if ($moduleId && !$programId) {
                $module = Module::find($moduleId);
                if ($module) {
                    $programId = $module->program_id;
                    
                    // Return both the program_id and modules
                    $response = [
                        'success' => true,
                        'program_id' => $programId
                    ];
                    
                    return response()->json($response);
                } else {
                    return response()->json(['error' => 'Module not found'], 404);
                }
            }
            
            if (!$programId) {
                return response()->json(['error' => 'Program ID is required'], 400);
            }

            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('professor_program.program_id', $programId)->first();
            if (!$assignedProgram) {
                return response()->json(['error' => 'You are not assigned to this program.'], 403);
            }

            $modules = Module::where('program_id', $programId)
                            ->where('is_archived', false)
                            ->with(['program', 'batch'])
                            ->orderBy('module_order', 'asc')
                            ->get();

            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController getModulesByProgram error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'program_id' => $request->input('program_id'),
                'module_id' => $request->input('module_id')
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get batches for a specific program.
     */
    public function getBatchesByProgram(Request $request)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in getBatchesByProgram');
                return response()->json(['error' => 'Authentication failed'], 401);
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in getBatchesByProgram', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in getBatchesByProgram', ['professor_id' => $professorId]);
                return response()->json(['error' => 'Professor not found'], 404);
            }
            
            Log::info('ProfessorModuleController: Professor found in getBatchesByProgram', [
                'professor_id' => $professor->professor_id,
                'professor_name' => $professor->professor_name
            ]);

            $programId = $request->input('program_id');
            if (!$programId) {
                return response()->json(['error' => 'Program ID is required'], 400);
            }

            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('professor_program.program_id', $programId)->first();
            if (!$assignedProgram) {
                return response()->json(['error' => 'You are not assigned to this program.'], 403);
            }

            $batches = StudentBatch::where('program_id', $programId)->get();
            return response()->json($batches);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController getBatchesByProgram error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'program_id' => $request->input('program_id')
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get courses for a specific program.
     */
    public function getCoursesByProgram(Request $request)
    {
        $this->checkModulePermission();

        $professor = Auth::guard('professor')->user();
        $programId = $request->input('program_id');
        
        // Check if professor is assigned to this program
        $assignedProgram = $professor->assignedPrograms()->where('program_id', $programId)->first();
        if (!$assignedProgram) {
            return response()->json(['error' => 'You are not assigned to this program.'], 403);
        }

        $courses = Course::where('program_id', $programId)->get();
        return response()->json($courses);
    }

    /**
     * View archived modules.
     */
    public function archived(Request $request)
    {
        try {
            $this->checkModulePermission();
            
            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in archived');
                return redirect()->route('login')->with('error', 'Please log in as a professor to access this page.');
            }
            
            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in archived', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in archived', ['professor_id' => $professorId]);
                return redirect()->route('login')->with('error', 'Professor not found.');
            }
            
            $programs = $professor->assignedPrograms()->get();
            $modules = collect();
        
        if ($request->has('program_id') && $request->program_id != '') {
            // Check if professor is assigned to this program
            $assignedProgram = $programs->where('program_id', $request->program_id)->first();
            if (!$assignedProgram) {
                return redirect()->route('professor.modules.archived')
                               ->with('error', 'You are not assigned to this program.');
            }
            
            $modules = Module::where('program_id', $request->program_id)
                           ->where('is_archived', true)
                           ->with(['program', 'batch'])
                           ->orderBy('updated_at', 'desc')
                           ->get();
        }

        return view('professor.modules.archived', compact('programs', 'modules'));
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController archived error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('professor.dashboard')->with('error', 'An error occurred while loading archived modules.');
        }
    }

    /**
     * Get courses for a specific module (AJAX)
     */
    public function getCoursesByModule($moduleId)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in getCoursesByModule');
                return response()->json(['error' => 'Authentication failed'], 401);
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in getCoursesByModule', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in getCoursesByModule', ['professor_id' => $professorId]);
                return response()->json(['error' => 'Professor not found'], 404);
            }

            // Find the module
            $module = Module::find($moduleId);
            if (!$module) {
                return response()->json(['error' => 'Module not found'], 404);
            }

            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('professor_program.program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json(['error' => 'You are not assigned to this program.'], 403);
            }

            // Get courses for this module
            $courses = Course::where('module_id', $moduleId)
                ->where('is_active', true)
                ->orderBy('subject_order', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController getCoursesByModule error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'module_id' => $moduleId
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get content for a specific module (AJAX)
     */
    public function getModuleContent($moduleId)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in getModuleContent');
                return response()->json(['error' => 'Authentication failed'], 401);
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in getModuleContent', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in getModuleContent', ['professor_id' => $professorId]);
                return response()->json(['error' => 'Professor not found'], 404);
            }

            // Find the module
            $module = Module::find($moduleId);
            if (!$module) {
                return response()->json(['error' => 'Module not found'], 404);
            }

            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('professor_program.program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json(['error' => 'You are not assigned to this program.'], 403);
            }

            // Get courses for this module with their content items
            $courses = Course::where('module_id', $moduleId)
                ->where('is_active', true)
                ->with(['contentItems' => function($query) {
                    $query->where('is_active', true)
                          ->orderBy('content_order', 'asc');
                }])
                ->orderBy('subject_order', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'module' => [
                    'module_id' => $module->modules_id,
                    'module_name' => $module->module_name,
                    'module_description' => $module->module_description,
                    'type' => $module->content_type ?? 'Standard',
                    'module_order' => $module->module_order
                ],
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController getModuleContent error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'module_id' => $moduleId
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get content item details for viewing in the content viewer
     */
    public function getContent($id)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in getContent');
                return response()->json(['error' => 'Authentication failed'], 401);
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in getContent', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in getContent', ['professor_id' => $professorId]);
                return response()->json(['error' => 'Professor not found'], 404);
            }
            
            Log::info('ProfessorModuleController: Professor found in getContent', [
                'professor_id' => $professor->professor_id,
                'professor_name' => $professor->professor_name
            ]);

            $contentItem = ContentItem::findOrFail($id);
            
            // Check if professor has access to this content via the course/module
            $course = $contentItem->course;
            if (!$course) {
                return response()->json(['error' => 'Content not found or no course associated.'], 404);
            }

            $module = $course->module;
            if (!$module) {
                return response()->json(['error' => 'Content not found or no module associated.'], 404);
            }
            
            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json(['error' => 'You are not assigned to this program.'], 403);
            }

            // Parse attachment_path if it's JSON
            $attachmentPath = $contentItem->attachment_path;
            $parsedAttachments = null;
            $attachmentUrls = null;
            
            if ($attachmentPath) {
                $parsedAttachments = json_decode($attachmentPath, true);
                if (is_array($parsedAttachments)) {
                    $attachmentUrls = [];
                    foreach ($parsedAttachments as $path) {
                        $attachmentUrls[] = asset('storage/' . $path);
                    }
                } else {
                    $attachmentUrls = asset('storage/' . $attachmentPath);
                }
            }

            // Parse file names if they're JSON
            $fileNames = null;
            if ($contentItem->file_name) {
                $parsedNames = json_decode($contentItem->file_name, true);
                $fileNames = is_array($parsedNames) ? $parsedNames : $contentItem->file_name;
            }

            return response()->json([
                'success' => true,
                'content_id' => $contentItem->id,
                'content_title' => $contentItem->content_title,
                'content_description' => $contentItem->content_description,
                'content_type' => $contentItem->content_type,
                'content_data' => $contentItem->content_data,
                'content_url' => $contentItem->content_url,
                'attachment_path' => $contentItem->attachment_path,
                'attachment_urls' => $attachmentUrls,
                'file_names' => $fileNames,
                'has_multiple_files' => $contentItem->has_multiple_files,
                'file_size' => $contentItem->file_size,
                'file_mime' => $contentItem->file_mime,
                'course_name' => $course->subject_name,
                'module_name' => $module->module_name,
                'content_html' => $this->formatContentForDisplay($contentItem)
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController getContent error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'content_id' => $id
            ]);
            return response()->json(['error' => 'Content not found.'], 404);
        }
    }

    /**
     * Update content item
     */
    public function updateContent(Request $request, $id)
    {
        $this->checkModulePermission();

        try {
            $contentItem = ContentItem::findOrFail($id);
            
            $request->validate([
                'content_title' => 'required|string|max:255',
                'content_description' => 'nullable|string',
                'content_type' => 'required|in:PDF,Video,Document,Link,Other,lesson,video,assignment,quiz,test,link',
                'content_file' => 'nullable|file|max:10240', // 10MB max
                'content_url' => 'nullable|url'
            ]);

            $updateData = [
                'content_title' => $request->content_title,
                'content_description' => $request->content_description,
                'content_type' => $request->content_type,
            ];

            // Handle file upload
            if ($request->hasFile('content_file')) {
                $file = $request->file('content_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('content', $fileName, 'public');
                $updateData['content_path'] = $filePath;
            }

            // Handle URL for Link type
            if ($request->content_type === 'Link' && $request->content_url) {
                $updateData['content_url'] = $request->content_url;
            }

            $contentItem->update($updateData);

            // Handle different response types
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Content updated successfully!',
                    'content' => $contentItem
                ]);
            } else {
                return redirect()->route('professor.modules.index')
                    ->with('success', 'Content updated successfully!');
            }
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController: Error in updateContent', [
                'error' => $e->getMessage(),
                'content_id' => $id
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating content.'], 500);
            } else {
                return redirect()->back()
                    ->with('error', 'Error updating content: ' . $e->getMessage())
                    ->withInput();
            }
        }
    }

    /**
     * Delete content item
     */
    public function deleteContent($id)
    {
        $this->checkModulePermission();

        try {
            $contentItem = ContentItem::findOrFail($id);
            
            // Get professor from session instead of auth guard
            $professorId = session('professor_id');
            if (!$professorId) {
                Log::error('ProfessorModuleController: No professor ID found in session for deleteContent');
                return response()->json(['success' => false, 'message' => 'Authentication error.'], 401);
            }
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in deleteContent', ['professor_id' => $professorId]);
                return response()->json(['success' => false, 'message' => 'Professor not found.'], 404);
            }
            
            // Check if professor has access to this content via the course/module
            $course = $contentItem->course;
            if (!$course) {
                return response()->json(['success' => false, 'message' => 'Associated course not found.'], 404);
            }
            
            $module = $course->module;
            if (!$module) {
                return response()->json(['success' => false, 'message' => 'Associated module not found.'], 404);
            }
            
            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                Log::warning('ProfessorModuleController: Professor not assigned to program in deleteContent', [
                    'professor_id' => $professorId, 
                    'program_id' => $module->program_id
                ]);
                return response()->json(['success' => false, 'message' => 'You are not assigned to this program.'], 403);
            }

            // Archive content instead of deleting it
            $contentItem->is_archived = true;
            $contentItem->archived_at = now();
            $contentItem->archived_by_professor_id = $professorId;
            $contentItem->save();

            Log::info('ProfessorModuleController: Content archived successfully', [
                'content_id' => $id,
                'professor_id' => $professorId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content archived successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting content.'], 500);
        }
    }

    /**
     * Format content for display in the content viewer
     */
    private function formatContentForDisplay($contentItem)
    {
        $html = '<div class="content-display">';
        
        if ($contentItem->content_description) {
            $html .= '<div class="content-description mb-3">' . nl2br(e($contentItem->content_description)) . '</div>';
        }

        switch ($contentItem->content_type) {
            case 'video':
                if ($contentItem->content_url) {
                    $html .= '<div class="video-container mb-3">';
                    if (strpos($contentItem->content_url, 'youtube.com') !== false || strpos($contentItem->content_url, 'youtu.be') !== false) {
                        $embedUrl = $this->convertYouTubeToEmbed($contentItem->content_url);
                        $html .= '<iframe width="100%" height="315" src="' . $embedUrl . '" frameborder="0" allowfullscreen></iframe>';
                    } else {
                        $html .= '<video width="100%" controls><source src="' . $contentItem->content_url . '" type="video/mp4">Your browser does not support the video tag.</video>';
                    }
                    $html .= '</div>';
                }
                break;
                
            case 'link':
                if ($contentItem->content_url) {
                    $html .= '<div class="link-container mb-3">';
                    $html .= '<a href="' . $contentItem->content_url . '" target="_blank" class="btn btn-primary"><i class="bi bi-link-45deg"></i> Open Link</a>';
                    $html .= '</div>';
                }
                break;
                
            case 'assignment':
                $data = $contentItem->content_data ?? [];
                $html .= '<div class="assignment-details">';
                if (isset($data['due_date'])) {
                    $html .= '<p><strong>Due Date:</strong> ' . date('M d, Y', strtotime($data['due_date'])) . '</p>';
                }
                if (isset($data['max_points'])) {
                    $html .= '<p><strong>Max Points:</strong> ' . $data['max_points'] . '</p>';
                }
                $html .= '</div>';
                break;
                
            case 'quiz':
                $data = $contentItem->content_data ?? [];
                $html .= '<div class="quiz-details">';
                if (isset($data['time_limit'])) {
                    $html .= '<p><strong>Time Limit:</strong> ' . $data['time_limit'] . ' minutes</p>';
                }
                if (isset($data['question_count'])) {
                    $html .= '<p><strong>Questions:</strong> ' . $data['question_count'] . '</p>';
                }
                $html .= '</div>';
                break;
        }

        if ($contentItem->attachment_path) {
            $html .= '<div class="attachment-container mt-3">';
            
            // Check if attachment_path is a JSON array of paths
            $attachmentPaths = json_decode($contentItem->attachment_path, true);
            
            if (is_array($attachmentPaths)) {
                // Multiple files
                $fileNames = $contentItem->file_name ? json_decode($contentItem->file_name, true) : null;
                
                foreach ($attachmentPaths as $index => $path) {
                    $displayName = (is_array($fileNames) && isset($fileNames[$index])) ? 
                        $fileNames[$index] : "Attachment " . ($index + 1);
                    
                    $html .= '<div class="attachment-item mb-3">';
                    $html .= $this->getFilePreviewHtml($path, $displayName);
                    $html .= '</div>';
                }
            } else {
                // Single file (not JSON)
                $displayName = $contentItem->file_name ?? "Attachment";
                $html .= $this->getFilePreviewHtml($contentItem->attachment_path, $displayName);
            }
            
            $html .= '</div>';
        }

        $html .= '</div>';
        
        return $html;
    }

    /**
     * Convert YouTube URL to embed format
     */
    private function convertYouTubeToEmbed($url)
    {
        // Extract video ID from different YouTube URL formats
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        
        return $url; // Return original if not a YouTube URL
    }
    
    /**
     * Helper function to handle file preview based on file type
     */
    private function getFilePreviewHtml($path, $displayName = 'Attachment')
    {
        $html = '';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            // Image preview
            $html .= '<div class="mb-2"><img src="' . asset('storage/' . $path) . '" class="img-fluid" alt="' . htmlspecialchars($displayName) . '"></div>';
        } elseif ($extension === 'pdf') {
            // PDF preview
            $html .= '<div class="mb-2 pdf-container"><iframe src="' . asset('storage/' . $path) . '" width="100%" height="500px"></iframe></div>';
        } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
            // Video preview
            $html .= '<div class="mb-2 video-container"><video width="100%" controls><source src="' . asset('storage/' . $path) . '" type="video/' . $extension . '">Your browser does not support the video tag.</video></div>';
        } elseif (in_array($extension, ['mp3', 'wav'])) {
            // Audio preview
            $html .= '<div class="mb-2 audio-container"><audio controls><source src="' . asset('storage/' . $path) . '" type="audio/' . $extension . '">Your browser does not support the audio tag.</audio></div>';
        } elseif (in_array($extension, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt'])) {
            // Document - just provide download link, no preview
            $html .= '<div class="mb-2"><i class="bi bi-file-earmark"></i> ' . htmlspecialchars($displayName) . ' (' . $extension . ' file)</div>';
        } else {
            // Other file types - just note the file type
            $html .= '<div class="mb-2"><i class="bi bi-file-earmark"></i> ' . htmlspecialchars($displayName) . ' (.' . $extension . ')</div>';
        }
        
        // Always add download button
        $html .= '<a href="' . asset('storage/' . $path) . '" target="_blank" class="btn btn-outline-secondary mb-2">';
        $html .= '<i class="bi bi-download"></i> Download ' . htmlspecialchars($displayName) . '</a>';
        
        return $html;
    }

    /**
     * Show the course content upload page for professors
     */
    public function showCourseContentUploadPage(Request $request)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in showCourseContentUploadPage');
                abort(403, 'You are not authenticated as a professor.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in showCourseContentUploadPage', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in showCourseContentUploadPage', ['professor_id' => $professorId]);
                abort(404, 'Professor not found');
            }
            
            // Get only programs assigned to this professor
            $programs = $professor->assignedPrograms()->get();
            
            Log::info('ProfessorModuleController: Successfully loaded course content upload page', [
                'professor_id' => $professorId,
                'programs_count' => $programs->count()
            ]);
            
            return view('professor.modules.course-content-upload', [
                'programs' => $programs
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController showCourseContentUploadPage error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_params' => $request->all()
            ]);
            abort(500, 'Internal server error occurred while loading the page.');
        }
    }

    /**
     * Store course content uploaded by professors
     */
    public function courseContentStore(Request $request)
    {
        $this->checkModulePermission();

        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'module_id' => 'required|exists:modules,modules_id',
            'course_id' => 'required|exists:courses,subject_id',
            'content_title' => 'required|string|max:255',
            'content_description' => 'nullable|string',
            'content_type' => 'required|in:lesson,video,assignment,quiz,test,link',
            'content_order' => 'nullable|integer|min:1',
            'content_url' => 'nullable|url',
            'attachment.*' => 'nullable|file|max:102400', // 100MB max per file
            'enable_submission' => 'nullable|boolean',
            'allowed_file_types' => 'nullable|string',
            'max_file_size' => 'nullable|integer|min:1|max:100',
            'submission_instructions' => 'nullable|string',
            'allow_multiple_submissions' => 'nullable|boolean'
        ]);

        // Use session-based authentication
        if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
            Log::error('ProfessorModuleController: No authenticated professor found via session in courseContentStore');
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        $professorId = session('professor_id');
        $professor = Professor::find($professorId);
        if (!$professor) {
            Log::error('ProfessorModuleController: Professor not found in database in courseContentStore', ['professor_id' => $professorId]);
            return response()->json(['error' => 'Professor not found'], 404);
        }
        
        // Check if professor is assigned to this program
        $assignedProgram = $professor->assignedPrograms()->where('program_id', $request->program_id)->first();
        if (!$assignedProgram) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this program.'
            ], 403);
        }

        try {
            $contentItem = new ContentItem();
            $contentItem->module_id = $request->module_id;
            $contentItem->course_id = $request->course_id;
            $contentItem->content_title = $request->content_title;
            $contentItem->content_description = $request->content_description;
            $contentItem->content_type = $request->content_type;
            $contentItem->content_order = $request->content_order ?? 1;
            $contentItem->content_url = $request->content_url;
            
            // Handle file upload
            if ($request->hasFile('attachment')) {
                $attachmentPaths = [];
                $fileNames = [];
                $totalSize = 0;
                $files = $request->file('attachment');
                
                if (!is_array($files)) {
                    $files = [$files]; // Convert single file to array for consistent handling
                }
                
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $originalName);
                        $path = $file->storeAs('content', $fileName, 'public');
                        
                        $attachmentPaths[] = $path;
                        $fileNames[] = $originalName;
                        $totalSize += $file->getSize();
                        
                        Log::info('ProfessorModuleController: File uploaded successfully', [
                            'original_name' => $originalName,
                            'stored_path' => $path,
                            'size' => $file->getSize(),
                            'mime' => $file->getMimeType()
                        ]);
                    } else {
                        Log::error('ProfessorModuleController: File upload failed', [
                            'error' => $file->getError(),
                            'original_name' => $file->getClientOriginalName()
                        ]);
                    }
                }
                
                // Store file paths (always as JSON array for consistency)
                if (count($attachmentPaths) > 0) {
                    $contentItem->attachment_path = json_encode($attachmentPaths);
                    $contentItem->file_name = count($fileNames) > 1 ? json_encode($fileNames) : $fileNames[0];
                    $contentItem->file_size = $totalSize;
                    $contentItem->file_mime = count($attachmentPaths) == 1 ? 
                        $files[0]->getMimeType() : 
                        'application/json';
                    $contentItem->has_multiple_files = count($attachmentPaths) > 1;
                }
            }

            // Handle submission settings
            if ($request->content_type === 'assignment' && $request->enable_submission) {
                $contentItem->enable_submission = true;
                $contentItem->allowed_file_types = $request->allowed_file_types;
                $contentItem->max_file_size = $request->max_file_size ?? 10;
                $contentItem->submission_instructions = $request->submission_instructions;
                $contentItem->allow_multiple_submissions = $request->allow_multiple_submissions ?? false;
            }

            $contentItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Content uploaded successfully!',
                'content_id' => $contentItem->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error storing course content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading content. Please try again.'
            ], 500);
        }
    }

    /**
     * Get content items for a specific course (AJAX)
     */
    public function getCourseContent($courseId)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in getCourseContent');
                return response()->json(['error' => 'Authentication failed'], 401);
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in getCourseContent', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in getCourseContent', ['professor_id' => $professorId]);
                return response()->json(['error' => 'Professor not found'], 404);
            }

            // Find the course
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json(['error' => 'Course not found'], 404);
            }

            // Get the module for this course
            $module = Module::find($course->module_id);
            if (!$module) {
                return response()->json(['error' => 'Module not found'], 404);
            }

            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('programs.program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json(['error' => 'You are not assigned to this program.'], 403);
            }

            // Get content items for this course (exclude archived content)
            $contentItems = ContentItem::where('course_id', $courseId)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->orderBy('content_order', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'course' => [
                    'course_id' => $course->subject_id,
                    'subject_name' => $course->subject_name,
                    'subject_description' => $course->subject_description,
                    'type' => $course->content_type ?? 'Standard'
                ],
                'content' => $contentItems
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController getCourseContent error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'course_id' => $courseId
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Store a new course
     */
    public function storeCourse(Request $request)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in storeCourse');
                return response()->json(['success' => false, 'message' => 'Not authenticated'], 403);
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in storeCourse', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in storeCourse', ['professor_id' => $professorId]);
                return response()->json(['success' => false, 'message' => 'Professor not found'], 404);
            }

            // Validate request
            $request->validate([
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'required|exists:modules,modules_id',
                'subject_name' => 'required|string|max:255',
                'subject_description' => 'nullable|string',
                'subject_price' => 'required|numeric|min:0',
                'is_required' => 'nullable|boolean'
            ]);

            // Check if professor has access to this program
            $hasAccess = $professor->assignedPrograms()
                                  ->where('program_id', $request->program_id)
                                  ->exists();

            if (!$hasAccess) {
                Log::warning('ProfessorModuleController: Professor does not have access to program in storeCourse', [
                    'professor_id' => $professorId,
                    'program_id' => $request->program_id
                ]);
                return response()->json(['success' => false, 'message' => 'Access denied to this program'], 403);
            }

            // Check if module belongs to the specified program
            $module = Module::where('modules_id', $request->module_id)
                           ->where('program_id', $request->program_id)
                           ->first();

            if (!$module) {
                Log::warning('ProfessorModuleController: Module not found or does not belong to program in storeCourse', [
                    'module_id' => $request->module_id,
                    'program_id' => $request->program_id
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid module for this program'], 400);
            }

            // Create the course
            $course = Course::create([
                'module_id' => $request->module_id,
                'subject_name' => $request->subject_name,
                'subject_description' => $request->subject_description,
                'subject_price' => $request->subject_price,
                'is_required' => $request->has('is_required') ? 1 : 0,
                'is_active' => 1,
                'subject_order' => Course::where('module_id', $request->module_id)->max('subject_order') + 1
            ]);

            Log::info('ProfessorModuleController: Successfully created course', [
                'professor_id' => $professorId,
                'course_id' => $course->subject_id,
                'course_name' => $course->subject_name,
                'module_id' => $course->module_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully',
                'course' => [
                    'subject_id' => $course->subject_id,
                    'subject_name' => $course->subject_name,
                    'subject_description' => $course->subject_description,
                    'subject_price' => $course->subject_price,
                    'is_required' => $course->is_required,
                    'is_active' => $course->is_active,
                    'subject_order' => $course->subject_order
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController storeCourse error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Edit course page
     */
    public function editCourse($courseId)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in editCourse');
                return redirect()->route('login')->with('error', 'Please log in as a professor to access this page.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in editCourse', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in editCourse', ['professor_id' => $professorId]);
                return redirect()->route('login')->with('error', 'Professor not found.');
            }
            
            // Get course and check if belongs to a program assigned to this professor
            $course = Course::with('module.program')->find($courseId);
            
            if (!$course) {
                return redirect()->route('professor.modules.index')->with('error', 'Course not found.');
            }
            
            // Check if professor is assigned to the program this course belongs to
            $programId = $course->module->program->program_id;
            $assignedProgram = $professor->assignedPrograms()->where('programs.program_id', $programId)->first();
            
            if (!$assignedProgram) {
                return redirect()->route('professor.modules.index')->with('error', 'You are not assigned to the program this course belongs to.');
            }
            
            $module = $course->module;
            $program = $module->program;
            
            return view('professor.modules.edit-course', compact('course', 'module', 'program'));
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController editCourse error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('professor.modules.index')->with('error', 'An error occurred while loading the course edit page.');
        }
    }

    /**
     * Update course
     */
    public function updateCourse(Request $request, $courseId)
    {
        try {
            $this->checkModulePermission();

            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in updateCourse');
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Authentication failed'], 401);
                }
                return redirect()->route('login')->with('error', 'Please log in as a professor to access this page.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in updateCourse', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in updateCourse', ['professor_id' => $professorId]);
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Professor not found'], 404);
                }
                return redirect()->route('login')->with('error', 'Professor not found.');
            }
            
            // Get course and check if belongs to a program assigned to this professor
            $course = Course::with('module')->find($courseId);
            
            if (!$course) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Course not found'], 404);
                }
                return redirect()->route('professor.modules.index')->with('error', 'Course not found.');
            }
            
            // Get the module for this course
            $module = Module::find($course->module_id);
            if (!$module) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Module not found'], 404);
                }
                return redirect()->route('professor.modules.index')->with('error', 'Module not found.');
            }

            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('programs.program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'You are not assigned to this program.'], 403);
                }
                return redirect()->route('professor.modules.index')->with('error', 'You are not assigned to the program this course belongs to.');
            }
            
            // Validate request
            $request->validate([
                'subject_name' => 'required|string|max:255',
                'subject_description' => 'nullable|string',
                'subject_price' => 'required|numeric|min:0',
                'subject_order' => 'nullable|integer|min:1',
                'is_required' => 'nullable|boolean',
                'is_active' => 'nullable|boolean'
            ]);
            
            // Update the course
            $course->update([
                'subject_name' => $request->subject_name,
                'subject_description' => $request->subject_description,
                'subject_price' => $request->subject_price,
                'subject_order' => $request->subject_order ?? $course->subject_order,
                'is_required' => $request->has('is_required') ? 1 : 0,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            Log::info('ProfessorModuleController: Successfully updated course', [
                'professor_id' => $professorId,
                'course_id' => $course->subject_id,
                'course_name' => $course->subject_name
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course updated successfully!',
                    'course' => $course
                ]);
            } else {
                return redirect()->route('professor.modules.index')->with('success', 'Course updated successfully!');
            }
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController updateCourse error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'course_id' => $courseId
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the course: ' . $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()->with('error', 'An error occurred while updating the course.')->withInput();
            }
        }
    }

    /**
     * Store course content
     */
    public function storeCourseContent(Request $request)
    {
        try {
            $this->checkModulePermission();

            $request->validate([
                'course_id' => 'required|exists:courses,subject_id',
                'content_title' => 'required|string|max:255',
                'content_description' => 'nullable|string',
                'content_type' => 'required|string',
                'attachment.*' => 'nullable|file|max:102400', // 100MB max file size per file
                'external_link' => 'nullable|url',
            ]);

            $course = Course::find($request->course_id);
            if (!$course) {
                Log::error('ProfessorModuleController: Course not found', ['course_id' => $request->course_id]);
                return response()->json(['success' => false, 'message' => 'Course not found'], 404);
            }

            // Prepare content-specific data
            $contentData = [];
            $contentType = $request->content_type;
            switch ($contentType) {
                case 'lesson':
                    $contentData = [
                        'lesson_video_url' => $request->input('content_url'),
                    ];
                    break;
                case 'video':
                    $contentData = [
                        'video_url' => $request->input('content_url'),
                    ];
                    break;
                case 'assignment':
                    $contentData = [
                        'assignment_instructions' => $request->input('assignment_instructions'),
                        'due_date' => $request->input('due_date'),
                        'max_points' => $request->input('max_points', 0),
                    ];
                    break;
                case 'quiz':
                    $contentData = [
                        'quiz_instructions' => $request->input('quiz_instructions'),
                        'time_limit' => $request->input('time_limit', 30),
                        'max_points' => $request->input('max_points', 0),
                    ];
                    break;
                case 'test':
                    $contentData = [
                        'test_instructions' => $request->input('test_instructions'),
                        'test_date' => $request->input('test_date'),
                        'test_duration' => $request->input('test_duration', 60),
                        'total_marks' => $request->input('total_marks', 100),
                    ];
                    break;
                case 'link':
                    $contentData = [
                        'link_url' => $request->input('content_url'),
                    ];
                    break;
            }

            // Create the content item
            $contentItem = new ContentItem();
            $contentItem->course_id = $request->course_id;
            $contentItem->content_title = $request->content_title;
            $contentItem->content_description = $request->content_description;
            $contentItem->content_type = $request->content_type;
            $contentItem->content_data = $contentData;
            $contentItem->content_url = $request->input('content_url');
            
            // Handle multiple file uploads if provided
            if ($request->hasFile('attachment')) {
                $attachmentPaths = [];
                $totalSize = 0;
                $fileNames = [];
                
                foreach ($request->file('attachment') as $file) {
                    if ($file->isValid()) {
                        $uniqueFileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $file->getClientOriginalName());
                        $path = $file->storeAs('content', $uniqueFileName, 'public');
                        
                        $attachmentPaths[] = $path;
                        $totalSize += $file->getSize();
                        $fileNames[] = $file->getClientOriginalName();
                    }
                }
                
                // Store file paths in attachment_path column as JSON for multiple files, or string for single file
                if (count($attachmentPaths) > 0) {
                    // Always store as JSON array for consistency with admin controller
                    $contentItem->attachment_path = json_encode($attachmentPaths);
                    
                    // Store additional file metadata
                    $contentItem->file_name = count($fileNames) == 1 ? $fileNames[0] : json_encode($fileNames);
                    $contentItem->file_size = $totalSize;
                    $contentItem->file_mime = count($attachmentPaths) == 1 ? mime_content_type(storage_path('app/public/' . $attachmentPaths[0])) : 'application/json';
                    $contentItem->has_multiple_files = count($attachmentPaths) > 1;
                }
                
                Log::info('ProfessorModuleController: Files uploaded successfully', [
                    'file_count' => count($attachmentPaths),
                    'total_size' => $totalSize,
                    'attachment_path' => $contentItem->attachment_path
                ]);
            }
            
            // Add external link if provided
            if ($request->filled('external_link')) {
                $contentItem->external_link = $request->external_link;
            }
            
            // Set created by professor ID from session
            $contentItem->created_by_professor_id = session('professor_id');
            
            $contentItem->save();
            
            Log::info('ProfessorModuleController: Course content created successfully', [
                'content_id' => $contentItem->id, 
                'course_id' => $request->course_id
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Content created successfully',
                'content' => $contentItem
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('ProfessorModuleController: Validation error in storeCourseContent', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Validation error', 
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController: Error in storeCourseContent', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while creating content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View content details
     */
    public function viewContent($id)
    {
        try {
            $this->checkModulePermission();
            
            $content = ContentItem::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'content' => $content
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController: Error in viewContent', [
                'error' => $e->getMessage(),
                'content_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while viewing content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show edit content form
     */
    public function editContent($id)
    {
        try {
            $this->checkModulePermission();
            
            $content = ContentItem::findOrFail($id);
            
            return view('professor.modules.edit-content', compact('content'));
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController: Error in editContent', [
                'error' => $e->getMessage(),
                'content_id' => $id
            ]);
            return redirect()->back()->with('error', 'Content not found or access denied.');
        }
    }

    /**
     * Archive content (soft delete)
     */
    public function archiveContent($id)
    {
        try {
            $this->checkModulePermission();
            
            $content = ContentItem::findOrFail($id);
            
            // Add archived_at timestamp or set a flag
            $content->update([
                'archived_at' => now(),
                'is_archived' => true
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Content archived successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController: Error in archiveContent', [
                'error' => $e->getMessage(),
                'content_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while archiving content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive course (soft delete)
     */
    public function archiveCourse($id)
    {
        try {
            $this->checkModulePermission();
            
            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: No authenticated professor found via session in archiveCourse');
                return response()->json(['error' => 'Authentication failed'], 401);
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Session professor_id in archiveCourse', ['professor_id' => $professorId]);
            
            $professor = Professor::find($professorId);
            if (!$professor) {
                Log::error('ProfessorModuleController: Professor not found in database in archiveCourse', ['professor_id' => $professorId]);
                return response()->json(['error' => 'Professor not found'], 404);
            }
            
            $course = Course::findOrFail($id);
            
            // Get the module for this course
            $module = Module::find($course->module_id);
            if (!$module) {
                return response()->json(['error' => 'Module not found'], 404);
            }

            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('programs.program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json(['error' => 'You are not assigned to this program.'], 403);
            }
            
            // Update only the is_archived flag since archived_at doesn't exist in courses table
            $course->update([
                'is_archived' => true
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Course archived successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController: Error in archiveCourse', [
                'error' => $e->getMessage(),
                'course_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while archiving course: ' . $e->getMessage()
            ], 500);
        }
    }
}
