<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if admin is authenticated
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }

        // Check if admin has super admin role
        if (!auth('admin')->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. Super Admin privileges required.');
        }

        return $next($request);
    }
}
