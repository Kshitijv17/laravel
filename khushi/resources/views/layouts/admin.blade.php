<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin Panel') - E-Commerce Store</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 72px;
            --header-height: 70px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #2c3e50;
            --light-color: #f8f9fc;
            --nav-bg: #ffffff;
            --nav-text: #5a5c69;
            --nav-border: #e3e6f0;
        }
        /* Make left-side group flex and allow shrinking */
        .top-navbar > .d-flex {
            flex: 1 1 auto;
            min-width: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--light-color);
            line-height: 1.6;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            box-shadow: 0 0 35px 0 rgba(154, 161, 171, 0.15);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            padding: 1.5rem 1.25rem;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1.5rem;
        }
        
        .sidebar-brand h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .nav-section {
            margin-bottom: 2rem;
        }
        
        .nav-section-title {
            padding: 0 1.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.6);
            margin-bottom: 0.5rem;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.25rem;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
            position: relative;
            font-weight: 500;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(4px);
        }
        
        .nav-link:hover::before {
            background: #fff;
        }
        
        .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.15);
            font-weight: 600;
        }
        
        .nav-link.active::before {
            background: #fff;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 1rem;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }

        /* Collapsed sidebar (desktop) */
        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }
        body.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }
        body.sidebar-collapsed .sidebar .nav-section-title,
        body.sidebar-collapsed .sidebar .nav-link span,
        body.sidebar-collapsed .sidebar .sidebar-brand span {
            display: none !important;
        }
        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        body.sidebar-collapsed .sidebar .nav-link i {
            margin: 0;
            font-size: 1.2rem;
        }
        body.sidebar-collapsed .sidebar .sidebar-brand {
            justify-content: center;
        }
        
        .top-navbar {
            background: var(--nav-bg);
            height: var(--header-height);
            border-bottom: 1px solid var(--nav-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
            gap: 1rem;
            flex-wrap: nowrap;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--nav-text);
            font-size: 1.3rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: #f8f9fc;
            color: var(--primary-color);
        }
        
        .navbar-search {
            flex: 1 1 clamp(180px, 22vw, 320px);
            max-width: 100%;
            min-width: 0; /* allow flex item to shrink without overflowing */
            margin: 0 1rem;
        }
        
        .search-input {
            border: 1px solid #e3e6f0;
            border-radius: 25px;
            padding: 0.5rem 1rem;
            width: 100%;
            background: #f8f9fc;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }
        
        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: auto;
            flex-shrink: 0; /* don't allow right-side group to shrink and wrap */
            white-space: nowrap; /* keep items on one line */
        }
        
        .nav-notification {
            position: relative;
            background: none;
            border: none;
            color: var(--nav-text);
            font-size: 1.2rem;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .nav-notification:hover {
            background: #f8f9fc;
            color: var(--primary-color);
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-dropdown .dropdown-toggle {
            background: none;
            border: none;
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            line-height: 1;
        }
        
        .user-dropdown .dropdown-toggle:hover {
            background: #f8f9fc;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        .user-dropdown .dropdown-toggle span {
            white-space: nowrap;              /* prevent wrapping onto a new line */
            max-width: 140px;                 /* constrain long names */
            overflow: hidden;                 /* hide overflow */
            text-overflow: ellipsis;          /* show ellipsis when truncated */
            margin-left: 0.5rem;
        }
        
        /* Normalize right-side control heights to prevent overflow below navbar */
        .navbar-nav .nav-notification,
        .navbar-nav .dropdown-toggle {
            min-height: 40px;
        }
        
        .content-wrapper {
            padding: 1.5rem;
            background: var(--light-color);
            min-height: calc(100vh - var(--header-height));
            width: 100%;
            box-sizing: border-box;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }
        
        .page-subtitle {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin: 0;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 15px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #f1f3f4;
            padding: 1.25rem;
            border-radius: 10px 10px 0 0;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 600;
            padding: 0.625rem 1.25rem;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(78, 115, 223, 0.3);
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
            font-size: 0.875rem;
        }
        
        .table thead th {
            background: #f8f9fc;
            border: none;
            font-weight: 600;
            color: var(--dark-color);
            padding: 0.875rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody td {
            padding: 0.875rem;
            border-color: #f1f3f4;
            vertical-align: middle;
        }
        
        .badge {
            font-weight: 600;
            padding: 0.375rem 0.625rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-info { color: var(--info-color) !important; }
        .text-warning { color: var(--warning-color) !important; }
        .text-danger { color: var(--danger-color) !important; }
        .text-secondary { color: var(--secondary-color) !important; }
        
        .badge-success { background-color: var(--success-color); }
        .badge-warning { background-color: var(--warning-color); }
        .badge-danger { background-color: var(--danger-color); }
        .badge-secondary { background-color: var(--secondary-color); }
        
        /* DataTables Fixes */
        .dataTables_wrapper {
            margin-top: 0 !important;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin: 0.5rem 0;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem;
            margin: 0 2px;
            border-radius: 4px;
        }
        
        /* Prevent duplicate DataTables controls */
        .dataTables_wrapper:not(:first-of-type) {
            display: none !important;
        }
        
        /* Fix dropdown positioning */
        .dropdown-menu {
            z-index: 1050 !important;
            min-width: 200px;
            max-width: calc(100vw - 1rem);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 0;
            margin-top: 8px;
        }
        .navbar-nav { position: relative; overflow: visible; }
        .top-navbar { overflow: visible; }
        .navbar-nav .dropdown-menu { right: 0; left: auto; }
        
        .dropdown-menu .dropdown-item {
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fc;
            color: var(--primary-color);
        }
        
        .dropdown-menu .dropdown-header {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .dropdown-toggle::after {
            display: none !important;
        }
        
        /* Medium viewport adjustments to prevent wrapping */
        @media (max-width: 1200px) {
            .top-navbar { padding: 0 1.25rem; }
            .navbar-search { flex-basis: clamp(160px, 20vw, 280px); margin: 0 0.75rem; }
            .navbar-nav { gap: 0.5rem; }
            /* Hide user name text to save space but keep avatar and chevron */
            .user-dropdown .dropdown-toggle span { display: none; }
            /* Hide optional utility icons to avoid overflow */
            .navbar-nav #fullscreenToggle,
            .navbar-nav .dropdown.me-1 { display: none; }
        }
        
        @media (max-width: 992px) {
            .navbar-search { flex-basis: clamp(140px, 30vw, 240px); margin: 0 0.5rem; }
            .navbar-nav { gap: 0.5rem; }
            /* Ensure no overflow on tablets */
            .navbar-nav #fullscreenToggle,
            .navbar-nav .dropdown.me-1 { display: none; }
        }
        
        /* Header fixes */
        .header {
            position: sticky;
            top: 0;
            z-index: 1040;
            background: var(--nav-bg);
            border-bottom: 1px solid var(--nav-border);
        }

        /* Dark theme overrides (focused on top navbar) */
        .theme-dark {
            --nav-bg: #0f172a; /* slate-900 */
            --nav-text: #e5e7eb; /* gray-200 */
            --nav-border: #1f2937; /* gray-800 */
            --light-color: #0b1220; /* body bg for content */
            --dark-color: #e5e7eb; /* text color */
        }
        .theme-dark .search-input {
            background: #0b1220;
            color: #e5e7eb;
            border-color: #1f2937;
        }
        .theme-dark .search-input::placeholder { color: #9ca3af; }
        .theme-dark .nav-notification:hover,
        .theme-dark .sidebar-toggle:hover { background: #111827; color: #f3f4f6; }
        
        .chart-area {
            position: relative;
            height: 400px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .content-wrapper {
                padding: 1rem 0.5rem;
                margin-left: 0;
            }
            
            .navbar-search {
                display: none;
            }
            
            .top-navbar {
                padding: 0 1rem;
                margin-left: 0;
            }
        }
        
        @media (max-width: 576px) {
            .content-wrapper {
                padding: 0.5rem;
            }
            
            .top-navbar {
                padding: 0 0.5rem;
            }
            
            .page-header {
                margin-bottom: 1rem;
            }
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-store me-2"></i>
            <span>Admin Panel</span>
        </a>
        
        <ul class="sidebar-nav list-unstyled">
            <div class="nav-section">
                <div class="nav-section-title">MAIN</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">E-COMMERCE</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" 
                       href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}" 
                       href="{{ route('admin.products.index') }}">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" 
                       href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-tags"></i>
                        <span>Categories</span>
                    </a>
                </li>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">MANAGEMENT</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.banners*') ? 'active' : '' }}" 
                       href="{{ route('admin.banners.index') }}">
                        <i class="fas fa-image"></i>
                        <span>Banners</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}" 
                       href="{{ route('admin.coupons.index') }}">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Coupons</span>
                    </a>
                </li>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">SUPPORT</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.support*') ? 'active' : '' }}" 
                       href="{{ route('admin.support.index') }}">
                        <i class="fas fa-headset"></i>
                        <span>Support Tickets</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.newsletter*') ? 'active' : '' }}" 
                       href="{{ route('admin.newsletter.index') }}">
                        <i class="fas fa-envelope"></i>
                        <span>Newsletter</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}" 
                       href="{{ route('admin.analytics') }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytics</span>
                    </a>
                </li>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">SYSTEM</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" 
                       href="{{ route('admin.settings') }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </div>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar" aria-expanded="true">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="navbar-search">
                    <input type="text" class="search-input" placeholder="Search...">
                </div>
            </div>
            
            <div class="navbar-nav">
                <!-- Quick Actions -->
                <div class="dropdown me-1">
                    <button class="nav-notification" data-bs-toggle="dropdown" data-bs-auto-close="true" data-bs-boundary="viewport" data-bs-offset="0,8" aria-expanded="false" title="Quick actions">
                        <i class="fas fa-bolt"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">Quick Actions</li>
                        <li><button class="dropdown-item" type="button" onclick="clearCache('all')"><i class="fas fa-broom me-2"></i>Clear All Cache</button></li>
                        <li><button class="dropdown-item" type="button" onclick="clearCache('config')"><i class="fas fa-cog me-2"></i>Clear Config Cache</button></li>
                        <li><button class="dropdown-item" type="button" onclick="clearCache('route')"><i class="fas fa-route me-2"></i>Clear Route Cache</button></li>
                        <li><button class="dropdown-item" type="button" onclick="clearCache('view')"><i class="fas fa-eye me-2"></i>Clear View Cache</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item" type="button" onclick="optimizeDatabase()"><i class="fas fa-database me-2"></i>Optimize Application</button></li>
                    </ul>
                </div>
                <button class="nav-notification" id="fullscreenToggle" title="Toggle fullscreen" aria-label="Toggle fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
                <button class="nav-notification" id="themeToggle" title="Toggle theme" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
                <!-- Notifications -->
                <button class="nav-notification" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                
                <!-- Messages -->
                <button class="nav-notification" data-bs-toggle="dropdown">
                    <i class="fas fa-envelope"></i>
                    <span class="notification-badge">5</span>
                </button>
                
                <!-- User Dropdown -->
                <div class="user-dropdown dropdown">
                    <button class="dropdown-toggle" data-bs-toggle="dropdown">
                        @if(auth('admin')->user()->avatar)
                        <img src="{{ asset('storage/' . auth('admin')->user()->avatar) }}" 
                             alt="Avatar" class="user-avatar">
                        @else
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center user-avatar">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        @endif
                        <span class="text-dark">{{ auth('admin')->user()->name }}</span>
                        <i class="fas fa-chevron-down ms-2 text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.settings') }}">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper fade-in">
            @yield('content')
        </div>
    </div>

    <!-- jQuery (must be loaded first) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const body = document.body;
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                sidebar.classList.toggle('show');
            } else {
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapsed') ? '1' : '0');
                handleSidebarTooltips();
            }

            const btn = document.querySelector('.sidebar-toggle');
            if (btn) {
                const expanded = isMobile ? sidebar.classList.contains('show') : !body.classList.contains('sidebar-collapsed');
                btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }
        }

        // Apply persisted collapsed state on desktop and init tooltips
        (function() {
            const collapsed = localStorage.getItem('sidebarCollapsed') === '1';
            if (collapsed && window.innerWidth > 768) {
                document.body.classList.add('sidebar-collapsed');
            }
            handleSidebarTooltips();
        })();

        // Re-evaluate on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                document.body.classList.remove('sidebar-collapsed');
            } else {
                const collapsed = localStorage.getItem('sidebarCollapsed') === '1';
                if (collapsed) {
                    document.body.classList.add('sidebar-collapsed');
                }
            }
            handleSidebarTooltips();
        });

        function handleSidebarTooltips() {
            const isCollapsed = document.body.classList.contains('sidebar-collapsed') && window.innerWidth > 768;
            const links = document.querySelectorAll('.sidebar .nav-link');
            links.forEach(link => {
                if (link._tooltip) {
                    link._tooltip.dispose();
                    link._tooltip = null;
                }
                if (isCollapsed) {
                    const labelEl = link.querySelector('span');
                    const label = labelEl ? labelEl.textContent.trim() : link.textContent.trim();
                    link.setAttribute('data-bs-toggle', 'tooltip');
                    link.setAttribute('data-bs-placement', 'right');
                    link.setAttribute('title', label);
                    try {
                        link._tooltip = new bootstrap.Tooltip(link);
                    } catch (e) {}
                } else {
                    link.removeAttribute('data-bs-toggle');
                    link.removeAttribute('data-bs-placement');
                    link.removeAttribute('title');
                }
            });
        }
        
        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Remove global DataTables initialization to prevent conflicts
        // Individual pages will handle their own DataTable initialization
        
        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Confirm delete actions
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }
        
        // AJAX form submission helper
        function submitForm(formId, successCallback = null) {
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (successCallback) {
                        successCallback(data);
                    } else {
                        location.reload();
                    }
                } else {
                    alert(data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request');
            });
        }

        // Theme toggle
        function initTheme() {
            const saved = localStorage.getItem('theme');
            const isDark = saved === 'dark';
            if (isDark) document.body.classList.add('theme-dark');
            const icon = document.querySelector('#themeToggle i');
            if (icon) {
                icon.classList.toggle('fa-sun', isDark);
                icon.classList.toggle('fa-moon', !isDark);
            }
        }
        function toggleTheme() {
            const isDark = document.body.classList.toggle('theme-dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            const icon = document.querySelector('#themeToggle i');
            if (icon) {
                icon.classList.toggle('fa-sun', isDark);
                icon.classList.toggle('fa-moon', !isDark);
            }
            showToast(isDark ? 'Dark theme enabled' : 'Light theme enabled', 'info');
        }

        // Fullscreen controls
        function setFullscreenIcon() {
            const icon = document.querySelector('#fullscreenToggle i');
            if (icon) {
                const isFs = !!document.fullscreenElement;
                icon.classList.toggle('fa-expand', !isFs);
                icon.classList.toggle('fa-compress', isFs);
            }
        }
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen?.();
            } else {
                document.exitFullscreen?.();
            }
        }
        document.addEventListener('fullscreenchange', setFullscreenIcon);

        // Close any open dropdowns (used after quick action click)
        function closeOpenDropdowns() {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                const toggle = menu.previousElementSibling;
                if (toggle) {
                    const inst = bootstrap.Dropdown.getInstance(toggle) || new bootstrap.Dropdown(toggle);
                    inst.hide();
                }
            });
            if (document.activeElement) {
                try { document.activeElement.blur(); } catch (e) {}
            }
        }

        // Quick actions API
        function clearCache(type = 'all') {
            closeOpenDropdowns();
            fetch('{{ route('admin.cache.clear') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({ type })
            })
            .then(r => r.json())
            .then(data => {
                showToast(data.message || 'Cache cleared', data.success ? 'success' : 'error');
            })
            .catch(() => showToast('Error clearing cache', 'error'));
        }
        function optimizeDatabase() {
            closeOpenDropdowns();
            fetch('{{ route('admin.database.optimize') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                showToast(data.message || 'Optimization complete', data.success ? 'success' : 'error');
            })
            .catch(() => showToast('Error optimizing database', 'error'));
        }

        // Bootstrap toasts helper
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            if (!container) return alert(message);
            const toastEl = document.createElement('div');
            let classes = 'bg-info text-dark';
            if (type === 'success') classes = 'bg-success';
            if (type === 'error') classes = 'bg-danger';
            if (type === 'warning') classes = 'bg-warning text-dark';
            toastEl.className = `toast align-items-center text-white border-0 ${classes}`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        // Initialize controls on load
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            setFullscreenIcon();
            document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);
            document.getElementById('fullscreenToggle')?.addEventListener('click', toggleFullscreen);
        });
    </script>
    
    <!-- Toast container -->
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>

    <!-- Global Modal Backdrop Fix -->
    <script>
    $(document).ready(function() {
        // Global function to force remove modal backdrop
        window.forceRemoveBackdrop = function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
            $('html').removeClass('modal-open');
        };
        
        // Initial cleanup on page load
        forceRemoveBackdrop();
        
        // Aggressive cleanup for first 2 seconds
        let cleanupAttempts = 0;
        const cleanupInterval = setInterval(function() {
            forceRemoveBackdrop();
            cleanupAttempts++;
            if (cleanupAttempts > 20) {
                clearInterval(cleanupInterval);
            }
        }, 100);
        
        // Global modal event handlers
        $(document).on('hidden.bs.modal', '.modal', function () {
            forceRemoveBackdrop();
        });
        
        $(document).on('show.bs.modal', '.modal', function () {
            forceRemoveBackdrop();
        });
        
        // Click handler to remove backdrop
        $(document).on('click', '.modal-backdrop', function() {
            forceRemoveBackdrop();
        });
        
        // Cleanup on window focus (when switching tabs)
        $(window).on('focus', function() {
            forceRemoveBackdrop();
        });
    });
    </script>
    
    @stack('scripts')
</body>
</html>
