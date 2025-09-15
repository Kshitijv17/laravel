<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;

class AnalyticsMiddleware
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Track page views for GET requests
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $this->trackPageView($request);
        }

        return $response;
    }

    private function trackPageView(Request $request)
    {
        // Skip tracking for admin routes, API routes, and assets
        $skipPaths = [
            'admin/',
            'api/',
            'css/',
            'js/',
            'images/',
            'storage/',
            'favicon.ico',
            'robots.txt',
            'sitemap.xml'
        ];

        $path = $request->path();
        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return;
            }
        }

        // Track the page view
        $this->analyticsService->trackEvent('page_view', [
            'page_title' => $this->getPageTitle($request),
            'path' => $path,
            'query_string' => $request->getQueryString()
        ]);
    }

    private function getPageTitle(Request $request)
    {
        $path = $request->path();
        
        // Map common paths to titles
        $titleMap = [
            '/' => 'Home',
            'products' => 'Products',
            'categories' => 'Categories',
            'cart' => 'Shopping Cart',
            'checkout' => 'Checkout',
            'about' => 'About Us',
            'contact' => 'Contact',
            'blog' => 'Blog',
            'login' => 'Login',
            'register' => 'Register'
        ];

        return $titleMap[$path] ?? ucfirst(str_replace(['-', '_'], ' ', $path));
    }
}
