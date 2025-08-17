<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteRequestsController extends Controller
{
    public function index()
    {
        // Provide empty collections for now
        $requests = collect([]);
        $recentRequests = collect([]);
        
        return view('smartprep.admin.website-requests', compact('requests', 'recentRequests'));
    }
    
    public function approve(Request $request, $requestId)
    {
        // Placeholder for approve functionality
        return redirect()->route('smartprep.admin.website-requests')->with('success', 'Website request approved successfully');
    }
    
    public function reject(Request $request, $requestId)
    {
        // Placeholder for reject functionality
        $request->validate([
            'admin_notes' => 'required|string'
        ]);
        
        return redirect()->route('smartprep.admin.website-requests')->with('success', 'Website request rejected successfully');
    }
}
