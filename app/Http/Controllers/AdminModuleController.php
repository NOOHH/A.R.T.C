<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Program;
use App\Models\Course;
use App\Models\ContentItem;
use App\Models\StudentBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\AdminSetting;
use App\Http\Controllers\Traits\AdminPreviewCustomization;

class AdminModuleController extends Controller
{
    use AdminPreviewCustomization;
    public function __construct()
    {
        $user = Auth::user();
        $director = Auth::guard('director')->user();
        $isDirector = $director !== null;
        Log::info('AdminModuleController access', [
            'isDirector' => $isDirector,
            'manage_modules' => \App\Models\AdminSetting::getValue('director_manage_modules', 'false'),
            'auth_user' => $user,
            'auth_director' => $director,
            'auth_user_role' => $user && isset($user->role) ? $user->role : null,
            'auth_director_id' => $director ? $director->directors_id : null,
        ]);
        if ($isDirector) {
            $canManage = \App\Models\AdminSetting::getValue('director_manage_modules', 'false') === 'true' || \App\Models\AdminSetting::getValue('director_manage_modules', '0') === '1';
            if (!$canManage) {
                abort(403, 'Access denied: You do not have permission to manage modules.');
            }
        }
    }

    /**
     * Display a listing of modules.
     */
    public function index(Request $request)
    {
        $programs = Program::all();
        $modules = collect();
        $allModules = Module::where('is_archived', false)->with(['program', 'batch'])->get();
        
        if ($request->has('program_id') && $request->program_id != '') {
            $modules = Module::where('program_id', $request->program_id)
                           ->where('is_archived', false)
                           ->with(['program', 'batch'])
                           ->orderBy('module_order', 'asc')
                           ->orderBy('created_at', 'desc')
                           ->get();
        }

        return view('admin.admin-modules.admin-modules', compact('programs', 'modules', 'allModules'));
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
        $request->validate([
            'module_name' => 'required|string|max:255',
            'module_description' => 'nullable|string',
            'program_id' => 'required|exists:programs,program_id',
            'batch_id' => ($request->input('plan') === 'full') ? 'required|exists:student_batches,batch_id' : 'nullable|exists:student_batches,batch_id',
            'learning_mode' => 'required|in:Synchronous,Asynchronous',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg,mp4,webm,ogg|max:102400',
            'content_type' => 'nullable|string|in:lesson,video,assignment,quiz,test,link',
            'video_url' => 'nullable|url',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('modules', $filename, 'public');
        }

        // Handle AI document upload
        $aiDocumentPath = null;
        if ($request->hasFile('ai_document')) {
            $file = $request->file('ai_document');
            $filename = time() . '_ai_doc_' . $file->getClientOriginalName();
            $aiDocumentPath = $file->storeAs('modules/ai_documents', $filename, 'public');
        }

        // Prepare content-specific data
        $contentData = [];
        $contentType = $request->input('content_type', 'module');

        // Extract content-specific fields based on type
        switch ($contentType) {
            case 'lesson':
                $contentData = [
                    'lesson_video_url' => $request->input('any_url'),
                ];
                break;
            case 'video':
                $contentData = [
                    'video_url' => $request->input('any_url'),
                ];
                break;
            case 'assignment':
                $contentData = [
                    'assignment_title' => $request->input('assignment_title'),
                    'assignment_instructions' => $request->input('assignment_instructions'),
                    'due_date' => $request->input('due_date'),
                    'max_points' => $request->input('max_points'),
                    'allow_late_submission' => $request->boolean('allow_late_submission'),
                ];
                break;
            case 'quiz':
                $contentData = [
                    'quiz_title' => $request->input('quiz_title'),
                    'quiz_description' => $request->input('quiz_description'),
                    'time_limit' => $request->input('time_limit'),
                    'question_count' => $request->input('question_count'),
                    'randomize_questions' => $request->boolean('randomize_questions'),
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
                    'external_url' => $request->input('any_url'),
                    'link_title' => $request->input('link_title'),
                    'link_description' => $request->input('link_description'),
                    'link_type' => $request->input('link_type', 'other'),
                    'open_in_new_tab' => $request->boolean('open_in_new_tab'),
                ];
                break;

        }

        // Handle video URL (YouTube, Vimeo, etc.)
        $videoUrl = null;
        $videoPath = null;
        if ($request->video_url) {
            $videoUrl = $request->video_url;
            // Convert YouTube URLs to embed format
            if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                $videoUrl = $this->convertYouTubeToEmbed($videoUrl);
            }
            $videoPath = $videoUrl; // Use video_path for storage
        }

        $module = Module::create([
            'module_name' => $request->module_name,
            'module_description' => $request->module_description,
            'program_id' => $request->program_id,
            'batch_id' => $request->batch_id,
            'learning_mode' => $request->learning_mode,
            'attachment' => $attachmentPath,
            'content_type' => $contentType,
            'content_data' => $contentData,
            'video_path' => $videoPath,
            'content_url' => $request->input('any_url'),
            'is_archived' => false,
        ]);

        // If this is an AI quiz, trigger AI generation
        if ($contentType === 'ai_quiz' && $aiDocumentPath) {
            $this->generateAIQuiz($module);
        }

        return redirect()->route('admin.modules.index', ['program_id' => $request->program_id])
                        ->with('success', 'Module created successfully!');
    }

    /**
     * Display the specified module.
     */
    public function show(Module $module)
    {
        return view('admin.admin-modules.show', compact('module'));
    }

