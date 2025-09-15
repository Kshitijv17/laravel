<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LocaleController extends Controller
{
    public function switch($locale)
    {
        $supportedLocales = ['en', 'es'];
        
        if (!in_array($locale, $supportedLocales)) {
            return redirect()->back()->with('error', 'Unsupported language');
        }
        
        // Store in session only - no text changes, just locale setting
        Session::put('locale', $locale);
        
        return redirect()->back()->with('success', 'Language changed successfully');
    }
    
    public function getAvailable()
    {
        return response()->json([
            'current' => app()->getLocale(),
            'supported' => config('locales.supported')
        ]);
    }
}
