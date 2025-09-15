@props(['page' => 'home', 'data' => []])

@php
    $seoService = app(\App\Services\SEOService::class);
    $breadcrumbs = $seoService->generateBreadcrumbs($page, $data);
@endphp

@if(count($breadcrumbs) > 1)
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        @foreach($breadcrumbs as $index => $breadcrumb)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $breadcrumb['name'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">
                        @if($loop->first)
                            <i class="fas fa-home me-1"></i>
                        @endif
                        {{ $breadcrumb['name'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
