<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SEOMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only process HTML responses
        if ($response->headers->get('Content-Type') && 
            strpos($response->headers->get('Content-Type'), 'text/html') !== false) {
            
            $content = $response->getContent();
            
            // Add meta viewport if not present
            if (strpos($content, '<meta name="viewport"') === false) {
                $content = str_replace(
                    '<head>',
                    '<head><meta name="viewport" content="width=device-width, initial-scale=1.0">',
                    $content
                );
            }
            
            // Add charset if not present
            if (strpos($content, '<meta charset') === false && strpos($content, '<meta http-equiv="Content-Type"') === false) {
                $content = str_replace(
                    '<head>',
                    '<head><meta charset="UTF-8">',
                    $content
                );
            }
            
            // Minify HTML if enabled
            if (config('performance.minification.html', false)) {
                $content = $this->minifyHTML($content);
            }
            
            // Add performance hints
            $content = $this->addPerformanceHints($content);
            
            $response->setContent($content);
        }

        // Add SEO headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }

    private function minifyHTML($content)
    {
        // Remove HTML comments (except IE conditionals)
        $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);
        
        // Remove extra whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Remove whitespace around block elements
        $content = preg_replace('/>\s+</', '><', $content);
        
        return trim($content);
    }

    private function addPerformanceHints($content)
    {
        // Add resource hints before closing head tag
        $hints = '';
        
        // Preload critical resources
        $criticalResources = config('performance.preloading.critical_resources', []);
        foreach ($criticalResources as $resource) {
            $type = pathinfo($resource, PATHINFO_EXTENSION) === 'css' ? 'style' : 'script';
            $hints .= "<link rel=\"preload\" href=\"{$resource}\" as=\"{$type}\">\n";
        }
        
        // DNS prefetch for external domains
        $dnsPrefetch = config('performance.preloading.dns_prefetch', []);
        foreach ($dnsPrefetch as $domain) {
            $hints .= "<link rel=\"dns-prefetch\" href=\"{$domain}\">\n";
        }
        
        if ($hints) {
            $content = str_replace('</head>', $hints . '</head>', $content);
        }
        
        return $content;
    }
}
