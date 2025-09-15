<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, string $key = 'global', int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $identifier = $this->getIdentifier($request, $key);
        
        if (RateLimiter::tooManyAttempts($identifier, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($identifier);
            
            // Log rate limit exceeded
            \Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'key' => $key,
                'identifier' => $identifier,
                'retry_after' => $seconds
            ]);
            
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $seconds
            ], 429)->header('Retry-After', $seconds);
        }
        
        RateLimiter::hit($identifier, $decayMinutes * 60);
        
        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - RateLimiter::attempts($identifier)));
        
        return $response;
    }
    
    private function getIdentifier(Request $request, string $key): string
    {
        $base = $request->ip();
        
        // Add user ID if authenticated
        if ($request->user()) {
            $base .= '|' . $request->user()->id;
        }
        
        return $key . ':' . sha1($base);
    }
}
