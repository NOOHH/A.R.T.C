<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\WebsiteRequest;
use App\Models\Client;
use App\Models\User;

class SmartPrepAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Check if user is authenticated as admin in smartprep database
            if (!Auth::guard('admin')->check()) {
                return redirect()->route('login')->with('error', 'Please log in as admin.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_clients' => Client::count(),
            'pending_requests' => WebsiteRequest::pending()->count(),
            'active_websites' => Client::where('status', 'active')->count(),
        ];

        $recentRequests = WebsiteRequest::with(['user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $recentClients = Client::with(['user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('smartprep.admin.dashboard', compact('stats', 'recentRequests', 'recentClients'));
    }

    public function websiteRequests()
    {
        $requests = WebsiteRequest::with(['user', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('smartprep.admin.website-requests', compact('requests'));
    }

    public function approveRequest(Request $request, WebsiteRequest $websiteRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Update the request status
            $websiteRequest->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::guard('admin')->id()
            ]);

            return back()->with('success', 'Website request approved successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    public function rejectRequest(Request $request, WebsiteRequest $websiteRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $websiteRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'approved_by' => Auth::guard('admin')->id()
        ]);

        return back()->with('success', 'Website request rejected.');
    }

    public function clients()
    {
        $clients = Client::with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('smartprep.admin.clients', compact('clients'));
    }

    public function settings()
    {
        return view('smartprep.admin.settings');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
