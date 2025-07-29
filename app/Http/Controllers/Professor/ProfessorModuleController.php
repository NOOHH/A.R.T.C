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
        // Check if feature is enabled
        $isEnabled = AdminSetting::getValue('professor_module_management_enabled', '0') === '1';
        if (!$isEnabled) {
            abort(403, 'Module management is not enabled for professors.');
        }

        // Check whitelist
        $whitelist = AdminSetting::getValue('professor_module_management_whitelist', '');

        $professor = Auth::guard('professor')->user();
        if (!$professor) {
            Log::error('ProfessorModuleController: Not authenticated as professor');
            abort(403, 'You are not authenticated as a professor.');
        }
        $professorId = $professor->professor_id;

        if (!empty($whitelist)) {
            $whitelistedIds = array_map('trim', explode(',', $whitelist));
            if (!in_array((string)$professorId, $whitelistedIds)) {
                abort(403, 'You are not authorized to manage modules.');
            }
        }
    }

    /**
     * Display a listing of modules for assigned programs only.
     */
    public function index(Request $request)
    {
        $this->checkModulePermission();

        $professor = Auth::guard('professor')->user();
        
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
        $this->checkModulePermission();

        $professor = Auth::guard('professor')->user();
        $programId = $request->input('program_id');
        
        // Check if professor is assigned to this program
        $assignedProgram = $professor->assignedPrograms()->where('program_id', $programId)->first();
        if (!$assignedProgram) {
            return response()->json(['error' => 'You are not assigned to this program.'], 403);
        }

        $modules = Module::where('program_id', $programId)
                        ->where('is_archived', false)
                        ->with(['program', 'batch', 'contentItems'])
                        ->orderBy('module_order', 'asc')
                        ->get();

        return response()->json($modules);
    }

    /**
     * Get batches for a specific program.
     */
    public function getBatchesByProgram(Request $request)
    {
        $this->checkModulePermission();

        $professor = Auth::guard('professor')->user();
        $programId = $request->input('program_id');
        
        // Check if professor is assigned to this program
        $assignedProgram = $professor->assignedPrograms()->where('program_id', $programId)->first();
        if (!$assignedProgram) {
            return response()->json(['error' => 'You are not assigned to this program.'], 403);
        }

        $batches = StudentBatch::where('program_id', $programId)->get();
        return response()->json($batches);
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
     * Get courses for a specific module that the professor has access to
     */
    public function getCoursesByModule(Request $request)
    {
        $this->checkModulePermission();

        try {
            $moduleId = $request->get('module_id');
            $module = Module::findOrFail($moduleId);
            
            $professor = Auth::guard('professor')->user();
            
            // Check if professor is assigned to this program
            $assignedProgram = $professor->assignedPrograms()->where('program_id', $module->program_id)->first();
            if (!$assignedProgram) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this program.'
                ]);
            }

            $courses = Course::where('module_id', $moduleId)
                ->select('subject_id as course_id', 'subject_name as course_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get content item details for viewing in the content viewer
     */
    public function getContent($id)
    {
        $this->checkModulePermission();

        try {
            $contentItem = ContentItem::findOrFail($id);
            $professor = Auth::guard('professor')->user();
            
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


}
