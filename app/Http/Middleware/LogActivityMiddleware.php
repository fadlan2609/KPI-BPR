<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogActivityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log only POST, PUT, PATCH, DELETE requests
        $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        
        if (Auth::check() && in_array($request->method(), $methods)) {
            $user = Auth::user();
            
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $request->method() . ' ' . $request->path(),
                'module' => $this->getModule($request->path()),
                'data' => json_encode([
                    'url' => $request->fullUrl(),
                    'input' => $request->except(['password', 'password_confirmation']),
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $next($request);
    }

    /**
     * Get module name from path
     */
    private function getModule($path)
    {
        $segments = explode('/', $path);
        
        if (count($segments) >= 2) {
            return $segments[1] ?? 'unknown';
        }
        
        return 'unknown';
    }
}