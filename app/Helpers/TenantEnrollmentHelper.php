<?php

if (!function_exists('tenant_enrollment_url')) {
    /**
     * Generate tenant-aware enrollment URL
     * 
     * @param string $type ('full', 'modular', 'login', etc.)
     * @return string
     */
    function tenant_enrollment_url($type = 'full') {
        // Check if we're in tenant context
        $request = request();
        
        // Extract tenant from current URL
        $currentUrl = $request->url();
        
        // Pattern: /t/{tenant}/ or /t/draft/{tenant}/
        if (preg_match('/\/t\/(?:draft\/)?([^\/]+)/', $currentUrl, $matches)) {
            $tenant = $matches[1];
            $isDraft = strpos($currentUrl, '/draft/') !== false;
            
            if ($type === 'login') {
                // Special handling for login
                if ($isDraft) {
                    return url("/t/draft/{$tenant}/login");
                } else {
                    return url("/t/{$tenant}/login");
                }
            } elseif ($type === 'modular.submit') {
                // Special handling for modular form submission
                if ($isDraft) {
                    return url("/t/draft/{$tenant}/enrollment/modular/submit");
                } else {
                    return url("/t/{$tenant}/enrollment/modular/submit");
                }
            } elseif ($type === 'full.submit') {
                // Special handling for full form submission
                if ($isDraft) {
                    return url("/t/draft/{$tenant}/enrollment/full/submit");
                } else {
                    return url("/t/{$tenant}/enrollment/full/submit");
                }
            } else {
                // Handle enrollment types
                if ($isDraft) {
                    return url("/t/draft/{$tenant}/enrollment/{$type}");
                } else {
                    return url("/t/{$tenant}/enrollment/{$type}");
                }
            }
        }
        
        // Fallback to regular route
        if ($type === 'login') {
            return route('login');
        } elseif ($type === 'modular.submit') {
            return route('enrollment.modular.submit');
        } elseif ($type === 'full.submit') {
            return route('enrollment.full.submit');
        }
        return route("enrollment.{$type}");
    }
}

if (!function_exists('current_tenant_slug')) {
    /**
     * Get current tenant slug from URL
     * 
     * @return string|null
     */
    function current_tenant_slug() {
        $request = request();
        $currentUrl = $request->url();
        
        if (preg_match('/\/t\/(?:draft\/)?([^\/]+)/', $currentUrl, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}

if (!function_exists('is_draft_tenant')) {
    /**
     * Check if current context is draft tenant
     * 
     * @return bool
     */
    function is_draft_tenant() {
        $request = request();
        return strpos($request->url(), '/t/draft/') !== false;
    }
}
