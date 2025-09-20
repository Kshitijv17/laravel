<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Shopkeeper Panel - {{ auth()->user()->shop->name ?? 'My Shop' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      position: fixed;
      width: 250px;
      overflow-y: auto;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 12px 20px;
      border-radius: 8px;
      margin: 2px 10px;
      transition: all 0.3s ease;
    }
    .sidebar a:hover {
      background-color: rgba(255,255,255,0.1);
      transform: translateX(5px);
    }
    .sidebar a.active {
      background-color: rgba(255,255,255,0.2);
    }
    .main-content {
      margin-left: 250px;
      min-height: 100vh;
    }
    .topbar {
      background-color: #fff;
      border-bottom: 1px solid #dee2e6;
      padding: 15px 30px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .card-hover:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transform: translateY(-2px);
      transition: all 0.3s ease;
    }
    .shop-logo {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 50%;
    }
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }
      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

  <div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="p-3">
        <div class="d-flex align-items-center mb-4">
          @if(auth()->user()->shop && auth()->user()->shop->logo)
            <img src="{{ asset('storage/' . auth()->user()->shop->logo) }}" 
                 alt="Shop Logo" class="shop-logo me-3">
          @else
            <div class="shop-logo bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3">
              <i class="fas fa-store text-white"></i>
            </div>
          @endif
          <div>
            <h5 class="mb-0">{{ auth()->user()->shop->name ?? 'My Shop' }}</h5>
            <small class="opacity-75">Shopkeeper Panel</small>
          </div>
        </div>
        
        <nav>
          <a href="{{ route('shopkeeper.dashboard') }}" class="{{ request()->routeIs('shopkeeper.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home me-2"></i> Dashboard
          </a>
          
          <a href="{{ route('shopkeeper.products.index') }}" class="{{ request()->routeIs('shopkeeper.products.*') ? 'active' : '' }}">
            <i class="fas fa-box-open me-2"></i> My Products
          </a>
          
          <a href="{{ route('shopkeeper.orders.index') }}" class="{{ request()->routeIs('shopkeeper.orders.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart me-2"></i> My Orders
          </a>
          
          <a href="{{ route('shopkeeper.shop.edit') }}" class="{{ request()->routeIs('shopkeeper.shop.*') ? 'active' : '' }}">
            <i class="fas fa-store me-2"></i> Shop Settings
          </a>
          
          <hr class="my-3 opacity-25">
          
          <a href="#" onclick="document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
          </a>
          
          <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </nav>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Topbar -->
      <div class="topbar">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">
              @if(auth()->user()->shop)
                {{ auth()->user()->shop->name }}
              @else
                Setup Your Shop
              @endif
            </h5>
            <small class="text-muted">Welcome, {{ auth()->user()->name }}</small>
          </div>
          <div class="d-flex align-items-center">
            <div class="dropdown">
              <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-1"></i>
                {{ auth()->user()->name }}
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Account</h6></li>
                <li><a class="dropdown-item" href="{{ route('shopkeeper.shop.edit') }}">
                  <i class="fas fa-store me-2"></i>Shop Settings
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" onclick="document.getElementById('logout-form').submit();">
                  <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <div class="content">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('info'))
          <div class="alert alert-info alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @yield('content')
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 5000);
  </script>
  
  @stack('scripts')
</body>
</html>
