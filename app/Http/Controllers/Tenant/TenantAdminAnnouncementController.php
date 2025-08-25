<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Services\TenantService;

class TenantAdminAnnouncementController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of announcements for the tenant.
     */
    public function index($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get announcements
            $announcements = DB::table('announcements')
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate statistics
            $totalAnnouncements = $announcements->count();
            $activeAnnouncements = $announcements->where('is_active', true)->count();
            $urgentAnnouncements = $announcements->where('type', 'urgent')->count();
            $recentAnnouncements = $announcements->where('created_at', '>=', now()->subDays(7))->count();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.announcements.index', compact(
                'announcements', 
                'totalAnnouncements', 
                'activeAnnouncements', 
                'urgentAnnouncements',
                'recentAnnouncements',
                'tenantModel'
            ));

        } catch (\Exception $e) {
            Log::error('Tenant announcements index error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.announcements.index', [
                'announcements' => collect(),
                'totalAnnouncements' => 0,
                'activeAnnouncements' => 0,
                'urgentAnnouncements' => 0,
                'recentAnnouncements' => 0,
                'tenantModel' => null
            ]);
        }
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get programs and batches for targeting
            $programs = DB::table('programs')->orderBy('program_name')->get();
            $batches = DB::table('student_batches')->orderBy('batch_name')->get();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.announcements.create', compact('tenantModel', 'programs', 'batches'))->with('errors', session('errors'));

        } catch (\Exception $e) {
            Log::error('Tenant announcement create error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.announcements.index', ['tenant' => $tenant])
                ->with('error', 'Error loading create form.');
        }
    }

    /**
     * Store a newly created announcement in tenant database.
     */
    public function store(Request $request, $tenant)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'is_active' => 'boolean',
                'type' => 'nullable|in:general,urgent,event,system,video,assignment,quiz',
                'target_scope' => 'nullable|in:all,specific',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Create announcement
            $announcementId = DB::table('announcements')->insertGetId([
                'title' => $request->title,
                'content' => $request->content,
                'is_active' => $request->has('is_active'),
                'type' => $request->type ?? 'general',
                'target_scope' => $request->target_scope ?? 'all',
                'admin_id' => Auth::user()->admin_id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.announcements.index', ['tenant' => $tenant])
                ->with('success', 'Announcement created successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant announcement store error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error creating announcement. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified announcement.
     */
    public function show($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $announcement = DB::table('announcements')->find($id);
            if (!$announcement) {
                throw new \Exception('Announcement not found');
            }

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.announcements.show', compact('announcement', 'tenantModel'));

        } catch (\Exception $e) {
            Log::error('Tenant announcement show error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.announcements.index', ['tenant' => $tenant])
                ->with('error', 'Announcement not found.');
        }
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $announcement = DB::table('announcements')->find($id);
            if (!$announcement) {
                throw new \Exception('Announcement not found');
            }

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.announcements.edit', compact('announcement', 'tenantModel'));

        } catch (\Exception $e) {
            Log::error('Tenant announcement edit error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.announcements.index', ['tenant' => $tenant])
                ->with('error', 'Announcement not found.');
        }
    }

    /**
     * Update the specified announcement in tenant database.
     */
    public function update(Request $request, $tenant, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'is_active' => 'boolean',
                'priority' => 'nullable|in:low,medium,high',
                'target_audience' => 'nullable|string',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update announcement
            DB::table('announcements')
                ->where('id', $id)
                ->update([
                    'title' => $request->title,
                    'content' => $request->content,
                    'is_active' => $request->has('is_active'),
                    'priority' => $request->priority ?? 'medium',
                    'target_audience' => $request->target_audience,
                    'updated_at' => now(),
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.announcements.index', ['tenant' => $tenant])
                ->with('success', 'Announcement updated successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant announcement update error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error updating announcement. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified announcement from tenant database.
     */
    public function destroy($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Delete announcement
            DB::table('announcements')->where('id', $id)->delete();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.announcements.index', ['tenant' => $tenant])
                ->with('success', 'Announcement deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant announcement destroy error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.announcements.index', ['tenant' => $tenant])
                ->with('error', 'Error deleting announcement.');
        }
    }
}
