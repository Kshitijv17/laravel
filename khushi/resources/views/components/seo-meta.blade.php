@props(['page' => 'home', 'data' => []])

@php
    $seoService = app(\App\Services\SEOService::class);
    $meta = $seoService->generateMetaTags($page, $data);
    $structuredData = $seoService->generateStructuredData($page, $data);
    $breadcrumbs = $seoService->generateBreadcrumbs($page, $data);
@endphp

<!-- SEO Meta Tags -->
<title>{{ $meta['title'] }}</title>
<meta name="description" content="{{ $meta['description'] }}">
<meta name="keywords" content="{{ $meta['keywords'] }}">
<link rel="canonical" href="{{ $meta['canonical'] }}">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="{{ $meta['og_title'] }}">
<meta property="og:description" content="{{ $meta['og_description'] }}">
<meta property="og:image" content="{{ $meta['og_image'] }}">
<meta property="og:url" content="{{ $meta['og_url'] }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="{{ $meta['twitter_card'] }}">
<meta name="twitter:title" content="{{ $meta['twitter_title'] }}">
<meta name="twitter:description" content="{{ $meta['twitter_description'] }}">
<meta name="twitter:image" content="{{ $meta['twitter_image'] }}">

<!-- Additional SEO Meta Tags -->
<meta name="robots" content="index, follow">
<meta name="author" content="{{ config('app.name') }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">

<!-- Structured Data -->
@if($structuredData)
<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif

<!-- Breadcrumb Structured Data -->
@if(count($breadcrumbs) > 1)
<script type="application/ld+json">
{!! json_encode($seoService->generateStructuredData('breadcrumb', ['breadcrumbs' => $breadcrumbs]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif

<!-- Preconnect for Performance -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com">

<!-- DNS Prefetch -->
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//ajax.googleapis.com">

<!-- Favicon and App Icons -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
<link rel="manifest" href="{{ route('pwa.manifest') }}">

<!-- Theme Color -->
<meta name="theme-color" content="#3b82f6">
<meta name="msapplication-TileColor" content="#3b82f6">

<!-- Language and Locale -->
<meta http-equiv="content-language" content="en">
<meta property="og:locale" content="en_US">

<!-- Cache Control -->
<meta http-equiv="Cache-Control" content="public, max-age=3600">

<!-- Additional Performance Hints -->
@if($page === 'product' && isset($data['product']))
    <link rel="prefetch" href="{{ route('cart.add') }}">
@endif

@if($page === 'category')
    <link rel="prefetch" href="{{ route('products.index') }}">
@endif
