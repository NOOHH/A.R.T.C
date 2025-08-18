<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugSmartprepRequests
{
    public function handle(Request $request, Closure $next)
    {
        if (str_contains($request->path(), 'smartprep/admin/settings')) {
            Log::info('SmartPrep Settings Request Debug:', [
                'method' => $request->method(),
                'path' => $request->path(),
                'all_input' => $request->all(),
                'hero_title' => $request->input('hero_title'),
                'hero_title_raw' => $request->input('hero_title', 'NOT_PROVIDED'),
                'hero_title_length' => strlen($request->input('hero_title', '')),
                'content_type' => $request->header('Content-Type'),
                'expects_json' => $request->expectsJson(),
                'headers' => $request->headers->all()
            ]);
        }
        
        return $next($request);
    }
}
