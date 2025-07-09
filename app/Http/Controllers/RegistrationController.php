<?php

namespace App\Http\Controllers;

use App\Helpers\SessionManager;
use App\Models\User;
use App\Models\Student;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    protected $ocrService;

    public function __construct(OcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    public function showForm(Request $request)
    {
        $data = $request->only(['course', 'package_id', 'enrollment_type']);
        
        // Add user data if logged in
        if (SessionManager::isLoggedIn()) {
            $userId = SessionManager::get('user_id');
            $user = User::with('student')->find($userId);
            if ($user) {
                $data['user'] = $user;
                $data['student'] = $user->student;
            }
        }
        
        return view('registration.form', $data);
    }

    public function loadUserData(Request $request)
    {
        if (!SessionManager::isLoggedIn()) {
            return response()->json(['error' => 'Not logged in'], 401);
        }

        $userId = SessionManager::get('user_id');
        $user = User::with('student')->find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'user' => $user,
            'student' => $user->student
        ]);
    }

    public function validateDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'documentType' => 'required|string|in:course_cert,tor,cert_grad'
        ]);

        $file = $request->file('document');
        $path = $file->store('temp_documents');
        
        // Extract text from document
        $extractedText = $this->ocrService->extractText(storage_path('app/' . $path));
        
        // Validate name
        $nameValid = $this->ocrService->validateName(
            $request->firstName,
            $request->lastName,
            $extractedText
        );

        if (!$nameValid) {
            return response()->json([
                'error' => 'Name validation failed',
                'message' => 'The uploaded document does not contain your full name. Please check and upload a document with your complete name.'
            ], 400);
        }

        // Get program suggestions
        $suggestions = $this->ocrService->suggestPrograms($extractedText);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    public function getBatchesForProgram($programId)
    {
        $batches = \App\Models\StudentBatch::where('program_id', $programId)
            ->where(function($query) {
                $query->where('batch_status', 'available')
                      ->orWhere(function($q) {
                          $q->where('batch_status', 'ongoing')
                            ->whereRaw('current_capacity < max_capacity');
                      });
            })
            ->where('registration_deadline', '>=', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($batches);
    }

    public function saveBatchEnrollment(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'batch_id' => 'required|exists:student_batches,batch_id',
        ]);

        $batch = \App\Models\StudentBatch::find($request->batch_id);

        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        // Check if batch is available and has capacity
        if ($batch->batch_status === 'closed' || $batch->current_capacity >= $batch->max_capacity) {
            return response()->json(['error' => 'Batch is not available for enrollment'], 400);
        }

        // Increment student count
        $batch->increment('current_capacity');

        return response()->json(['success' => true]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'course' => 'required|string',
            'package_id' => 'required|exists:packages,package_id',
            'enrollment_type' => 'required|in:Modular,Full',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'documents.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        // Save user (account creation)
        $user = new User();
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->user_firstname = $validated['firstname'];
        $user->user_lastname = $validated['lastname'];
        $user->role = 'student';
        $user->save();

        // Save enrollment
        $enrollment = new \App\Models\Enrollment();
        if ($validated['enrollment_type'] === 'Modular') {
            $enrollment->Modular_enrollment = $validated['course'];
            $enrollment->Full_Program = '';
        } else {
            $enrollment->Modular_enrollment = '';
            $enrollment->Full_Program = $validated['course'];
        }
        $enrollment->package_id = $validated['package_id'];
        $enrollment->save();

        // Optionally link user and enrollment (if your schema supports it)
        $user->enrollment_id = $enrollment->enrollment_id;
        $user->save();

        // Redirect to a success page or dashboard
        return redirect()->route('home')->with('success', 'Registration and enrollment successful!');
    }

    public function showAccountForm(Request $request)
    {
        // Store enrollment selection in session
        session([
            'enrollment.course' => $request->course,
            'enrollment.package_id' => $request->package_id,
            'enrollment.enrollment_type' => $request->enrollment_type,
        ]);
        return view('registration.account');
    }

    public function showDetailsForm(Request $request)
    {
        // Save account info in session
        session([
            'enrollment.firstname' => $request->firstname,
            'enrollment.lastname' => $request->lastname,
            'enrollment.email' => $request->email,
            'enrollment.password' => bcrypt($request->password),
        ]);
        return view('registration.details');
    }

    public function submit(Request $request)
    {
        // Save all registration info in session
        session(['enrollment' => array_merge((array)session('enrollment', []), $request->all())]);
        $data = session('enrollment');

        // Save user
        $user = new \App\Models\User();
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->user_firstname = $data['firstname'];
        $user->user_lastname = $data['lastname'];
        $user->role = 'student';
        $user->save();

        // Save enrollment
        $enrollment = new \App\Models\Enrollment();
        if ($data['enrollment_type'] === 'Modular') {
            $enrollment->Modular_enrollment = $data['course'];
            $enrollment->Full_Program = '';
        } else {
            $enrollment->Modular_enrollment = '';
            $enrollment->Full_Program = $data['course'];
        }
        $enrollment->package_id = $data['package_id'];
        $enrollment->save();

        // Link user and enrollment
        $user->enrollment_id = $enrollment->enrollment_id;
        $user->save();

        // Clear session
        session()->forget('enrollment');

        return redirect()->route('home')->with('success', 'Registration and enrollment successful!');
    }

    public function validateStep3(Request $request)
    {
        $rules = [
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'enrollment_type' => 'required|in:Full,Modular'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Step 3 validation passed'
        ]);
    }

    public function validateStep4(Request $request)
    {
        $rules = [
            'First_Name' => 'required|string|max:255',
            'Last_Name' => 'required|string|max:255',
            'program_id' => 'nullable',  // Will be filled automatically
            'start_date' => 'nullable',  // Will be filled automatically
            'batch_id' => 'nullable'
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Step 4 validation passed',
            'data' => $validator->validated()
        ]);
    }
    
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            // Create user
            $user = new User();
            $user->firstname = $request->user_firstname;
            $user->lastname = $request->user_lastname;
            $user->email = $request->user_email;
            $user->password = bcrypt($request->password);
            $user->user_type = 'student';
            $user->save();

            // Create student registration
            $student = new Student();
            $student->user_id = $user->id;
            $student->First_Name = $request->First_Name;
            $student->Last_Name = $request->Last_Name;
            $student->program_id = $request->program_id ?? null;
            $student->start_date = $request->start_date ?? now();
            $student->batch_id = $request->batch_id;
            $student->save();

            DB::commit();

            // Log the user in
            SessionManager::set('user_id', $user->id);
            SessionManager::set('user_type', 'student');
            SessionManager::set('user_name', $user->firstname . ' ' . $user->lastname);
            SessionManager::set('user_email', $user->email);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'redirect' => '/student/dashboard'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
