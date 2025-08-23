<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AdminPreviewCustomization;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Program;
use App\Models\StudentBatch;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Director;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    use AdminPreviewCustomization;
    public function index()
    {
        $announcements = Announcement::with(['program', 'admin', 'professor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        // Get all programs and batches for targeting, regardless of status
        $programs = Program::orderBy('program_name')->get();
        $batches = StudentBatch::orderBy('batch_name')->get();
        
        return view('admin.announcements.create', compact('programs', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:general,urgent,event,system',
            'target_scope' => 'required|in:all,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'in:students,professors,directors',
            'target_programs' => 'nullable|array',
            'target_programs.*' => 'integer',
            'target_batches' => 'nullable|array',
            'target_batches.*' => 'integer',
            'target_plans' => 'nullable|array',
            'target_plans.*' => 'in:full,modular',
            'publish_date' => 'nullable|date|after_or_equal:today',
            'expire_date' => 'nullable|date|after:publish_date',
            'video_link' => 'nullable|url',
            'is_published' => 'boolean'
        ]);

        $announcement = new Announcement();
        $announcement->admin_id = session('user_id'); // Get admin ID from session
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->description = $request->description;
        $announcement->type = $request->type;
        $announcement->target_scope = $request->target_scope;
        $announcement->video_link = $request->video_link;
        $announcement->is_published = filter_var($request->input('is_published', false), FILTER_VALIDATE_BOOLEAN);
        $announcement->is_active = true;
        
        // Handle targeting options
        if ($request->target_scope === 'specific') {
            Log::info('Creating announcement with specific targeting', [
                'target_users' => $request->target_users,
                'target_programs' => $request->target_programs,
                'target_batches' => $request->target_batches,
                'target_plans' => $request->target_plans
            ]);
            
            $announcement->target_users = $request->target_users ?: null;
            $announcement->target_programs = $request->target_programs ?: null;
            $announcement->target_batches = $request->target_batches ?: null;
            $announcement->target_plans = $request->target_plans ?: null;
        } else {
            Log::info('Creating announcement with all users targeting');
        }
        
        // Handle dates
        $announcement->publish_date = $request->publish_date ? Carbon::parse($request->publish_date) : now();
        $announcement->expire_date = $request->expire_date ? Carbon::parse($request->expire_date) : null;
        
        // Set program_id for compatibility (use first program if specific targeting)
        if ($request->target_programs && count($request->target_programs) > 0) {
            $announcement->program_id = $request->target_programs[0];
        }
        
        $announcement->save();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function show($id)
    {
        try {
            $announcement = Announcement::with(['program'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.announcements.index')
                ->with('error', "Announcement with ID {$id} not found.");
        }
        
        // Get target audience stats
        $stats = $this->getAnnouncementStats($announcement);
        
        return view('admin.announcements.show', compact('announcement', 'stats'));
    }

    public function edit($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.announcements.index')
                ->with('error', "Announcement with ID {$id} not found.");
        }
        
        // Get all programs and batches for targeting, regardless of status
        $programs = Program::orderBy('program_name')->get();
        $batches = StudentBatch::orderBy('batch_name')->get();
        
        return view('admin.announcements.edit', compact('announcement', 'programs', 'batches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:general,urgent,event,system',
            'target_scope' => 'required|in:all,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'in:students,professors,directors',
            'target_programs' => 'nullable|array',
            'target_programs.*' => 'exists:programs,program_id',
            'target_batches' => 'nullable|array',
            'target_batches.*' => 'exists:student_batches,batch_id',
            'target_plans' => 'nullable|array',
            'target_plans.*' => 'in:full,modular',
            'publish_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after:publish_date',
            'video_link' => 'nullable|url',
            'is_published' => 'boolean'
        ]);

        try {
            $announcement = Announcement::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.announcements.index')
                ->with('error', "Announcement with ID {$id} not found.");
        }

        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->description = $request->description;
        $announcement->type = $request->type;
        $announcement->target_scope = $request->target_scope;
        $announcement->video_link = $request->video_link;
        $announcement->is_published = filter_var($request->input('is_published', false), FILTER_VALIDATE_BOOLEAN);
        
        // Handle targeting options
        if ($request->target_scope === 'specific') {
            $announcement->target_users = $request->target_users ?: null;
            $announcement->target_programs = $request->target_programs ?: null;
            $announcement->target_batches = $request->target_batches ?: null;
            $announcement->target_plans = $request->target_plans ?: null;
        } else {
            $announcement->target_users = null;
            $announcement->target_programs = null;
            $announcement->target_batches = null;
            $announcement->target_plans = null;
        }
        
        // Handle dates
        $announcement->publish_date = $request->publish_date ? Carbon::parse($request->publish_date) : $announcement->publish_date;
        $announcement->expire_date = $request->expire_date ? Carbon::parse($request->expire_date) : null;
        
        // Update program_id for compatibility
        if ($request->target_programs && count($request->target_programs) > 0) {
            $announcement->program_id = $request->target_programs[0];
        }
        
        $announcement->save();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->delete();

            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.announcements.index')
                ->with('error', "Announcement with ID {$id} not found.");
        }
    }

    public function toggleStatus($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->is_active = !$announcement->is_active;
            $announcement->save();

            return response()->json([
                'success' => true,
                'message' => 'Announcement status updated successfully!',
                'is_active' => $announcement->is_active
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Announcement with ID {$id} not found."
            ], 404);
        }
    }

    public function togglePublished($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->is_published = !$announcement->is_published;
            $announcement->save();

            return response()->json([
                'success' => true,
                'message' => 'Announcement publish status updated successfully!',
                'is_published' => $announcement->is_published
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Announcement with ID {$id} not found."
            ], 404);
        }
    }

    private function getAnnouncementStats($announcement)
    {
        $stats = [
            'target_students' => 0,
            'target_professors' => 0,
            'target_directors' => 0,
            'target_programs' => [],
            'target_batches' => []
        ];

        if ($announcement->target_scope === 'all') {
            $stats['target_students'] = Student::count();
            $stats['target_professors'] = Professor::count();
            $stats['target_directors'] = Director::count();
            $stats['target_programs'] = Program::where('is_active', true)->pluck('program_name')->toArray();
        } else {
            $targetUsers = $announcement->target_users ? json_decode($announcement->target_users, true) : [];
            $targetPrograms = $announcement->target_programs ? json_decode($announcement->target_programs, true) : [];
            $targetBatches = $announcement->target_batches ? json_decode($announcement->target_batches, true) : [];

            if (in_array('students', $targetUsers)) {
                if (!empty($targetPrograms)) {
                    $stats['target_students'] = Student::whereHas('enrollments', function($q) use ($targetPrograms) {
                        $q->whereIn('program_id', $targetPrograms);
                    })->count();
                } else {
                    $stats['target_students'] = Student::count();
                }
            }

            if (in_array('professors', $targetUsers)) {
                $stats['target_professors'] = Professor::count();
            }

            if (in_array('directors', $targetUsers)) {
                $stats['target_directors'] = Director::count();
            }

            if (!empty($targetPrograms)) {
                $stats['target_programs'] = Program::whereIn('program_id', $targetPrograms)->pluck('program_name')->toArray();
            }

            if (!empty($targetBatches)) {
                $stats['target_batches'] = StudentBatch::whereIn('batch_id', $targetBatches)->pluck('batch_name')->toArray();
            }
        }

        return $stats;
    }
    
    /**
     * Preview mode for tenant preview system
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

            // Create mock announcement objects that behave like real Announcement models
            $mockAnnouncements = collect([
                $this->createMockAnnouncement([
                    'announcement_id' => 1,
                    'title' => 'Welcome to the New Academic Year',
                    'content' => 'We are excited to announce the start of the new academic year. This year brings new opportunities, courses, and exciting developments for all our students and faculty members.',
                    'description' => 'Important information for all students and faculty',
                    'type' => 'general',
                    'target_scope' => 'all',
                    'is_published' => true,
                    'admin_id' => 1,
                    'professor_id' => null,
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ]),
                $this->createMockAnnouncement([
                    'announcement_id' => 2,
                    'title' => 'System Maintenance Scheduled',
                    'content' => 'Please be advised that system maintenance is scheduled for this weekend from 2:00 AM to 6:00 AM EST. During this time, the platform may be temporarily unavailable.',
                    'description' => 'Temporary service interruption notice',
                    'type' => 'system',
                    'target_scope' => 'all',
                    'is_published' => true,
                    'admin_id' => 2,
                    'professor_id' => null,
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                ]),
                $this->createMockAnnouncement([
                    'announcement_id' => 3,
                    'title' => 'New Course Materials Available',
                    'content' => 'New study materials and practice exams are now available in the student portal. Please check your enrolled courses for the latest resources.',
                    'description' => 'Course updates and new materials',
                    'type' => 'event',
                    'target_scope' => 'specific',
                    'is_published' => true,
                    'admin_id' => null,
                    'professor_id' => 1,
                    'created_at' => now()->subDays(1),
                    'updated_at' => now()->subDays(1),
                ])
            ]);

            // Create paginator
            $announcements = new \Illuminate\Pagination\LengthAwarePaginator(
                $mockAnnouncements,
                $mockAnnouncements->count(),
                15,
                1,
                ['path' => request()->url()]
            );

            $html = view('admin.announcements.index', [
                'announcements' => $announcements,
                'isPreview' => true
            ])->render();

            
            
            // Generate mock announcements data 
            $announcements = $this->generateMockData('announcements');
            view()->share('announcements', $announcements);
            view()->share('isPreviewMode', true);
            
            return response($html);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin announcements preview error: ' . $e->getMessage());
            // Fallback to simple HTML on error
            return response('
                <html>
                    <head><title>Admin Announcements Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Admin Announcements Preview - Tenant: '.$tenant.'</h1>
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

    /**
     * Create a mock announcement object with all required methods
     */
    private function createMockAnnouncement($data)
    {
        // Create a mock announcement instance
        $mockAnnouncement = new class extends Announcement {
            public $mockData = [];
            
            public function __construct($data = []) {
                $this->mockData = $data;
                // Set properties directly
                foreach ($data as $key => $value) {
                    $this->$key = $value;
                }
            }
            
            public function getCreator() {
                if ($this->professor_id) {
                    return (object)[
                        'professor_id' => $this->professor_id,
                        'professor_name' => 'Dr. Preview Professor',
                        'professor_first_name' => 'Dr. Preview',
                        'professor_last_name' => 'Professor',
                        'professor_email' => 'professor@preview.com',
                        'email' => 'professor@preview.com',
                        'avatar' => null
                    ];
                } elseif ($this->admin_id) {
                    return (object)[
                        'admin_id' => $this->admin_id,
                        'admin_name' => 'Preview Admin',
                        'first_name' => 'Preview',
                        'last_name' => 'Admin',
                        'email' => 'admin@preview.com',
                        'avatar' => null
                    ];
                }
                return null;
            }
            
            public function getCreatorName() {
                $creator = $this->getCreator();
                if (!$creator) return 'Unknown';
                
                if ($this->professor_id) {
                    return $creator->professor_name ?? 'Dr. Preview Professor';
                } else {
                    return $creator->admin_name ?? 'Preview Admin';
                }
            }
            
            public function getCreatorAvatar() {
                return null; // No avatars in preview mode
            }
            
            // Mock relationship methods
            public function admin() {
                return $this->getCreator();
            }
            
            public function professor() {
                return $this->getCreator();
            }
            
            public function program() {
                return null; // No program relationships in preview
            }
        };
        
        return new $mockAnnouncement($data);
    }

    /**
     * Preview show method for tenant announcement viewing
     */
    public function previewShow($tenant, $id)
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

            // Create mock announcement based on ID
            $announcement = $this->createMockAnnouncement([
                'announcement_id' => $id,
                'title' => 'Sample Announcement #' . $id,
                'content' => 'This is a detailed view of announcement #' . $id . '. In a real scenario, this would show the actual announcement content with all formatting, attachments, and metadata.',
                'description' => 'Preview description for announcement #' . $id,
                'type' => 'general',
                'target_scope' => 'all',
                'is_published' => true,
                'admin_id' => 1,
                'professor_id' => null,
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now()->subDays(rand(1, 5)),
            ]);

            // Create mock stats for the template
            $stats = [
                'target_students' => 125,
                'target_professors' => 15,
                'target_directors' => 3,
                'target_programs' => ['Computer Science', 'Business Administration'],
                'target_batches' => ['Batch 2024-A', 'Batch 2024-B']
            ];

            $html = view('admin.announcements.show', [
                'announcement' => $announcement,
                'stats' => $stats,
                'isPreview' => true
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin announcement show preview error: ' . $e->getMessage());
            // Fallback to simple HTML on error
            return response('
                <html>
                    <head><title>View Announcement Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>View Announcement Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>Announcement ID: '.$id.'</p>
                        <a href="/t/draft/'.$tenant.'/admin/announcements">← Back to Announcements</a>
                    </body>
                </html>
            ', 200);
        } finally {
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
        }
    }

    /**
     * Preview edit method for tenant announcement editing
     */
    public function previewEdit($tenant, $id)
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

            // Create mock announcement for editing
            $announcement = $this->createMockAnnouncement([
                'announcement_id' => $id,
                'title' => 'Sample Announcement #' . $id,
                'content' => 'This is the editable content of announcement #' . $id . '. In edit mode, you would be able to modify all aspects of this announcement.',
                'description' => 'Preview description for announcement #' . $id,
                'type' => 'general',
                'target_scope' => 'all',
                'is_published' => true,
                'admin_id' => 1,
                'professor_id' => null,
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now()->subDays(rand(1, 5)),
            ]);

            // Mock programs and batches for the form
            $programs = collect([
                (object)['program_id' => 1, 'program_name' => 'Computer Science'],
                (object)['program_id' => 2, 'program_name' => 'Business Administration'],
                (object)['program_id' => 3, 'program_name' => 'Engineering'],
            ]);

            $batches = collect([
                (object)['batch_id' => 1, 'batch_name' => 'Batch 2024-A'],
                (object)['batch_id' => 2, 'batch_name' => 'Batch 2024-B'],
                (object)['batch_id' => 3, 'batch_name' => 'Batch 2025-A'],
            ]);

            $html = view('admin.announcements.edit', [
                'announcement' => $announcement,
                'programs' => $programs,
                'batches' => $batches,
                'isPreview' => true
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin announcement edit preview error: ' . $e->getMessage());
            // Fallback to simple HTML on error
            return response('
                <html>
                    <head><title>Edit Announcement Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Edit Announcement Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>Announcement ID: '.$id.'</p>
                        <a href="/t/draft/'.$tenant.'/admin/announcements">← Back to Announcements</a>
                    </body>
                </html>
            ', 200);
        } finally {
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
        }
    }

    /**
     * Preview create method for tenant announcement creation
     */
    public function previewCreate($tenant)
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

            // Mock programs and batches for the form
            $programs = collect([
                (object)['program_id' => 1, 'program_name' => 'Computer Science'],
                (object)['program_id' => 2, 'program_name' => 'Business Administration'],
                (object)['program_id' => 3, 'program_name' => 'Engineering'],
            ]);

            $batches = collect([
                (object)['batch_id' => 1, 'batch_name' => 'Batch 2024-A'],
                (object)['batch_id' => 2, 'batch_name' => 'Batch 2024-B'],
                (object)['batch_id' => 3, 'batch_name' => 'Batch 2025-A'],
            ]);

            $html = view('admin.announcements.create', [
                'programs' => $programs,
                'batches' => $batches,
                'isPreview' => true
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin announcement create preview error: ' . $e->getMessage());
            // Fallback to simple HTML on error
            return response('
                <html>
                    <head><title>Create Announcement Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Create Announcement Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <a href="/t/draft/'.$tenant.'/admin/announcements">← Back to Announcements</a>
                    </body>
                </html>
            ', 200);
        } finally {
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
        }
    }
}
