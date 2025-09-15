<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SEOService;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'seo:generate-sitemap';
    protected $description = 'Generate XML sitemap for the website';

    public function handle()
    {
        $this->info('Generating sitemap...');
        
        $seoService = app(SEOService::class);
        $urls = $seoService->generateSitemap();
        
        $xml = view('seo.sitemap', compact('urls'))->render();
        
        // Save sitemap to public directory
        File::put(public_path('sitemap.xml'), $xml);
        
        $this->info('Sitemap generated successfully with ' . $urls->count() . ' URLs');
        $this->info('Sitemap saved to: ' . public_path('sitemap.xml'));
        
        return 0;
    }
}
