<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Provide mock data for admin dashboard
        $stats = [
            'total_users' => 0,
            'active_websites' => 0,
            'pending_requests' => 0,
            'total_clients' => 0,
        ];
        
        $recentRequests = collect([]);
        $recentClients = collect([]);
        
        return view('smartprep.admin.dashboard', compact('stats', 'recentRequests', 'recentClients'));
    }
}