    /**
     * Get module data for editing (JSON).
     */
    public function getModule($id)
    {
        try {
            $module = Module::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'module' => [
                    'modules_id' => $module->modules_id,
                    'module_name' => $module->module_name,
                    'module_description' => $module->module_description,
                    'program_id' => $module->program_id,
                    'batch_id' => $module->batch_id,
                    'attachment_path' => $module->attachment_path,
                    'created_at' => $module->created_at,
                    'updated_at' => $module->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Module not found'
            ], 404);
        }
    }

    /**
     * Get modules for a specific program (JSON).
     */
    public function getModulesForProgram($programId)
    {
        try {
            $modules = Module::where('program_id', $programId)
                             ->where('is_archived', false)
                             ->orderBy('module_order','asc')
                             ->get(['modules_id as id','module_name']);

            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading modules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing a module.
     */
    public function edit($id)
    {
        $module = Module::findOrFail($id);
        $programs = Program::where('is_archived', false)->get();
        
        // Check if this is an AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'module' => $module,
                'programs' => $programs
            ]);
        }
        
        return view('admin.admin-modules.edit', compact('module', 'programs'));
    }

    /**
     * Update the specified module in storage.
     */
    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        
        $request->validate([
            'module_name' => 'required|string|max:255',
            'module_description' => 'nullable|string',
            'program_id' => 'required|exists:programs,program_id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg,mp4,avi,mov|max:102400',
            'content_type' => 'nullable|string|in:module,assignment,quiz,test,link',
        ]);

        // Handle file upload if provided
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($module->attachment && Storage::disk('public')->exists($module->attachment)) {
                Storage::disk('public')->delete($module->attachment);
            }
            
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $module->attachment = $file->storeAs('modules', $filename, 'public');
        }

        // Update module details
        $module->module_name = $request->module_name;
        $module->module_description = $request->module_description;
        $module->program_id = $request->program_id;
        $module->save();

        return redirect()->route('admin.modules.index', ['program_id' => $module->program_id])
                        ->with('success', 'Module updated successfully!');
    }

    /**
     * Upload video for a module.
     */
    public function uploadVideo(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov,wmv|max:512000', // 500MB max
        ]);

        try {
            if ($request->hasFile('video')) {
                // Delete old video if exists
                if ($module->video_path && Storage::disk('public')->exists($module->video_path)) {
                    Storage::disk('public')->delete($module->video_path);
                }
                
                $file = $request->file('video');
                $filename = time() . '_video_' . $file->getClientOriginalName();
                $videoPath = $file->storeAs('modules/videos', $filename, 'public');
                
                // Update module with video path
                $module->video_path = $videoPath;
                $module->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Video uploaded successfully!',
                    'video_url' => asset('storage/' . $videoPath)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Video upload failed: ' . $e->getMessage()
            ], 500);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No video file provided.'
        ]);
    }

    /**
     * Add additional content to a module.
     */
    public function addContent(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        
        $request->validate([
            'content_type' => 'required|string|in:text,file,link,video',
            'content_title' => 'required|string|max:255',
            'content_description' => 'nullable|string',
            'content_file' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg,mp4,avi,mov|max:102400',
            'content_link' => 'nullable|url',
            'content_text' => 'nullable|string',
        ]);

        try {
            $contentData = [
                'type' => $request->content_type,
                'title' => $request->content_title,
                'description' => $request->content_description,
                'created_at' => now()->toISOString(),
            ];

            // Handle different content types
            switch ($request->content_type) {
                case 'file':
                case 'video':
                    if ($request->hasFile('content_file')) {
                        $file = $request->file('content_file');
                        $filename = time() . '_content_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('modules/content', $filename, 'public');
                        $contentData['file_path'] = $filePath;
                        $contentData['file_name'] = $file->getClientOriginalName();
                        $contentData['file_size'] = $file->getSize();
                    }
                    break;
                    
                case 'link':
                    $contentData['url'] = $request->content_link;
                    break;
                    
                case 'text':
                    $contentData['content'] = $request->content_text;
                    break;
            }

            // Get existing additional content or create new array
            $additionalContent = $module->additional_content ? json_decode($module->additional_content, true) : [];
            $additionalContent[] = $contentData;
            
            // Update module with new content
            $module->additional_content = json_encode($additionalContent);
            $module->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Content added successfully!',
                'content' => $contentData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add content: ' . $e->getMessage()
            ], 500);
        }
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
        $courses = Course::with(['module.program'])->whereHas('module', function($query) {
            $query->where('is_archived', true);
        })->orWhereHas('contentItems', function($query) {
            $query->where('is_archived', true);
        })->get();
        
        $archivedModules = collect();
        $archivedCourses = collect();
        $archivedContent = collect();
        $stats = [
            'total_archived' => 0,
            'archived_modules' => 0,
            'archived_courses' => 0,
            'archived_content' => 0
        ];
        
        if ($request->has('program_id') && $request->program_id != '') {
            // Get archived modules
            $archivedModules = Module::where('program_id', $request->program_id)
                           ->where('is_archived', true)
                           ->with(['program', 'courses'])
                           ->orderBy('updated_at', 'desc')
                           ->get();
            
            // Get courses with archived content from modules in this program
            $archivedCourses = Course::whereHas('module', function($query) use ($request) {
                $query->where('program_id', $request->program_id)
                      ->where('is_archived', false); // Include active modules too
            })->with(['module.program', 'contentItems' => function($query) {
                $query->where('is_archived', true)->orderBy('archived_at', 'desc');
            }])->whereHas('contentItems', function($query) {
                $query->where('is_archived', true);
            })->orderBy('updated_at', 'desc')->get();
            
            // Also get courses from archived modules
            $coursesFromArchivedModules = Course::whereHas('module', function($query) use ($request) {
                $query->where('program_id', $request->program_id)
                      ->where('is_archived', true);
            })->with(['module.program', 'contentItems' => function($query) {
                $query->orderBy('created_at', 'desc'); // All content from archived modules
            }])->orderBy('updated_at', 'desc')->get();
            
            // Merge the course collections
            $archivedCourses = $archivedCourses->merge($coursesFromArchivedModules)->unique('subject_id');
            
            // Get all archived content for this program (including standalone archived content)
            $archivedContent = ContentItem::whereHas('course.module', function($query) use ($request) {
                $query->where('program_id', $request->program_id);
            })->where('is_archived', true)
              ->with(['course.module.program'])
              ->orderBy('archived_at', 'desc')
              ->get();
              
            $stats['archived_modules'] = $archivedModules->count();
            $stats['archived_courses'] = $archivedCourses->filter(function($course) {
                return $course->contentItems->where('is_archived', true)->count() > 0;
            })->count();
            $stats['archived_content'] = $archivedContent->count();
            $stats['total_archived'] = $stats['archived_modules'] + $stats['archived_content'];
        } else {
            // Get all archived modules
            $archivedModules = Module::where('is_archived', true)
                           ->with(['program', 'courses'])
                           ->orderBy('updated_at', 'desc')
                           ->get();
            
            // Get all courses with archived content
            $archivedCourses = Course::with(['module.program', 'contentItems' => function($query) {
                $query->where('is_archived', true);
            }])->whereHas('contentItems', function($query) {
                $query->where('is_archived', true);
            })->orderBy('updated_at', 'desc')->get();
            
            // Get all archived content
            $archivedContent = ContentItem::where('is_archived', true)
              ->with(['course.module.program'])
              ->orderBy('archived_at', 'desc')
              ->get();
              
            $stats['archived_modules'] = $archivedModules->count();
            $stats['archived_courses'] = $archivedCourses->count();
            $stats['archived_content'] = $archivedContent->count();
            $stats['total_archived'] = $stats['archived_modules'] + $stats['archived_content'];
        }
        
        return view('admin.admin-modules.admin-modules-archived', compact(
            'programs', 'archivedModules', 'archivedCourses', 'archivedContent', 'stats'
        ));
    }

    /**
     * Update module order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'module_ids' => 'required|array',
            'module_ids.*' => 'exists:modules,modules_id'
        ]);

        try {
            foreach ($request->module_ids as $index => $moduleId) {
                Module::where('modules_id', $moduleId)
                    ->update(['module_order' => $index + 1]);
            }

            return response()->json(['success' => true, 'message' => 'Module order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update module order: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle admin override for a module.
     */
    public function toggleAdminOverride(Request $request, $moduleId)
    {
        try {
            $module = Module::findOrFail($moduleId);
            $module->admin_override = !$module->admin_override;
            $module->save();

            return response()->json([
                'success' => true, 
                'message' => 'Admin override updated successfully',
                'admin_override' => $module->admin_override
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update admin override']);
        }
    }

    /**
     * Generate AI quiz from uploaded document
     */
    private function generateAIQuiz($module)
    {
        try {
            $contentData = $module->content_data;
            $documentPath = storage_path('app/public/' . $contentData['ai_document_path']);
            
            // Extract text from document
            $text = $this->extractTextFromDocument($documentPath);
            
            // Generate quiz questions using AI (placeholder for now)
            $questions = $this->generateQuizQuestions($text, $contentData);
            
            // Update module with generated questions
            $contentData['generated_questions'] = $questions;
            $contentData['generation_status'] = 'completed';
            $contentData['generated_at'] = now()->toISOString();
            
            $module->content_data = $contentData;
            $module->save();
            
        } catch (\Exception $e) {
            Log::error('AI Quiz generation failed: ' . $e->getMessage());
            
            // Update module with error status
            $contentData = $module->content_data;
            $contentData['generation_status'] = 'failed';
            $contentData['generation_error'] = $e->getMessage();
            $module->content_data = $contentData;
            $module->save();
        }
    }

    /**
     * Extract text from uploaded document
     */
    private function extractTextFromDocument($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $text = '';
        
        switch (strtolower($extension)) {
            case 'txt':
                $text = file_get_contents($filePath);
                break;
            case 'pdf':
                // PDF text extraction (requires additional libraries)
                // For now, return placeholder
                $text = 'PDF content extraction requires additional setup';
                break;
            case 'doc':
            case 'docx':
                // DOC/DOCX text extraction (requires additional libraries)
                // For now, return placeholder
                $text = 'DOC/DOCX content extraction requires additional setup';
                break;
            default:
                throw new \Exception('Unsupported file format');
        }
        
        return $text;
    }

    /**
     * Generate quiz questions using AI
     */
    private function generateQuizQuestions($text, $contentData)
    {
        // This is a placeholder for AI quiz generation
        // In a real implementation, you would integrate with an AI service
        
        $numQuestions = $contentData['ai_num_questions'];
        $difficulty = $contentData['ai_difficulty'];
        $quizType = $contentData['ai_quiz_type'];
        
        $questions = [];
        
        // Generate sample questions based on parameters
        for ($i = 1; $i <= $numQuestions; $i++) {
            if ($quizType === 'multiple_choice' || $quizType === 'mixed') {
                $questions[] = [
                    'id' => $i,
                    'question' => "Sample question $i based on the document content",
                    'type' => 'multiple_choice',
                    'options' => [
                        'A' => 'Option A',
                        'B' => 'Option B',
                        'C' => 'Option C',
                        'D' => 'Option D'
                    ],
                    'correct_answer' => 'A',
                    'difficulty' => $difficulty,
                    'points' => 1
                ];
            } else {
                $questions[] = [
                    'id' => $i,
                    'question' => "True/False question $i based on the document content",
                    'type' => 'true_false',
                    'options' => [
                        'A' => 'True',
                        'B' => 'False'
                    ],
                    'correct_answer' => 'A',
                    'difficulty' => $difficulty,
                    'points' => 1
                ];
            }
        }
        
        return $questions;
    }

    /**
     * Get batches for a specific program
     */
    public function getBatchesForProgram($programId)
    {
        try {
            $batches = \App\Models\StudentBatch::where('program_id', $programId)
                ->select('batch_id as id', 'batch_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'batches' => $batches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading batches: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get batches by program ID (alias for route compatibility)
     */
    public function getBatchesByProgram($programId)
    {
        return $this->getBatchesForProgram($programId);
    }

    /**
     * Get modules by program
     */
    public function getModulesByProgram(Request $request)
    {
        try {
            $programId = $request->get('program_id');
            $moduleId = $request->get('module_id');
            
            if ($moduleId) {
                // Get program info for a specific module
                $module = Module::findOrFail($moduleId);
                return response()->json([
                    'success' => true,
                    'program_id' => $module->program_id,
                    'module' => $module
                ]);
            }
            
            if ($programId) {
                // Get all modules for a program
                $modules = Module::where('program_id', $programId)
                    ->where('is_archived', false)
                    ->select('modules_id', 'module_name', 'program_id')
                    ->orderBy('module_order', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return response()->json([
                    'success' => true,
                    'modules' => $modules
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Program ID or Module ID required'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading modules: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get courses for a specific program
     */
    public function getCoursesForProgram($programId)
    {
        try {
            $courses = \App\Models\Course::where('program_id', $programId)
                ->select('subject_id', 'subject_name')
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
     * Archive a module
     */
    public function archive($id)
    {
        try {
            $module = Module::findOrFail($id);
            $module->is_archived = true;
            $module->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Module archived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving module: ' . $e->getMessage()
            ]);
        }
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
     * Update admin override settings for a module
     */
    public function updateOverride(Request $request, $moduleId)
    {
        try {
            $module = Module::findOrFail($moduleId);
            $overrides = $request->input('admin_override', []);
            
            $module->admin_override = $overrides;
            $module->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Override settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating override settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get override settings for a module
     */
    public function getOverrideSettings($moduleId)
    {
        try {
            $module = Module::findOrFail($moduleId);
            $overrides = $module->admin_override ? $module->admin_override : [];
            
            return response()->json([
                'success' => true,
                'overrides' => $overrides
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading override settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the admin quiz generator form
     */


    /**
     * Store course content (lessons, quizzes, tests, assignments)
     */
    public function courseContentStore(Request $request)
    {
        // Enhanced logging for debugging
        \Log::info('Course content store request received', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'all_data' => $request->all(),
            'files' => $request->files->all(),
            'has_attachment' => $request->hasFile('attachment'),
            'content_type' => $request->get('content_type'),
            'course_id' => $request->get('course_id'),
            'module_id' => $request->get('module_id'),
            'program_id' => $request->get('program_id')
        ]);

        // Check if required fields are present
        $requiredFields = ['program_id', 'module_id', 'course_id', 'content_type', 'content_title'];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!$request->has($field) || $request->get($field) === null || $request->get($field) === '') {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            \Log::warning('Missing required fields', [
                'missing_fields' => $missingFields,
                'received_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missingFields),
                'errors' => [
                    'missing_fields' => $missingFields,
                    'received_fields' => array_keys($request->all())
                ]
            ], 422);
        }

        // Add detailed logging before validation
        $fileInfo = 'No file';
        if ($request->hasFile('attachment')) {
            $files = $request->file('attachment');
            if (is_array($files)) {
                $fileInfo = [];
                foreach ($files as $file) {
                    $fileInfo[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'error' => $file->getError(),
                        'is_valid' => $file->isValid()
                    ];
                }
            } else {
                $fileInfo = [
                    'original_name' => $files->getClientOriginalName(),
                    'size' => $files->getSize(),
                    'mime_type' => $files->getMimeType(),
                    'error' => $files->getError(),
                    'is_valid' => $files->isValid()
                ];
            }
        }
        \Log::info('Course content store request received', [
            'has_file' => $request->hasFile('attachment'),
            'file_info' => $fileInfo,
            'request_data_size' => count($request->all()),
            'php_limits' => [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_file_uploads' => ini_get('max_file_uploads')
            ],
            'all_request_data' => $request->except(['attachment', '_token'])
        ]);

        try {
            $attachmentPaths = [];
            // Handle file upload if present (multiple files)
            if ($request->hasFile('attachment')) {
                $files = $request->file('attachment');
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    // Check file upload errors
                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        $errorMessages = [
                            UPLOAD_ERR_INI_SIZE => 'File is too large (exceeds server upload limit)',
                            UPLOAD_ERR_FORM_SIZE => 'File is too large (exceeds form limit)',
                            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server',
                            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
                        ];
                        $errorMessage = $errorMessages[$file->getError()] ?? 'Unknown upload error';
                        \Log::error('File upload error', [
                            'error_code' => $file->getError(),
                            'error_message' => $errorMessage,
                            'file_size' => $file->getSize(),
                            'file_name' => $file->getClientOriginalName()
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'The attachment failed to upload',
                            'errors' => ['attachment' => [$errorMessage]]
                        ], 422);
                    }
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $attachmentPath = $file->storeAs('content', $filename, 'public');
                    if (!$attachmentPath) {
                        \Log::error('File storage failed', [
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize()
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'The attachment failed to upload',
                            'errors' => ['attachment' => ['Failed to store file on server']]
                        ], 422);
                    }
                    $attachmentPaths[] = $attachmentPath;
                }
                \Log::info('Files uploaded successfully', ['paths' => $attachmentPaths]);
            }

            // Get the course
            $course = Course::findOrFail($request->course_id);
            // (Removed: Find or create lesson for this course)

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

            // Create content item (lesson_id removed)
            $contentItem = \App\Models\ContentItem::create([
                'content_title' => $request->content_title,
                'content_description' => $request->content_description,
                // 'lesson_id' => $lesson->lesson_id, // Removed
                'course_id' => $course->subject_id,
                'content_type' => $contentType,
                'content_data' => $contentData,
                'attachment_path' => !empty($attachmentPaths) ? json_encode($attachmentPaths) : null,
                'content_url' => $request->input('content_url'),
                'max_points' => $request->input('max_points', 0),
                'due_date' => $request->input('due_date'),
                'time_limit' => $request->input('time_limit'),
                'is_required' => true,
                'is_active' => true,
                'enable_submission' => $request->boolean('enable_submission'),
                'allowed_file_types' => $request->allowed_file_types,
                'max_file_size' => $request->input('max_file_size', 10),
                'submission_instructions' => $request->submission_instructions,
            ]);

            // If this is an assignment with a due date, create deadline entries for enrolled students
            if ($contentType === 'assignment' && $request->input('due_date')) {
                $this->createAssignmentDeadlines($contentItem, $request->input('program_id'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Course content created successfully!',
                'content_item' => $contentItem
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['attachment']),
                'files' => $request->hasFile('attachment') ? 'Has file' : 'No file',
                'validator_messages' => $e->validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'validation_details' => [
                    'received_fields' => array_keys($request->all()),
                    'required_fields' => ['program_id', 'module_id', 'course_id', 'content_type', 'content_title'],
                    'validation_messages' => $e->validator->errors()->toArray()
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating course content: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating course content: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Create assignment deadlines for all enrolled students
     */
    private function createAssignmentDeadlines($contentItem, $programId)
    {
        try {
            // Get all students enrolled in this program
            $enrolledStudents = \App\Models\Student::whereHas('enrollments', function($query) use ($programId) {
                $query->where('program_id', $programId)
                      ->where('enrollment_status', 'approved');
            })->get();

            // Also get students enrolled in courses that belong to this assignment's course
            $courseStudents = \App\Models\Student::whereHas('enrollments.enrollmentCourses', function($query) use ($contentItem) {
                $query->where('course_id', $contentItem->course_id)
                      ->where('is_active', true);
            })->get();

            // Merge and get unique students
            $allStudents = $enrolledStudents->merge($courseStudents)->unique('student_id');

            foreach ($allStudents as $student) {
                // Check if deadline already exists for this student and assignment
                $existingDeadline = \App\Models\Deadline::where('student_id', $student->student_id)
                    ->where('type', 'assignment')
                    ->where('reference_id', $contentItem->id)
                    ->first();

                if (!$existingDeadline) {
                    \App\Models\Deadline::create([
                        'student_id' => $student->student_id,
                        'program_id' => $programId,
                        'title' => 'Assignment: ' . $contentItem->content_title,
                        'description' => $contentItem->content_description ?? 'Complete the assigned assignment',
                        'type' => 'assignment',
                        'reference_id' => $contentItem->id,
                        'due_date' => $contentItem->due_date,
                        'status' => 'pending',
                    ]);
                }
            }

            \Log::info('Assignment deadlines created', [
                'content_item_id' => $contentItem->id,
                'program_id' => $programId,
                'students_count' => $allStudents->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating assignment deadlines: ' . $e->getMessage(), [
                'content_item_id' => $contentItem->id,
                'program_id' => $programId,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get courses by module
     */
    public function getCoursesByModule($moduleId)
    {
        try {
            $courses = Course::where('module_id', $moduleId)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->orderBy('subject_name')
                ->get(['subject_id', 'subject_name', 'subject_description', 'subject_price']);

            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading courses by module: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get content item for editing
     */
    public function getContent($id)
    {
        try {
            $content = ContentItem::with(['course.module'])->findOrFail($id);
            
            // Parse attachment_path if it's JSON
            $attachmentPath = $content->attachment_path;
            $attachmentUrls = [];
            $fileNames = [];
            $hasMultipleFiles = false;
            
            if ($attachmentPath) {
                $parsedAttachments = json_decode($attachmentPath, true);
                if (is_array($parsedAttachments)) {
                    // Multiple files
                    $hasMultipleFiles = true;
                    foreach ($parsedAttachments as $path) {
                        $attachmentUrls[] = asset('storage/' . $path);
                    }
                    
                    // Parse file names if they're stored as JSON
                    if ($content->file_name) {
                        $parsedNames = json_decode($content->file_name, true);
                        $fileNames = is_array($parsedNames) ? $parsedNames : [$content->file_name];
                    } else {
                        // Extract filenames from paths if no names are stored
                        foreach ($parsedAttachments as $path) {
                            $fileNames[] = basename($path);
                        }
                    }
                } else {
                    // Single file
                    $attachmentUrls = [asset('storage/' . $attachmentPath)];
                    $fileNames = [$content->file_name ?? basename($attachmentPath)];
                    $hasMultipleFiles = false;
                }
            }
            
            // Log for debugging
            \Log::info('AdminModuleController: Processing content item', [
                'content_id' => $content->id,
                'attachment_path' => $content->attachment_path,
                'parsed_attachments' => $parsedAttachments ?? null,
                'has_multiple_files' => $hasMultipleFiles,
                'file_names' => $fileNames,
                'is_attachment_json' => is_array($parsedAttachments)
            ]);
            
            return response()->json([
                'success' => true,
                'content' => [
                    'id' => $content->id,
                    'title' => $content->content_title,
                    'content_title' => $content->content_title,
                    'description' => $content->content_description,
                    'content_description' => $content->content_description,
                    'type' => $content->content_type,
                    'content_type' => $content->content_type,
                    'file_path' => $content->attachment_path,
                    'attachment_path' => $content->attachment_path,
                    'attachment_urls' => $attachmentUrls,
                    'file_names' => $fileNames,
                    'has_multiple_files' => $hasMultipleFiles || $content->has_multiple_files,
                    'content_text' => $content->content_text ?? '',
                    'file_mime' => $content->file_mime,
                    'file_size' => $content->file_size,
                    'link' => $content->content_url,
                    'content_url' => $content->content_url,
                    'sort_order' => $content->sort_order,
                    'course_id' => $content->course_id,
                    'enable_submission' => $content->enable_submission ?? false,
                    'allowed_file_types' => $content->allowed_file_types,
                    'submission_instructions' => $content->submission_instructions,
                    'due_date' => $content->due_date
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Content not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Delete content item
     */
    public function deleteContent($id)
    {
        try {
            $content = \App\Models\ContentItem::findOrFail($id);
            
            // Delete associated files if any
            if ($content->attachment_path) {
                Storage::delete('public/' . $content->attachment_path);
            }
            
            $content->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Content deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update content item
     */
    public function updateContent(Request $request, $id)
    {
        try {
            // Debug logging for file upload issues
            \Log::info('updateContent debugging', [
                'content_id' => $id,
                'inputs' => $request->except(['attachment']),
                'hasAttachment' => $request->hasFile('attachment'),
                'files' => $request->allFiles(),
                'request_all' => $request->all(),
                'php_upload_settings' => [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'max_file_uploads' => ini_get('max_file_uploads'),
                    'memory_limit' => ini_get('memory_limit')
                ]
            ]);
            
            if ($request->hasFile('attachment')) {
                $upload = $request->file('attachment');
                \Log::info('Attachment info', [
                    'originalName' => $upload->getClientOriginalName(),
                    'size' => $upload->getSize(),
                    'mime' => $upload->getClientMimeType(),
                    'error' => $upload->getError(),
                    'isValid' => $upload->isValid(),
                    'tempName' => $upload->getPathname()
                ]);
            }
            
            // Special check for empty file fields that Laravel might reject
            if ($request->has('attachment') && !$request->hasFile('attachment')) {
                \Log::warning('Attachment field present but no file received', [
                    'contentId' => $id,
                    'attachmentValue' => $request->get('attachment'),
                    'hasFile' => $request->hasFile('attachment')
                ]);
                // Remove attachment from validation if it's empty
                $validationData = $request->except(['attachment']);
            } else {
                $validationData = $request->all();
            }
            
            $validator = \Validator::make($validationData, [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|string|in:lesson,assignment,pdf,link,quiz,test,video',
                'attachment' => 'nullable|file', // Remove size limit for testing
                'link' => 'nullable|url',
                'sort_order' => 'nullable|integer|min:0',
                'enable_submission' => 'nullable|boolean',
                'allowed_file_types' => 'nullable|string',
                'course_id' => 'nullable|exists:courses,subject_id' // Make course_id nullable for testing
            ]);

            // Also validate that content exists and get lesson_id from it
            $content = \App\Models\ContentItem::findOrFail($id);

            if ($validator->fails()) {
                \Log::error('Validation failed for content update', [
                    'contentId' => $id,
                    'errors' => $validator->errors()->toArray(),
                    'requestData' => $request->except(['attachment']),
                    'hasFile' => $request->hasFile('attachment'),
                    'fileInfo' => $request->hasFile('attachment') ? [
                        'name' => $request->file('attachment')->getClientOriginalName(),
                        'size' => $request->file('attachment')->getSize(),
                        'error' => $request->file('attachment')->getError(),
                        'isValid' => $request->file('attachment')->isValid()
                    ] : null
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . $validator->errors()->first(),
                    'errors' => $validator->errors(),
                    'debug' => [
                        'hasFile' => $request->hasFile('attachment'),
                        'requestKeys' => array_keys($request->all()),
                        'validationRules' => [
                            'title' => 'required|string|max:255',
                            'description' => 'nullable|string',
                            'type' => 'required|string|in:lesson,assignment,pdf,link,quiz,test,video',
                            'attachment' => 'nullable|file',
                            'course_id' => 'nullable|exists:courses,subject_id'
                        ]
                    ]
                ], 422);
            }
            
            // Map form fields to database fields
            $updateData = [
                'content_title' => $request->title,
                'content_description' => $request->description,
                'content_type' => $request->type,
                'sort_order' => $request->sort_order ?? $content->sort_order ?? 1,
                'enable_submission' => $request->boolean('enable_submission'),
                'allowed_file_types' => $request->allowed_file_types
            ];
            
            // Handle link URL for link type content
            if ($request->type === 'link' && $request->link) {
                $updateData['content_url'] = $request->link;
            }
            
            // Handle file upload if provided
            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $upload = $request->file('attachment');

                try {
                    // Delete old file if exists
                    if ($content->attachment_path && \Storage::disk('public')->exists($content->attachment_path)) {
                        \Storage::disk('public')->delete($content->attachment_path);
                        \Log::info('Deleted old file', ['path' => $content->attachment_path]);
                    }
                    
                    // Sanitize filename
                    $originalName = $upload->getClientOriginalName();
                    $sanitizedName = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $originalName);
                    $filename = time() . '_' . $sanitizedName;
                    
                    // Store new file
                    $path = $upload->storeAs('content', $filename, 'public');
                    
                    // Update attachment path
                    $updateData['attachment_path'] = $path;
                    
                    \Log::info('File uploaded successfully', [
                        'originalName' => $originalName,
                        'sanitizedName' => $sanitizedName,
                        'storedPath' => $path,
                        'contentId' => $id,
                        'fileSize' => $upload->getSize()
                    ]);
                } catch (\Exception $fileError) {
                    \Log::error('File upload error', [
                        'error' => $fileError->getMessage(),
                        'file' => $fileError->getFile(),
                        'line' => $fileError->getLine(),
                        'contentId' => $id
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload file: ' . $fileError->getMessage()
                    ], 500);
                }
            } else if ($request->hasFile('attachment') && !$request->file('attachment')->isValid()) {
                // File was uploaded but is invalid
                $upload = $request->file('attachment');
                \Log::error('Invalid file upload', [
                    'originalName' => $upload->getClientOriginalName(),
                    'size' => $upload->getSize(),
                    'mime' => $upload->getClientMimeType(),
                    'error' => $upload->getError(),
                    'errorMessage' => $upload->getErrorMessage(),
                    'contentId' => $id
                ]);

                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File is too large (exceeds php.ini limit)',
                    UPLOAD_ERR_FORM_SIZE => 'File is too large (exceeds form limit)',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
                ];
                
                $errorMessage = $errorMessages[$upload->getError()] ?? 'Unknown upload error';

                return response()->json([
                    'success' => false,
                    'message' => 'The attachment failed to upload: ' . $errorMessage
                ], 422);
            }
            
            // Update content with all data including file path
            $content->update($updateData);
            
            \Log::info('Content updated successfully', [
                'contentId' => $id,
                'updatedFields' => array_keys($updateData)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Content updated successfully',
                'content' => $content->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in updateContent', [
                'contentId' => $id,
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating content: ' . $e->getMessage(), [
                'contentId' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update content order (drag and drop)
     */
    public function updateContentOrder(Request $request)
    {
        try {
            $contentIds = $request->input('content_ids', []);
            
            foreach ($contentIds as $index => $contentId) {
                \App\Models\ContentItem::where('id', $contentId)
                    ->update(['content_order' => $index + 1]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Content order updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating content order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating content order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move content item to different course/module
     */
    public function moveContent(Request $request)
    {
        try {
            $request->validate([
                'content_id' => 'required|integer|exists:content_items,id',
                'new_course_id' => 'required|integer|exists:courses,subject_id',
                'new_module_id' => 'required|integer|exists:modules,modules_id'
            ]);

            $contentId = $request->input('content_id');
            $newCourseId = $request->input('new_course_id');
            $newModuleId = $request->input('new_module_id');

            // Find the content item
            $content = ContentItem::findOrFail($contentId);
            
            // Get the highest sort order in the new course
            $maxSortOrder = ContentItem::where('course_id', $newCourseId)
                                     ->max('content_order') ?? 0;

            // Update the content item
            $content->update([
                'course_id' => $newCourseId,
                'content_order' => $maxSortOrder + 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content moved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error moving content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to move content: ' . $e->getMessage()
            ], 500);
        }
    }

    public function moveContentToModule(Request $request)
    {
        try {
            $request->validate([
                'content_id' => 'required|integer|exists:content_items,id',
                'module_id' => 'required|integer|exists:modules,modules_id'
            ]);

            $contentId = $request->input('content_id');
            $moduleId = $request->input('module_id');

            // Find the content item
            $content = ContentItem::findOrFail($contentId);
            
            // Get the first course in the target module, or create a default one
            $targetCourse = Course::where('module_id', $moduleId)
                                ->orderBy('created_at', 'asc')
                                ->first();

            if (!$targetCourse) {
                return response()->json([
                    'success' => false,
                    'message' => 'No courses found in the target module. Please add a course first.'
                ], 400);
            }
            
            // Get the highest sort order in the target course
            $maxSortOrder = ContentItem::where('course_id', $targetCourse->subject_id)
                                     ->max('content_order') ?? 0;

            // Update the content item
            $content->update([
                'course_id' => $targetCourse->subject_id,
                'content_order' => $maxSortOrder + 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content moved successfully to ' . $targetCourse->subject_name
            ]);
        } catch (\Exception $e) {
            Log::error('Error moving content to module: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to move content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get module content for the admin content viewer
     */
    public function getModuleContent($moduleId)
    {
        try {
            $module = Module::findOrFail($moduleId);
            
            // Get courses for this module
            $courses = Course::where('module_id', $moduleId)
                          ->orderBy('subject_order', 'asc')
                          ->get()
                          ->map(function($course) {
                              return [
                                  'course_id' => $course->subject_id,
                                  'course_name' => $course->subject_name,
                                  'course_description' => $course->subject_description,
                                  'course_type' => $course->subject_type,
                                  'content_items_count' => ContentItem::where('course_id', $course->subject_id)->count()
                              ];
                          });

            return response()->json([
                'success' => true,
                'module' => [
                    'module_id' => $module->modules_id,
                    'module_name' => $module->module_name,
                    'module_description' => $module->module_description,
                    'module_order' => $module->module_order,
                    'type' => $module->type ?? 'Standard'
                ],
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching module content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load module content'
            ], 500);
        }
    }

    /**
     * Get course content items for the admin content viewer
     */
    public function getCourseContentItems($moduleId, $courseId)
    {
        try {
            $module = Module::findOrFail($moduleId);
            $course = Course::findOrFail($courseId);
            
            // Get content items for this course
            $contentItems = ContentItem::where('course_id', $courseId)
                                     ->orderBy('content_order', 'asc')
                                     ->get()
                                     ->map(function($item) {
                                         return [
                                             'id' => $item->id,
                                             'content_title' => $item->content_title,
                                             'content_description' => $item->content_description,
                                             'content_type' => $item->content_type,
                                             'content_order' => $item->content_order,
                                             'attachment_path' => $item->attachment_path,
                                             'content_url' => $item->content_url,
                                             'created_at' => $item->created_at->format('M d, Y')
                                         ];
                                     });

            return response()->json([
                'success' => true,
                'module' => [
                    'module_id' => $module->modules_id,
                    'module_name' => $module->module_name
                ],
                'course' => [
                    'course_id' => $course->subject_id,
                    'course_name' => $course->subject_name,
                    'course_description' => $course->subject_description,
                    'course_type' => $course->subject_type ?? 'Standard'
                ],
                'content_items' => $contentItems
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching course content items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load course content'
            ], 500);
        }
    }

    /**
     * Show the course content upload page
     */
    public function showCourseContentUploadPage(Request $request)
    {
        $programs = Program::where('is_archived', false)->get();
        
        return view('admin.admin-modules.course-content-upload', [
            'programs' => $programs
        ]);
    }

    /**
     * Archive content item
     */
    public function archiveContent($id)
    {
        try {
            Log::info("Archive content request received for ID: {$id}");
            
            $content = ContentItem::findOrFail($id);
            Log::info("Content found: {$content->content_title} (ID: {$id})");
            
            // Set archived status
            $content->is_archived = true;
            $content->archived_at = now();
            $content->save();

            Log::info("Content archived successfully: {$content->content_title} (ID: {$id})");

            return response()->json([
                'success' => true,
                'message' => 'Content archived successfully!',
                'content' => $content
            ]);
        } catch (\Exception $e) {
            Log::error('Error archiving content: ' . $e->getMessage(), [
                'content_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error archiving content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore archived content item
     */
    public function restoreContent(Request $request, $id)
    {
        try {
            $content = ContentItem::findOrFail($id);
            
            $content->update([
                'is_archived' => false,
                'archived_at' => null,
                'archived_by_professor_id' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content restored successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error restoring content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk restore archived content for a course
     */
    public function bulkRestoreCourseContent(Request $request, $courseId)
    {
        try {
            $archivedCount = ContentItem::where('course_id', $courseId)
                ->where('is_archived', true)
                ->update([
                    'is_archived' => false,
                    'archived_at' => null,
                    'archived_by_professor_id' => null
                ]);

            return response()->json([
                'success' => true,
                'message' => "Restored {$archivedCount} content items successfully!"
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk restoring content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error restoring content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete archived content
     */
    public function deleteArchivedContent(Request $request, $id)
    {
        try {
            $content = ContentItem::where('id', $id)->where('is_archived', true)->firstOrFail();
            
            // Delete associated files if they exist
            if ($content->attachment_path && Storage::disk('public')->exists($content->attachment_path)) {
                Storage::disk('public')->delete($content->attachment_path);
            }
            
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Content permanently deleted!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting archived content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archived content statistics
     */
    public function getArchivedStats(Request $request)
    {
        try {
            $programId = $request->get('program_id');
            
            $stats = [
                'modules' => 0,
                'content' => 0,
                'by_type' => [
                    'module' => 0,
                    'assignment' => 0,
                    'quiz' => 0,
                    'test' => 0,
                    'link' => 0
                ]
            ];
            
            if ($programId) {
                $stats['modules'] = Module::where('program_id', $programId)
                    ->where('is_archived', true)
                    ->count();
                    
                $stats['content'] = ContentItem::whereHas('course.module', function($query) use ($programId) {
                    $query->where('program_id', $programId);
                })->where('is_archived', true)->count();
                
                $contentByType = ContentItem::whereHas('course.module', function($query) use ($programId) {
                    $query->where('program_id', $programId);
                })->where('is_archived', true)
                  ->selectRaw('content_type, COUNT(*) as count')
                  ->groupBy('content_type')
                  ->get();
            } else {
                $stats['modules'] = Module::where('is_archived', true)->count();
                $stats['content'] = ContentItem::where('is_archived', true)->count();
                
                $contentByType = ContentItem::where('is_archived', true)
                  ->selectRaw('content_type, COUNT(*) as count')
                  ->groupBy('content_type')
                  ->get();
            }
            
            foreach ($contentByType as $type) {
                $stats['by_type'][$type->content_type] = $type->count;
            }
            
            return response()->json(['success' => true, 'stats' => $stats]);
        } catch (\Exception $e) {
            Log::error('Error getting archived stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting statistics'
            ], 500);
        }
    }

    /**
     * Preview mode for tenant preview system - Modules
     */
    public function previewIndex($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Mock data for preview
            $programs = collect([
                (object)[
                    'program_id' => 1,
                    'program_name' => 'Nursing Review'
                ],
                (object)[
                    'program_id' => 2,
                    'program_name' => 'Medical Technology Review'
                ]
            ]);

            $modules = collect([
                (object)[
                    'module_id' => 1,
                    'module_name' => 'Fundamentals of Nursing',
                    'module_description' => 'Basic nursing principles and practices',
                    'is_archived' => false,
                    'program_id' => 1,
                    'program' => $programs->first(),
                    'courses_count' => 5,
                    'content_count' => 12
                ],
                (object)[
                    'module_id' => 2,
                    'module_name' => 'Clinical Chemistry',
                    'module_description' => 'Laboratory analysis and chemistry fundamentals',
                    'is_archived' => false,
                    'program_id' => 2,
                    'program' => $programs->last(),
                    'courses_count' => 4,
                    'content_count' => 8
                ]
            ]);

            $html = view('admin.admin-modules.admin-modules', [
                'modules' => $modules,
                'programs' => $programs,
                'isPreview' => true
            ])->render();

            // Clear session after render
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
            
            
            
            // Generate mock modules data
            $modules = $this->generateMockData('modules');
            view()->share('modules', $modules);
            view()->share('isPreviewMode', true);
            
            return response($html);

        } catch (\Exception $e) {
            Log::error('Admin modules preview error: ' . $e->getMessage());
            return response('Preview mode error: ' . $e->getMessage(), 500);
        }
    }
}
