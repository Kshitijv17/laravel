<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Check if user is authenticated and is an Admin model instance
        if (!$user || !($user instanceof \App\Models\Admin)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        // Check if admin is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Admin account is inactive.'
            ], 403);
        }

        return $next($request);
    }
}
