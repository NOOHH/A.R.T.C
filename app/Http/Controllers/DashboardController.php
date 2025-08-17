<?php

namespace App\Http\Controllers;

use App\Models\WebsiteRequest;
use App\Models\Client;
use App\Models\UiSetting;
use App\Helpers\TemplateCustomizationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Check if user is admin
        if ($user->email === 'admin@smartprep.com' || $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Regular user dashboard
        $websiteRequests = WebsiteRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeWebsites = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        return view('dashboard.client', compact('websiteRequests', 'activeWebsites'));
    }

    public function requestWebsite(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'domain_preference' => 'nullable|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        WebsiteRequest::create([
            'user_id' => Auth::id(),
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'description' => $request->description,
            'domain_preference' => $request->domain_preference,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Website request submitted successfully! We will review your request and get back to you soon.');
    }

    public function submitCustomizedWebsite(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'domain_preference' => 'nullable|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'customization_data' => 'nullable|string',
        ]);

        WebsiteRequest::create([
            'user_id' => Auth::id(),
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'description' => $request->description,
            'domain_preference' => $request->domain_preference,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'template_data' => $request->customization_data,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Customized website request submitted successfully! Your customizations will be applied to the website once approved by our admin.');
    }

    public function customizeWebsite(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if ($user->email === 'admin@smartprep.com' || $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Get user's active websites (for potential future use)
        $activeWebsites = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        // For customization before request, we don't need an existing website
        // Create a virtual website object for the customization interface
        $selectedWebsite = null; // We'll handle this in the view
        
        // Get default settings sections for customization (similar to admin settings)
        $sections = [
            'homepage' => $this->getDefaultHomepageSettings(),
            'navbar' => $this->getDefaultNavbarSettings(), 
            'footer' => $this->getDefaultFooterSettings(),
            'buttons' => $this->getDefaultButtonSettings(),
            'login' => $this->getDefaultLoginSettings(),
            'program_cards' => $this->getDefaultProgramCardSettings(),
            'enrollment' => $this->getDefaultEnrollmentSettings(),
            'student_portal' => $this->getDefaultStudentPortalSettings(),
        ];

        return view('dashboard.customize-website', compact('activeWebsites', 'selectedWebsite', 'sections'));
    }

    public function updateWebsiteSettings(Request $request, $section)
    {
        $user = Auth::user();
        
        // Check if user has permission to customize websites
        $activeWebsites = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        // For customization before website creation, we'll store the data temporarily
        // Since there's no active website yet, we'll just validate and return success
        
        // Handle different sections
        switch ($section) {
            case 'general':
                return $this->updateGeneralSettings($request);
            case 'branding':
                return $this->updateBrandingSettings($request);
            case 'navbar':
                return $this->updateNavbarSettings($request);
            case 'homepage':
                return $this->updateHomepageSettings($request);
            case 'student':
                return $this->updateStudentSettings($request);
            case 'professor':
                return $this->updateProfessorSettings($request);
            case 'admin':
                return $this->updateAdminSettings($request);
            case 'advanced':
                return $this->updateAdvancedSettings($request);
            default:
                return response()->json(['success' => false, 'error' => 'Invalid section.'], 400);
        }
    }

    private function updateGeneralSettings(Request $request)
    {
        // Store general settings in ui_settings table for preview
        $request->validate([
            'site_title' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        // Save each field to ui_settings for preview
        foreach (['site_title', 'tagline', 'contact_email', 'phone', 'address'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('general', $key, (string) $request->$key, 'text');
            }
        }
        
        return response()->json(['success' => true, 'message' => 'General settings updated successfully!']);
    }

    private function updateBrandingSettings(Request $request)
    {
        // Store branding settings in ui_settings table
        $request->validate([
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'background_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'font_family' => 'nullable|string|max:50',
        ]);
        
        // Save color fields
        foreach (['primary_color', 'secondary_color', 'background_color', 'font_family'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('branding', $key, (string) $request->$key, str_contains($key, 'color') ? 'color' : 'text');
            }
        }
        
        // Handle file uploads
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('customization/logos', 'public');
            UiSetting::set('branding', 'logo', $path, 'file');
        }
        
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('customization/favicons', 'public');
            UiSetting::set('branding', 'favicon', $path, 'file');
        }
        
        return response()->json(['success' => true, 'message' => 'Branding settings updated successfully!']);
    }
    
    private function updateNavbarSettings(Request $request)
    {
        // Store navbar settings
        $request->validate([
            'navbar_background' => 'nullable|string|max:7',
            'navbar_text_color' => 'nullable|string|max:7',
            'navbar_brand_color' => 'nullable|string|max:7',
            'navbar_hover_color' => 'nullable|string|max:7',
        ]);
        
        foreach (['navbar_background', 'navbar_text_color', 'navbar_brand_color', 'navbar_hover_color'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('navbar', $key, (string) $request->$key, 'color');
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Navigation settings updated successfully!']);
    }
    
    private function updateHomepageSettings(Request $request)
    {
        // Store homepage settings
        $request->validate([
            'background_color' => 'nullable|string|max:7',
            'gradient_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);
        
        foreach (['background_color', 'gradient_color', 'text_color'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('homepage', $key, (string) $request->$key, 'color');
            }
        }
        
        foreach (['title', 'subtitle'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('homepage', $key, (string) $request->$key, 'text');
            }
        }
        
        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('customization/homepage', 'public');
            UiSetting::set('homepage', 'background_image', $path, 'file');
        }
        
        return response()->json(['success' => true, 'message' => 'Homepage settings updated successfully!']);
    }
    
    private function updateStudentSettings(Request $request)
    {
        // Store student portal settings
        $request->validate([
            'student_sidebar_color' => 'nullable|string|max:7',
            'student_sidebar_text' => 'nullable|string|max:7',
            'student_dashboard_title' => 'nullable|string|max:255',
            'student_welcome_message' => 'nullable|string|max:1000',
        ]);
        
        foreach (['student_sidebar_color', 'student_sidebar_text'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('student_portal', $key, (string) $request->$key, 'color');
            }
        }
        
        foreach (['student_dashboard_title', 'student_welcome_message'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('student_portal', $key, (string) $request->$key, 'text');
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Student portal settings updated successfully!']);
    }
    
    private function updateProfessorSettings(Request $request)
    {
        // Store professor panel settings
        $request->validate([
            'professor_sidebar_color' => 'nullable|string|max:7',
            'professor_sidebar_text' => 'nullable|string|max:7',
            'professor_dashboard_title' => 'nullable|string|max:255',
            'professor_welcome_message' => 'nullable|string|max:1000',
        ]);
        
        foreach (['professor_sidebar_color', 'professor_sidebar_text'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('professor_panel', $key, (string) $request->$key, 'color');
            }
        }
        
        foreach (['professor_dashboard_title', 'professor_welcome_message'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('professor_panel', $key, (string) $request->$key, 'text');
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Professor panel settings updated successfully!']);
    }
    
    private function updateAdminSettings(Request $request)
    {
        // Store admin panel settings
        $request->validate([
            'admin_sidebar_color' => 'nullable|string|max:7',
            'admin_sidebar_text' => 'nullable|string|max:7',
            'admin_dashboard_title' => 'nullable|string|max:255',
            'admin_welcome_message' => 'nullable|string|max:1000',
        ]);
        
        foreach (['admin_sidebar_color', 'admin_sidebar_text'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('admin_panel', $key, (string) $request->$key, 'color');
            }
        }
        
        foreach (['admin_dashboard_title', 'admin_welcome_message'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('admin_panel', $key, (string) $request->$key, 'text');
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Admin panel settings updated successfully!']);
    }

    private function updateContentSettings(Request $request, $website)
    {
        // Implement content settings update
        return response()->json(['success' => true, 'message' => 'Content settings updated successfully!']);
    }

    private function updateFeaturesSettings(Request $request, $website)
    {
        // Implement features settings update
        return response()->json(['success' => true, 'message' => 'Features settings updated successfully!']);
    }

    private function updateLayoutSettings(Request $request, $website)
    {
        // Implement layout settings update
        return response()->json(['success' => true, 'message' => 'Layout settings updated successfully!']);
    }

    private function updateAdvancedSettings(Request $request)
    {
        // Store advanced settings
        $request->validate([
            'custom_css' => 'nullable|string|max:10000',
            'custom_js' => 'nullable|string|max:10000',
            'google_analytics' => 'nullable|string|max:500',
            'meta_tags' => 'nullable|string|max:1000',
        ]);
        
        foreach (['custom_css', 'custom_js', 'google_analytics', 'meta_tags'] as $key) {
            if ($request->has($key) && $request->$key !== null) {
                UiSetting::set('advanced', $key, (string) $request->$key, 'textarea');
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Advanced settings updated successfully!']);
    }

    /**
     * Apply customizations to a client's website
     */
    public function applyCustomizationsToWebsite(Request $request)
    {
        $request->validate([
            'client_slug' => 'required|string|exists:clients,slug',
        ]);

        $client = Client::where('slug', $request->client_slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            // Get all customization settings
            $customizations = $this->getAllCustomizations();
            
            // Apply customizations using the helper
            TemplateCustomizationHelper::applyCustomizations($client, $customizations);
            
            return response()->json([
                'success' => true,
                'message' => 'Customizations applied successfully to your website!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error applying customizations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all customization settings from the database
     */
    private function getAllCustomizations()
    {
        $sections = ['general', 'branding', 'navbar', 'homepage', 'student', 'professor', 'admin', 'advanced'];
        $customizations = [];
        
        foreach ($sections as $section) {
            $customizations[$section] = UiSetting::getSection($section);
        }
        
        return $customizations;
    }

    
    
    // Default settings for new website customization
    private function getDefaultHomepageSettings()
    {
        return [
            'background_color' => '#1e40af',
            'gradient_color' => '#3b82f6', 
            'text_color' => '#ffffff',
            'title' => 'Welcome to Our Training Center',
            'subtitle' => 'Excellence in Education & Training',
            'background_image' => null,
        ];
    }
    
    private function getDefaultNavbarSettings()
    {
        return [
            'background_color' => '#ffffff',
            'text_color' => '#1f2937',
            'brand_color' => '#1e40af',
            'hover_color' => '#3b82f6',
        ];
    }
    
    private function getDefaultFooterSettings()
    {
        return [
            'background_color' => '#1f2937',
            'text_color' => '#f9fafb',
            'link_color' => '#60a5fa',
        ];
    }
    
    private function getDefaultButtonSettings()
    {
        return [
            'primary_color' => '#1e40af',
            'primary_hover_color' => '#1d4ed8',
            'secondary_color' => '#6b7280',
            'secondary_hover_color' => '#4b5563',
        ];
    }
    
    private function getDefaultLoginSettings()
    {
        return [
            'background_color' => '#f9fafb',
            'card_background' => '#ffffff',
            'primary_color' => '#1e40af',
        ];
    }
    
    private function getDefaultProgramCardSettings()
    {
        return [
            'background_color' => '#ffffff',
            'border_color' => '#e5e7eb',
            'text_color' => '#1f2937',
            'accent_color' => '#1e40af',
        ];
    }
    
    private function getDefaultEnrollmentSettings()
    {
        return [
            'form_background' => '#ffffff',
            'input_border' => '#d1d5db',
            'submit_button_color' => '#10b981',
        ];
    }
    
    private function getDefaultStudentPortalSettings()
    {
        return [
            'sidebar_color' => '#1f2937',
            'sidebar_text' => '#f9fafb',
            'content_background' => '#ffffff',
        ];
    }
}

