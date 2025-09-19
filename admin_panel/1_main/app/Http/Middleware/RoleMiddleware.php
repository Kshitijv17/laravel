<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('user.login')->with('error', 'Please login first.');
        }

        $user = auth()->user();

        // Check if user has the required role
        switch ($role) {
            case 'admin':
                if (!$user->isAdmin() && !$user->isSuperAdmin()) {
                    return redirect()->route('user.dashboard')->with('error', 'Access denied. Admin privileges required.');
                }
                break;

            case 'superadmin':
                if (!$user->isSuperAdmin()) {
                    return redirect()->route('user.dashboard')->with('error', 'Access denied. Super Admin privileges required.');
                }
                break;

            case 'customer':
                if (!$user->isCustomer() && !$user->isAdmin() && !$user->isSuperAdmin()) {
                    return redirect()->route('user.dashboard')->with('error', 'Access denied. Customer privileges required.');
                }
                break;

            default:
                return redirect()->route('user.dashboard')->with('error', 'Access denied. Invalid role.');
        }

        return $next($request);
    }
}
