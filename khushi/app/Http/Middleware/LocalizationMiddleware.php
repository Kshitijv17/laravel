<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, URL parameter, or default to 'en'
        $locale = $request->session()->get('locale', config('app.locale', 'en'));
        
        // Validate locale - only en and es supported
        $supportedLocales = ['en', 'es'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }
        
        // Set application locale for translations
        App::setLocale($locale);
        
        // Store in session for persistence
        Session::put('locale', $locale);
        
        // Add locale to view data
        view()->share('currentLocale', $locale);
        view()->share('supportedLocales', $supportedLocales);
        
        return $next($request);
    }

    private function getLocale(Request $request, array $supportedLocales, string $defaultLocale): string
    {
        // 1. Check URL parameter
        if ($request->has('lang') && array_key_exists($request->lang, $supportedLocales)) {
            return $request->lang;
        }
        
        // 2. Check session
        if (Session::has('locale') && array_key_exists(Session::get('locale'), $supportedLocales)) {
            return Session::get('locale');
        }
        
        // 3. Check user preference (if authenticated)
        if ($request->user() && $request->user()->locale && array_key_exists($request->user()->locale, $supportedLocales)) {
            return $request->user()->locale;
        }
        
        // 4. Check browser Accept-Language header
        $browserLocale = $this->getBrowserLocale($request, $supportedLocales);
        if ($browserLocale) {
            return $browserLocale;
        }
        
        // 5. Fall back to default
        return $defaultLocale;
    }

    private function getBrowserLocale(Request $request, array $supportedLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }
        
        // Parse Accept-Language header
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', trim($lang));
            $locale = trim($parts[0]);
            $quality = 1.0;
            
            if (count($parts) > 1 && strpos($parts[1], 'q=') === 0) {
                $quality = floatval(substr($parts[1], 2));
            }
            
            $languages[$locale] = $quality;
        }
        
        // Sort by quality
        arsort($languages);
        
        // Find best match
        foreach ($languages as $locale => $quality) {
            // Exact match
            if (array_key_exists($locale, $supportedLocales)) {
                return $locale;
            }
            
            // Language-only match (e.g., 'en' for 'en-US')
            $langOnly = substr($locale, 0, 2);
            if (array_key_exists($langOnly, $supportedLocales)) {
                return $langOnly;
            }
        }
        
        return null;
    }
}
