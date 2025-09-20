<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shopkeeper Panel')</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <style type="text/tailwindcss">
        :root {
            --background-light: #F5F7F0;
            --background-dark: #2D3748;
            --card-light: #FFFFFF;
            --card-dark: #4A5568;
            --text-light: #4A5568;
            --text-dark: #E2E8F0;
            --heading-light: #2D3748;
            --heading-dark: #F7FAFC;
            --border-light: #E2E8F0;
            --border-dark: #4A5568;
            --accent-light: #68D391;
            --accent-dark: #9AE6B4;
            --primary: #F0FFF4;
            --sidebar-bg: #68D391;
            --sidebar-active: #48BB78;
        }
        .font-display {
            font-family: 'Montserrat', sans-serif;
        }
        .font-serif {
            font-family: 'Lora', serif;
        }
        body {
            background-color: var(--background-light);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill='%23E8E8D9' fill-opacity='0.2'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zM41 48c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zM71 78c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7z'/%3E%3C/g%3E%3C/svg%3E");
        }
        .dark body {
            background-color: var(--background-dark);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill='%234F583B' fill-opacity='0.2'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zM41 48c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zM71 78c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7z'/%3E%3C/g%3E%3C/svg%3E");
        }
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 300,
                'GRAD' 0,
                'opsz' 24;
        }
    </style>
</head>
<body class="font-display bg-[var(--background-light)] dark:bg-[var(--background-dark)] text-[var(--text-light)] dark:text-[var(--text-dark)]">
<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 bg-[var(--sidebar-bg)] text-white flex flex-col shadow-lg">
        <div class="h-20 flex items-center justify-center border-b border-white border-opacity-20">
            @if(auth()->user()->shop && auth()->user()->shop->logo)
                <img src="{{ asset('storage/' . auth()->user()->shop->logo) }}" alt="Shop Logo" class="w-8 h-8 rounded-full mr-2">
            @else
                <span class="material-symbols-outlined text-3xl text-white mr-2">eco</span>
            @endif
            <h1 class="text-xl font-serif font-bold text-white">
                {{ auth()->user()->shop->name ?? 'HerbDash' }}
            </h1>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-1">
            <a class="flex items-center px-4 py-3 rounded-lg text-white {{ request()->routeIs('shopkeeper.dashboard') ? 'bg-[var(--sidebar-active)] bg-opacity-80' : 'hover:bg-white hover:bg-opacity-10' }} transition-all duration-200" href="{{ route('shopkeeper.dashboard') }}">
                <span class="material-symbols-outlined mr-3 text-lg">dashboard</span> Dashboard
            </a>
            
            @if(!auth()->user()->shop)
                <a class="flex items-center px-4 py-3 rounded-lg text-white {{ request()->routeIs('shopkeeper.shop.*') ? 'bg-[var(--sidebar-active)] bg-opacity-80' : 'hover:bg-white hover:bg-opacity-10' }} transition-all duration-200" href="{{ route('shopkeeper.shop.create') }}">
                    <span class="material-symbols-outlined mr-3 text-lg">add_business</span> Setup Shop
                </a>
            @else
                <a class="flex items-center px-4 py-3 rounded-lg text-white {{ request()->routeIs('shopkeeper.shop.*') ? 'bg-[var(--sidebar-active)] bg-opacity-80' : 'hover:bg-white hover:bg-opacity-10' }} transition-all duration-200" href="{{ route('shopkeeper.shop.edit') }}">
                    <span class="material-symbols-outlined mr-3 text-lg">spa</span> Manage Shop
                </a>
            @endif
            
            <a class="flex items-center px-4 py-3 rounded-lg text-white {{ request()->routeIs('shopkeeper.products.*') ? 'bg-[var(--sidebar-active)] bg-opacity-80' : 'hover:bg-white hover:bg-opacity-10' }} transition-all duration-200" href="{{ route('shopkeeper.products.index') }}">
                <span class="material-symbols-outlined mr-3 text-lg">grass</span> Products
            </a>
            <a class="flex items-center px-4 py-3 rounded-lg text-white {{ request()->routeIs('shopkeeper.orders.*') ? 'bg-[var(--sidebar-active)] bg-opacity-80' : 'hover:bg-white hover:bg-opacity-10' }} transition-all duration-200" href="{{ route('shopkeeper.orders.index') }}">
                <span class="material-symbols-outlined mr-3 text-lg">receipt_long</span> Orders
            </a>
            <a class="flex items-center px-4 py-3 rounded-lg text-white {{ request()->routeIs('shopkeeper.categories.*') ? 'bg-[var(--sidebar-active)] bg-opacity-80' : 'hover:bg-white hover:bg-opacity-10' }} transition-all duration-200" href="{{ route('shopkeeper.categories.index') }}">
                <span class="material-symbols-outlined mr-3 text-lg">eco</span> Categories
            </a>
            <a class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10 transition-all duration-200" href="#">
                <span class="material-symbols-outlined mr-3 text-lg">potted_plant</span> Analytics
            </a>
            <a class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10 transition-all duration-200" href="#">
                <span class="material-symbols-outlined mr-3 text-lg">settings</span> Settings
            </a>
        </nav>
        
        <div class="p-4 border-t border-white border-opacity-20">
            <form action="{{ route('shopkeeper.logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-red-500 hover:bg-opacity-20 w-full text-left transition-all duration-200">
                    <span class="material-symbols-outlined mr-3 text-lg">logout</span> Logout
                </button>
            </form>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white border-b border-[var(--border-light)] flex items-center justify-between px-8 shadow-sm">
            <div class="flex items-center">
                <h2 class="text-2xl font-semibold text-[var(--heading-light)]">@yield('page-title', 'Dashboard')</h2>
            </div>
            <div class="flex items-center space-x-4">
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <span class="material-symbols-outlined text-[var(--text-light)]">notifications</span>
                </button>
                <div class="flex items-center space-x-3">
                    @if(auth()->user()->shop && auth()->user()->shop->logo)
                        <img alt="Shop avatar" class="w-10 h-10 rounded-full border-2 border-[var(--accent-light)]" src="{{ asset('storage/' . auth()->user()->shop->logo) }}">
                    @else
                        <div class="w-10 h-10 rounded-full border-2 border-[var(--accent-light)] bg-[var(--accent-light)] flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-sm">person</span>
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-[var(--heading-light)]">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-[var(--text-light)]">Shopkeeper</p>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="flex-1 p-8 overflow-y-auto">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="material-symbols-outlined inline mr-2">check_circle</span>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="material-symbols-outlined inline mr-2">error</span>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

@yield('scripts')
</body>
</html>

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
