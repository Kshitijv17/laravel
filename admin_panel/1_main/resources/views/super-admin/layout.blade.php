<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin Panel')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #65a30d;
            --secondary-color: #84cc16;
            --accent-color: #16a34a;
            --success-color: #059669;
            --warning-color: #eab308;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --dark-color: #365314;
            --light-color: #f7fee7;
            --border-color: #d9f99d;
            --text-primary: #1a2e05;
            --text-secondary: #365314;
            --text-muted: #65a30d;
            --sidebar-width: 280px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #84cc16 0%, #65a30d 50%, #16a34a 100%);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }
        
        /* Modern Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border-color);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
            letter-spacing: -0.025em;
        }
        
        .sidebar-brand:hover {
            color: var(--accent-color);
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-section {
            margin-bottom: 2rem;
        }
        
        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1.5rem 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            margin: 0.125rem 0.75rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }
        
        .sidebar .nav-link:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(101, 163, 13, 0.3);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            box-shadow: 0 4px 12px rgba(101, 163, 13, 0.3);
        }
        
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: white;
            border-radius: 0 2px 2px 0;
        }
        
        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .top-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .notification-btn:hover {
            background: var(--light-color);
            color: var(--primary-color);
        }
        
        .notification-badge {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            width: 8px;
            height: 8px;
            background: var(--danger-color);
            border-radius: 50%;
        }
        
        .user-dropdown {
            position: relative;
        }
        
        .user-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: none;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .user-btn:hover {
            background: var(--light-color);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .main-content {
            padding: 2rem;
            background: transparent;
        }
        
        /* Modern Cards */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 1rem 1rem 0 0 !important;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Modern Buttons */
        .btn {
            font-weight: 600;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            border: none;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(101, 163, 13, 0.4);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #047857);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            color: white;
        }
        
        .btn-warning:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(234, 179, 8, 0.4);
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info-color), #0e7490);
            color: white;
        }
        
        .btn-info:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(8, 145, 178, 0.4);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #b91c1c);
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
            color: white;
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: rgba(5, 150, 105, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-wrapper {
                margin-left: 0;
            }
            
            .main-content {
                padding: 1rem;
            }
        }
        
        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 2px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
    </style>
</head>
<body>
    <!-- Modern Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('super-admin.dashboard') }}" class="sidebar-brand">
                <i class="fas fa-crown me-2"></i>
                <span>SuperAdmin</span>
            </a>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Overview</div>
                <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytics</span>
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Marketplace</div>
                <a href="{{ route('super-admin.products.index') }}" class="nav-link {{ request()->routeIs('super-admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i>
                    <span>All Products</span>
                </a>
                <a href="{{ route('super-admin.categories.index') }}" class="nav-link {{ request()->routeIs('super-admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-store"></i>
                    <span>Shops</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="#" class="nav-link">
                    <i class="fas fa-users-cog"></i>
                    <span>Users</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-shield-alt"></i>
                    <span>Permissions</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Tools</div>
                <a href="{{ route('customer.home') }}" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Store</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-download"></i>
                    <span>Reports</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navigation -->
        <div class="top-navbar">
            <div class="navbar-content">
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                
                <div class="navbar-actions">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <div class="notification-badge"></div>
                    </button>
                    
                    <div class="user-dropdown dropdown">
                        <button class="user-btn" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name }}</div>
                                <div class="user-role" style="font-size: 0.75rem; color: var(--text-muted);">Super Admin</div>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 0.75rem; color: var(--text-muted);"></i>
                        </button>
                        
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('super-admin.dashboard') }}"><i class="fas fa-chart-pie me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
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
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
