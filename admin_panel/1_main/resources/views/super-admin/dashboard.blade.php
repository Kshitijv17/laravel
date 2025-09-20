<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-crown text-warning me-2"></i>Super Admin Panel
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->name }}
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
