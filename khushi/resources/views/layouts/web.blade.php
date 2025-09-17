<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Modern UI CSS -->
    <link href="{{ asset('css/modern-ui.css') }}" rel="stylesheet">
    <!-- Premium UI CSS -->
    <link href="{{ asset('css/premium-ui.css') }}" rel="stylesheet">
    <!-- Hero Enhancements CSS -->
    <link href="{{ asset('css/hero-enhancements.css') }}" rel="stylesheet">
    <!-- Layout Fix CSS -->
    <link href="{{ asset('css/layout-fix.css') }}" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @stack('styles')
</head>
<body class="font-sans antialiased" style="font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh;">
    <div class="min-vh-100 d-flex flex-column">
        <!-- Header -->
        <header class="position-sticky top-0" style="z-index: 1000;">
            <nav class="navbar navbar-expand-lg navbar-light" id="mainNavbar">
                <div class="container">
                    <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ url('/') }}">
                        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="me-2" style="height:32px; max-height:32px; width:auto;">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="menDropdown" role="button" data-bs-toggle="dropdown">
                                    Men
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/categories/men-shirts">Shirts</a></li>
                                    <li><a class="dropdown-item" href="/categories/men-tshirts">T-Shirts</a></li>
                                    <li><a class="dropdown-item" href="/categories/men-jeans">Jeans</a></li>
                                    <li><a class="dropdown-item" href="/categories/men-formal">Formal Wear</a></li>
                                    <li><a class="dropdown-item" href="/categories/men-ethnic">Ethnic Wear</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="womenDropdown" role="button" data-bs-toggle="dropdown">
                                    Women
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/categories/women-kurtis">Kurtis</a></li>
                                    <li><a class="dropdown-item" href="/categories/women-sarees">Sarees</a></li>
                                    <li><a class="dropdown-item" href="/categories/women-dresses">Dresses</a></li>
                                    <li><a class="dropdown-item" href="/categories/women-tops">Tops & Shirts</a></li>
                                    <li><a class="dropdown-item" href="/categories/women-ethnic">Ethnic Wear</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="kidsDropdown" role="button" data-bs-toggle="dropdown">
                                    Kids
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/categories/boys-clothing">Boys</a></li>
                                    <li><a class="dropdown-item" href="/categories/girls-clothing">Girls</a></li>
                                    <li><a class="dropdown-item" href="/categories/baby-clothing">Baby</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/categories/footwear">Footwear</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/categories/accessories">Accessories</a>
                            </li>
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Language Switcher -->
                            <li class="nav-item me-2">
                                @include('components.language-switcher')
                            </li>
                            
                            <!-- Currency Switcher -->
                            <li class="nav-item me-2">
                                @include('components.currency-switcher')
                            </li>
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" 
                                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('user.dashboard') }}">Dashboard</a>
                                        <a class="dropdown-item" href="{{ route('user.profile') }}">Profile</a>
                                        <a class="dropdown-item" href="{{ route('user.orders') }}">Orders</a>
                                        <a class="dropdown-item" href="{{ route('user.wishlist') }}">Wishlist</a>
                                        <a class="dropdown-item" href="{{ route('chat.index') }}">Support Chat</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Page Content -->
        <main class="flex-grow-1 py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-dark text-white py-4 mt-auto">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <h5>About Us</h5>
                        <p class="text-muted">Your one-stop shop for all your needs. Quality products at affordable prices.</p>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <h5>Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="/about" class="text-decoration-none text-muted">About Us</a></li>
                            <li><a href="/contact" class="text-decoration-none text-muted">Contact</a></li>
                            <li><a href="/privacy" class="text-decoration-none text-muted">Privacy Policy</a></li>
                            <li><a href="/terms" class="text-decoration-none text-muted">Terms & Conditions</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Contact Us</h5>
                        <ul class="list-unstyled text-muted">
                            <li><i class="fas fa-map-marker-alt me-2"></i> 123 Street, City, Country</li>
                            <li><i class="fas fa-phone me-2"></i> +1 234 567 890</li>
                            <li><i class="fas fa-envelope me-2"></i> info@example.com</li>
                        </ul>
                    </div>
                </div>
                <hr class="my-4 bg-secondary">
                <div class="text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
    
    <!-- Modern Animations JS -->
    <script src="{{ asset('js/modern-animations.js') }}"></script>
    
    <script>
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Flash message auto-hide with animation
        document.addEventListener('DOMContentLoaded', function() {
            var alertList = [].slice.call(document.querySelectorAll('.alert'));
            alertList.forEach(function (alert) {
                alert.style.animation = 'fadeInUp 0.5s ease-out';
                setTimeout(function() {
                    alert.style.animation = 'fadeOut 0.3s ease-in-out';
                    setTimeout(function() {
                        if (alert.parentElement) {
                            alert.remove();
                        }
                    }, 300);
                }, 5000);
            });
        });
        
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(-20px); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
