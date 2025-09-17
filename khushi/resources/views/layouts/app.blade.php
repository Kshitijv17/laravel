<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'srcreationworld')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            /* Fashion brand palette */
            --primary-color: #ff3f6c; /* brand pink */
            --secondary-color: #6b7280; /* muted gray */
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #111827;
            --brand-grad-start: #ff3f6c;
            --brand-grad-end: #ff905a;
            --chip-bg: #ffffff;
            --chip-border: #e5e7eb;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--primary-color) !important;
            letter-spacing: .3px;
            display:flex; align-items:center;
        }
        .brand-logo { width: 34px; height: 34px; object-fit: contain; border-radius: 6px; background: transparent; }
        .brand-text { text-transform: lowercase; font-weight: 800; letter-spacing: .5px; }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #e7335c; /* darker brand pink */
            border-color: #e7335c;
        }
        
        .card {
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .product-card {
            position: relative;
            overflow: hidden;
        }
        
        .product-card .card-img-top {
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Out-of-stock overlay */
        .product-card .oos-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,.35);
            backdrop-filter: blur(1px);
            color: #fff;
            z-index: 2;
        }
        .product-card .oos-overlay .badge { font-size: .9rem; }

        /* Small hover bg utility (used in live search rows) */
        .hover-bg:hover { background: #f8fafc; }
        
        .hero-section {
            background: linear-gradient(135deg, var(--brand-grad-start) 0%, var(--brand-grad-end) 100%);
            color: white;
            padding: 100px 0;
        }
        
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 50px 0 20px;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .price {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.25rem;
        }
        
        .original-price {
            text-decoration: line-through;
            color: var(--secondary-color);
            font-size: 1rem;
        }
        
        .rating {
            color: #fbbf24;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: var(--secondary-color);
        }
        
        .alert {
            border: none;
            border-radius: 8px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        @media (max-width: 768px) {
            .hero-section { padding: 60px 0; }
            .hero-section h1 { font-size: 2rem; }
        }

        /* Mega header */
        .offer-bar { background: #fff; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
        .offer-bar .container { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.4rem 0; }
        .offer-pill { display:inline-flex; align-items:center; gap:.5rem; padding:.25rem .6rem; border-radius:999px; border:1px dashed #ffd1dc; background:#fff5f8; color:#7f1d1d; font-size:.85rem; }

        .header-main { background:#fff; }
        .header-main .container { display:flex; align-items:center; gap:1rem; padding:.75rem 0; }
        .header-search { flex: 1 1 560px; }
        .header-search .input-group { border:1px solid #e5e7eb; border-radius:999px; overflow:hidden; }
        .header-search .form-control { border:0; padding:.6rem 1rem; }
        .header-search .btn { border:0; background: var(--primary-color); color:#fff; padding: .6rem 1rem; }
        .header-actions { display:flex; align-items:center; gap:.75rem; }
        .header-actions .icon-btn { position:relative; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; color: var(--dark-color); border:1px solid #e5e7eb; background:#fff; }
        .header-actions .icon-btn:hover { color: var(--primary-color); border-color:#ffd1dc; box-shadow:0 4px 14px rgba(255,63,108,.15); }

        /* Category rail */
        .category-bar { background:#fff; border-top:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; }
        .category-bar .nav { gap:.5rem; }
        .category-bar .nav-link { font-weight:600; color:#374151; padding:.75rem .75rem; border-radius:8px; }
        .category-bar .nav-link:hover { color: var(--primary-color); background:#fff5f8; }

        /* Mega menu (simple grid) */
        .mega-dropdown .dropdown-menu { width: min(900px, 90vw); padding:1rem; border-radius:12px; border:none; box-shadow: 0 12px 30px rgba(0,0,0,.12); }
        .mega-col h6 { font-weight:700; font-size:.9rem; color:#111827; }
        .mega-col a { display:block; color:#4b5563; text-decoration:none; padding:.25rem 0; }
        .mega-col a:hover { color: var(--primary-color); }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Mega Header -->
    <div class="offer-bar sticky-top">
        <div class="container">
            <span class="offer-pill"><i class="fas fa-bolt"></i> Sale is Live: Up to 50% Off</span>
            <span class="text-muted small">Free Shipping on $50+ • Easy Returns • Secure Payments</span>
        </div>
    </div>

    <header class="header-main shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="srcreationworld logo" class="brand-logo me-2">
                <span class="brand-text">srcreationworld</span>
            </a>
            
            <!-- Prominent Search -->
            <form class="header-search position-relative" action="{{ route('search') }}" method="GET" autocomplete="off">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" id="globalSearchInput" placeholder="Search for brands, products and more" value="{{ request('q') }}">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </div>
                <div id="searchDropdown" class="position-absolute bg-white shadow rounded-3 mt-2 w-100" style="display:none; z-index: 1055; max-height: 60vh; overflow:auto;">
                    <div id="searchSuggestions" class="p-2 border-bottom"></div>
                    <div id="searchProducts" class="p-2"></div>
                </div>
            </form>

            <!-- Actions -->
            <div class="header-actions">
                <a class="icon-btn" href="{{ route('cart.index') }}" title="Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge" id="cart-count">0</span>
                </a>
                @auth
                <div class="dropdown">
                    <button class="icon-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Account">
                        <i class="fas fa-user"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2">
                        <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.orders') }}"><i class="fas fa-bag-shopping me-2"></i>Orders</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.wishlist') }}"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <a class="icon-btn" href="{{ route('login') }}" title="Login"><i class="fas fa-user"></i></a>
                <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Sign Up</a>
                @endauth
            </div>
        </div>
        
        <!-- Category Rail -->
        <div class="category-bar">
            <div class="container">
                <ul class="nav">
                    <li class="nav-item dropdown mega-dropdown position-static">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Men</a>
                        <div class="dropdown-menu w-100 shadow">
                            <div class="row gx-4 gy-3">
                                <div class="col-6 col-md-3 mega-col">
                                    <h6>Topwear</h6>
                                    <a href="{{ route('products.index', ['search' => 'T-Shirts']) }}">T-Shirts</a>
                                    <a href="{{ route('products.index', ['search' => 'Shirts']) }}">Shirts</a>
                                    <a href="{{ route('products.index', ['search' => 'Sweatshirts']) }}">Sweatshirts</a>
                                </div>
                                <div class="col-6 col-md-3 mega-col">
                                    <h6>Bottomwear</h6>
                                    <a href="{{ route('products.index', ['search' => 'Jeans']) }}">Jeans</a>
                                    <a href="{{ route('products.index', ['search' => 'Trousers']) }}">Trousers</a>
                                    <a href="{{ route('products.index', ['search' => 'Shorts']) }}">Shorts</a>
                                </div>
                                <div class="col-6 col-md-3 mega-col">
                                    <h6>Footwear</h6>
                                    <a href="{{ route('products.index', ['search' => 'Sneakers']) }}">Sneakers</a>
                                    <a href="{{ route('products.index', ['search' => 'Formal Shoes']) }}">Formal Shoes</a>
                                </div>
                                <div class="col-6 col-md-3 mega-col">
                                    <h6>Accessories</h6>
                                    <a href="{{ route('products.index', ['search' => 'Belts']) }}">Belts</a>
                                    <a href="{{ route('products.index', ['search' => 'Wallets']) }}">Wallets</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown mega-dropdown position-static">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Women</a>
                        <div class="dropdown-menu w-100 shadow">
                            <div class="row gx-4 gy-3">
                                <div class="col-6 col-md-3 mega-col">
                                    <h6>Western Wear</h6>
                                    <a href="{{ route('products.index', ['search' => 'Dresses']) }}">Dresses</a>
                                    <a href="{{ route('products.index', ['search' => 'Tops']) }}">Tops</a>
                                    <a href="{{ route('products.index', ['search' => 'Jeans']) }}">Jeans</a>
                                </div>
                                <div class="col-6 col-md-3 mega-col">
                                    <h6>Ethnic</h6>
                                    <a href="{{ route('products.index', ['search' => 'Saree']) }}">Sarees</a>
                                    <a href="{{ route('products.index', ['search' => 'Kurtas']) }}">Kurtas</a>
                                </div>
                                <div class="col-6 col-md-3 mega-col">
                                    <h6>Footwear</h6>
                                    <a href="{{ route('products.index', ['search' => 'Heels']) }}">Heels</a>
                                    <a href="{{ route('products.index', ['search' => 'Flats']) }}">Flats</a>
                                </div>
                                <div class="col-6 col-md-3 mega-col">
                                    <h6>Accessories</h6>
                                    <a href="{{ route('products.index', ['search' => 'Bags']) }}">Bags</a>
                                    <a href="{{ route('products.index', ['search' => 'Jewellery']) }}">Jewellery</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index', ['search' => 'Kids']) }}">Kids</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index', ['search' => 'Footwear']) }}">Footwear</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index', ['search' => 'Accessories']) }}">Accessories</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index', ['search' => 'Beauty']) }}">Beauty</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index', ['search' => 'Home']) }}">Home & Living</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="{{ route('products.index', ['search' => 'Sale']) }}">Offers</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-white mb-3">srcreationworld</h5>
                    <p class="text-light">Your trusted online shopping destination for quality products at great prices.</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('about') }}" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="text-light text-decoration-none">Contact</a></li>
                        <li><a href="{{ route('faq') }}" class="text-light text-decoration-none">FAQ</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Electronics</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Fashion</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Home & Garden</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Sports</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Newsletter</h6>
                    <p class="text-light">Subscribe to get updates on new products and offers.</p>
                    <form id="newsletter-form" class="d-flex">
                        @csrf
                        <input type="email" name="email" class="form-control me-2" placeholder="Your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            <hr class="my-4 text-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-light mb-0">&copy; {{ date('Y') }} srcreationworld. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <img src="https://via.placeholder.com/40x25/007bff/ffffff?text=VISA" alt="Visa" class="me-2">
                    <img src="https://via.placeholder.com/40x25/ff6b35/ffffff?text=MC" alt="Mastercard" class="me-2">
                    <img src="https://via.placeholder.com/40x25/00457c/ffffff?text=PP" alt="PayPal">
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Update cart count on page load
        $(document).ready(function() {
            updateCartCount();
        });
        
        // Update cart count
        function updateCartCount() {
            $.get('{{ route("cart.count") }}', function(data) {
                if(data.success) {
                    $('#cart-count').text(data.count);
                }
            });
        }
        
        // Newsletter subscription
        $('#newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '{{ route("newsletter.subscribe") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(data) {
                    if(data.success) {
                        alert('Successfully subscribed to newsletter!');
                        $('#newsletter-form')[0].reset();
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });
        
        // Add to cart function
        function addToCart(productId, quantity = 1) {
            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity
                },
                success: function(data) {
                    if(data.success) {
                        updateCartCount();
                        showAlert('success', data.message);
                    } else {
                        showAlert('danger', data.message);
                    }
                },
                error: function(xhr) {
                    console.log('Cart error:', xhr.responseJSON);
                    let errorMessage = 'Error adding product to cart';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                    }
                    showAlert('danger', errorMessage);
                }
            });
        }
        
        // Show alert function
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                     style="top: 80px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(alertHtml);
            
            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
        
        // Live search (debounced)
        (function(){
            const input = document.getElementById('globalSearchInput');
            const dropdown = document.getElementById('searchDropdown');
            const suggestionsBox = document.getElementById('searchSuggestions');
            const productsBox = document.getElementById('searchProducts');
            if(!input || !dropdown) return;

            let timer = null;
            let lastQuery = '';
            const debounce = (fn, delay=350) => { return (...args)=>{ clearTimeout(timer); timer=setTimeout(()=>fn(...args), delay); }; };

            const render = (data) => {
                if(!data || (!data.products || data.products.length===0) && (!data.suggestions || data.suggestions.length===0)){
                    dropdown.style.display = 'none';
                    return;
                }
                // Suggestions
                suggestionsBox.innerHTML = '';
                if(data.suggestions && data.suggestions.length){
                    const chips = data.suggestions.map(s => `<button type="button" class="btn btn-sm btn-light me-2 mb-2 suggestion-chip" data-q="${s.replace(/"/g,'&quot;')}"><i class=\"fas fa-magnifying-glass me-1\"></i>${s}</button>`).join('');
                    suggestionsBox.innerHTML = `<div class="small text-muted mb-1">Suggestions</div><div class="d-flex flex-wrap">${chips}</div>`;
                }
                // Products
                productsBox.innerHTML = '';
                if(data.products && data.products.length){
                    const items = data.products.map(p => `
                        <a href="/products/${p.slug}" class="text-decoration-none text-dark d-flex align-items-center p-2 rounded hover-bg">
                            <img src="${p.image_url}" alt="${p.name}" class="rounded me-2" style="width:48px;height:48px;object-fit:cover;">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${p.name}</div>
                                <div class="small text-muted">₹${(p.final_price ?? p.price)}</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>`).join('');
                    productsBox.innerHTML = `<div class="small text-muted mb-1">Products</div>${items}`;
                }
                dropdown.style.display = 'block';
            };

            const fetchSuggestions = debounce(async (q) => {
                try{
                    const res = await fetch(`{{ route('search.suggest') }}?q=${encodeURIComponent(q)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                    const data = await res.json();
                    if(q===lastQuery){ render(data); }
                }catch(e){ dropdown.style.display='none'; }
            }, 400);

            input.addEventListener('input', (e)=>{
                const q = e.target.value.trim();
                lastQuery = q;
                if(q.length < 2){ dropdown.style.display='none'; return; }
                fetchSuggestions(q);
            });

            // Click outside to close
            document.addEventListener('click', (e)=>{
                if(!dropdown.contains(e.target) && e.target !== input){ dropdown.style.display='none'; }
            });

            // Click suggestion
            dropdown.addEventListener('click', (e)=>{
                const chip = e.target.closest('.suggestion-chip');
                if(chip){ input.value = chip.dataset.q; input.form.submit(); }
            });

            // Navigate on Enter opens search page with current query
            input.addEventListener('keydown', (e)=>{
                if(e.key==='Enter'){
                    if(input.value.trim().length){ input.form.submit(); }
                }
            });
        })();
    </script>
    
    <script>
        // Add live search dropdown UI and debounced fetch to /search/suggest; render products and suggestions; navigate on enter.
    </script>
    
    @stack('scripts')
</body>
</html>
