<?php

namespace App\Http\Controllers\Smartprep\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomizeWebsiteController extends Controller
{
    public function current()
    {
        return view('smartprep.dashboard.customize-website');
    }

    public function old()
    {
        return view('smartprep.dashboard.customize-website-old');
    }

    public function new()
    {
        return view('smartprep.dashboard.customize-website-new');
    }

    public function cacheTest()
    {
        return view('smartprep.dashboard.cache-test');
    }

    public function submitCustomization(Request $request)
    {
        $user = Auth::guard('smartprep')->user();
        
        // Create a website request record
        DB::table('website_requests')->insert([
            'user_id' => $user->id,
            'business_name' => $request->input('business_name', 'Unnamed Business'),
            'business_type' => $request->input('business_type', 'General'),
            'description' => $request->input('description', 'Website customization request'),
            'domain_preference' => $request->input('domain_preference'),
            'contact_email' => $request->input('contact_email', $user->email),
            'contact_phone' => $request->input('contact_phone'),
            'template_data' => $request->input('customization_data'),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('smartprep.dashboard.customize')
            ->with('success', 'Your website customization request has been submitted successfully!');
    }
}
