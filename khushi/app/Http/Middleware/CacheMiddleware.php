<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CacheMiddleware
{
    public function handle(Request $request, Closure $next, int $minutes = 60)
    {
        // Skip caching for authenticated users or non-GET requests
        if (Auth::check() || !$request->isMethod('GET')) {
            return $next($request);
        }

        // Skip caching for requests with query parameters (except page)
        $queryParams = $request->query();
        unset($queryParams['page']);
        if (!empty($queryParams)) {
            return $next($request);
        }

        $cacheKey = $this->getCacheKey($request);
        
        // Return cached response if exists
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);
            return response($cachedResponse['content'])
                ->header('Content-Type', $cachedResponse['content_type'])
                ->header('X-Cache', 'HIT')
                ->header('Cache-Control', 'public, max-age=' . ($minutes * 60));
        }

        $response = $next($request);

        // Cache successful responses
        if ($response->getStatusCode() === 200 && $response->headers->get('Content-Type', '')) {
            $cacheData = [
                'content' => $response->getContent(),
                'content_type' => $response->headers->get('Content-Type')
            ];
            
            Cache::put($cacheKey, $cacheData, now()->addMinutes($minutes));
            
            $response->header('X-Cache', 'MISS');
            $response->header('Cache-Control', 'public, max-age=' . ($minutes * 60));
        }

        return $response;
    }

    private function getCacheKey(Request $request): string
    {
        $uri = $request->getRequestUri();
        $page = $request->get('page', 1);
        
        return 'page_cache:' . md5($uri . ':page:' . $page);
    }
}
