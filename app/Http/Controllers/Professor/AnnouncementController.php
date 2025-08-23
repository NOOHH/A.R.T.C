<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Program;
use App\Models\StudentBatch;
use App\Models\Professor;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        // Apply middleware conditionally - skip for preview requests
        $this->middleware('professor.auth')->except(['previewIndex']);
    }
    
    /**
     * Safely get professor with preview mode handling
     */
    private function getProfessorSafely()
    {
        // Check if this is preview mode
        if (session('professor_id') === 'preview-professor') {
            return null; // Signal that this is preview mode
        }
        
        try {
            return Professor::where('professor_id', session('professor_id'))->first();
        } catch (\Exception $e) {
            Log::error('AnnouncementController: Failed to query professor', [
                'professor_id' => session('professor_id'),
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to access professor data. Please try again.');
        }
    }
    
    /**
     * Check if professor announcement management is enabled and if professor is whitelisted
     */
    private function checkAnnouncementPermission()
    {
        try {
            // Use session-based authentication instead of Auth guard
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('ProfessorAnnouncementController: Not authenticated as professor via session');
                abort(403, 'You are not authenticated as a professor.');
            }

            $professorId = session('professor_id');
            Log::info('ProfessorAnnouncementController: Professor authenticated via session', ['professor_id' => $professorId]);

            // Check if feature is globally enabled
            $isEnabled = AdminSetting::getValue('professor_announcement_management_enabled', '0') === '1';
            Log::info('ProfessorAnnouncementController: Announcement management enabled check', ['enabled' => $isEnabled]);

            // Check whitelist
            $whitelist = AdminSetting::getValue('professor_announcement_management_whitelist', '');
            Log::info('ProfessorAnnouncementController: Whitelist check', ['whitelist' => $whitelist]);

            // If feature is globally enabled
            if ($isEnabled) {
                // If whitelist is empty, allow all professors
                if (empty($whitelist) || trim($whitelist) === '') {
                    Log::info('ProfessorAnnouncementController: Feature enabled, whitelist empty - allowing all professors', ['professor_id' => $professorId]);
                    return;
                }
                
                // If whitelist has IDs, check if professor is in whitelist
                $whitelistedIds = array_filter(array_map('trim', explode(',', $whitelist)), function($id) {
                    return !empty($id) && $id !== '';
                });
                
                if (!empty($whitelistedIds) && !in_array((string)$professorId, $whitelistedIds)) {
                    Log::warning('ProfessorAnnouncementController: Feature enabled but professor not in whitelist', [
                        'professor_id' => $professorId,
                        'whitelist' => $whitelistedIds
                    ]);
                    abort(403, 'You are not authorized to manage announcements.');
                }
                
                Log::info('ProfessorAnnouncementController: Feature enabled and professor in whitelist', ['professor_id' => $professorId]);
                return;
            }
            
            // If feature is globally disabled, check if professor is specifically whitelisted
            if (!empty($whitelist) && trim($whitelist) !== '') {
                $whitelistedIds = array_filter(array_map('trim', explode(',', $whitelist)), function($id) {
                    return !empty($id) && $id !== '';
                });
                
                if (!empty($whitelistedIds) && in_array((string)$professorId, $whitelistedIds)) {
                    Log::info('ProfessorAnnouncementController: Feature disabled but professor whitelisted - allowing access', ['professor_id' => $professorId]);
                    return;
                }
            }
            
            // Feature is disabled and professor is not whitelisted
            Log::warning('ProfessorAnnouncementController: Announcement management not enabled and professor not whitelisted');
            abort(403, 'Announcement management is not enabled for professors.');

        } catch (\Exception $e) {
            Log::error('ProfessorAnnouncementController checkAnnouncementPermission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check if professor can create/edit announcements (stricter check)
     */
    private function checkAnnouncementCreationPermission()
    {
        $this->checkAnnouncementPermission(); // This includes the whitelist check
    }

    /**
     * Check if professor can view announcements (always allow viewing)
     */
    private function checkAnnouncementViewPermission()
    {
        try {
            // Always allow viewing - no feature check needed
            // Use session-based authentication
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                abort(403, 'You are not authenticated as a professor.');
            }
        } catch (\Exception $e) {
            Log::error('ProfessorAnnouncementController checkAnnouncementViewPermission error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function index()
    {
        // Check if this is a preview request
        if (request()->has('preview') && request('preview') === 'true') {
            // Extract tenant slug from referrer or current path if available
            $tenantSlug = null;
            $referer = request()->header('referer');
            if ($referer && preg_match('/\/t\/draft\/([^\/]+)\//', $referer, $matches)) {
                $tenantSlug = $matches[1];
            }
            return $this->previewIndex($tenantSlug);
        }
        
        // Check if this is a preview context (session has preview professor)
        if (session('professor_id') === 'preview-professor') {
            // Extract tenant slug from URL path or session
            $tenantSlug = null;
            $path = request()->path();
            if (preg_match('/\/t\/draft\/([^\/]+)\//', $path, $matches)) {
                $tenantSlug = $matches[1];
            }
            return $this->previewIndex($tenantSlug);
        }

        $this->checkAnnouncementViewPermission(); // Allow viewing if authenticated
        
        // Get professor safely with preview mode handling
        try {
            $professor = $this->getProfessorSafely();
        } catch (\Exception $e) {
            return redirect()->route('professor.dashboard')->with('error', $e->getMessage());
        }
        
        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }
        
        // Check if professor can create announcements using the new logic
        $canCreateAnnouncements = false;
        try {
            $this->checkAnnouncementCreationPermission();
            $canCreateAnnouncements = true;
            Log::info('AnnouncementController index: Professor can create announcements', ['professor_id' => $professor->professor_id]);
        } catch (\Exception $e) {
            // Professor can view but not create announcements
            Log::info('AnnouncementController index: Professor cannot create announcements', [
                'professor_id' => $professor->professor_id,
                'error' => $e->getMessage()
            ]);
            $canCreateAnnouncements = false;
        }
        
        // Get announcements created by this professor
        $announcements = Announcement::where('professor_id', $professor->professor_id)
            ->with(['program', 'admin', 'professor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('professor.announcements.index', compact('announcements', 'canCreateAnnouncements'));
    }

    public function create()
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for creation
        
        // Get professor safely with preview mode handling
        try {
            $professor = $this->getProfessorSafely();
        } catch (\Exception $e) {
            return redirect()->route('professor.dashboard')->with('error', $e->getMessage());
        }
        
        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }
        
        // Get only programs assigned to this professor
        $programs = $professor->assignedPrograms()->orderBy('program_name')->get();
        
        // Get batches for the assigned programs
        $programIds = $programs->pluck('program_id')->toArray();
        $batches = StudentBatch::whereIn('program_id', $programIds)
            ->with('program')
            ->orderBy('batch_name')
            ->get();
        
        return view('professor.announcements.create', compact('programs', 'batches'));
    }

    public function store(Request $request)
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for creation
        
        // Get professor safely with preview mode handling
        try {
            $professor = $this->getProfessorSafely();
        } catch (\Exception $e) {
            return redirect()->route('professor.dashboard')->with('error', $e->getMessage());
        }
        
        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:general,urgent,event,system',
            'target_scope' => 'required|in:all,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'in:students', // Only students allowed for professors
            'target_programs' => 'nullable|array',
            'target_programs.*' => 'integer',
            'target_batches' => 'nullable|array',
            'target_batches.*' => 'integer',
            'target_plans' => 'nullable|array',
            'target_plans.*' => 'in:full,modular',
            'publish_date' => 'nullable|date|after_or_equal:today',
            'expire_date' => 'nullable|date|after:publish_date',
            'video_link' => 'nullable|url'
        ]);

        // Validate that selected programs are assigned to this professor
        if ($request->target_programs) {
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            $invalidPrograms = array_diff($request->target_programs, $assignedProgramIds);
            
            if (!empty($invalidPrograms)) {
                return back()->withErrors(['target_programs' => 'You can only target programs assigned to you.']);
            }
        }

        // Validate that selected batches belong to assigned programs
        if ($request->target_batches) {
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            $validBatches = StudentBatch::whereIn('program_id', $assignedProgramIds)
                ->pluck('batch_id')
                ->toArray();
            $invalidBatches = array_diff($request->target_batches, $validBatches);
            
            if (!empty($invalidBatches)) {
                return back()->withErrors(['target_batches' => 'You can only target batches from your assigned programs.']);
            }
        }

        $announcement = new Announcement();
        $announcement->professor_id = $professor->professor_id;
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->description = $request->description;
        $announcement->type = $request->type;
        $announcement->target_scope = $request->target_scope;
        $announcement->video_link = $request->video_link;
        
        // Handle targeting options
        if ($request->target_scope === 'specific') {
            Log::info('Professor creating announcement with specific targeting', [
                'professor_id' => $professor->professor_id,
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
            Log::info('Professor creating announcement with all users targeting', [
                'professor_id' => $professor->professor_id
            ]);
        }
        
        // Handle dates
        $announcement->publish_date = $request->publish_date ? Carbon::parse($request->publish_date) : now();
        $announcement->expire_date = $request->expire_date ? Carbon::parse($request->expire_date) : null;
        
        // Set program_id for compatibility (use first program if specific targeting)
        if ($request->target_programs && count($request->target_programs) > 0) {
            $announcement->program_id = $request->target_programs[0];
        }
        
        $announcement->save();

        return redirect()->route('professor.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function show(Announcement $announcement)
    {
        $this->checkAnnouncementViewPermission(); // Use lenient check for viewing
        // Get professor safely with preview mode handling
        try {
            $professor = $this->getProfessorSafely();
        } catch (\Exception $e) {
            return redirect()->route('professor.dashboard')->with('error', $e->getMessage());
        }
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }
        
        return view('professor.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for editing
        // Get professor safely with preview mode handling
        try {
            $professor = $this->getProfessorSafely();
        } catch (\Exception $e) {
            return redirect()->route('professor.dashboard')->with('error', $e->getMessage());
        }
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }
        
        // Get only programs assigned to this professor
        $programs = $professor->assignedPrograms()->orderBy('program_name')->get();
        
        // Get batches for the assigned programs
        $programIds = $programs->pluck('program_id')->toArray();
        $batches = StudentBatch::whereIn('program_id', $programIds)
            ->with('program')
            ->orderBy('batch_name')
            ->get();
        
        return view('professor.announcements.edit', compact('announcement', 'programs', 'batches'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for updating
        // Get professor safely with preview mode handling
        try {
            $professor = $this->getProfessorSafely();
        } catch (\Exception $e) {
            return redirect()->route('professor.dashboard')->with('error', $e->getMessage());
        }
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:general,urgent,event,system',
            'target_scope' => 'required|in:all,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'in:students', // Only students allowed for professors
            'target_programs' => 'nullable|array',
            'target_programs.*' => 'integer',
            'target_batches' => 'nullable|array',
            'target_batches.*' => 'integer',
            'target_plans' => 'nullable|array',
            'target_plans.*' => 'in:full,modular',
            'publish_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after:publish_date',
            'video_link' => 'nullable|url'
        ]);

        // Validate that selected programs are assigned to this professor
        if ($request->target_programs) {
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            $invalidPrograms = array_diff($request->target_programs, $assignedProgramIds);
            
            if (!empty($invalidPrograms)) {
                return back()->withErrors(['target_programs' => 'You can only target programs assigned to you.']);
            }
        }

        // Validate that selected batches belong to assigned programs
        if ($request->target_batches) {
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            $validBatches = StudentBatch::whereIn('program_id', $assignedProgramIds)
                ->pluck('batch_id')
                ->toArray();
            $invalidBatches = array_diff($request->target_batches, $validBatches);
            
            if (!empty($invalidBatches)) {
                return back()->withErrors(['target_batches' => 'You can only target batches from your assigned programs.']);
            }
        }

        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->description = $request->description;
        $announcement->type = $request->type;
        $announcement->target_scope = $request->target_scope;
        $announcement->video_link = $request->video_link;
        
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
        
        // Set program_id for compatibility (use first program if specific targeting)
        if ($request->target_programs && count($request->target_programs) > 0) {
            $announcement->program_id = $request->target_programs[0];
        }
        
        $announcement->save();

        return redirect()->route('professor.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $this->checkAnnouncementCreationPermission(); // Use stricter check for deletion
        // Get professor safely with preview mode handling
        try {
            $professor = $this->getProfessorSafely();
        } catch (\Exception $e) {
            return redirect()->route('professor.dashboard')->with('error', $e->getMessage());
        }
        
        // Check if this professor owns the announcement
        if ($announcement->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.announcements.index')->with('error', 'Unauthorized access.');
        }
        
        $announcement->delete();

        return redirect()->route('professor.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }
    
    /**
     * Preview announcements page for tenant customization
     */
    public function previewIndex($tenantSlug = null)
    {
        $this->setupTenantPreviewContext($tenantSlug);
        
        // Create mock announcements data with required methods
        $mockAnnouncements = [
            new class {
                public $id = 1;
                public $announcement_id = 1;
                public $title = 'Welcome to the New Semester';
                public $content = 'Welcome everyone to the new semester! We have exciting updates and new course materials ready for you.';
                public $description = 'Welcome announcement for new semester';
                public $announcement_type = 'general';
                public $type = 'general';
                public $priority = 'high';
                public $target_scope = 'general';
                public $target_program_id = null;
                public $target_batch_id = null;
                public $professor_id = 'preview-professor';
                public $is_expired = false;
                public $target_users = [];
                public $target_programs = [];
                public $expire_date;
                public $created_at;
                public $professor;
                public $program;
                public $batch;
                
                public function __construct() {
                    $this->expire_date = now()->addDays(30);
                    $this->created_at = now()->subDays(2);
                    $this->professor = (object) [
                        'professor_first_name' => 'Dr. Jane',
                        'professor_last_name' => 'Professor'
                    ];
                    $this->program = null;
                    $this->batch = null;
                }
                
                public function getCreator() {
                    return $this->professor;
                }
                
                public function getCreatorName() {
                    return 'Dr. Jane Professor';
                }
                
                public function getCreatorAvatar() {
                    return null;
                }
            },
            new class {
                public $id = 2;
                public $announcement_id = 2;
                public $title = 'Nursing Board Review Schedule Update';
                public $content = 'The nursing board review sessions have been updated. Please check the new schedule in your dashboard.';
                public $description = 'Update on nursing board review schedule';
                public $announcement_type = 'schedule';
                public $type = 'schedule';
                public $priority = 'medium';
                public $target_scope = 'program';
                public $target_program_id = 1;
                public $target_batch_id = null;
                public $professor_id = 'preview-professor';
                public $is_expired = false;
                public $target_users = [];
                public $target_programs = [1];
                public $expire_date;
                public $created_at;
                public $professor;
                public $program;
                public $batch;
                
                public function __construct() {
                    $this->expire_date = now()->addDays(7);
                    $this->created_at = now()->subDays(1);
                    $this->professor = (object) [
                        'professor_first_name' => 'Dr. Jane',
                        'professor_last_name' => 'Professor'
                    ];
                    $this->program = (object) ['program_name' => 'Nursing Board Review'];
                    $this->batch = null;
                }
                
                public function getCreator() {
                    return $this->professor;
                }
                
                public function getCreatorName() {
                    return 'Dr. Jane Professor';
                }
                
                public function getCreatorAvatar() {
                    return null;
                }
            },
            new class {
                public $id = 3;
                public $announcement_id = 3;
                public $title = 'Batch A Meeting Tomorrow';
                public $content = 'Reminder: Batch A has a meeting scheduled for tomorrow at 10:00 AM. Please be on time.';
                public $description = 'Meeting reminder for Batch A';
                public $announcement_type = 'meeting';
                public $type = 'meeting';
                public $priority = 'high';
                public $target_scope = 'batch';
                public $target_program_id = 1;
                public $target_batch_id = 1;
                public $professor_id = 'preview-professor';
                public $is_expired = false;
                public $target_users = [];
                public $target_programs = [1];
                public $expire_date;
                public $created_at;
                public $professor;
                public $program;
                public $batch;
                
                public function __construct() {
                    $this->expire_date = now()->addDays(1);
                    $this->created_at = now()->subHours(6);
                    $this->professor = (object) [
                        'professor_first_name' => 'Dr. Jane',
                        'professor_last_name' => 'Professor'
                    ];
                    $this->program = (object) ['program_name' => 'Nursing Board Review'];
                    $this->batch = (object) ['batch_name' => 'Batch A - Morning'];
                }
                
                public function getCreator() {
                    return $this->professor;
                }
                
                public function getCreatorName() {
                    return 'Dr. Jane Professor';
                }
                
                public function getCreatorAvatar() {
                    return null;
                }
            }
        ];
        
        $announcements = collect($mockAnnouncements);
        
        // Create mock programs and batches for announcement creation
        $assignedPrograms = collect([
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
        
        // Calculate statistics
        $totalAnnouncements = $announcements->count();
        $activeAnnouncements = $announcements->where('is_expired', false)->count();
        $highPriorityAnnouncements = $announcements->where('priority', 'high')->count();
        
        // Mock feature settings
        $canCreateAnnouncements = true;
        $announcementManagementEnabled = true;
        
        // Convert to paginated collection to match view expectations
        $perPage = 10;
        $currentPage = 1;
        $total = $announcements->count();
        
        $announcements = new \Illuminate\Pagination\LengthAwarePaginator(
            $announcements->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );
        
        return view('professor.announcements.index', compact(
            'announcements', 'assignedPrograms', 'totalAnnouncements',
            'activeAnnouncements', 'highPriorityAnnouncements', 
            'canCreateAnnouncements', 'announcementManagementEnabled'
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
                    \Illuminate\Support\Facades\Log::warning('Failed to load tenant settings for professor announcement preview', [
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
