<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background-color: #343a40;
      color: white;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 12px 20px;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .topbar {
      background-color: #fff;
      border-bottom: 1px solid #dee2e6;
      padding: 10px 20px;
    }
    .card-hover:hover {
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      transform: scale(1.02);
      transition: 0.3s;
    }
  </style>
</head>
<body>

  <div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
      <h4 class="mb-4">Admin Panel</h4>
      <a href="{{ route('super-admin.dashboard') }}"><i class="fas fa-home me-2"></i> Dashboard</a>
      <a href="#"><i class="fas fa-shopping-cart me-2"></i> Orders</a>
      <a href="{{ route('super-admin.products.index') }}"><i class="fas fa-box-open me-2"></i> Products</a>
      <a href="{{ route('super-admin.categories.index') }}"><i class="fas fa-tags me-2"></i> Categories</a>
      <a href="#"><i class="fas fa-users-cog me-2"></i> Users</a>
      <a href="#"><i class="fas fa-chart-line me-2"></i> Reports</a>
      <form action="{{ route('shopkeeper.logout') }}" method="POST" class="mt-4">
        @csrf
        <button class="btn btn-danger w-100"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
      </form>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1">
      <!-- Topbar -->
      <div class="topbar d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Welcome, {{ Auth::guard('admin')->user()?->name ?? 'Admin' }}</h5>
        <span class="text-muted">Admin Dashboard</span>
      </div>

      <!-- Page Content -->
      @yield('content')
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
