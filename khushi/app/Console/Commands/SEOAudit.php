<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class SEOAudit extends Command
{
    protected $signature = 'seo:audit {--fix : Automatically fix issues where possible}';
    protected $description = 'Audit SEO issues and optionally fix them';

    public function handle()
    {
        $this->info('Starting SEO audit...');
        
        $issues = [];
        $fixed = 0;
        $shouldFix = $this->option('fix');
        
        // Audit products
        $this->info('Auditing products...');
        $productIssues = $this->auditProducts($shouldFix);
        $issues = array_merge($issues, $productIssues['issues']);
        $fixed += $productIssues['fixed'];
        
        // Audit categories
        $this->info('Auditing categories...');
        $categoryIssues = $this->auditCategories($shouldFix);
        $issues = array_merge($issues, $categoryIssues['issues']);
        $fixed += $categoryIssues['fixed'];
        
        // Audit blog posts
        $this->info('Auditing blog posts...');
        $blogIssues = $this->auditBlogPosts($shouldFix);
        $issues = array_merge($issues, $blogIssues['issues']);
        $fixed += $blogIssues['fixed'];
        
        // Display results
        $this->displayResults($issues, $fixed, $shouldFix);
        
        return 0;
    }
    
    private function auditProducts($shouldFix)
    {
        $issues = [];
        $fixed = 0;
        
        Product::chunk(100, function ($products) use (&$issues, &$fixed, $shouldFix) {
            foreach ($products as $product) {
                // Check for missing meta description
                if (empty($product->meta_description)) {
                    if ($shouldFix) {
                        $product->meta_description = Str::limit(strip_tags($product->description), 155);
                        $product->save();
                        $fixed++;
                    } else {
                        $issues[] = "Product '{$product->name}' missing meta description";
                    }
                }
                
                // Check for missing meta title
                if (empty($product->meta_title)) {
                    if ($shouldFix) {
                        $product->meta_title = $product->name . ' - Buy Online at ' . config('app.name');
                        $product->save();
                        $fixed++;
                    } else {
                        $issues[] = "Product '{$product->name}' missing meta title";
                    }
                }
                
                // Check for duplicate slugs
                $duplicateCount = Product::where('slug', $product->slug)
                    ->where('id', '!=', $product->id)
                    ->count();
                    
                if ($duplicateCount > 0) {
                    if ($shouldFix) {
                        $product->slug = $product->slug . '-' . $product->id;
                        $product->save();
                        $fixed++;
                    } else {
                        $issues[] = "Product '{$product->name}' has duplicate slug";
                    }
                }
                
                // Check for missing alt text on images
                foreach ($product->images as $image) {
                    if (empty($image->alt_text)) {
                        if ($shouldFix) {
                            $image->alt_text = $product->name . ' - Product Image';
                            $image->save();
                            $fixed++;
                        } else {
                            $issues[] = "Product '{$product->name}' has image without alt text";
                        }
                    }
                }
                
                // Check title length
                if (strlen($product->name) > 60) {
                    $issues[] = "Product '{$product->name}' title too long (>60 chars)";
                }
                
                // Check description length
                if (strlen($product->description) < 100) {
                    $issues[] = "Product '{$product->name}' description too short (<100 chars)";
                }
            }
        });
        
        return ['issues' => $issues, 'fixed' => $fixed];
    }
    
    private function auditCategories($shouldFix)
    {
        $issues = [];
        $fixed = 0;
        
        Category::chunk(100, function ($categories) use (&$issues, &$fixed, $shouldFix) {
            foreach ($categories as $category) {
                // Check for missing meta description
                if (empty($category->meta_description)) {
                    if ($shouldFix) {
                        $category->meta_description = "Shop {$category->name} products online at " . config('app.name') . ". Best prices and fast delivery.";
                        $category->save();
                        $fixed++;
                    } else {
                        $issues[] = "Category '{$category->name}' missing meta description";
                    }
                }
                
                // Check for missing meta title
                if (empty($category->meta_title)) {
                    if ($shouldFix) {
                        $category->meta_title = $category->name . ' - Shop Online at ' . config('app.name');
                        $category->save();
                        $fixed++;
                    } else {
                        $issues[] = "Category '{$category->name}' missing meta title";
                    }
                }
                
                // Check for duplicate slugs
                $duplicateCount = Category::where('slug', $category->slug)
                    ->where('id', '!=', $category->id)
                    ->count();
                    
                if ($duplicateCount > 0) {
                    if ($shouldFix) {
                        $category->slug = $category->slug . '-' . $category->id;
                        $category->save();
                        $fixed++;
                    } else {
                        $issues[] = "Category '{$category->name}' has duplicate slug";
                    }
                }
                
                // Check for missing description
                if (empty($category->description)) {
                    $issues[] = "Category '{$category->name}' missing description";
                }
            }
        });
        
        return ['issues' => $issues, 'fixed' => $fixed];
    }
    
    private function auditBlogPosts($shouldFix)
    {
        $issues = [];
        $fixed = 0;
        
        BlogPost::chunk(100, function ($posts) use (&$issues, &$fixed, $shouldFix) {
            foreach ($posts as $post) {
                // Check for missing meta description
                if (empty($post->meta_description)) {
                    if ($shouldFix) {
                        $post->meta_description = Str::limit(strip_tags($post->content), 155);
                        $post->save();
                        $fixed++;
                    } else {
                        $issues[] = "Blog post '{$post->title}' missing meta description";
                    }
                }
                
                // Check for missing meta title
                if (empty($post->meta_title)) {
                    if ($shouldFix) {
                        $post->meta_title = $post->title . ' - ' . config('app.name') . ' Blog';
                        $post->save();
                        $fixed++;
                    } else {
                        $issues[] = "Blog post '{$post->title}' missing meta title";
                    }
                }
                
                // Check for duplicate slugs
                $duplicateCount = BlogPost::where('slug', $post->slug)
                    ->where('id', '!=', $post->id)
                    ->count();
                    
                if ($duplicateCount > 0) {
                    if ($shouldFix) {
                        $post->slug = $post->slug . '-' . $post->id;
                        $post->save();
                        $fixed++;
                    } else {
                        $issues[] = "Blog post '{$post->title}' has duplicate slug";
                    }
                }
                
                // Check for missing featured image
                if (empty($post->featured_image)) {
                    $issues[] = "Blog post '{$post->title}' missing featured image";
                }
                
                // Check for missing excerpt
                if (empty($post->excerpt)) {
                    if ($shouldFix) {
                        $post->excerpt = Str::limit(strip_tags($post->content), 155);
                        $post->save();
                        $fixed++;
                    } else {
                        $issues[] = "Blog post '{$post->title}' missing excerpt";
                    }
                }
                
                // Check title length
                if (strlen($post->title) > 60) {
                    $issues[] = "Blog post '{$post->title}' title too long (>60 chars)";
                }
                
                // Check content length
                if (strlen(strip_tags($post->content)) < 300) {
                    $issues[] = "Blog post '{$post->title}' content too short (<300 words)";
                }
            }
        });
        
        return ['issues' => $issues, 'fixed' => $fixed];
    }
    
    private function displayResults($issues, $fixed, $shouldFix)
    {
        $this->info('SEO Audit Complete!');
        $this->info('==================');
        
        if ($shouldFix) {
            $this->info("Fixed {$fixed} issues automatically.");
        }
        
        if (count($issues) > 0) {
            $this->warn('Found ' . count($issues) . ' SEO issues:');
            foreach ($issues as $issue) {
                $this->line('- ' . $issue);
            }
            
            if (!$shouldFix) {
                $this->info('');
                $this->info('Run with --fix flag to automatically fix issues where possible:');
                $this->info('php artisan seo:audit --fix');
            }
        } else {
            $this->info('No SEO issues found! 🎉');
        }
    }
}
