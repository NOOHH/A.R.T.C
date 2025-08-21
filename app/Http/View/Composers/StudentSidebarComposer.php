<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Http\Traits\StudentProgramsTrait;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class StudentSidebarComposer
{
    use StudentProgramsTrait;

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Only provide student programs if user is authenticated as student
        if (session('user_role') === 'student' && session('user_id')) {
            // Check if this is a preview request and handle database context
            if (Request::has('preview') && Request::get('preview') === 'true' && Request::has('website')) {
                $this->handlePreviewContext();
            }
            
            $studentPrograms = $this->getStudentPrograms();
            $view->with('studentPrograms', $studentPrograms);
        }
    }
    
    /**
     * Handle database context for preview mode
     */
    private function handlePreviewContext()
    {
        try {
            $websiteId = Request::get('website');
            $client = \App\Models\Client::on('mysql')->find($websiteId);
            
            if ($client && $client->db_name) {
                // Switch to tenant database for this request
                $tenantConfig = config('database.connections.mysql');
                $tenantConfig['database'] = $client->db_name;
                config(['database.connections.tenant_preview' => $tenantConfig]);
                config(['database.default' => 'tenant_preview']);
                
                Log::info('StudentSidebarComposer: Switched to tenant database', [
                    'client_id' => $websiteId,
                    'database' => $client->db_name
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('StudentSidebarComposer: Failed to switch database context', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
