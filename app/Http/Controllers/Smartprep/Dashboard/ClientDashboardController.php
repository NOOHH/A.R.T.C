<?php

namespace App\Http\Controllers\Smartprep\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\WebsiteRequest;
use App\Models\Client;

class ClientDashboardController extends Controller
{
    public function index()
    {
        // If an admin is authenticated, redirect to the admin dashboard
        if (Auth::guard('smartprep_admin')->check()) {
            return redirect()->route('smartprep.admin.dashboard');
        }

        $spUser = Auth::guard('smartprep')->user();

        $websiteRequests = WebsiteRequest::where('user_id', $spUser?->id)->orderByDesc('created_at')->get();
        $activeWebsites = Client::where('user_id', $spUser?->id)
            ->whereIn('status', ['active', 'draft']) // show drafts too for visibility
            ->orderByRaw("FIELD(status,'active','draft')")
            ->latest('created_at')
            ->get();

        return view('smartprep.dashboard.client', [
            'activeWebsites' => $activeWebsites,
            'websiteRequests' => $websiteRequests,
        ]);
    }
}
