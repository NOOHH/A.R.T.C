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
            // Check if feature is enabled
            $isEnabled = AdminSetting::getValue('professor_module_management_enabled', '0') === '1';
            Log::info('ProfessorModuleController: Module management enabled check', ['enabled' => $isEnabled]);

            if (!$isEnabled) {
                Log::warning('ProfessorModuleController: Module management not enabled');
                abort(403, 'Module management is not enabled for professors.');
            }

            // Check whitelist
            $whitelist = AdminSetting::getValue('professor_module_management_whitelist', '');
            Log::info('ProfessorModuleController: Whitelist check', ['whitelist' => $whitelist]);

            // Use session-based authentication instead of Auth guard
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorModuleController: Not authenticated as professor via session');
                abort(403, 'You are not authenticated as a professor.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorModuleController: Professor authenticated via session', ['professor_id' => $professorId]);

            if (!empty($whitelist)) {
                $whitelistedIds = array_map('trim', explode(',', $whitelist));
                if (!in_array((string)$professorId, $whitelistedIds)) {
                    Log::warning('ProfessorModuleController: Professor not in whitelist', [
                        'professor_id' => $professorId,
                        'whitelist' => $whitelistedIds
                    ]);
                    abort(403, 'You are not authorized to manage modules.');
                }
            }

            Log::info('ProfessorModuleController: Permission check passed');
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

            return response()->json($modules);
        } catch (\Exception $e) {
            Log::error('ProfessorModuleController getModulesByProgram error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'program_id' => $request->input('program_id')
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
        $this->checkModulePermission();

        $professor = Auth::guard('professor')->user();
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

            return response()->json([
                'success' => true,
                'content_id' => $contentItem->id,
                'content_title' => $contentItem->content_title,
                'content_description' => $contentItem->content_description,
                'content_type' => $contentItem->content_type,
                'content_data' => $contentItem->content_data,
                'content_url' => $contentItem->content_url,
                'attachment_path' => $contentItem->attachment_path,
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
            $professor = Auth::guard('professor')->user();
            
            // Check if professor has access to this content via the course/module
            $course = $contentItem->course;
            $module = $course->module;
            
            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json(['success' => false, 'message' => 'You are not assigned to this program.'], 403);
            }

            $request->validate([
                'content_title' => 'required|string|max:255',
                'content_description' => 'nullable|string',
                'content_type' => 'required|in:lesson,video,assignment,quiz,test,link',
            ]);

            $contentItem->update([
                'content_title' => $request->content_title,
                'content_description' => $request->content_description,
                'content_type' => $request->content_type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content updated successfully!',
                'content' => $contentItem
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating content.'], 500);
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
            $professor = Auth::guard('professor')->user();
            
            // Check if professor has access to this content via the course/module
            $course = $contentItem->course;
            $module = $course->module;
            
            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json(['success' => false, 'message' => 'You are not assigned to this program.'], 403);
            }

            // Delete attachment file if exists
            if ($contentItem->attachment_path && Storage::disk('public')->exists($contentItem->attachment_path)) {
                Storage::disk('public')->delete($contentItem->attachment_path);
            }

            $contentItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Content deleted successfully!'
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
            $html .= '<a href="' . asset('storage/' . $contentItem->attachment_path) . '" target="_blank" class="btn btn-outline-secondary">';
            $html .= '<i class="bi bi-paperclip"></i> Download Attachment</a>';
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
            'attachment' => 'nullable|file|max:10240', // 10MB max
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
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('content', $fileName, 'public');
                $contentItem->attachment_path = $path;
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
            $assignedProgram = $professor->assignedPrograms()->where('professor_program.program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json(['error' => 'You are not assigned to this program.'], 403);
            }

            // Get content items for this course
            $contentItems = ContentItem::where('course_id', $courseId)
                ->where('is_active', true)
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

}
