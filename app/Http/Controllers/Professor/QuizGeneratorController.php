<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\AdminSetting;
use App\Models\Module;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use App\Models\Course;
use App\Models\ContentItem;
use App\Services\GeminiQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuizGeneratorController extends Controller
{
    protected $geminiQuizService;

    public function __construct(GeminiQuizService $geminiQuizService)
    {
        $this->middleware('professor.auth');
        $this->geminiQuizService = $geminiQuizService;
    }

    public function index()
    {
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        if (!$aiQuizEnabled) {
            return redirect()->route('professor.dashboard')->with('error', 'AI Quiz Generator is currently disabled.');
        }

        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        $allQuizzes = Quiz::where('professor_id', $professor->professor_id)
                          ->with(['program', 'questions'])
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        $draftQuizzes = $allQuizzes->where('status', 'draft');
        $publishedQuizzes = $allQuizzes->where('status', 'published');
        $archivedQuizzes = $allQuizzes->where('status', 'archived');
        
        return view('Quiz Generator.professor.quiz-generator', compact('assignedPrograms', 'draftQuizzes', 'publishedQuizzes', 'archivedQuizzes'));
    }

    public function getModulesByProgram($programId)
    {
        $modules = Module::where('program_id', $programId)
            ->where('is_archived', false)
            ->orderBy('module_name')
            ->get(['modules_id as module_id', 'module_name']);

        return response()->json(['success' => true, 'modules' => $modules]);
    }

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

    public function generateAIQuestions(Request $request)
    {
        Log::info('=== AI Quiz Generation Request ===', [
            'professor_id' => session('professor_id'),
            'request_data' => $request->except(['file', '_token']),
            'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'No file'
        ]);

        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,csv,txt|max:20480', // 20MB limit
                'num_questions' => 'required|integer|min:1|max:50',
                'question_type' => 'required|in:multiple_choice,true_false,mixed',
            ]);

            if (empty(env('GEMINI_API_KEY')) || env('GEMINI_API_KEY') === 'your_gemini_api_key_here') {
                Log::error('Gemini API key is not configured.');
                return response()->json(['success' => false, 'message' => 'AI Service is not configured. Please contact administrator.'], 500);
            }

            $file = $request->file('file');
            $numQuestions = (int) $request->input('num_questions');
            $quizType = $request->input('question_type');

            if ($quizType === 'mixed') {
                $minMcq = (int) ceil($numQuestions / 2);
                $minTf = (int) floor($numQuestions / 2);
            } else {
                $minMcq = ($quizType === 'multiple_choice') ? $numQuestions : 0;
                $minTf = ($quizType === 'true_false') ? $numQuestions : 0;
            }
            
            $documentPath = $file->store('quiz-documents', 'public');
            $documentResult = $this->geminiQuizService->extractDocumentContent('public/' . $documentPath);

            if (empty($documentResult['content'])) {
                Log::warning('Failed to extract document content', ['error' => $documentResult['error'] ?? 'Unknown error']);
                return response()->json(['success' => false, 'message' => 'Failed to extract content from document.'], 400);
            }

            $quizData = $this->geminiQuizService->generateQuiz($documentResult['content'], $minMcq, $minTf);

            // If Gemini returns an error or no questions, log more details and give user a helpful message
            if (!empty($quizData['error'])) {
                Log::error('Gemini quiz generation failed', [
                    'error' => $quizData['error'],
                    'raw_content' => $quizData['raw_content'] ?? null
                ]);
                $userMessage = 'AI quiz generation failed: ' . $quizData['error'];
                if (str_contains($quizData['error'], 'quality') || str_contains($quizData['error'], 'empty')) {
                    $userMessage .= ' The AI could not generate enough quality questions. Try a smaller document, a more focused file, or fewer questions.';
                }
                return response()->json(['success' => false, 'message' => $userMessage], 500);
            }

            // If the AI returns no questions, log and suggest next steps
            if (empty($generatedQuestions)) {
                Log::warning('AI service returned no questions.', [
                    'quizData' => $quizData
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'The AI could not generate questions from the provided document. Try a different document, a smaller file, or fewer questions.'
                ], 400);
            }

            Log::info('✓ AI questions generated successfully via service', ['count' => count($generatedQuestions)]);

            return response()->json([
                'success' => true,
                'questions' => $generatedQuestions
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed during AI question generation', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Invalid input.', 'errors' => $e->errors()], 422);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('API connection error during AI question generation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Connection to AI service timed out. Please try with a smaller document or fewer questions.'
            ], 504); // Gateway Timeout
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('API request error during AI question generation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Error communicating with AI service: ' . $e->getMessage()
            ], 502); // Bad Gateway
        } catch (\Exception $e) {
            // Check for timeout error specifically
            if (strpos($e->getMessage(), 'time') !== false && strpos($e->getMessage(), 'exceeded') !== false) {
                Log::error('Timeout error in AI question generation', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => 'AI quiz generation timed out. Please try with a smaller document or fewer questions.'
                ], 504);
            }
            
            Log::error('An unexpected error occurred in generateAIQuestions', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'error' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'AI quiz generation failed: ' . $e->getMessage(),
                'error' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function saveQuizWithQuestions(Request $request)
    {
        Log::info('=== Saving AI-Generated Quiz ===', [
            'professor_id' => session('professor_id'),
            'request_data' => $request->except(['questions', '_token'])
        ]);

        try {
            $validatedData = $request->validate([
                'quiz_title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'required|exists:modules,modules_id',
                'course_id' => 'required|exists:courses,subject_id',
                'questions' => 'required|array|min:1',
                'questions.*.question' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answer' => 'required|string',
                'questions.*.explanation' => 'nullable|string',
            ]);

            $professor = Professor::find(session('professor_id'));
            if (!$professor) {
                Log::error('Professor not found for saving quiz', ['session_id' => session('professor_id')]);
                return response()->json(['success' => false, 'message' => 'Professor session not found.'], 401);
            }

            DB::beginTransaction();

            $quiz = Quiz::create([
                'professor_id' => $professor->professor_id,
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'quiz_title' => $validatedData['quiz_title'],
                'quiz_description' => 'AI-generated quiz.',
                'instructions' => 'Please answer the following questions.',
                'status' => 'draft',
                'is_draft' => true,
                'is_active' => false,
                'total_questions' => count($validatedData['questions']),
                'created_at' => now(),
            ]);

            $contentItem = ContentItem::create([
                'content_title' => $validatedData['quiz_title'],
                'content_description' => 'AI Generated Quiz',
                'course_id' => $validatedData['course_id'],
                'content_type' => 'quiz',
                'content_data' => json_encode(['quiz_id' => $quiz->quiz_id]),
                'is_active' => true,
            ]);

            $quiz->update(['content_id' => $contentItem->id]);

            foreach ($validatedData['questions'] as $questionData) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['question_type'],
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $questionData['correct_answer'],
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'generated',
                    'points' => 1,
                    'is_active' => true,
                    'created_by_professor' => $professor->professor_id,
                ]);
            }

            DB::commit();

            Log::info('✓ AI-generated quiz saved successfully', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz saved successfully as a draft.',
                'quiz_id' => $quiz->quiz_id,
                'redirect_url' => route('professor.quiz.generator')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed during quiz save', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Invalid data provided.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An unexpected error occurred while saving the quiz', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'An unexpected server error occurred while saving the quiz.'], 500);
        }
    }
}
