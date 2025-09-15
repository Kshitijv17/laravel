<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompressionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only compress if client accepts gzip
        if (!$this->shouldCompress($request, $response)) {
            return $response;
        }

        $content = $response->getContent();
        
        if (strlen($content) > 1024) { // Only compress if content > 1KB
            $compressed = gzencode($content, 6);
            
            if ($compressed !== false && strlen($compressed) < strlen($content)) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Content-Length', strlen($compressed));
                $response->headers->set('Vary', 'Accept-Encoding');
            }
        }

        return $response;
    }

    private function shouldCompress(Request $request, $response): bool
    {
        // Check if client accepts gzip
        $acceptEncoding = $request->header('Accept-Encoding', '');
        if (strpos($acceptEncoding, 'gzip') === false) {
            return false;
        }

        // Don't compress if already compressed
        if ($response->headers->has('Content-Encoding')) {
            return false;
        }

        // Only compress text-based content
        $contentType = $response->headers->get('Content-Type', '');
        $compressibleTypes = [
            'text/html',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'text/xml',
            'text/plain'
        ];

        foreach ($compressibleTypes as $type) {
            if (strpos($contentType, $type) !== false) {
                return true;
            }
        }

        return false;
    }
}
