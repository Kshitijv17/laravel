<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Skip if user is not authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip if 2FA is not enabled for this user
        if (!$user->two_factor_confirmed_at) {
            return $next($request);
        }

        // Skip if already verified in this session
        if (Session::get('two_factor_verified')) {
            return $next($request);
        }

        // Skip for 2FA related routes
        if ($request->routeIs('two-factor.*')) {
            return $next($request);
        }

        // Redirect to 2FA challenge
        return redirect()->route('two-factor.challenge');
    }
}
