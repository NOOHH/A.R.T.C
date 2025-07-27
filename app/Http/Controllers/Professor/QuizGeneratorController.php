<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Student;
use App\Models\Deadline;
use App\Models\Announcement;
use App\Models\AdminSetting;
use App\Models\Module;
use App\Models\Course;
use App\Models\ContentItem;
use App\Models\Program;
use App\Models\QuizAttempt;
use App\Services\GeminiQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class QuizGeneratorController extends Controller
{
    public function __construct()
    {
        $this->middleware('professor.auth');
    }

    public function index()
    {
        // Check if AI Quiz feature is enabled
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        
        if (! $aiQuizEnabled) {
            return redirect()->route('professor.dashboard')->with('error', 'AI Quiz Generator is currently disabled.');
        }

        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        // Fetch quizzes by status for professor
        $draftQuizzes = Quiz::where('professor_id', $professor->professor_id)
                             ->where('status', 'draft')
                             ->with(['program', 'questions'])
                             ->orderBy('created_at', 'desc')
                             ->get();
        $publishedQuizzes = Quiz::where('professor_id', $professor->professor_id)
                                ->where('status', 'published')
                                ->with(['program', 'questions'])
                                ->orderBy('created_at', 'desc')
                                ->get();
        $archivedQuizzes = Quiz::where('professor_id', $professor->professor_id)
                               ->where('status', 'archived')
                               ->with(['program', 'questions'])
                               ->orderBy('created_at', 'desc')
                               ->get();

        return view('Quiz Generator.professor.quiz-generator-overhauled', compact(
            'assignedPrograms',
            'draftQuizzes',
            'publishedQuizzes',
            'archivedQuizzes'
        ));
    }

    // Get modules for selected program (AJAX)
    public function getModulesByProgram($programId)
    {
        Log::info('getModulesByProgram called', ['programId' => $programId, 'professor_id' => session('professor_id'), 'url' => request()->fullUrl()]);
        $modules = Module::where('program_id', $programId)
            ->where('is_archived', false)
            ->orderBy('module_name')
            ->get(['modules_id as module_id', 'module_name']);

        return response()->json(['success' => true, 'modules' => $modules]);
    }

    // Get courses for selected module (AJAX)
    public function getCoursesByModule($moduleId)
    {
        $courses = Course::where('module_id', $moduleId)
            ->where('is_archived', false)
            ->orderBy('subject_name')
            ->get(['subject_id as course_id', 'subject_name as course_name']);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function getContentsByCourse($courseId)
    {
        $contents = ContentItem::where('course_id', $courseId)
                       ->active()
                       ->ordered()
                       ->get(['id as content_id','content_title']);

        return response()->json(['success' => true, 'contents' => $contents]);
    }

    // Alias for route compatibility: singular 'Content'
    public function getContentByCourse($courseId)
    {
        return $this->getContentsByCourse($courseId);
    }

    // Generate quiz via Gemini AI (AJAX)
    public function generate(Request $request)
    {
        Log::info('=== QUIZ GENERATION STARTED (GEMINI) ===', ['professor_id' => session('professor_id'), 'timestamp' => now()->toISOString(), 'request_data' => $request->except(['document','_token']), 'files' => $request->hasFile('document') ? ['filename' => $request->file('document')->getClientOriginalName(),'size' => $request->file('document')->getSize(),'mime' => $request->file('document')->getMimeType()] : 'No document']);
        
        try {
            // Check if AI Quiz feature is enabled
            $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
            
            if (! $aiQuizEnabled) {
                Log::warning('AI Quiz Generator disabled');
                return response()->json(['success' => false, 'message' => 'AI Quiz Generator is currently disabled.'], 403);
            }

            $request->validate([
                'program_id' => 'required|exists:programs,program_id',
                'module_id'  => 'required|exists:modules,modules_id',
                'course_id' => 'required|exists:courses,subject_id',
                'document' => 'nullable|file|mimes:pdf,doc,docx,csv,txt|max:10240',
                'num_questions' => 'required|integer|min:5|max:50',
                'quiz_type' => 'required|in:multiple_choice,true_false,flashcard,mixed',
                'quiz_title' => 'required|string|max:255',
                'quiz_description' => 'nullable|string|max:1000',
                'instructions' => 'nullable|string|max:1000',
                'tags' => 'nullable|string',
                'time_limit' => 'nullable|integer|min:1|max:300',
                'max_attempts' => 'nullable|integer|min:1|max:10',
                'randomize_order' => 'nullable',
                'randomize_mc_options' => 'nullable',
                'allow_retakes' => 'nullable',
                'instant_feedback' => 'nullable',
                'show_correct_answers' => 'nullable',
            ]);

            $professor = Professor::find(session('professor_id'));
            if (! $professor) {
                Log::error('Professor not found', ['session_professor_id' => session('professor_id')]);
                return response()->json(['success' => false, 'message' => 'Professor session not found.'], 401);
            }

            // Program assignment verification
            $programCheck = $professor->programs()->where('programs.program_id', $request->program_id)->exists();
            if (! $programCheck) {
                return response()->json(['success' => false, 'message' => 'You are not assigned to this program.'], 403);
            }

            // Initialize Gemini service and generate questions
            $geminiService = new GeminiQuizService();
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('quiz-documents','public');
                $generatedQuestions = $geminiService->generateQuizFromFile($request->file('document'), ['num_questions' => $request->num_questions,'quiz_type' => $request->quiz_type,'topic' => $request->quiz_title]);
            } else {
                $generatedQuestions = $geminiService->generateQuizFromModule($request->module_id, ['num_questions' => $request->num_questions,'quiz_type' => $request->quiz_type,'topic' => $request->quiz_title]);
            }

            if (empty($generatedQuestions)) {
                return response()->json(['success' => false, 'message' => 'Failed to generate questions. Please try again.'], 500);
            }

            // Create quiz record
            $randomizeOrder = $request->boolean('randomize_order', false);
            $tagsArray = $request->tags ? array_filter(array_map('trim',explode(',',$request->tags))) : [];
            $quiz = Quiz::create([
                'professor_id' => $professor->professor_id,
                'program_id' => $request->program_id,
                'module_id'=> $request->module_id,
                'course_id'=> $request->course_id,
                'quiz_title' => $request->quiz_title,
                'quiz_description'=> $request->quiz_description ?? '',
                'instructions'=> $request->instructions,
                'randomize_order'=> $randomizeOrder,
                'randomize_mc_options'=> $request->boolean('randomize_mc_options',false),
                'tags'=> json_encode($tagsArray),
                'status'=> 'draft',
                'allow_retakes'=> $request->boolean('allow_retakes',false),
                'instant_feedback'=> $request->boolean('instant_feedback',false),
                'show_correct_answers'=> $request->boolean('show_correct_answers',true),
                'max_attempts'=> $request->input('max_attempts',1),
                'total_questions'=> count($generatedQuestions),
                'time_limit'=> $request->input('time_limit',60),
                'document_path'=> $documentPath ?? null,
                'created_at'=> now(),
            ]);

            // Create associated content item
            $contentItem = ContentItem::create([
                'content_title'=> $request->quiz_title,
                'content_description'=> $request->instructions ?? 'AI Generated Quiz',
                'course_id'=> $request->course_id,
                'content_type'=> 'quiz',
                'content_data'=> json_encode(['quiz_id'=>$quiz->quiz_id,'total_questions'=>count($generatedQuestions),'time_limit'=>60,'quiz_type'=> $request->quiz_type]),
                'is_active'=> true,
                'created_at'=> now(),
            ]);
            $quiz->update(['content_id'=> $contentItem->id]);

            // Save each generated question
            $count = 0;
            foreach ($generatedQuestions as $data) {
                QuizQuestion::create([
                    'quiz_id'=> $quiz->quiz_id,
                    'question_text'=> $data['question'],
                    'question_type'=> $data['type'] ?? 'multiple_choice',
                    'options'=> $data['options'],
                    'correct_answer'=> $data['correct_answer'],
                    'explanation'=> $data['explanation'] ?? '',
                    'points'=> $data['points'] ?? 1,
                    'is_active'=> true,
                ]);
                $count++;
            }

            return response()->json(['success'=>true,'message'=>'Quiz generated successfully!','quiz_id'=>$quiz->quiz_id,'questions_count'=>$count]);

        } catch (\Exception $e) {
            Log::error('Quiz generation failed: '.$e->getMessage());
            return response()->json(['success'=>false,'message'=>'Server error during quiz generation.'],500);
        }
    }

    // Additional methods for preview, export, delete, publish, archive, restore, updateQuestions, viewQuestions, getModalQuestions, getQuestionOptions, save, and private helpers...
    // ...existing code...
}