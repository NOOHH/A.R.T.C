<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\StudentBatch;
use App\Models\Program;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AdminSetting;

class AdminBatchController extends Controller
{
    public function __construct()
    {
        // Only apply to directors
        $isDirector = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'director')
            || (session('user_type') === 'director');
        if ($isDirector) {
            $canManage = AdminSetting::getValue('director_manage_batches', 'false') === 'true' || AdminSetting::getValue('director_manage_batches', '0') === '1';
            if (!$canManage) {
                abort(403, 'Access denied: You do not have permission to manage batches.');
            }
        }
    }

    /**
     * Display a listing of batches
     */
    public function index()
    {
        $batches = StudentBatch::with(['program', 'creator', 'assignedProfessor'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.admin-student-enrollment.batch-enroll', compact('batches'));
    }

    /**
     * Show the form for creating a new batch
     */
    public function create()
    {
        $programs = Program::where('is_archived', false)->get();
        $professors = \App\Models\Professor::where('is_active', true)->get();
        return view('admin.admin-student-enrollment.create-batch', compact('programs', 'professors'));
    }

    /**
     * Store a newly created batch
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'batch_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_capacity' => 'required|integer|min:1|max:500',
            'batch_status' => 'required|in:available,ongoing,closed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'registration_deadline' => 'required|date',
            'professor_id' => 'nullable|exists:professors,professor_id'
        ]);

        $validated['created_by'] = session('admin_id') ?? session('user_id');

        StudentBatch::create($validated);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch created successfully!');
    }

    /**
     * Display the specified batch
     */
    public function show(StudentBatch $batch)
    {
        $batch->load(['program', 'enrollments.student', 'creator', 'assignedProfessor']);
        
        return view('admin.admin-student-enrollment.show-batch', compact('batch'));
    }

    /**
     * Show the form for editing the specified batch
     */
    public function edit(StudentBatch $batch)
    {
        $programs = Program::where('is_archived', false)->get();
        return view('admin.admin-student-enrollment.edit-batch', compact('batch', 'programs'));
    }

    /**
     * Update the specified batch
     */
    public function update(Request $request, StudentBatch $batch)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'batch_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_capacity' => 'required|integer|min:1|max:500',
            'batch_status' => 'required|in:available,ongoing,closed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'registration_deadline' => 'required|date'
        ]);

        // Don't allow reducing capacity below current enrollment count
        $currentEnrollmentCount = $batch->current_capacity ?? 0;
        if ($validated['max_capacity'] < $currentEnrollmentCount) {
            return back()->withErrors([
                'max_capacity' => "Cannot reduce capacity below current enrollment count ({$currentEnrollmentCount} students)"
            ]);
        }

        $batch->update($validated);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch updated successfully!');
    }

    /**
     * Remove the specified batch
     */
    public function destroy(StudentBatch $batch)
    {
        // Check if there are any enrollments
        if ($batch->enrollments()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete batch with existing enrollments. Please move students to another batch first.'
            ]);
        }

        $batch->delete();

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch deleted successfully!');
    }

    /**
     * Get batches for a specific program (AJAX)
     */
    public function getBatchesForProgram(Request $request, $programId)
    {
        $batches = StudentBatch::where('program_id', $programId)
            ->where('batch_status', '!=', 'closed')
            ->withCount('enrollments')
            ->get()
            ->map(function ($batch) {
                return [
                    'batch_id' => $batch->batch_id,
                    'batch_name' => $batch->batch_name,
                    'batch_status' => $batch->batch_status,
                    'current_enrollment' => $batch->enrollments_count,
                    'max_capacity' => $batch->max_capacity,
                    'status_display' => ucfirst($batch->batch_status),
                    'is_available' => $batch->batch_status === 'available',
                    'registration_deadline' => $batch->registration_deadline ? $batch->registration_deadline->format('M d, Y') : null
                ];
            });

        return response()->json($batches);
    }

    /**
     * Move student to different batch
     */
    public function moveStudent(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,enrollment_id',
            'new_batch_id' => 'required|exists:student_batches,batch_id'
        ]);

        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);
        $newBatch = StudentBatch::findOrFail($validated['new_batch_id']);

        // Check if new batch has capacity
        if ($newBatch->enrollments()->count() >= $newBatch->max_capacity) {
            return back()->withErrors(['error' => 'Target batch is full.']);
        }

        // Check if new batch is available
        if ($newBatch->batch_status !== 'available') {
            return back()->withErrors(['error' => 'Target batch is not available for enrollment.']);
        }

        $enrollment->update(['batch_id' => $validated['new_batch_id']]);

        return back()->with('success', 'Student moved to new batch successfully!');
    }

    /**
     * Toggle batch status
     */
    public function toggleStatus(StudentBatch $batch)
    {
        $statusCycle = [
            'available' => 'ongoing',
            'ongoing' => 'closed',
            'closed' => 'completed',
            'completed' => 'available'
        ];

        $newStatus = $statusCycle[$batch->batch_status] ?? 'available';
        $batch->update(['batch_status' => $newStatus]);

        return back()->with('success', "Batch status changed to: " . ucfirst($newStatus));
    }

    /**
     * Get batch statistics (AJAX)
     */
    public function getStatistics()
    {
        $stats = [
            'total_batches' => StudentBatch::count(),
            'available_batches' => StudentBatch::where('batch_status', 'available')->count(),
            'ongoing_batches' => StudentBatch::where('batch_status', 'ongoing')->count(),
            'completed_batches' => StudentBatch::where('batch_status', 'completed')->count(),
            'total_enrolled_students' => DB::table('enrollments')
                ->whereNotNull('batch_id')
                ->count(),
        ];

        return response()->json($stats);
    }
    
    /**
     * Preview mode for tenant preview system
     */
    public function previewIndex($tenant)
    {
        try {
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Mock batches data
            $batchesCollection = collect([
                (object)[
                    'batch_id' => 1,
                    'batch_name' => 'Nursing Review Batch 2025-A',
                    'batch_code' => 'NUR2025A',
                    'program_id' => 1,
                    'start_date' => now()->format('Y-m-d'),
                    'end_date' => now()->addMonths(6)->format('Y-m-d'),
                    'batch_status' => 'available',
                    'max_students' => 50,
                    'created_at' => now()->subDays(10),
                    'updated_at' => now()->subDays(10),
                    'program' => (object)[
                        'program_id' => 1,
                        'program_name' => 'Nursing Review'
                    ],
                    'creator' => (object)[
                        'firstname' => 'Admin',
                        'lastname' => 'User'
                    ],
                    'assignedProfessor' => (object)[
                        'professor_id' => 1,
                        'firstname' => 'Dr. Maria',
                        'lastname' => 'Santos'
                    ],
                    'enrollments_count' => 25
                ],
                (object)[
                    'batch_id' => 2,
                    'batch_name' => 'Medical Technology Review Batch 2025-A',
                    'batch_code' => 'MEDTECH2025A',
                    'program_id' => 2,
                    'start_date' => now()->addDays(30)->format('Y-m-d'),
                    'end_date' => now()->addMonths(6)->addDays(30)->format('Y-m-d'),
                    'batch_status' => 'ongoing',
                    'max_students' => 40,
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                    'program' => (object)[
                        'program_id' => 2,
                        'program_name' => 'Medical Technology Review'
                    ],
                    'creator' => (object)[
                        'firstname' => 'Tech',
                        'lastname' => 'Admin'
                    ],
                    'assignedProfessor' => (object)[
                        'professor_id' => 2,
                        'firstname' => 'Dr. Juan',
                        'lastname' => 'Garcia'
                    ],
                    'enrollments_count' => 35
                ]
            ]);

            // Create paginator
            $batches = new \Illuminate\Pagination\LengthAwarePaginator(
                $batchesCollection,
                $batchesCollection->count(),
                15,
                1,
                ['path' => request()->url()]
            );

            $html = view('admin.admin-student-enrollment.batch-enroll', [
                'batches' => $batches,
                'isPreview' => true
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin batches preview error: ' . $e->getMessage());
            // Fallback to simple HTML on error
            return response('
                <html>
                    <head><title>Admin Batches Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Admin Batches Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        } finally {
            // Clear session after render
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
        }
    }
}
