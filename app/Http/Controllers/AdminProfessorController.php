<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Traits\AdminPreviewCustomization;
use App\Models\Professor;
use App\Models\Program;
use App\Models\StudentBatch;
use App\Models\ClassMeeting;
use App\Http\Controllers\UnifiedLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Log;

class AdminProfessorController extends Controller
{
    use AdminPreviewCustomization;
    public function __construct()
    {
        // Only apply to directors
        $isDirector = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'director')
            || (session('user_type') === 'director');
        if ($isDirector) {
            $canManage = AdminSetting::getValue('director_manage_professors', 'false') === 'true' || AdminSetting::getValue('director_manage_professors', '0') === '1';
            if (!$canManage) {
                abort(403, 'Access denied: You do not have permission to manage professors.');
            }
        }
    }

    public function index()
    {
        $professors = Professor::with('programs')->active()->paginate(10);
        $programs = Program::all();
        
        return view('admin.professors.index', compact('professors', 'programs'));
    }

    public function archived()
    {
        try {
            // Check if this is a preview request
            if (request()->has('preview') && request('preview') === 'true') {
                return $this->previewArchived(request()->segment(3) ?? 'test1');
            }
            
            $professors = Professor::with('programs')->archived()->paginate(10);
            
            return view('admin.professors.archived', compact('professors'));
        } catch (\Exception $e) {
            \Log::error('Archived professors error: ' . $e->getMessage());
            return view('admin.professors.archived', [
                'professors' => collect(),
                'error' => 'Unable to load archived professors data.'
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'referral_code' => 'nullable|string|max:20|unique:professors,referral_code|unique:directors,referral_code',
            'programs' => 'nullable|array',
            'programs.*' => 'exists:programs,program_id'
        ]);

        // Check email uniqueness across all user tables
        if (!UnifiedLoginController::isEmailUnique($request->email)) {
            return back()->withErrors([
                'email' => 'This email address is already registered in the system. Each user must have a unique email across all account types (Admin, Professor, Director, Student).'
            ])->withInput();
        }

        $professor = new Professor();
        $professor->professor_first_name = $request->first_name; // Use correct field name
        $professor->professor_last_name = $request->last_name;   // Use correct field name
        $professor->professor_email = $request->email;
        $professor->professor_password = $request->password; // Store plain text - will be hashed on first login
        $professor->professor_name = $request->first_name . ' ' . $request->last_name; // Set full name
        
        // Get valid admin ID - ensure it exists in admins table
        $adminId = session('user_id');
        if (!$adminId || !\App\Models\Admin::where('admin_id', $adminId)->exists()) {
            // Fallback to first available admin
            $firstAdmin = \App\Models\Admin::first();
            $adminId = $firstAdmin ? $firstAdmin->admin_id : 1;
        }
        $professor->admin_id = $adminId;

        $professor->save();

        // Generate and save referral code after professor is saved (to get the ID)
        if ($request->referral_code) {
            $professor->referral_code = strtoupper($request->referral_code);
        } else {
            $professor->referral_code = \App\Helpers\ReferralCodeGenerator::generateCode(
                $request->first_name, 
                $request->last_name, 
                'professor', 
                $professor->professor_id
            );
        }
        $professor->save();

        // Sync to users table for email uniqueness tracking
        UnifiedLoginController::syncToUsersTable(
            $request->email, 
            $request->first_name . ' ' . $request->last_name, 
            'professor',
            $request->password
        );

        // Assign programs
        if ($request->programs) {
            $professor->programs()->attach($request->programs);
        }

        return redirect()->back()->with('success', 'Professor added successfully! They can now log in using the main login page.');
    }

    public function edit($professor_id)
    {
        $professor = Professor::with(['programs', 'batches'])->findOrFail($professor_id);
        $programs = Program::all();
        
        // Get available batches for assignment (all active batches)
        $batches = \App\Models\StudentBatch::with('program')
            ->where('batch_status', '!=', 'closed')
            ->orderBy('start_date', 'asc')
            ->get();
        
        // Get current batch assignments for this professor (using many-to-many)
        $assignedBatches = $professor->batches()->with('program')->orderBy('start_date', 'asc')->get();
        
        return view('admin.professors.edit', compact('professor', 'programs', 'batches', 'assignedBatches'));
    }

    public function update(Request $request, $professor_id)
    {
        $professor = Professor::findOrFail($professor_id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('professors', 'professor_email')->ignore($professor->professor_id, 'professor_id')],
            'password' => 'nullable|string|min:8',
            'referral_code' => 'nullable|string|max:20|unique:professors,referral_code,' . $professor->professor_id . ',professor_id|unique:directors,referral_code',
            'programs' => 'nullable|array',
            'programs.*' => 'exists:programs,program_id'
        ]);

        // Check email uniqueness across all user tables (exclude current professor)
        if ($request->email !== $professor->professor_email) {
            if (!UnifiedLoginController::isEmailUnique($request->email)) {
                return back()->withErrors([
                    'email' => 'This email address is already registered in the system. Each user must have a unique email across all account types (Admin, Professor, Director, Student).'
                ])->withInput();
            }
        }

        $professor->professor_first_name = $request->first_name;
        $professor->professor_last_name = $request->last_name;
        $professor->professor_email = $request->email;
        $professor->professor_name = $request->first_name . ' ' . $request->last_name; // Update full name
        
        if ($request->password) {
            $professor->professor_password = $request->password; // Store plain text - will be hashed on next login
        }

        // Handle referral code update
        if ($request->referral_code) {
            $professor->referral_code = strtoupper($request->referral_code);
        } elseif (!$professor->referral_code) {
            // Generate if not exists
            $professor->referral_code = \App\Helpers\ReferralCodeGenerator::generateCode(
                $request->first_name, 
                $request->last_name, 
                'professor', 
                $professor->professor_id
            );
        }

        $professor->save();

        // Handle program assignments and batch auto-unassignment
        $oldPrograms = $professor
            ->programs()
            ->pluck('programs.program_id')
            ->toArray();
        
        if ($request->programs) {
            $professor->programs()->sync($request->programs);
            $newPrograms = $request->programs;
        } else {
            $professor->programs()->detach();
            $newPrograms = [];
        }

        // Auto-unassign from batches when unassigned from programs
        $removedPrograms = array_diff($oldPrograms, $newPrograms);
        if (!empty($removedPrograms)) {
            // Get batches associated with removed programs
            $batchesToRemove = \App\Models\StudentBatch::whereIn('program_id', $removedPrograms)->pluck('batch_id')->toArray();
            
            if (!empty($batchesToRemove)) {
                // Remove professor from these batches
                $professor->batches()->detach($batchesToRemove);
            }
        }

        return redirect()->route('admin.professors.index')->with('success', 'Professor updated successfully!');
    }

    public function archive($professor_id)
    {
        $professor = Professor::findOrFail($professor_id);
        $professor->archive();

        return redirect()->back()->with('success', 'Professor archived successfully!');
    }

    public function restore($professor_id)
    {
        $professor = Professor::findOrFail($professor_id);
        $professor->restore();

        return redirect()->back()->with('success', 'Professor restored successfully!');
    }

    public function destroy($professor_id)
    {
        $professor = Professor::findOrFail($professor_id);
        
        // Detach programs
        $professor->programs()->detach();
        
        $professor->delete();

        return redirect()->back()->with('success', 'Professor deleted successfully!');
    }

    public function updateVideoLink(Request $request, $professor_id, $programId)
    {
        $request->validate([
            'video_link' => 'required|url',
            'video_description' => 'nullable|string|max:500'
        ]);

        $professor = Professor::findOrFail($professor_id);
        
        // Update the pivot table
        $professor->programs()->updateExistingPivot($programId, [
            'video_link' => $request->video_link,
            'video_description' => $request->video_description
        ]);

        return response()->json(['success' => true, 'message' => 'Video link updated successfully!']);
    }

    public function assignBatch(Request $request, $professor_id)
    {
        $request->validate([
            'batch_id' => 'required|exists:student_batches,batch_id'
        ]);

        $professor = Professor::findOrFail($professor_id);
        $batch = \App\Models\StudentBatch::findOrFail($request->batch_id);

        // Check if professor is already assigned to this batch
        if ($professor->batches()->where('student_batches.batch_id', $request->batch_id)->exists()) {
            return back()->with('error', 'This professor is already assigned to this batch.');
        }

        // Assign professor to batch using the pivot table
        $professor->batches()->attach($request->batch_id, [
            'assigned_at' => now(),
            'assigned_by' => session('admin_id') ?? session('user_id')
        ]);

        return back()->with('success', "Professor {$professor->professor_name} assigned to batch '{$batch->batch_name}' successfully.");
    }

    public function unassignBatch($professor_id, $batch_id)
    {
        $professor = Professor::findOrFail($professor_id);
        $batch = \App\Models\StudentBatch::findOrFail($batch_id);

        // Check if professor is assigned to this batch
        if (!$professor->batches()->where('batch_id', $batch_id)->exists()) {
            return back()->with('error', 'Professor is not assigned to this batch.');
        }

        // Unassign professor from batch
        $professor->batches()->detach($batch_id);

        return back()->with('success', "Professor {$professor->professor_name} unassigned from batch '{$batch->batch_name}' successfully.");
    }

    public function getProfessorPrograms($professor_id)
    {
        $professor = Professor::with(['programs' => function($query) {
            $query->withPivot(['video_link', 'video_description']);
        }])->findOrFail($professor_id);

        return response()->json([
            'programs' => $professor->programs
        ]);
    }

    /**
     * Get professor's video links for all programs
     */
    public function getVideos($professor_id)
    {
        try {
            $professor = Professor::with(['programs' => function($query) {
                $query->withPivot(['video_link', 'video_description']);
            }])->findOrFail($professor_id);

            $html = view('admin.professors.partials.video-management', compact('professor'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getVideos:', [
                'professor_id' => $professor_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'html' => '<p class="text-danger">Error loading video data.</p>'
            ], 500);
        }
    }

    /**
     * Get professor's batches for meeting creation
     */
    public function getProfessorBatches($professor_id)
    {
        try {
            // Load professor and programs
            $professor = Professor::with('programs')->findOrFail($professor_id);
            
            // Get professor's programs
            $programs = $professor->programs->map(function ($program) {
                return [
                    'program_id' => $program->program_id,
                    'program_name' => $program->program_name,
                    'description' => $program->description
                ];
            });
            
            // Get professor's assigned batches
            $batches = StudentBatch::with('program')
                ->where('professor_id', $professor_id)
                ->get()
                ->map(function ($batch) {
                return [
                    'batch_id' => $batch->batch_id,
                    'batch_name' => $batch->batch_name,
                    'program_id' => $batch->program_id,
                    'program_name' => $batch->program ? $batch->program->program_name : 'Unknown Program',
                    'start_date' => $batch->start_date,
                    'end_date' => $batch->end_date,
                    'batch_status' => $batch->batch_status
                ];
            });

            return response()->json([
                'success' => true,
                'programs' => $programs,
                'batches' => $batches
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getProfessorBatches:', [
                'professor_id' => $professor_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading professor data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a meeting for a professor (admin functionality)
     */
    public function createMeeting(Request $request, $professor_id)
    {
        $request->validate([
            'program_ids' => 'required|array|min:1',
            'program_ids.*' => 'exists:programs,program_id',
            'batch_ids' => 'required|array|min:1',
            'batch_ids.*' => 'exists:student_batches,batch_id',
            'meeting_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_date' => 'required|date|after:now',
            'meeting_link' => 'nullable|url'
        ]);

        $professor = Professor::findOrFail($professor_id);

        // Verify professor has access to the selected batches
        // Get assigned batch IDs, specifying table to avoid ambiguous column
        $professorBatchIds = $professor->batches()->pluck('student_batches.batch_id')->toArray();
        $invalidBatches = array_diff($request->batch_ids, $professorBatchIds);
        
        if (!empty($invalidBatches)) {
            return back()->withErrors(['batch_ids' => 'Some selected batches are not assigned to this professor.']);
        }

        // Create or update meetings for each selected batch
        $createdMeetings = [];
        foreach ($request->batch_ids as $batchId) {
            $meeting = \App\Models\ClassMeeting::where('professor_id', $professor_id)
                ->where('batch_id', $batchId)
                ->where('meeting_date', $request->meeting_date)
                ->where('title', $request->meeting_title)
                ->first();
            if ($meeting) {
                $meeting->update([
                    'meeting_url' => $request->meeting_link,
                    'description' => $request->description,
                    'status' => 'scheduled',
                    'created_by' => session('admin_id') ?? session('user_id')
                ]);
            } else {
                $meeting = \App\Models\ClassMeeting::create([
                    'batch_id' => $batchId,
                    'professor_id' => $professor_id,
                    'title' => $request->meeting_title,
                    'description' => $request->description,
                    'meeting_date' => $request->meeting_date,
                    'meeting_url' => $request->meeting_link,
                    'status' => 'scheduled',
                    'created_by' => session('admin_id') ?? session('user_id')
                ]);
            }
            $createdMeetings[] = $meeting;
        }

        $batchCount = count($request->batch_ids);
        $message = $batchCount === 1 
            ? "Meeting created successfully for {$professor->professor_name}!" 
            : "Meeting created successfully for {$professor->professor_name} across {$batchCount} batches!";

        return back()->with('success', $message);
    }

    /**
     * View professor meetings (admin functionality)
     */
    public function viewMeetings($professor_id)
    {
        $professor = Professor::with([
            'classMeetings' => function($query) {
                $query->with(['batch.program'])->orderBy('meeting_date', 'desc');
            },
            'programs',
            'batches.program'
        ])->findOrFail($professor_id);

        $meetings = $professor->classMeetings->filter(function($meeting) {
            return $meeting->batch !== null;
        });
        
        // Categorize meetings
        $currentMeetings = $meetings->where('status', 'ongoing');
        $todayMeetings = $meetings->filter(function($meeting) {
            return \Carbon\Carbon::parse($meeting->meeting_date)->isToday();
        });
        $upcomingMeetings = $meetings->where('meeting_date', '>', now())->where('status', '!=', 'completed');
        $finishedMeetings = $meetings->where('status', 'completed');

        return view('admin.professors.meetings', compact(
            'professor', 
            'meetings', 
            'currentMeetings', 
            'todayMeetings', 
            'upcomingMeetings', 
            'finishedMeetings'
        ));
    }

    /**
     * Delete a meeting for a professor (admin functionality)
     */
    public function deleteMeeting($professor_id, $meeting_id)
    {
        // Ensure the meeting belongs to the given professor
        $meeting = ClassMeeting::where('professor_id', $professor_id)
            ->where('meeting_id', $meeting_id)
            ->firstOrFail();

        $meeting->delete();

        return back()->with('success', 'Meeting deleted successfully!');
    }

    /**
     * Show professor details
     */
    public function show($id)
    {
        $professor = Professor::with('programs')->findOrFail($id);
        return view('admin.professors.show', compact('professor'));
    }

    /**
     * Show professor profile (for search modal)
     */
    public function showProfile($id)
    {
        $user = \App\Models\User::where('user_id', $id)->where('role', 'professor')->firstOrFail();
        $professor = Professor::where('user_id', $id)->first();
        
        $profile = [
            'id' => $user->user_id,
            'name' => $user->user_firstname . ' ' . $user->user_lastname,
            'email' => $user->email,
            'role' => 'Professor',
            'avatar' => asset('images/default-avatar.png'),
            'status' => $user->is_online ? 'Online' : 'Offline',
            'created_at' => $user->created_at,
            'professor_data' => $professor ? [
                'referral_code' => $professor->referral_code,
                'programs' => $professor->programs->pluck('program_name')->toArray()
            ] : null
        ];

        return response()->json([
            'success' => true,
            'profile' => $profile
        ]);
    }

    /**
     * Preview mode for tenant preview system - Professors
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
                (object)['program_id' => 1, 'program_name' => 'Nursing Review'],
                (object)['program_id' => 2, 'program_name' => 'Medical Technology Review']
            ]);

            $professorsCollection = collect([
                (object)[
                    'professor_id' => 1,
                    'firstname' => 'Dr. Jane',
                    'lastname' => 'Smith',
                    'full_name' => 'Dr. Jane Smith',
                    'email' => 'jane.smith@university.edu',
                    'specialization' => 'Nursing',
                    'is_archived' => false,
                    'created_at' => now(),
                    'programs_count' => 2,
                    'batches_count' => 3,
                    'programs' => collect([$programs->first()])
                ],
                (object)[
                    'professor_id' => 2,
                    'firstname' => 'Dr. Robert',
                    'lastname' => 'Johnson',
                    'full_name' => 'Dr. Robert Johnson',
                    'email' => 'robert.johnson@university.edu',
                    'specialization' => 'Medical Technology',
                    'is_archived' => false,
                    'created_at' => now()->subDays(15),
                    'programs_count' => 1,
                    'batches_count' => 2,
                    'programs' => collect([$programs->last()])
                ]
            ]);

            // Create a mock paginator
            $professors = new \Illuminate\Pagination\LengthAwarePaginator(
                $professorsCollection,
                $professorsCollection->count(),
                10,
                1,
                ['path' => request()->url()]
            );

            $html = view('admin.professors.index', [
                'professors' => $professors,
                'programs' => $programs,
                'isPreview' => true
            ])->render();

            // Clear session after render
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
            
            
            
            // Generate mock professors data
            $professors = $this->generateMockData('professors');
            view()->share('professors', $professors);
            view()->share('isPreviewMode', true);
            
            return response($html);

        } catch (\Exception $e) {
            Log::error('Admin professors preview error: ' . $e->getMessage());
            return response('Preview mode error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Preview archived professors page
     */
    public function previewArchived($tenant)
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

            // Generate mock archived professors
            $mockProfessors = [
                $this->createMockObject([
                    'professor_id' => 1,
                    'id' => 1,
                    'professor_first_name' => 'Dr. Maria',
                    'professor_last_name' => 'Rodriguez',
                    'full_name' => 'Dr. Maria Rodriguez',
                    'professor_email' => 'maria.rodriguez@example.com',
                    'email' => 'maria.rodriguez@example.com',
                    'updated_at' => 'Jan 20, 2024',
                    'professor_archived' => true,
                    'programs' => collect([
                        $this->createMockObject([
                            'program_name' => 'Nursing Review',
                            'program_id' => 1
                        ])
                    ])
                ]),
                $this->createMockObject([
                    'professor_id' => 2,
                    'id' => 2,
                    'professor_first_name' => 'Dr. Carlos',
                    'professor_last_name' => 'Martinez',
                    'full_name' => 'Dr. Carlos Martinez',
                    'professor_email' => 'carlos.martinez@example.com',
                    'email' => 'carlos.martinez@example.com',
                    'updated_at' => 'Feb 15, 2024',
                    'professor_archived' => true,
                    'programs' => collect([
                        $this->createMockObject([
                            'program_name' => 'Medical Technology Review',
                            'program_id' => 2
                        ])
                    ])
                ])
            ];

            // Create a mock paginated collection
            $professors = new \Illuminate\Pagination\LengthAwarePaginator(
                $mockProfessors,
                count($mockProfessors),
                10,
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
            $professors->withQueryString();

            return view('admin.professors.archived', compact('professors'))->with([
                'isPreview' => true,
                'previewTenant' => $tenant
            ]);

        } catch (\Exception $e) {
            Log::error('Archived professors preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>Archived Professors Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Archived Professors Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }
}
