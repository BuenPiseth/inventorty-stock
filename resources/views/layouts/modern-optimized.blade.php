<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">

    <title>DEER BAKERY & CAKE - @yield('title', 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/deer-bakery-logo.png') }}">

    <!-- Preload Critical Resources -->
    <link rel="preload" href="{{ asset('css/modern-optimized.css') }}" as="style">
    <link rel="preload" href="{{ asset('js/theme-manager.js') }}" as="script">
    
    <!-- DNS Prefetch for External Resources -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    
    <!-- Fonts - Optimized Loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Critical CSS - Inline for Performance -->
    <style>
        /* Critical above-the-fold styles */
        .preload * { transition: none !important; }
        body { font-family: 'Inter', sans-serif; margin: 0; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 280px; z-index: 1000; }
        .main-content { margin-left: 280px; min-height: 100vh; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>

    <!-- Main Optimized CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-optimized.css') }}">
    
    <!-- External CSS - Async Loading -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></noscript>
    
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"></noscript>
    
    <!-- DataTables CSS - Lazy Load -->
    @stack('datatables-css')
    
    <!-- Select2 CSS - Lazy Load -->
    @stack('select2-css')

    <!-- Theme Initialization Script - Critical -->
    <script>
        (function() {
            document.documentElement.classList.add('preload');
            const theme = localStorage.getItem('theme') || 'auto';
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const effectiveTheme = theme === 'auto' ? (mediaQuery.matches ? 'dark' : 'light') : theme;
            document.documentElement.classList.add(`theme-${effectiveTheme}`);
            document.documentElement.setAttribute('data-theme', effectiveTheme);
            setTimeout(() => document.documentElement.classList.remove('preload'), 100);
        })();
    </script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <!-- Brand -->
            <div class="sidebar-brand p-4">
                <div class="d-flex align-items-center">
                    <div class="brand-icon me-3">
                        <i class="bi bi-shop text-white fs-2"></i>
                    </div>
                    <div class="brand-text">
                        <h5 class="text-white mb-0 fw-bold">DEER BAKERY</h5>
                        <small class="text-white-50">Inventory System</small>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="bi bi-house-door me-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Products -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                           href="{{ route('products.index') }}">
                            <i class="bi bi-box me-3"></i>
                            <span>Products</span>
                            <small class="badge bg-primary ms-auto">{{ App\Models\Product::count() }}</small>
                        </a>
                    </li>

                    <!-- Categories -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                           href="{{ route('categories.index') }}">
                            <i class="bi bi-tags me-3"></i>
                            <span>Categories</span>
                        </a>
                    </li>

                    <!-- Stock Operations -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stock.in') ? 'active' : '' }}"
                           href="{{ route('stock.in') }}">
                            <i class="bi bi-plus-circle me-3 text-success"></i>
                            <span>Stock In</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stock.out') ? 'active' : '' }}"
                           href="{{ route('stock.out') }}">
                            <i class="bi bi-dash-circle me-3 text-warning"></i>
                            <span>Stock Out</span>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stock-movements.index') ? 'active' : '' }}"
                           href="{{ route('stock-movements.index') }}">
                            <i class="bi bi-clock-history me-3"></i>
                            <span>Movement History</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                           href="{{ route('reports.stock-value') }}">
                            <i class="bi bi-graph-up me-3"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Top Navigation -->
        <div class="top-nav d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary me-3 d-md-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                    <small class="text-muted">@yield('page-subtitle', 'Welcome back!')</small>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Optimized Theme Toggle -->
                <div class="theme-toggle-container">
                    <div class="btn-group" role="group" aria-label="Theme toggle">
                        <input type="radio" class="btn-check" name="theme" id="theme-light" value="light">
                        <label class="btn btn-outline-secondary btn-sm" for="theme-light" title="Light Mode">
                            <i class="bi bi-sun-fill"></i>
                            <span class="d-none d-lg-inline ms-1">Light</span>
                        </label>

                        <input type="radio" class="btn-check" name="theme" id="theme-auto" value="auto" checked>
                        <label class="btn btn-outline-secondary btn-sm" for="theme-auto" title="Auto Mode">
                            <i class="bi bi-circle-half"></i>
                            <span class="d-none d-lg-inline ms-1">Auto</span>
                        </label>

                        <input type="radio" class="btn-check" name="theme" id="theme-dark" value="dark">
                        <label class="btn btn-outline-secondary btn-sm" for="theme-dark" title="Dark Mode">
                            <i class="bi bi-moon-stars-fill"></i>
                            <span class="d-none d-lg-inline ms-1">Dark</span>
                        </label>
                    </div>
                </div>

                @auth
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle d-flex align-items-center text-decoration-none" 
                                type="button" data-bs-toggle="dropdown">
                            <div class="rounded-circle avatar-sm avatar-placeholder me-2">
                                <i class="bi bi-person-fill text-white"></i>
                            </div>
                            <span class="text-dark">{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Optimized JavaScript Loading -->
    <script src="{{ asset('js/theme-manager.js') }}" defer></script>
    
    <!-- Bootstrap JS - Async -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    
    <!-- jQuery - Only if needed -->
    @stack('jquery')
    
    <!-- DataTables JS - Lazy Load -->
    @stack('datatables-js')
    
    <!-- Select2 JS - Lazy Load -->
    @stack('select2-js')

    <!-- Page-specific scripts -->
    @stack('scripts')

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>
