<?php

namespace App\Http\Controllers;

use App\Models\ClassMeeting;
use App\Models\Professor;
use App\Models\Program;
use App\Models\StudentBatch;
use App\Models\AdminSetting;
use App\Models\MeetingAttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfessorMeetingController extends Controller
{
    public function __construct()
    {
        // Apply middleware conditionally - skip for preview requests
        $this->middleware('professor.auth')->except(['previewIndex']);
    }
    
    /**
     * Check if professor can create meetings
     */
    private function canCreateMeetings($professorId)
    {
        // Check whitelist first: whitelisted professors always can create
        $whitelist = AdminSetting::getValue('meeting_whitelist_professors', '');
        if (!empty($whitelist)) {
            $ids = array_filter(explode(',', $whitelist));
            return in_array($professorId, $ids);
        }
        // Otherwise use global toggle
        return AdminSetting::getValue('meeting_creation_enabled', '1') === '1';
    }

    public function index()
    {
        // Check if this is a preview request - handle before any other logic
        if (request()->has('preview') && request('preview') === 'true') {
            return $this->previewIndex();
        }
        
        // Check if this is a tenant preview context
        if (request()->route() && str_contains(request()->route()->getName() ?? '', 'tenant.')) {
            return $this->previewIndex();
        }
        
        $professorId = session('professor_id');
        if (!$professorId) {
            return redirect()->route('professor.login')->withErrors('Session expired. Please log in again.');
        }

        // Check if this is preview mode
        if ($professorId === 'preview-professor') {
            return $this->previewIndex();
        }
        
        // Check for additional preview contexts
        if (request()->has('website') || session('preview_mode')) {
            return $this->previewIndex();
        }

        $professor = Professor::with(['programs'])->findOrFail($professorId);

        // Get batches assigned to this professor
        $professorBatches = StudentBatch::with(['program'])
            ->where('professor_id', $professorId)
            ->get();

        // Get all programs that have batches assigned to this professor
        $programsWithBatches = $professorBatches->pluck('program')->filter()->unique('program_id');
        
        // Combine professor's assigned programs with programs that have his batches
        $allRelevantPrograms = $professor->programs()->get()->merge($programsWithBatches)->filter()->unique('program_id');

        $professorPrograms = collect();
        
        // Organize batches by program
        foreach ($allRelevantPrograms as $program) {
            if (!$program || !$program->program_id) {
                continue; // Skip null programs
            }
            
            $programBatches = $professorBatches->where('program_id', $program->program_id);
            
            if ($programBatches->count() > 0) {
                $program->batches = $programBatches;
                
                foreach ($program->batches as $batch) {
                    // Get all meetings for this batch
                    $allMeetings = ClassMeeting::with(['batch.program'])
                        ->where('professor_id', $professorId)
                        ->where('batch_id', $batch->batch_id)
                        ->orderBy('meeting_date', 'asc')
                        ->get();

                    // Categorize meetings
                    $now = now();
                    $today = $now->startOfDay();
                    $endOfToday = $now->copy()->endOfDay();

                    $batch->currentMeetings = $allMeetings->filter(function ($meeting) {
                        return $meeting->status == 'ongoing';
                    });

                    $batch->todayMeetings = $allMeetings->filter(function ($meeting) use ($today, $endOfToday) {
                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                        return $meetingDate->between($today, $endOfToday) && $meeting->status != 'completed';
                    });

                    $batch->upcomingMeetings = $allMeetings->filter(function ($meeting) use ($endOfToday) {
                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                        return $meetingDate->gt($endOfToday) && $meeting->status == 'scheduled';
                    });

                    $batch->finishedMeetings = $allMeetings->filter(function ($meeting) {
                        return $meeting->status == 'completed';
                    });
                }
                
                $professorPrograms->push($program);
            }
        }

        // Get professor's programs with meeting links for the create form
        $programs = $allRelevantPrograms;

        // Get professor's batches for the create form
        $batches = $professorBatches;

        // Get all meetings for statistics
        $meetings = ClassMeeting::where('professor_id', $professorId)
            ->orderBy('meeting_date', 'desc')
            ->get();

        // Check if professor can create meetings
        $canCreateMeetings = $this->canCreateMeetings($professorId);

        return view('professor.meetings.index', compact('professorPrograms', 'programs', 'batches', 'professor', 'canCreateMeetings', 'meetings'));
    }

    public function store(Request $request)
    {
        $professorId = session('professor_id');
        
        // Check if professor can create meetings
        if (!$this->canCreateMeetings($professorId)) {
            return back()->withErrors(['general' => 'You do not have permission to create meetings. Please contact your administrator.']);
        }

        // Debug: Log the request data
        Log::info('Meeting creation request:', $request->all());
        Log::info('Program IDs:', $request->input('program_ids', []));
        Log::info('Batch IDs:', $request->input('batch_ids', []));
        Log::info('Meeting title:', ['title' => $request->input('meeting_title')]);
        Log::info('Meeting date:', ['date' => $request->input('meeting_date')]);
        Log::info('Meeting link:', ['link' => $request->input('meeting_link')]);
        
        // Additional debugging for form data
        Log::info('Raw POST data:', $_POST);
        Log::info('Request headers:', $request->headers->all());
        
        // Check if arrays are empty
        $programIds = $request->input('program_ids', []);
        $batchIds = $request->input('batch_ids', []);
        
        Log::info('Program IDs count:', ['count' => count($programIds)]);
        Log::info('Batch IDs count:', ['count' => count($batchIds)]);
        
        if (empty($programIds)) {
            Log::error('Program IDs is empty or null');
        }
        if (empty($batchIds)) {
            Log::error('Batch IDs is empty or null');
        }

        $request->validate([
            'meeting_title' => 'required|string|max:255',
            'meeting_date' => 'required|date|after:now',
            'program_ids' => 'required|array|min:1',
            'program_ids.*' => 'exists:programs,program_id',
            'batch_ids' => 'required|array|min:1',
            'batch_ids.*' => 'exists:student_batches,batch_id',
            'meeting_link' => 'required|url',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean'
        ]);

        $professor = Professor::findOrFail($professorId);
        
        // Verify professor has access to all selected batches
        $accessibleBatchIds = StudentBatch::where('professor_id', $professorId)->pluck('batch_id')->toArray();
        $requestedBatchIds = $request->batch_ids;
        
        $invalidBatchIds = array_diff($requestedBatchIds, $accessibleBatchIds);
        if (!empty($invalidBatchIds)) {
            return back()->withErrors(['batch_ids' => 'You do not have access to some of the selected batches.']);
        }

        // Verify that selected batches belong to selected programs
        $selectedBatches = StudentBatch::whereIn('batch_id', $requestedBatchIds)->get();
        $batchProgramIds = $selectedBatches->pluck('program_id')->unique()->toArray();
        $requestedProgramIds = $request->program_ids;
        
        $invalidProgramBatchCombination = array_diff($batchProgramIds, $requestedProgramIds);
        if (!empty($invalidProgramBatchCombination)) {
            return back()->withErrors(['batch_ids' => 'Some selected batches do not belong to the selected programs.']);
        }

        // Create or update meetings for each selected batch
        $createdMeetings = [];
        
        DB::transaction(function () use ($request, $professorId, $requestedBatchIds, &$createdMeetings) {
            foreach ($requestedBatchIds as $batchId) {
                $meeting = \App\Models\ClassMeeting::where('professor_id', $professorId)
                    ->where('batch_id', $batchId)
                    ->where('meeting_date', $request->meeting_date)
                    ->where('title', $request->meeting_title)
                    ->first();
                if ($meeting) {
                    $meeting->update([
                        'meeting_url' => $request->meeting_link,
                        'description' => $request->description,
                        'status' => 'scheduled',
                        'created_by' => auth()->id() ?? 1
                    ]);
                } else {
                    $meeting = \App\Models\ClassMeeting::create([
                        'professor_id' => $professorId,
                        'batch_id' => $batchId,
                        'title' => $request->meeting_title,
                        'meeting_date' => $request->meeting_date,
                        'meeting_url' => $request->meeting_link,
                        'description' => $request->description,
                        'status' => 'scheduled',
                        'created_by' => auth()->id() ?? 1
                    ]);
                }
                $createdMeetings[] = $meeting;
            }
        });

        $batchCount = count($requestedBatchIds);
        $message = $batchCount === 1 
            ? 'Meeting created successfully!' 
            : "Meeting created successfully for {$batchCount} batches!";

        return redirect()->route('professor.meetings')
            ->with('success', $message);
    }

    public function show(ClassMeeting $meeting)
    {
        // Verify professor owns this meeting
        if ($meeting->professor_id !== session('professor_id')) {
            abort(403, 'Unauthorized access to meeting.');
        }

        $meeting->load(['batch.program', 'professor', 'attendanceLogs.student']);

        return view('professor.meetings.show', compact('meeting'));
    }

    public function update(Request $request, ClassMeeting $meeting)
    {
        // Verify professor owns this meeting
        if ($meeting->professor_id !== session('professor_id')) {
            abort(403, 'Unauthorized access to meeting.');
        }

        $request->validate([
            'meeting_title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_link' => 'required|url',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled'
        ]);

        $meeting->update($request->only([
            'meeting_title', 'meeting_date', 'meeting_link', 
            'description', 'status'
        ]));

        return redirect()->route('professor.meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully!');
    }

    public function destroy(ClassMeeting $meeting)
    {
        // Verify professor owns this meeting
        if ($meeting->professor_id !== session('professor_id')) {
            abort(403, 'Unauthorized access to meeting.');
        }

        $meeting->delete();

        return redirect()->route('professor.meetings')
            ->with('success', 'Meeting deleted successfully!');
    }

    public function reports()
    {
        $professorId = session('professor_id');

        // Get meeting statistics
        $totalMeetings = ClassMeeting::where('professor_id', $professorId)->count();
        $completedMeetings = ClassMeeting::where('professor_id', $professorId)
            ->where('status', 'completed')->count();
        $upcomingMeetings = ClassMeeting::where('professor_id', $professorId)
            ->where('meeting_date', '>', now())->count();

        // Get recent meetings with attendance data
        $recentMeetings = ClassMeeting::with(['batch.program', 'attendanceLogs'])
            ->where('professor_id', $professorId)
            ->orderBy('meeting_date', 'desc')
            ->limit(10)
            ->get();

        return view('professor.meetings.reports', compact(
            'totalMeetings', 'completedMeetings', 'upcomingMeetings', 'recentMeetings'
        ));
    }

    public function start(Request $request, ClassMeeting $meeting)
    {
        $professorId = session('professor_id');

        // Handle preview mode
        if ($professorId === 'preview-professor') {
            return response()->json(['success' => true, 'message' => 'Preview mode: Meeting start simulated']);
        }

        // Verify professor owns this meeting
        if ($meeting->professor_id != $professorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Update meeting status and start time
            $meeting->update([
                'status' => 'ongoing',
                'actual_start_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Meeting started successfully',
                'meeting' => $meeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start meeting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function finish(Request $request, ClassMeeting $meeting)
    {
        $professorId = session('professor_id');

        // Handle preview mode
        if ($professorId === 'preview-professor') {
            return response()->json(['success' => true, 'message' => 'Preview mode: Meeting finish simulated']);
        }

        // Verify professor owns this meeting
        if ($meeting->professor_id != $professorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Update meeting status and end time
            $meeting->update([
                'status' => 'completed',
                'actual_end_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Meeting finished successfully',
                'meeting' => $meeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to finish meeting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats(ClassMeeting $meeting)
    {
        $professorId = session('professor_id'); // Consistent with rest of controller

        // Handle preview mode
        if ($professorId === 'preview-professor') {
            return response()->json([
                'success' => true,
                'total_students' => rand(10, 30),
                'joined_students' => rand(5, 25),
                'meeting' => [
                    'status' => 'scheduled',
                    'actual_start_time' => null,
                    'actual_end_time' => null
                ]
            ]);
        }

        // Verify professor owns this meeting
        if ($meeting->professor_id != $professorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Get total students in the batch via enrollments table
            $totalStudents = DB::table('enrollments')
                ->where('batch_id', $meeting->batch_id)
                ->where('enrollment_status', 'approved')
                ->count();
            
            // Get students who have joined the meeting
            $joinedStudents = MeetingAttendanceLog::where('meeting_id', $meeting->meeting_id)
                ->whereNotNull('joined_at')
                ->count();
            
            return response()->json([
                'success' => true,
                'total_students' => $totalStudents,
                'joined_students' => $joinedStudents,
                'meeting' => [
                    'status' => $meeting->status,
                    'actual_start_time' => $meeting->actual_start_time,
                    'actual_end_time' => $meeting->actual_end_time
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get meeting stats: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Preview meetings page for both tenant customization and regular preview mode
     */
    public function previewIndex($tenantSlug = null)
    {
        // Only setup tenant context if we have a tenant slug
        if ($tenantSlug) {
            $this->setupTenantPreviewContext($tenantSlug);
        } else {
            // Regular preview mode - setup session data
            session([
                'user_id' => 'preview-professor',
                'user_name' => 'Dr. Jane Professor',
                'user_role' => 'professor',
                'user_type' => 'professor',
                'professor_id' => 'preview-professor',
                'logged_in' => true
            ]);
        }
        
        // Create mock meetings data
        $meetings = collect([
            (object) [
                'meeting_id' => 1,
                'title' => 'Nursing Fundamentals Review',
                'description' => 'Comprehensive review of nursing fundamentals and core concepts',
                'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
                'scheduled_time' => '10:00:00',
                'meeting_date' => now()->addDays(2),
                'duration' => 120,
                'status' => 'scheduled',
                'batch' => (object) [
                    'batch_name' => 'Batch A - Morning',
                    'program' => (object) ['program_name' => 'Nursing Board Review']
                ],
                'students_count' => 25,
                'meeting_link' => 'https://meet.example.com/nursing-review',
                'meeting_url' => 'https://meet.example.com/nursing-review',
                'created_at' => now()->subDays(5),
                'actual_start_time' => null,
                'actual_end_time' => null,
                'attendance_count' => null
            ],
            (object) [
                'meeting_id' => 2,
                'title' => 'Pharmacology Deep Dive',
                'description' => 'Detailed discussion on drug interactions and dosage calculations',
                'scheduled_date' => now()->addDays(5)->format('Y-m-d'),
                'scheduled_time' => '14:00:00',
                'meeting_date' => now()->addDays(5),
                'duration' => 90,
                'status' => 'scheduled',
                'batch' => (object) [
                    'batch_name' => 'Batch B - Evening',
                    'program' => (object) ['program_name' => 'Nursing Board Review']
                ],
                'students_count' => 18,
                'meeting_link' => 'https://meet.example.com/pharmacology',
                'meeting_url' => 'https://meet.example.com/pharmacology',
                'created_at' => now()->subDays(3),
                'actual_start_time' => null,
                'actual_end_time' => null,
                'attendance_count' => null
            ],
            (object) [
                'meeting_id' => 3,
                'title' => 'Lab Techniques Workshop',
                'description' => 'Hands-on workshop for advanced laboratory procedures',
                'scheduled_date' => now()->subDays(2)->format('Y-m-d'),
                'scheduled_time' => '09:00:00',
                'meeting_date' => now()->subDays(2),
                'duration' => 180,
                'status' => 'completed',
                'batch' => (object) [
                    'batch_name' => 'Med Tech Batch 1',
                    'program' => (object) ['program_name' => 'Medical Technology Review']
                ],
                'students_count' => 15,
                'meeting_link' => 'https://meet.example.com/lab-workshop',
                'meeting_url' => 'https://meet.example.com/lab-workshop',
                'created_at' => now()->subDays(7),
                'actual_start_time' => now()->subDays(2)->addHours(9),
                'actual_end_time' => now()->subDays(2)->addHours(12),
                'attendance_count' => 14
            ]
        ]);
        
        // Create mock programs and batches for meeting creation
        $programs = collect([
            (object) [
                'program_id' => 1,
                'program_name' => 'Nursing Board Review'
            ],
            (object) [
                'program_id' => 2,
                'program_name' => 'Medical Technology Review'
            ]
        ]);
        
        $programsWithBatches = collect([
            (object) [
                'program_id' => 1,
                'program_name' => 'Nursing Board Review',
                'batches' => collect([
                    (object) ['batch_id' => 1, 'batch_name' => 'Batch A - Morning'],
                    (object) ['batch_id' => 2, 'batch_name' => 'Batch B - Evening']
                ])
            ],
            (object) [
                'program_id' => 2,
                'program_name' => 'Medical Technology Review',
                'batches' => collect([
                    (object) ['batch_id' => 3, 'batch_name' => 'Med Tech Batch 1']
                ])
            ]
        ]);
        
        // Create separate batches collection for the view
        $batches = collect([
            (object) [
                'batch_id' => 1, 
                'batch_name' => 'Batch A - Morning', 
                'program_id' => 1,
                'program' => (object) ['program_name' => 'Nursing Board Review']
            ],
            (object) [
                'batch_id' => 2, 
                'batch_name' => 'Batch B - Evening', 
                'program_id' => 1,
                'program' => (object) ['program_name' => 'Nursing Board Review']
            ],
            (object) [
                'batch_id' => 3, 
                'batch_name' => 'Med Tech Batch 1', 
                'program_id' => 2,
                'program' => (object) ['program_name' => 'Medical Technology Review']
            ]
        ]);
        
        // Create professor programs for detailed view
        $professorPrograms = collect([
            (object) [
                'program_id' => 1,
                'program_name' => 'Nursing Board Review',
                'description' => 'Comprehensive nursing board review program',
                'batches' => collect([
                    (object) [
                        'batch_id' => 1, 
                        'batch_name' => 'Batch A - Morning',
                        'students_count' => 25,
                        'meetings_count' => 8
                    ],
                    (object) [
                        'batch_id' => 2, 
                        'batch_name' => 'Batch B - Evening',
                        'students_count' => 18,
                        'meetings_count' => 6
                    ]
                ])
            ],
            (object) [
                'program_id' => 2,
                'program_name' => 'Medical Technology Review',
                'description' => 'Advanced medical technology certification preparation',
                'batches' => collect([
                    (object) [
                        'batch_id' => 3, 
                        'batch_name' => 'Med Tech Batch 1',
                        'students_count' => 15,
                        'meetings_count' => 12
                    ]
                ])
            ]
        ]);
        
        // Calculate statistics
        $upcomingMeetings = $meetings->where('status', 'scheduled')->count();
        $completedMeetings = $meetings->where('status', 'completed')->count();
        $totalStudents = $meetings->sum('students_count');
        
        // Mock feature settings
        $canCreateMeetings = true;
        $meetingCreationEnabled = true;
        
        return view('professor.meetings.index', compact(
            'meetings', 'programs', 'programsWithBatches', 'batches', 'professorPrograms',
            'upcomingMeetings', 'completedMeetings', 'totalStudents', 'canCreateMeetings', 'meetingCreationEnabled'
        ));
    }
    
    /**
     * Setup tenant preview context (common method for all preview methods)
     */
    private function setupTenantPreviewContext($tenantSlug = null)
    {
        // Load tenant settings if provided
        if ($tenantSlug) {
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToMain();
            
            $tenant = \App\Models\Tenant::where('slug', $tenantSlug)->first();
            
            if ($tenant) {
                try {
                    $tenantService->switchToTenant($tenant);
                    
                    // Load settings from tenant database
                    $settings = [
                        'navbar' => [
                            'brand_name' => \App\Models\Setting::get('navbar', 'brand_name', 'Ascendo Review & Training Center'),
                            'brand_logo' => \App\Models\Setting::get('navbar', 'brand_logo', null),
                        ],
                        'professor_panel' => [
                            'brand_name' => \App\Models\Setting::get('professor_panel', 'brand_name', 'Ascendo Review & Training Center'),
                            'brand_logo' => \App\Models\Setting::get('professor_panel', 'brand_logo', null),
                        ],
                    ];
                    
                    $tenantService->switchToMain();
                    
                    // Share settings with the view
                    view()->share('settings', $settings);
                    view()->share('navbar', $settings['navbar'] ?? []);
                    
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to load tenant settings for professor meeting preview', [
                        'tenant' => $tenant->slug,
                        'error' => $e->getMessage()
                    ]);
                    $tenantService->switchToMain();
                }
            }
        }
        
        // Set up session data for preview mode
        session([
            'user_id' => 'preview-professor',
            'user_name' => 'Dr. Jane Professor',
            'user_role' => 'professor',
            'user_type' => 'professor',
            'professor_id' => 'preview-professor',
            'logged_in' => true
        ]);
    }
}
