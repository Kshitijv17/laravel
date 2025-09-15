<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SEOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SEOController extends Controller
{
    protected $seoService;

    public function __construct(SEOService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function sitemap()
    {
        $urls = $this->seoService->generateSitemap();
        
        $xml = view('seo.sitemap', compact('urls'))->render();
        
        return Response::make($xml, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    public function robots()
    {
        $content = $this->seoService->generateRobotsTxt();
        
        return Response::make($content, 200, [
            'Content-Type' => 'text/plain'
        ]);
    }

    public function generateMeta(Request $request)
    {
        $page = $request->get('page');
        $data = $request->get('data', []);
        
        $meta = $this->seoService->generateMetaTags($page, $data);
        
        return response()->json($meta);
    }

    public function generateStructuredData(Request $request)
    {
        $page = $request->get('page');
        $data = $request->get('data', []);
        
        $structuredData = $this->seoService->generateStructuredData($page, $data);
        
        return response()->json($structuredData);
    }

    public function optimizeSlug(Request $request)
    {
        $title = $request->get('title');
        $slug = $this->seoService->optimizeSlug($title);
        
        return response()->json(['slug' => $slug]);
    }
}
