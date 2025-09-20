@extends('super-admin.layout')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Welcome Hero Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border: none; color: white;">
            <div class="card-body py-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3" style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-crown fa-2x"></i>
                            </div>
                            <div>
                                <h1 class="mb-1" style="font-size: 2rem; font-weight: 800;">Welcome back, Super Admin!</h1>
                                <p class="mb-0 opacity-75" style="font-size: 1.1rem;">You have complete control over the marketplace ecosystem</p>
                            </div>
                        </div>
                        <div class="d-flex gap-4 mt-4">
                            <div class="text-center">
                                <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 1rem; backdrop-filter: blur(10px);">
                                    <h3 class="mb-1">{{ \App\Models\Shop::count() }}</h3>
                                    <small class="opacity-75">Active Shops</small>
                                </div>
                            </div>
                            <div class="text-center">
                                <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 1rem; backdrop-filter: blur(10px);">
                                    <h3 class="mb-1">{{ \App\Models\Product::count() }}</h3>
                                    <small class="opacity-75">Total Products</small>
                                </div>
                            </div>
                            <div class="text-center">
                                <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 1rem; backdrop-filter: blur(10px);">
                                    <h3 class="mb-1">{{ \App\Models\Order::count() }}</h3>
                                    <small class="opacity-75">Orders Today</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="position-relative">
                            <div style="width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; backdrop-filter: blur(10px);">
                                <i class="fas fa-chart-line" style="font-size: 4rem; opacity: 0.7;"></i>
                            </div>
                            <div class="position-absolute" style="top: -10px; right: 20px; width: 40px; height: 40px; background: rgba(255,255,255,0.3); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-trending-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics Cards -->
<div class="row g-4 mb-5">
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-store fa-lg text-white"></i>
                </div>
                <h3 class="fw-bold text-primary">{{ \App\Models\Shop::count() }}</h3>
                <p class="text-muted mb-2">Total Shops</p>
                <div class="d-flex justify-content-center align-items-center">
                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">
                        <i class="fas fa-arrow-up me-1"></i>+12%
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--success-color), #047857); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-box-open fa-lg text-white"></i>
                </div>
                <h3 class="fw-bold text-success">{{ \App\Models\Product::count() }}</h3>
                <p class="text-muted mb-2">Products Listed</p>
                <div class="d-flex justify-content-center align-items-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill">
                        <i class="fas fa-arrow-up me-1"></i>+8%
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--warning-color), #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-shopping-cart fa-lg text-white"></i>
                </div>
                <h3 class="fw-bold text-warning">{{ \App\Models\Order::count() }}</h3>
                <p class="text-muted mb-2">Total Orders</p>
                <div class="d-flex justify-content-center align-items-center">
                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill">
                        <i class="fas fa-arrow-up me-1"></i>+24%
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--info-color), #0e7490); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-users fa-lg text-white"></i>
                </div>
                <h3 class="fw-bold text-info">{{ \App\Models\User::where('role', 'admin')->count() }}</h3>
                <p class="text-muted mb-2">Shopkeepers</p>
                <div class="d-flex justify-content-center align-items-center">
                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill">
                        <i class="fas fa-arrow-up me-1"></i>+5%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Management Grid -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Marketplace Management</h2>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                <i class="fas fa-crown me-1"></i>Super Admin Tools
            </span>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 management-card">
            <div class="card-body text-center p-4">
                <div class="mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-box-open fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-3">Product Oversight</h5>
                <p class="text-muted mb-4">Manage all products across every shop in the marketplace</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.products.index') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>View All Products
                    </a>
                    <a href="{{ route('super-admin.products.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 management-card">
            <div class="card-body text-center p-4">
                <div class="mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--success-color), #047857); border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-tags fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-3">Category Control</h5>
                <p class="text-muted mb-4">Define and organize product categories for the entire platform</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.categories.index') }}" class="btn btn-success">
                        <i class="fas fa-list me-2"></i>Manage Categories
                    </a>
                    <a href="{{ route('super-admin.categories.create') }}" class="btn btn-outline-success">
                        <i class="fas fa-plus me-2"></i>New Category
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 management-card">
            <div class="card-body text-center p-4">
                <div class="mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--warning-color), #d97706); border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-store fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-3">Shop Monitoring</h5>
                <p class="text-muted mb-4">Monitor all shops and their performance across the marketplace</p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-warning">
                        <i class="fas fa-chart-bar me-2"></i>Shop Analytics
                    </a>
                    <a href="#" class="btn btn-outline-warning">
                        <i class="fas fa-cog me-2"></i>Shop Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 management-card">
            <div class="card-body text-center p-4">
                <div class="mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--info-color), #0e7490); border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-shopping-cart fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-3">Order Management</h5>
                <p class="text-muted mb-4">View and manage all orders across the entire marketplace</p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-info">
                        <i class="fas fa-list-alt me-2"></i>All Orders
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fas fa-download me-2"></i>Export Data
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 management-card">
            <div class="card-body text-center p-4">
                <div class="mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--danger-color), #b91c1c); border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-users-cog fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-3">User Administration</h5>
                <p class="text-muted mb-4">Manage shopkeepers, customers, and user permissions</p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-danger">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                    <a href="#" class="btn btn-outline-danger">
                        <i class="fas fa-shield-alt me-2"></i>Permissions
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 management-card">
            <div class="card-body text-center p-4">
                <div class="mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--dark-color), #1f2937); border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-chart-line fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-3">Analytics & Reports</h5>
                <p class="text-muted mb-4">Access comprehensive marketplace analytics and insights</p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn" style="background: linear-gradient(135deg, var(--dark-color), #1f2937); color: white;">
                        <i class="fas fa-chart-pie me-2"></i>View Analytics
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-file-export me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Panel -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-bolt me-2 text-primary"></i>Quick Actions
                    </h5>
                    <span class="badge bg-light text-dark">Marketplace Tools</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('super-admin.products.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fas fa-plus fa-2x mb-2"></i>
                            <span class="fw-semibold">Add Product</span>
                            <small class="text-muted">Create new product</small>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('super-admin.categories.create') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fas fa-tag fa-2x mb-2"></i>
                            <span class="fw-semibold">New Category</span>
                            <small class="text-muted">Add category</small>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('super-admin.products.bulk-upload-form') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fas fa-upload fa-2x mb-2"></i>
                            <span class="fw-semibold">Bulk Upload</span>
                            <small class="text-muted">Import products</small>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3" target="_blank">
                            <i class="fas fa-external-link-alt fa-2x mb-2"></i>
                            <span class="fw-semibold">View Store</span>
                            <small class="text-muted">Customer view</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.management-card {
    transition: all 0.3s ease;
}

.management-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
}

.management-card .card-body {
    position: relative;
    overflow: hidden;
}

.management-card .card-body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.management-card:hover .card-body::before {
    opacity: 1;
}
</style>
@endsection
