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
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin' && auth()->user()->email !== 'admin@smartprep.com') {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
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


}
