<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProtectUserDashboard
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()->route('user.login')->withErrors(['Please log in to access the dashboard.']);
        }

        if ($user->is_guest && $user->expires_at && $user->expires_at->isPast()) {
            Auth::guard('web')->logout();
            return redirect()->route('user.login')->withErrors(['Your guest session has expired.']);
        }

        return $next($request);
    }
}
