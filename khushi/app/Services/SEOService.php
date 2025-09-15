<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SEOService
{
    public function generateMetaTags($page, $data = [])
    {
        $meta = [
            'title' => $this->generateTitle($page, $data),
            'description' => $this->generateDescription($page, $data),
            'keywords' => $this->generateKeywords($page, $data),
            'canonical' => $this->generateCanonical($page, $data),
            'og_title' => $this->generateOGTitle($page, $data),
            'og_description' => $this->generateOGDescription($page, $data),
            'og_image' => $this->generateOGImage($page, $data),
            'og_url' => $this->generateCanonical($page, $data),
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $this->generateTwitterTitle($page, $data),
            'twitter_description' => $this->generateTwitterDescription($page, $data),
            'twitter_image' => $this->generateTwitterImage($page, $data)
        ];

        return $meta;
    }

    public function generateStructuredData($page, $data = [])
    {
        switch ($page) {
            case 'product':
                return $this->generateProductStructuredData($data);
            case 'category':
                return $this->generateCategoryStructuredData($data);
            case 'blog_post':
                return $this->generateBlogPostStructuredData($data);
            case 'home':
                return $this->generateWebsiteStructuredData();
            case 'breadcrumb':
                return $this->generateBreadcrumbStructuredData($data);
            default:
                return $this->generateWebsiteStructuredData();
        }
    }

    public function generateSitemap()
    {
        $urls = collect();

        // Static pages
        $staticPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/products', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/categories', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => '/about', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $urls->push([
                'url' => url($page['url']),
                'lastmod' => now()->toISOString(),
                'priority' => $page['priority'],
                'changefreq' => $page['changefreq']
            ]);
        }

        // Products
        Product::where('status', 'active')->chunk(1000, function ($products) use ($urls) {
            foreach ($products as $product) {
                $urls->push([
                    'url' => route('product.show', $product->slug),
                    'lastmod' => $product->updated_at->toISOString(),
                    'priority' => '0.8',
                    'changefreq' => 'weekly'
                ]);
            }
        });

        // Categories
        Category::where('status', 'active')->chunk(100, function ($categories) use ($urls) {
            foreach ($categories as $category) {
                $urls->push([
                    'url' => route('category.show', $category->slug),
                    'lastmod' => $category->updated_at->toISOString(),
                    'priority' => '0.7',
                    'changefreq' => 'weekly'
                ]);
            }
        });

        // Blog posts
        BlogPost::where('status', 'published')->chunk(100, function ($posts) use ($urls) {
            foreach ($posts as $post) {
                $urls->push([
                    'url' => route('blog.show', $post->slug),
                    'lastmod' => $post->updated_at->toISOString(),
                    'priority' => '0.6',
                    'changefreq' => 'monthly'
                ]);
            }
        });

        return $urls;
    }

    public function generateRobotsTxt()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Disallow: /cart\n";
        $content .= "Disallow: /checkout\n";
        $content .= "Disallow: /user/\n";
        $content .= "Disallow: /search?*\n";
        $content .= "\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return $content;
    }

    public function optimizeSlug($title)
    {
        return Str::slug($title, '-', 'en');
    }

    public function generateBreadcrumbs($page, $data = [])
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')]
        ];

        switch ($page) {
            case 'product':
                if (isset($data['product'])) {
                    $product = $data['product'];
                    $breadcrumbs[] = ['name' => 'Products', 'url' => route('products.index')];
                    if ($product->category) {
                        $breadcrumbs[] = ['name' => $product->category->name, 'url' => route('category.show', $product->category->slug)];
                    }
                    $breadcrumbs[] = ['name' => $product->name, 'url' => null];
                }
                break;

            case 'category':
                if (isset($data['category'])) {
                    $category = $data['category'];
                    $breadcrumbs[] = ['name' => 'Categories', 'url' => route('categories.index')];
                    if ($category->parent) {
                        $breadcrumbs[] = ['name' => $category->parent->name, 'url' => route('category.show', $category->parent->slug)];
                    }
                    $breadcrumbs[] = ['name' => $category->name, 'url' => null];
                }
                break;

            case 'blog_post':
                if (isset($data['post'])) {
                    $post = $data['post'];
                    $breadcrumbs[] = ['name' => 'Blog', 'url' => route('blog.index')];
                    if ($post->category) {
                        $breadcrumbs[] = ['name' => $post->category->name, 'url' => route('blog.category', $post->category->slug)];
                    }
                    $breadcrumbs[] = ['name' => $post->title, 'url' => null];
                }
                break;
        }

        return $breadcrumbs;
    }

    private function generateTitle($page, $data)
    {
        $siteName = config('app.name');
        
        switch ($page) {
            case 'home':
                return "Best Online Shopping Store - {$siteName}";
            case 'product':
                return isset($data['product']) ? $data['product']->name . " - Buy Online at {$siteName}" : "Product - {$siteName}";
            case 'category':
                return isset($data['category']) ? $data['category']->name . " - Shop Online at {$siteName}" : "Category - {$siteName}";
            case 'blog_post':
                return isset($data['post']) ? $data['post']->title . " - {$siteName} Blog" : "Blog Post - {$siteName}";
            case 'blog':
                return "Blog - Latest News & Updates - {$siteName}";
            case 'products':
                return "All Products - Shop Online at {$siteName}";
            default:
                return ucfirst($page) . " - {$siteName}";
        }
    }

    private function generateDescription($page, $data)
    {
        switch ($page) {
            case 'home':
                return "Shop the best products online at " . config('app.name') . ". Fast delivery, secure payment, and excellent customer service. Find everything you need in one place.";
            case 'product':
                if (isset($data['product'])) {
                    $product = $data['product'];
                    return Str::limit(strip_tags($product->description), 155) . " Buy now at " . config('app.name') . " with fast delivery.";
                }
                return "High-quality products at the best prices. Shop now at " . config('app.name');
            case 'category':
                if (isset($data['category'])) {
                    $category = $data['category'];
                    return "Shop " . $category->name . " products online at " . config('app.name') . ". Best prices, fast delivery, and secure payment options.";
                }
                return "Browse our wide range of categories at " . config('app.name');
            case 'blog_post':
                if (isset($data['post'])) {
                    return Str::limit(strip_tags($data['post']->content), 155);
                }
                return "Read our latest blog posts and stay updated with news and tips.";
            default:
                return "Discover amazing products and services at " . config('app.name') . ". Your trusted online shopping destination.";
        }
    }

    private function generateKeywords($page, $data)
    {
        $baseKeywords = [config('app.name'), 'online shopping', 'ecommerce', 'buy online'];
        
        switch ($page) {
            case 'product':
                if (isset($data['product'])) {
                    $product = $data['product'];
                    $keywords = array_merge($baseKeywords, [
                        $product->name,
                        $product->category->name ?? '',
                        $product->brand->name ?? '',
                        'buy ' . $product->name
                    ]);
                    return implode(', ', array_filter($keywords));
                }
                break;
            case 'category':
                if (isset($data['category'])) {
                    $category = $data['category'];
                    $keywords = array_merge($baseKeywords, [
                        $category->name,
                        $category->name . ' products',
                        'buy ' . $category->name
                    ]);
                    return implode(', ', $keywords);
                }
                break;
        }
        
        return implode(', ', $baseKeywords);
    }

    private function generateCanonical($page, $data)
    {
        switch ($page) {
            case 'product':
                return isset($data['product']) ? route('product.show', $data['product']->slug) : url()->current();
            case 'category':
                return isset($data['category']) ? route('category.show', $data['category']->slug) : url()->current();
            case 'blog_post':
                return isset($data['post']) ? route('blog.show', $data['post']->slug) : url()->current();
            default:
                return url()->current();
        }
    }

    private function generateOGTitle($page, $data)
    {
        return $this->generateTitle($page, $data);
    }

    private function generateOGDescription($page, $data)
    {
        return $this->generateDescription($page, $data);
    }

    private function generateOGImage($page, $data)
    {
        switch ($page) {
            case 'product':
                if (isset($data['product']) && $data['product']->images->isNotEmpty()) {
                    return asset('storage/' . $data['product']->images->first()->image_path);
                }
                break;
            case 'category':
                if (isset($data['category']) && $data['category']->image) {
                    return asset('storage/' . $data['category']->image);
                }
                break;
            case 'blog_post':
                if (isset($data['post']) && $data['post']->featured_image) {
                    return asset('storage/' . $data['post']->featured_image);
                }
                break;
        }
        
        return asset('images/og-default.jpg');
    }

    private function generateTwitterTitle($page, $data)
    {
        return $this->generateOGTitle($page, $data);
    }

    private function generateTwitterDescription($page, $data)
    {
        return $this->generateOGDescription($page, $data);
    }

    private function generateTwitterImage($page, $data)
    {
        return $this->generateOGImage($page, $data);
    }

    private function generateProductStructuredData($data)
    {
        if (!isset($data['product'])) return null;
        
        $product = $data['product'];
        
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => strip_tags($product->description),
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand->name ?? config('app.name')
            ],
            'category' => $product->category->name ?? '',
            'image' => $product->images->isNotEmpty() ? 
                $product->images->map(fn($img) => asset('storage/' . $img->image_path))->toArray() : [],
            'offers' => [
                '@type' => 'Offer',
                'price' => $product->selling_price,
                'priceCurrency' => 'USD',
                'availability' => $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('product.show', $product->slug),
                'seller' => [
                    '@type' => 'Organization',
                    'name' => config('app.name')
                ]
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->average_rating ?? 5,
                'reviewCount' => $product->reviews_count ?? 1
            ]
        ];
    }

    private function generateCategoryStructuredData($data)
    {
        if (!isset($data['category'])) return null;
        
        $category = $data['category'];
        
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => $category->description ?? '',
            'url' => route('category.show', $category->slug)
        ];
    }

    private function generateBlogPostStructuredData($data)
    {
        if (!isset($data['post'])) return null;
        
        $post = $data['post'];
        
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->excerpt ?? Str::limit(strip_tags($post->content), 155),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author->name ?? 'Admin'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png')
                ]
            ],
            'datePublished' => $post->created_at->toISOString(),
            'dateModified' => $post->updated_at->toISOString(),
            'image' => $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/default-blog.jpg'),
            'url' => route('blog.show', $post->slug)
        ];
    }

    private function generateWebsiteStructuredData()
    {
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search?q={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            ]
        ];
    }

    private function generateBreadcrumbStructuredData($breadcrumbs)
    {
        $items = [];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'] ?? url()->current()
            ];
        }
        
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
    }
}
