<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PWAController extends Controller
{
    public function manifest()
    {
        return response()->file(public_path('manifest.json'), [
            'Content-Type' => 'application/json'
        ]);
    }

    public function serviceWorker()
    {
        return response()->file(public_path('sw.js'), [
            'Content-Type' => 'application/javascript'
        ]);
    }

    public function offline()
    {
        return view('web.offline');
    }

    public function ping()
    {
        return response()->json(['status' => 'online']);
    }

    public function installPrompt(Request $request)
    {
        // Store user's install prompt interaction
        $action = $request->input('action'); // 'accepted', 'dismissed', 'shown'
        
        // Log the interaction for analytics
        \Log::info('PWA Install Prompt', [
            'action' => $action,
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        return response()->json(['success' => true]);
    }

    public function csrfToken()
    {
        return response()->json([
            'token' => csrf_token()
        ]);
    }
}
