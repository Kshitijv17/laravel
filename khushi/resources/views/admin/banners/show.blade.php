@extends('layouts.admin')

@section('title', 'Banner Details')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-1">Banner Details</h1>
                <p class="page-subtitle mb-0">View banner information</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Banners
                </a>
                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Banner
                </a>
            </div>
        </div>
        
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
                <li class="breadcrumb-item active">{{ $banner->title }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- Banner Preview -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-bold">Banner Preview</h5>
                </div>
                <div class="card-body text-center">
                    @if($banner->image)
                    <div class="banner-preview mb-3">
                        <img src="{{ $banner->image_url }}" 
                             alt="{{ $banner->title }}" 
                             class="img-fluid rounded shadow-sm" 
                             style="max-height: 400px;">
                    </div>
                    @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                        <div class="text-muted">
                            <i class="fas fa-image fa-3x mb-3"></i>
                            <p>No image uploaded</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($banner->link_url)
                    <div class="mt-3">
                        <a href="{{ $banner->link_url }}" target="_blank" class="btn btn-primary">
                            Learn More
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Banner Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-bold">Banner Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">TITLE</label>
                        <div class="fs-5 fw-semibold">{{ $banner->title }}</div>
                    </div>
                    
                    @if($banner->description)
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">DESCRIPTION</label>
                        <div>{{ $banner->description }}</div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">POSITION</label>
                        <div>
                            <span class="badge bg-info px-2 py-1">{{ ucfirst($banner->display_position) }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">STATUS</label>
                        <div>
                            @if($banner->status)
                                <span class="badge bg-success px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-danger px-2 py-1">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($banner->link_url)
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">LINK URL</label>
                        <div>
                            <a href="{{ $banner->link_url }}" target="_blank" class="text-break">
                                {{ $banner->link_url }}
                                <i class="fas fa-external-link-alt ms-1 small"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Schedule Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-bold">Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">START DATE</label>
                        <div>
                            @if($banner->start_date)
                                {{ $banner->start_date->format('M d, Y') }}
                                <small class="text-muted d-block">{{ $banner->start_date->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">END DATE</label>
                        <div>
                            @if($banner->end_date)
                                {{ $banner->end_date->format('M d, Y') }}
                                <small class="text-muted d-block">{{ $banner->end_date->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">CREATED</label>
                        <div>
                            {{ $banner->created_at->format('M d, Y h:i A') }}
                            <small class="text-muted d-block">{{ $banner->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    <div>
                        <label class="fw-semibold text-muted small">LAST UPDATED</label>
                        <div>
                            {{ $banner->updated_at->format('M d, Y h:i A') }}
                            <small class="text-muted d-block">{{ $banner->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 12px;
    }
    
    .banner-preview img {
        border-radius: 8px;
    }
    
    .text-break {
        word-break: break-all;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .banner-preview img {
            max-height: 250px !important;
        }
    }
</style>
@endpush
