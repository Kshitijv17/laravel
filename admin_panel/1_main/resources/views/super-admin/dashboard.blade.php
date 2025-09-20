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
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('shopkeeper.logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-crown fa-4x text-warning mb-3"></i>
                        <h1 class="display-5 fw-bold">Welcome, Super Admin!</h1>
                        <p class="lead text-muted">You have complete control over the marketplace</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-store fa-3x mb-3"></i>
                        <h3>{{ \App\Models\Shop::count() }}</h3>
                        <p class="mb-0">Total Shops</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <h3>{{ \App\Models\Product::count() }}</h3>
                        <p class="mb-0">Total Products</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <h3>{{ \App\Models\Order::count() }}</h3>
                        <p class="mb-0">Total Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h3>{{ \App\Models\User::where('role', 'admin')->count() }}</h3>
                        <p class="mb-0">Shopkeepers</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Cards -->
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-box-open fa-3x text-primary mb-3"></i>
                        <h5>Product Management</h5>
                        <p class="text-muted">Manage all products across all shops</p>
                        <a href="{{ route('super-admin.products.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-1"></i>Manage Products
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-tags fa-3x text-success mb-3"></i>
                        <h5>Category Management</h5>
                        <p class="text-muted">Organize products into categories</p>
                        <a href="{{ route('super-admin.categories.index') }}" class="btn btn-success">
                            <i class="fas fa-arrow-right me-1"></i>Manage Categories
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-store fa-3x text-info mb-3"></i>
                        <h5>Shop Oversight</h5>
                        <p class="text-muted">Monitor all shops and their activities</p>
                        <a href="#" class="btn btn-info">
                            <i class="fas fa-arrow-right me-1"></i>View Shops
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-3x text-warning mb-3"></i>
                        <h5>Order Management</h5>
                        <p class="text-muted">View and manage all marketplace orders</p>
                        <a href="#" class="btn btn-warning">
                            <i class="fas fa-arrow-right me-1"></i>View Orders
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users-cog fa-3x text-danger mb-3"></i>
                        <h5>User Management</h5>
                        <p class="text-muted">Manage shopkeepers and customers</p>
                        <a href="#" class="btn btn-danger">
                            <i class="fas fa-arrow-right me-1"></i>Manage Users
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-3x text-secondary mb-3"></i>
                        <h5>Analytics & Reports</h5>
                        <p class="text-muted">View marketplace analytics and reports</p>
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-1"></i>View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="{{ route('super-admin.products.create') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-plus me-1"></i>Add Product
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('super-admin.categories.create') }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-plus me-1"></i>Add Category
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('super-admin.products.bulk-upload-form') }}" class="btn btn-outline-info w-100">
                                    <i class="fas fa-upload me-1"></i>Bulk Upload
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary w-100" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i>View Store
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
