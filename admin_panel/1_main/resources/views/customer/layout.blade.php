<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Store') - Multi-Vendor Marketplace</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #65a30d;
            --secondary-color: #84cc16;
            --accent-color: #a3a3a3;
            --success-color: #16a34a;
            --danger-color: #dc2626;
            --warning-color: #eab308;
            --info-color: #059669;
            --dark-color: #365314;
            --light-color: #f7fee7;
            --border-color: #d9f99d;
            --text-primary: #1a2e05;
            --text-secondary: #365314;
            --text-muted: #65a30d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #ffffff;
            color: var(--text-primary);
            line-height: 1.6;
            font-weight: 400;
        }
        
        /* Modern Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.75rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.025em;
        }
        
        .navbar-nav .nav-link {
            color: var(--text-primary) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: var(--light-color);
            color: var(--primary-color) !important;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 0.75rem;
            padding: 0.5rem;
        }
        
        .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
        }
        
        /* Modern Search Bar */
        .search-bar {
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            background: white;
            transition: all 0.2s ease;
        }
        
        .search-bar:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(101, 163, 13, 0.1);
            outline: none;
        }
        
        .search-btn {
            border: 2px solid var(--primary-color);
            background: var(--primary-color);
            color: white;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .search-btn:hover {
            background: #16a34a;
            border-color: #16a34a;
            transform: translateY(-1px);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #84cc16 0%, #65a30d 50%, #16a34a 100%);
            color: white;
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        /* Modern Product Cards */
        .product-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--primary-color);
        }
        
        .product-image {
            height: 240px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-card .card-body {
            padding: 1.25rem;
        }
        
        .card-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        
        .price-tag {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .original-price {
            text-decoration: line-through;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .discount-badge {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: var(--danger-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }
        
        .shop-badge {
            background: var(--info-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            margin-right: 0.5rem;
            margin-bottom: 0.25rem;
        }
        
        .category-badge {
            background: var(--secondary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .stock-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 500;
            display: inline-block;
        }
        
        .out-of-stock {
            background: #fecaca;
            color: var(--danger-color);
        }
        
        .low-stock {
            background: #fef08a;
            color: #a16207;
        }
        
        .in-stock {
            background: #dcfce7;
            color: #16a34a;
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
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #16a34a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(101, 163, 13, 0.4);
        }
        
        .btn-buy-now {
            background: linear-gradient(135deg, #84cc16, #65a30d);
            color: white;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }
        
        .btn-buy-now:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(132, 204, 22, 0.4);
            color: white;
        }
        
        .btn-outline-secondary {
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
            background: white;
        }
        
        .btn-outline-secondary:hover {
            background: var(--light-color);
            border-color: var(--secondary-color);
            color: var(--text-primary);
        }
        
        /* Filter Card */
        .filter-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .filter-card .card-body {
            padding: 1.5rem;
        }
        
        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(101, 163, 13, 0.1);
            outline: none;
        }
        
        /* Section Headings */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.025em;
        }
        
        .display-4 {
            font-weight: 800;
            letter-spacing: -0.05em;
        }
        
        .lead {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }
        
        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1.5rem;
            margin-top: 4rem;
        }
        
        .footer h5, .footer h6 {
            color: white;
            font-weight: 600;
        }
        
        .footer .text-muted {
            color: #94a3b8 !important;
        }
        
        .footer a {
            transition: color 0.2s ease;
        }
        
        .footer a:hover {
            color: var(--primary-color) !important;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section {
                padding: 3rem 0;
            }
            
            .display-4 {
                font-size: 2rem;
            }
            
            .product-image {
                height: 200px;
            }
            
            .navbar-brand {
                font-size: 1.5rem;
            }
        }
        
        /* Additional Modern Styles */
        .min-vh-50 {
            min-height: 50vh;
        }
        
        .backdrop-blur {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .bg-opacity-20 {
            background-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        .rounded-4 {
            border-radius: 1.5rem !important;
        }
        
        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        
        .fs-5 {
            font-size: 1.25rem !important;
        }
        
        /* Section Spacing */
        section {
            position: relative;
        }
        
        /* Kind Words Section Styling */
        .testimonial-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--border-color);
        }
        
        /* Utility Classes */
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-light {
            background-color: var(--light-color) !important;
        }
        
        .border-light {
            border-color: var(--border-color) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
                <a class="navbar-brand" href="{{ route('customer.home') }}">
                <i class="fas fa-home me-2"></i>H
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.home') }}">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-th-large me-1"></i>Categories
                        </a>
                        <ul class="dropdown-menu">
                            @php
                                $categories = \App\Models\Category::orderBy('title')->get();
                            @endphp
                            @foreach($categories as $category)
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.category', $category) }}">
                                        {{ $category->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-store me-1"></i>Shops
                        </a>
                        <ul class="dropdown-menu">
                            @php
                                $shops = \App\Models\Shop::where('is_active', true)->orderBy('name')->get();
                            @endphp
                            @foreach($shops as $shop)
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.shop', $shop) }}">
                                        {{ $shop->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
                
                <!-- Search Form -->
                <form class="d-flex me-3" method="GET" action="{{ route('customer.home') }}">
                    <input class="form-control search-bar me-2" type="search" name="search" 
                           placeholder="Search products..." value="{{ request('search') }}">
                    <button class="btn btn-light search-btn" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <!-- User Menu -->
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders') }}"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.wishlist') }}"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('user.logout') }}">
                                        @csrf
                                        <button class="dropdown-item" type="submit">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.register') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-store me-2"></i>E-Store</h5>
                    <p class="text-muted">Your trusted multi-vendor marketplace for quality products from verified sellers.</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('customer.home') }}" class="text-muted">Home</a></li>
                        <li><a href="#" class="text-muted">About Us</a></li>
                        <li><a href="#" class="text-muted">Contact</a></li>
                        <li><a href="#" class="text-muted">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        @foreach($categories->take(4) as $category)
                            <li><a href="{{ route('customer.category', $category) }}" class="text-muted">{{ $category->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Help Center</a></li>
                        <li><a href="#" class="text-muted">Shipping Info</a></li>
                        <li><a href="#" class="text-muted">Returns</a></li>
                        <li><a href="#" class="text-muted">Track Order</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Sellers</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('shopkeeper.login') }}" class="text-muted">Sell on E-Store</a></li>
                        <li><a href="#" class="text-muted">Seller Guidelines</a></li>
                        <li><a href="#" class="text-muted">Seller Support</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; {{ date('Y') }} E-Store. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted mb-0">
                        <a href="#" class="text-muted me-3">Privacy Policy</a>
                        <a href="#" class="text-muted me-3">Terms of Service</a>
                        <a href="#" class="text-muted">Cookie Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Add to cart animation
        function addToCart(productId) {
            // Add your cart functionality here
            alert('Product added to cart!');
        }
        
        // Search suggestions (can be enhanced with AJAX)
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    // Add search suggestions functionality here
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
