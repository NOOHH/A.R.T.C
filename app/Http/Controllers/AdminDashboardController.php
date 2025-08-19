<?php

namespace App\Http\Controllers;

use App\Models\WebsiteRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['showPreviewDashboard']);
        $this->middleware(function ($request, $next) {
            // Skip for preview mode
            if ($request->boolean('preview', false)) {
                return $next($request);
            }
            
            if (auth()->user()->role !== 'admin' && auth()->user()->email !== 'admin@smartprep.com') {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        })->except(['showPreviewDashboard']);
    }

    public function index()
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

        return view('admin.dashboard', compact('stats', 'recentRequests', 'recentClients'));
    }

    public function websiteRequests()
    {
        $requests = WebsiteRequest::with(['user', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.website-requests', compact('requests'));
    }

    public function approveRequest(Request $request, WebsiteRequest $websiteRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Use the new ClientWebsiteController to create A.R.T.C format website
            $websiteController = new \App\Http\Controllers\ClientWebsiteController();
            $result = $websiteController->createWebsiteFromRequest($websiteRequest);
            
            $data = json_decode($result->getContent(), true);
            
            if ($data['success']) {
                // Update with admin notes
                $websiteRequest->update(['admin_notes' => $request->admin_notes]);
                return back()->with('success', $data['message']);
            } else {
                return back()->with('error', $data['message']);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating website: ' . $e->getMessage());
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
            'approved_by' => auth()->id()
        ]);

        return back()->with('success', 'Website request rejected.');
    }

    public function showPreviewDashboard()
    {
        // Create mock data for preview
        $stats = [
            'total_users' => 1250,
            'total_clients' => 45,
            'pending_requests' => 8,
            'active_websites' => 42,
        ];

        $recentRequests = collect([
            (object) [
                'id' => 1,
                'business_name' => 'Preview Medical Center',
                'user' => (object) ['name' => 'John Doe', 'email' => 'john@preview.com'],
                'status' => 'pending',
                'created_at' => now(),
            ],
            (object) [
                'id' => 2,
                'business_name' => 'Sample Clinic',
                'user' => (object) ['name' => 'Jane Smith', 'email' => 'jane@sample.com'],
                'status' => 'approved',
                'created_at' => now()->subDays(1),
            ]
        ]);

        $recentClients = collect([
            (object) [
                'id' => 1,
                'business_name' => 'Active Medical Practice',
                'user' => (object) ['name' => 'Dr. Wilson', 'email' => 'wilson@medical.com'],
                'status' => 'active',
                'created_at' => now()->subDays(5),
            ]
        ]);

        return view('admin.dashboard', compact('stats', 'recentRequests', 'recentClients'));
    }


}
