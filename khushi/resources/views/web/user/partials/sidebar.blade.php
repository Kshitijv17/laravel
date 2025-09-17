<!-- User Dashboard Sidebar -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-user-circle me-2"></i>
            {{ Auth::user()->name }}
        </h5>
    </div>
    <div class="card-body p-0">
        <nav class="nav flex-column">
            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active bg-light' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </a>
            <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile') ? 'active bg-light' : '' }}">
                <i class="fas fa-user me-2"></i>
                Profile
            </a>
            <a href="{{ route('user.orders') }}" class="nav-link {{ request()->routeIs('user.orders') ? 'active bg-light' : '' }}">
                <i class="fas fa-shopping-bag me-2"></i>
                My Orders
            </a>
            <a href="{{ route('user.wishlist') }}" class="nav-link {{ request()->routeIs('user.wishlist') ? 'active bg-light' : '' }}">
                <i class="fas fa-heart me-2"></i>
                Wishlist
            </a>
            <a href="{{ route('user.change-password') }}" class="nav-link {{ request()->routeIs('user.change-password') ? 'active bg-light' : '' }}">
                <i class="fas fa-key me-2"></i>
                Change Password
            </a>
            @if(Route::has('user.two-factor'))
            <a href="{{ route('user.two-factor') }}" class="nav-link {{ request()->routeIs('user.two-factor') ? 'active bg-light' : '' }}">
                <i class="fas fa-shield-halved me-2"></i>
                Two-Factor Auth
            </a>
            @endif
            @if(Route::has('user.addresses'))
            <a href="{{ route('user.addresses') }}" class="nav-link {{ request()->routeIs('user.addresses*') ? 'active bg-light' : '' }}">
                <i class="fas fa-address-book me-2"></i>
                Addresses
            </a>
            @endif
            @if(Route::has('user.support-tickets') || Route::has('user.support-tickets.index'))
            <a href="{{ Route::has('user.support-tickets') ? route('user.support-tickets') : route('user.support-tickets.index') }}" class="nav-link {{ request()->routeIs('user.support-tickets*') ? 'active bg-light' : '' }}">
                <i class="fas fa-life-ring me-2"></i>
                Support Tickets
            </a>
            @endif
            <a href="{{ route('chat.index') }}" class="nav-link">
                <i class="fas fa-comments me-2"></i>
                Support Chat
            </a>
            <div class="nav-link border-top mt-2 pt-2">
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Are you sure you want to logout?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
    </div>
</div>

<!-- Quick Stats -->
<div class="card shadow-sm mt-3">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="fas fa-chart-bar me-2"></i>
            Quick Stats
        </h6>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-6">
                <div class="border-end">
                    <h4 class="text-primary mb-1">{{ $stats['total_orders'] ?? 0 }}</h4>
                    <small class="text-muted">Orders</small>
                </div>
            </div>
            <div class="col-6">
                <h4 class="text-success mb-1">{{ $stats['wishlist_items'] ?? 0 }}</h4>
                <small class="text-muted">Wishlist</small>
            </div>
        </div>
    </div>
</div>
