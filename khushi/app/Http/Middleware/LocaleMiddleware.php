<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = array_keys(config('locales.supported'));
        $defaultLocale = config('locales.default', 'en');
        
        // 1. Check URL parameter
        if ($request->has('lang') && in_array($request->lang, $supportedLocales)) {
            $locale = $request->lang;
            Session::put('locale', $locale);
        }
        // 2. Check session
        elseif (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales)) {
            $locale = Session::get('locale');
        }
        // 3. Check user preference (if logged in)
        elseif (auth()->check() && auth()->user()->locale && in_array(auth()->user()->locale, $supportedLocales)) {
            $locale = auth()->user()->locale;
            Session::put('locale', $locale);
        }
        // 4. Auto-detect from browser
        elseif (config('locales.auto_detect')) {
            $locale = $this->detectBrowserLocale($request, $supportedLocales) ?: $defaultLocale;
        }
        // 5. Default locale
        else {
            $locale = $defaultLocale;
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
    
    private function detectBrowserLocale(Request $request, array $supportedLocales)
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }
        
        // Parse Accept-Language header
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', trim($lang));
            $code = trim($parts[0]);
            $priority = isset($parts[1]) ? (float) str_replace('q=', '', $parts[1]) : 1.0;
            $languages[$code] = $priority;
        }
        
        // Sort by priority
        arsort($languages);
        
        // Find first supported language
        foreach (array_keys($languages) as $lang) {
            $code = substr($lang, 0, 2); // Get language code without country
            if (in_array($code, $supportedLocales)) {
                return $code;
            }
        }
        
        return null;
    }
}
