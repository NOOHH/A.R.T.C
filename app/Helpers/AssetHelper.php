<?php

namespace App\Helpers;

use Illuminate\Support\Facades\URL;

class AssetHelper
{
    /**
     * Generate a secure asset URL for the application.
     *
     * @param  string  $path
     * @return string
     */
    public static function secureAsset($path)
    {
        $url = asset($path);
        
        // Force HTTPS if not already secure
        if (!str_starts_with($url, 'https://')) {
            $url = str_replace('http://', 'https://', $url);
        }
        
        return $url;
    }
}
