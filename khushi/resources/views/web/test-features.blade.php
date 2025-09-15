@extends('layouts.web')

@section('title', __('messages.features'))

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }
    
    .feature-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .feature-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }
    
    .btn-modern {
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .status-badge {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .section-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: center;
        color: #2c3e50;
    }
    
    .gradient-bg-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .gradient-bg-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .gradient-bg-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .gradient-bg-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .gradient-bg-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .gradient-bg-6 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
    
    .admin-section {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        border-radius: 15px;
        padding: 2rem;
        color: white;
        margin: 2rem 0;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">{{ __('messages.comprehensive_ecommerce_platform') }}</h1>
        <p class="lead mb-4">{{ __('messages.test_explore_features') }}</p>
        <div class="status-badge">All Systems Operational</div>
    </div>
</div>

<div class="container">
    <!-- Internationalization Features -->
    <h2 class="section-title">🌍 Global Features</h2>
    <div class="row mb-5">
        <div class="col-lg-6 mb-4">
            <div class="feature-card card gradient-bg-1 text-white">
                <div class="card-body text-center p-4">
                    <span class="feature-icon">🌐</span>
                    <h5 class="card-title">{{ __('messages.language_switching') }}</h5>
                    <p class="card-text">{{ __('messages.test_language_currency') }}</p>
                    <p>Current: <strong>{{ strtoupper(app()->getLocale()) }}</strong></p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="/locale/en" class="btn btn-light btn-modern btn-sm">🇺🇸 EN</a>
                        <a href="/locale/es" class="btn btn-light btn-modern btn-sm">🇪🇸 ES</a>
                        <a href="/locale/fr" class="btn btn-light btn-modern btn-sm">🇫🇷 FR</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="feature-card card gradient-bg-2 text-white">
                <div class="card-body text-center p-4">
                    <span class="feature-icon">💱</span>
                    <a href="#" class="btn btn-success">{{ __('messages.switch_currency') }}</a>
                    <h4 class="fw-bold">Multi-Currency</h4>
                    <p class="mb-3">Active: <strong>{{ session('currency', 'USD') }}</strong></p>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold">$99.99</div>
                            <small>Standard</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold">$299.99</div>
                            <small>Premium</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Communication Features -->
    <h2 class="section-title">💬 Communication</h2>
    <div class="row mb-5">
        <div class="col-12">
            <div class="feature-card card gradient-bg-3 text-white">
                <div class="card-body text-center p-4">
                    <span class="feature-icon">🗨️</span>
                    <h4 class="fw-bold">Real-time Support Chat</h4>
                    <p class="mb-3">Instant messaging with typing indicators & file attachments</p>
                    <a href="{{ route('chat.index') }}" class="btn btn-light btn-modern">
                        <i class="fas fa-comments me-2"></i>Start Chat
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Shopping Features -->
    <h2 class="section-title">🛍️ Shopping Experience</h2>
    <div class="row mb-5">
        <div class="col-lg-4 mb-4">
            <div class="feature-card card gradient-bg-4 text-white">
                <div class="card-body text-center p-4">
                    <span class="feature-icon">🛒</span>
                    <h5 class="card-title">{{ __('messages.product_browsing') }}</h5>
                    <p class="card-text">{{ __('messages.explore_product_catalog') }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-modern">View All</a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-modern">Categories</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="feature-card card gradient-bg-5 text-white">
                <div class="card-body text-center p-4">
                    <span class="feature-icon">📊</span>
                    <h4 class="fw-bold">Compare</h4>
                    <p class="mb-3">Side-by-side analysis</p>
                    <a href="#" class="btn btn-primary">{{ __('messages.switch_language') }}</a>
                    <a href="/compare" class="btn btn-light btn-modern">Compare Now</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="feature-card card gradient-bg-6 text-dark">
                <div class="card-body text-center p-4">
                    <span class="feature-icon">🔍</span>
                    <h4 class="fw-bold">Search</h4>
                    <p class="mb-3">Find products...</p>
                    <form action="{{ route('search') }}" method="GET" class="mt-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Find products...">
                            <button class="btn btn-dark btn-modern" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Features -->
    @auth
        @if(auth()->user()->role === 'admin')
        <div class="admin-section">
            <div class="text-center mb-4">
                <span class="feature-icon">⚙️</span>
                <h5 class="card-title">{{ __('messages.admin_panel') }}</h5>
                <p class="card-text">{{ __('messages.admin_dashboard_management') }}</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="/admin/inventory" class="btn btn-light btn-modern w-100">
                        <i class="fas fa-boxes me-2"></i>Inventory
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="/admin/chat" class="btn btn-outline-light btn-modern w-100">
                        <i class="fas fa-comments me-2"></i>Chat Management
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="/admin/reports" class="btn btn-outline-light btn-modern w-100">
                        <i class="fas fa-chart-bar me-2"></i>Analytics
                    </a>
                </div>
            </div>
        </div>
        @endif
    @endauth

    <!-- System Status -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="feature-card card">
                <div class="card-body p-4">
                    <h2 class="text-center mb-5">{{ __('messages.ecommerce_features') }}</h2>
                    <div class="row text-center">
                        <div class="col-md-2 mb-3">
                            <div class="text-success fs-2">✓</div>
                            <small>Languages</small>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-success fs-2">✓</div>
                            <small>Currency</small>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-success fs-2">✓</div>
                            <small>Chat</small>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-success fs-2">✓</div>
                            <small>Products</small>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-success fs-2">✓</div>
                            <small>Search</small>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="text-success fs-2">✓</div>
                            <small>Admin</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
