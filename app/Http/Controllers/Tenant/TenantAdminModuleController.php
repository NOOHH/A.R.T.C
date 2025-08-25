<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Services\TenantService;

class TenantAdminModuleController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of modules for the tenant.
     */
    public function index($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get modules with program information
            $modules = DB::table('modules')
                ->leftJoin('programs', 'modules.program_id', '=', 'programs.program_id')
                ->select('modules.*', 'programs.program_name')
                ->where('modules.is_archived', false)
                ->orderBy('modules.created_at', 'desc')
                ->get();

            // Get programs for dropdown
            $programs = DB::table('programs')
                ->where('is_archived', false)
                ->orderBy('program_name', 'asc')
                ->get();

            // Calculate statistics
            $totalModules = $modules->count();
            $archivedModules = DB::table('modules')->where('is_archived', true)->count();
            $totalCourses = DB::table('courses')->count();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-modules.admin-modules', compact(
                'modules', 
                'programs', 
                'totalModules', 
                'archivedModules', 
                'totalCourses',
                'tenantModel'
            ));

        } catch (\Exception $e) {
            Log::error('Tenant modules index error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.admin-modules.admin-modules', [
                'modules' => collect(),
                'programs' => collect(),
                'totalModules' => 0,
                'archivedModules' => 0,
                'totalCourses' => 0,
                'tenantModel' => null
            ]);
        }
    }

    /**
     * Store a newly created module in tenant database.
     */
    public function store(Request $request, $tenant)
    {
        try {
            $request->validate([
                'module_name' => 'required|string|max:255',
                'module_description' => 'nullable|string',
                'program_id' => 'required|exists:programs,program_id',
                'module_order' => 'nullable|integer|min:1',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Create module
            $moduleId = DB::table('modules')->insertGetId([
                'module_name' => $request->module_name,
                'module_description' => $request->module_description,
                'program_id' => $request->program_id,
                'module_order' => $request->module_order ?? 1,
                'created_by_admin_id' => Auth::user()->admin_id ?? 1,
                'is_archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.modules.index', ['tenant' => $tenant])
                ->with('success', 'Module created successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant module store error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error creating module. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $module = DB::table('modules')->find($id);
            if (!$module) {
                throw new \Exception('Module not found');
            }

            $programs = DB::table('programs')
                ->where('is_archived', false)
                ->orderBy('program_name', 'asc')
                ->get();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-modules.edit', compact('module', 'programs', 'tenantModel'));

        } catch (\Exception $e) {
            Log::error('Tenant module edit error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.modules.index', ['tenant' => $tenant])
                ->with('error', 'Module not found.');
        }
    }

    /**
     * Update the specified module in tenant database.
     */
    public function update(Request $request, $tenant, $id)
    {
        try {
            $request->validate([
                'module_name' => 'required|string|max:255',
                'module_description' => 'nullable|string',
                'program_id' => 'required|exists:programs,program_id',
                'module_order' => 'nullable|integer|min:1',
            ]);

            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Update module
            DB::table('modules')
                ->where('modules_id', $id)
                ->update([
                    'module_name' => $request->module_name,
                    'module_description' => $request->module_description,
                    'program_id' => $request->program_id,
                    'module_order' => $request->module_order ?? 1,
                    'updated_at' => now(),
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.modules.index', ['tenant' => $tenant])
                ->with('success', 'Module updated successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant module update error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->back()
                ->with('error', 'Error updating module. Please try again.')
                ->withInput();
        }
    }

    /**
     * Toggle archive status of the specified module.
     */
    public function toggleArchive($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            $module = DB::table('modules')->find($id);
            if (!$module) {
                throw new \Exception('Module not found');
            }

            // Toggle archive status
            $newStatus = !$module->is_archived;
            DB::table('modules')
                ->where('modules_id', $id)
                ->update([
                    'is_archived' => $newStatus,
                    'updated_at' => now(),
                ]);

            // Switch back to main database
            $this->tenantService->switchToMain();

            $message = $newStatus ? 'Module archived successfully!' : 'Module restored successfully!';
            return redirect()->route('tenant.admin.modules.index', ['tenant' => $tenant])
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Tenant module toggle archive error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.modules.index', ['tenant' => $tenant])
                ->with('error', 'Error updating module status.');
        }
    }

    /**
     * Remove the specified module from tenant database.
     */
    public function destroy($tenant, $id)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Delete module
            DB::table('modules')->where('modules_id', $id)->delete();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.modules.index', ['tenant' => $tenant])
                ->with('success', 'Module deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Tenant module destroy error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return redirect()->route('tenant.admin.modules.index', ['tenant' => $tenant])
                ->with('error', 'Error deleting module.');
        }
    }

    /**
     * Display archived modules for the tenant.
     */
    public function archived($tenant)
    {
        try {
            // Get tenant and switch to tenant database
            $tenantModel = Tenant::where('slug', $tenant)->firstOrFail();
            $this->tenantService->switchToTenant($tenantModel);

            // Get archived modules
            $modules = DB::table('modules')
                ->leftJoin('programs', 'modules.program_id', '=', 'programs.program_id')
                ->select('modules.*', 'programs.program_name')
                ->where('modules.is_archived', true)
                ->orderBy('modules.created_at', 'desc')
                ->get();

            // Switch back to main database
            $this->tenantService->switchToMain();

            return view('admin.admin-modules.archived', compact('modules', 'tenantModel'));

        } catch (\Exception $e) {
            Log::error('Tenant archived modules error: ' . $e->getMessage());

            // Switch back to main database in case of error
            $this->tenantService->switchToMain();

            return view('admin.admin-modules.archived', [
                'modules' => collect(),
                'tenantModel' => null
            ]);
        }
    }
}
