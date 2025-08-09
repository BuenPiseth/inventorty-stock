<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DEER BAKERY & CAKE - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .nav-link .badge,
        .sidebar.collapsed .nav-header,
        .sidebar.collapsed .sidebar-content > div:first-child {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.75rem;
            position: relative;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed .nav-link:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 70px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 1002;
            pointer-events: none;
        }

        .sidebar.collapsed .menu-section {
            display: none;
        }

        .sidebar-brand {
            padding: 1.5rem 1.5rem 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .logo-container {
            position: relative;
            margin-right: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 15px;
            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .logo-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border-radius: 15px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.08) translateY(-2px);
            background: rgba(255, 255, 255, 0.18);
            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.2),
                0 0 20px rgba(255, 255, 255, 0.1);
        }

        .logo-container:hover::before {
            opacity: 1;
        }

        .brand-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
            filter:
                drop-shadow(0 2px 8px rgba(0, 0, 0, 0.15))
                drop-shadow(0 0 0 rgba(255, 255, 255, 0.1));
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 8px;
        }

        .logo-container:hover .brand-logo {
            filter:
                drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2))
                drop-shadow(0 0 15px rgba(255, 255, 255, 0.15));
            transform: scale(1.02);
        }

        .brand-text {
            flex: 1;
        }

        .brand-title {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 800;
            color: white;
            text-shadow:
                0 1px 3px rgba(0, 0, 0, 0.3),
                0 0 10px rgba(255, 255, 255, 0.1);
            letter-spacing: 0.8px;
            line-height: 1.2;
            transition: all 0.3s ease;
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.95rem;
            font-weight: 600;
            text-shadow:
                0 1px 2px rgba(0, 0, 0, 0.2),
                0 0 8px rgba(255, 255, 255, 0.05);
            letter-spacing: 1.2px;
            transition: all 0.3s ease;
        }

        .logo-container:hover + .brand-text .brand-title {
            text-shadow:
                0 1px 3px rgba(0, 0, 0, 0.3),
                0 0 15px rgba(255, 255, 255, 0.2);
        }

        .logo-container:hover + .brand-text .brand-subtitle {
            color: white;
            text-shadow:
                0 1px 2px rgba(0, 0, 0, 0.2),
                0 0 12px rgba(255, 255, 255, 0.1);
        }

        /* Collapsed state adjustments */
        .sidebar.collapsed .logo-container {
            margin-right: 0;
            margin-bottom: 0.5rem;
            padding: 6px;
            border-radius: 10px;
        }

        .sidebar.collapsed .brand-logo {
            width: 50px;
            height: 50px;
        }

        .sidebar.collapsed .sidebar-brand {
            padding: 1rem;
            text-align: center;
        }

        .sidebar.collapsed .sidebar-brand .d-flex {
            flex-direction: column;
            align-items: center;
        }

        /* Subtle logo animation */
        @keyframes logoGlow {
            0%, 100% {
                box-shadow:
                    0 4px 15px rgba(0, 0, 0, 0.1),
                    0 0 0 1px rgba(255, 255, 255, 0.1);
            }
            50% {
                box-shadow:
                    0 4px 15px rgba(0, 0, 0, 0.1),
                    0 0 0 1px rgba(255, 255, 255, 0.15),
                    0 0 20px rgba(255, 255, 255, 0.05);
            }
        }

        .logo-container {
            animation: logoGlow 4s ease-in-out infinite;
        }

        .logo-container:hover {
            animation: none;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .sidebar-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        .scroll-indicator {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            width: 4px;
            height: 60px;
            border-radius: 2px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .scroll-indicator.show {
            opacity: 1;
        }

        .scroll-thumb {
            background: rgba(255, 255, 255, 0.6);
            width: 100%;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .nav-item {
            margin: 0.1rem 1rem;
        }

        .nav-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            margin: 0.75rem 0;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 0.1rem;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateX(3px);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.25);
            color: white !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link span {
            flex-grow: 1;
        }

        .nav-link .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
            margin-left: auto;
            font-weight: 600;
            border-radius: 0.75rem;
        }

        .nav-link small:not(.badge) {
            font-size: 0.7rem;
            font-weight: 500;
            opacity: 0.9;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: white;
            border-radius: 0 2px 2px 0;
        }

        .nav-header {
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-header:hover {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .nav-header i {
            opacity: 0.7;
        }

        .nav-header::after {
            content: '\F282';
            font-family: 'bootstrap-icons';
            position: absolute;
            right: 1rem;
            transition: transform 0.3s ease;
        }

        .nav-header.collapsed::after {
            transform: rotate(-90deg);
        }

        .menu-section {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .menu-section.collapsed {
            max-height: 0;
            opacity: 0;
            margin: 0;
        }

        /* Enhanced hover effects */
        .nav-link:hover .badge {
            background-color: rgba(255, 255, 255, 0.9) !important;
            color: #667eea !important;
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .main-content.sidebar-collapsed {
            margin-left: 70px;
        }

        /* Top Navigation */
        .top-nav {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* Real-time Clock Styles */
        #real-time-clock {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        #real-time-clock:hover {
            transform: scale(1.05);
        }

        #current-date {
            color: #6c757d;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Clock animation */
        @keyframes clockPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        #real-time-clock .bi-clock {
            animation: clockPulse 2s infinite;
            color: #667eea;
        }

        /* Responsive clock */
        @media (max-width: 768px) {
            #real-time-clock {
                font-size: 0.9rem !important;
            }

            #current-date {
                font-size: 0.7rem !important;
            }
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            border-radius: 0.75rem 0.75rem 0 0 !important;
            padding: 1.25rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Forms */
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        /* Tables */
        .table {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .table th {
            background-color: var(--light-color);
            border: none;
            font-weight: 600;
            color: var(--secondary-color);
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            border-color: var(--border-color);
        }

        /* Alerts */
        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem 1.25rem;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Product Image */
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .product-image-large {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 0.75rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        /* Avatar Styles */
        .avatar-sm {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .avatar-lg {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid #007bff;
        }

        .avatar-placeholder {
            background: linear-gradient(135deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Toggle Button Styles */
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .sidebar-toggle.collapsed {
            left: 20px;
        }

        .sidebar-toggle:not(.collapsed) {
            left: 240px;
        }

        @media (max-width: 768px) {
            .sidebar-toggle {
                left: 20px !important;
            }
        }

        /* Loading Spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
        <i class="bi bi-list fs-5"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center">
                <div class="logo-container">
                    <img src="{{ asset('images/deer-bakery-logo.png') }}?v={{ time() }}"
                         alt="DEER BAKERY & CAKE"
                         class="brand-logo">
                </div>
                <div class="brand-text">
                    <h4 class="brand-title">DEER BAKERY</h4>
                    <small class="brand-subtitle">& CAKE</small>
                </div>
            </div>
        </div>

        <div class="sidebar-content">
            <!-- Quick Search -->
            <div class="px-3 pb-3">
                <div class="position-relative">
                    <input type="text"
                           class="form-control form-control-sm bg-white bg-opacity-10 border-0 text-white"
                           placeholder="Search menu..."
                           id="menuSearch"
                           autocomplete="off"
                           style="padding-left: 2.5rem;"
                           aria-label="Search menu items">
                    <i class="bi bi-search position-absolute text-white-50"
                       style="left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
                    <button type="button"
                            class="btn btn-sm position-absolute text-white-50 d-none"
                            id="clearSearch"
                            style="right: 0.5rem; top: 50%; transform: translateY(-50%); border: none; background: none;"
                            aria-label="Clear search">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="scroll-indicator" id="scrollIndicator">
                <div class="scroll-thumb" id="scrollThumb"></div>
            </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column" id="menuItems">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}"
                       data-tooltip="Dashboard">
                        <i class="bi bi-house-door"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Main Navigation -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                       href="{{ route('products.index') }}"
                       data-tooltip="Products">
                        <i class="bi bi-box"></i>
                        <span>Products</span>
                        <small class="badge bg-primary text-white ms-auto">{{ App\Models\Product::count() }}</small>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                       href="{{ route('categories.index') }}"
                       data-tooltip="Categories">
                        <i class="bi bi-tags"></i>
                        <span>Categories</span>
                        <small class="badge bg-info text-white ms-auto">{{ App\Models\Category::count() }}</small>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}"
                       href="{{ route('warehouses.index') }}"
                       data-tooltip="Warehouses">
                        <i class="bi bi-building"></i>
                        <span>Warehouses</span>
                        <small class="badge bg-success text-white ms-auto">{{ App\Models\Warehouse::active()->count() }}</small>
                    </a>
                </li>

                <!-- Stock Operations -->
                <li class="nav-item mt-2">
                    <div class="nav-divider"></div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock.in') ? 'active' : '' }}"
                       href="{{ route('stock.in') }}"
                       data-tooltip="Stock In">
                        <i class="bi bi-plus-circle text-success"></i>
                        <span>Stock In</span>
                        <small class="text-success ms-auto">Add</small>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock.out') ? 'active' : '' }}"
                       href="{{ route('stock.out') }}"
                       data-tooltip="Stock Out">
                        <i class="bi bi-dash-circle text-warning"></i>
                        <span>Stock Out</span>
                        <small class="text-warning ms-auto">Remove</small>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}"
                       href="{{ route('stock-movements.index') }}"
                       data-tooltip="Movement History">
                        <i class="bi bi-clock-history"></i>
                        <span>History</span>
                        <small class="badge bg-secondary text-white ms-auto">{{ App\Models\StockMovement::count() }}</small>
                    </a>
                </li>

                <!-- Reports -->
                <li class="nav-item mt-2">
                    <div class="nav-divider"></div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.stock-levels') ? 'active' : '' }}"
                       href="{{ route('reports.stock-levels') }}"
                       data-tooltip="Stock Levels">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Stock Levels</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.low-stock-alert') ? 'active' : '' }}"
                       href="{{ route('reports.low-stock-alert') }}"
                       data-tooltip="Low Stock Alert">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                        <span>Low Stock Alert</span>
                        @php
                            $lowStockCount = App\Models\Product::where('quantity', '<=', 5)->count();
                        @endphp
                        @if($lowStockCount > 0)
                            <small class="badge bg-danger ms-auto">{{ $lowStockCount }}</small>
                        @endif
                    </a>
                </li>



                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.stock-value') ? 'active' : '' }}"
                       href="{{ route('reports.stock-value') }}"
                       data-tooltip="Stock Value Report">
                        <i class="bi bi-currency-dollar"></i>
                        <span>Value Report</span>
                    </a>
                </li>

                <!-- Tools & Quick Actions -->
                <li class="nav-item mt-2">
                    <div class="nav-divider"></div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('import.*') ? 'active' : '' }}"
                       href="{{ route('import.index') }}"
                       data-tooltip="Import Data">
                        <i class="bi bi-upload"></i>
                        <span>Import Data</span>
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-divider">
                    <hr class="nav-divider-line">
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('database.*') ? 'active' : '' }}"
                       href="{{ route('database.status') }}"
                       data-tooltip="Database Status">
                        <i class="bi bi-database"></i>
                        <span>Database Status</span>
                        <small class="badge bg-success text-white ms-auto">MySQL</small>
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-divider">
                    <hr class="nav-divider-line">
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       href="{{ route('profile.show') }}"
                       data-tooltip="My Profile">
                        <i class="bi bi-person-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                       href="{{ route('settings.index') }}"
                       data-tooltip="Settings">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>

                <!-- Quick Add Actions -->
                <li class="nav-item mt-2">
                    <div class="nav-divider"></div>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                       href="{{ route('products.create') }}"
                       data-tooltip="Add Product">
                        <i class="bi bi-plus-square text-success"></i>
                        <span>Add Product</span>
                        <small class="text-success ms-auto">New</small>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                       href="{{ route('categories.create') }}"
                       data-tooltip="Add Category">
                        <i class="bi bi-folder-plus text-info"></i>
                        <span>Add Category</span>
                        <small class="text-info ms-auto">New</small>
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
                <div>
                    <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                    <small class="text-muted">@yield('page-subtitle', 'Welcome back!')</small>
                </div>
            </div>

            <!-- Real-time Clock -->
            <div class="d-flex align-items-center me-3">
                <div class="text-center">
                    <div id="real-time-clock" class="fw-bold text-primary" style="font-size: 1.1rem;">
                        <i class="bi bi-clock me-1"></i>
                        <span id="current-time">Loading...</span>
                    </div>
                    <div id="current-date" class="small text-muted" style="font-size: 0.8rem;">
                        Loading...
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle d-flex align-items-center text-decoration-none" type="button" data-bs-toggle="dropdown">
                            @if(Auth::user()->avatar && file_exists(public_path('storage/' . Auth::user()->avatar)))
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                     alt="Profile Picture"
                                     class="rounded-circle avatar-sm me-2"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="rounded-circle avatar-sm avatar-placeholder me-2" style="display: none;">
                                    <i class="bi bi-person-fill text-white"></i>
                                </div>
                            @else
                                <div class="rounded-circle avatar-sm avatar-placeholder me-2">
                                    <i class="bi bi-person-fill text-white"></i>
                                </div>
                            @endif
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
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="bi bi-person-circle me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('settings.index') }}">
                                    <i class="bi bi-gear me-2"></i>Settings
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

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            // Load saved state
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
                sidebarToggle.classList.add('collapsed');
                sidebarToggle.querySelector('i').className = 'bi bi-arrow-right fs-5';
            }

            sidebarToggle.addEventListener('click', function() {
                const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');

                if (isCurrentlyCollapsed) {
                    // Expand sidebar
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-collapsed');
                    sidebarToggle.classList.remove('collapsed');
                    sidebarToggle.querySelector('i').className = 'bi bi-list fs-5';
                    localStorage.setItem('sidebar-collapsed', 'false');
                } else {
                    // Collapse sidebar
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                    sidebarToggle.classList.add('collapsed');
                    sidebarToggle.querySelector('i').className = 'bi bi-arrow-right fs-5';
                    localStorage.setItem('sidebar-collapsed', 'true');
                }
            });

            // Mobile handling
            if (window.innerWidth <= 768) {
                sidebar.classList.add('show');
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
        });

        // Menu Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('menuSearch');
            const menuItems = document.getElementById('menuItems');
            const sidebarNav = document.querySelector('.sidebar-nav');
            const scrollIndicator = document.getElementById('scrollIndicator');
            const scrollThumb = document.getElementById('scrollThumb');

            // Enhanced search functionality
            if (searchInput && menuItems) {
                const clearButton = document.getElementById('clearSearch');

                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const allItems = menuItems.querySelectorAll('.nav-item');
                    let hasResults = false;

                    // Show/hide clear button
                    if (clearButton) {
                        clearButton.classList.toggle('d-none', searchTerm === '');
                    }

                    allItems.forEach(function(item) {
                        const link = item.querySelector('.nav-link');
                        const divider = item.querySelector('.nav-divider');

                        if (link) {
                            const text = link.textContent.toLowerCase();
                            if (text.includes(searchTerm) || searchTerm === '') {
                                item.style.display = '';
                                hasResults = true;
                                // Highlight matching text
                                if (searchTerm && searchTerm !== '') {
                                    link.style.background = 'rgba(255, 255, 255, 0.2)';
                                    link.style.borderLeft = '3px solid rgba(255, 255, 255, 0.8)';
                                } else {
                                    link.style.background = '';
                                    link.style.borderLeft = '';
                                }
                            } else {
                                item.style.display = 'none';
                            }
                        } else if (divider) {
                            // Hide dividers when searching
                            item.style.display = searchTerm ? 'none' : '';
                        }
                    });

                    // Show "no results" message if needed
                    showNoResults(!hasResults && searchTerm !== '');
                    updateScrollIndicator();
                });

                // Clear search functionality
                if (clearButton) {
                    clearButton.addEventListener('click', function() {
                        searchInput.value = '';
                        searchInput.dispatchEvent(new Event('input'));
                        searchInput.focus();
                    });
                }

                // Keyboard navigation
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        this.dispatchEvent(new Event('input'));
                    } else if (e.key === 'Enter') {
                        // Navigate to first visible result
                        const firstVisible = menuItems.querySelector('.nav-item:not([style*="display: none"]) .nav-link');
                        if (firstVisible) {
                            firstVisible.click();
                        }
                    }
                });
            }

            // Show/hide no results message
            function showNoResults(show) {
                let noResultsMsg = document.getElementById('no-results-msg');
                if (show && !noResultsMsg) {
                    noResultsMsg = document.createElement('li');
                    noResultsMsg.id = 'no-results-msg';
                    noResultsMsg.className = 'nav-item text-center py-3';
                    noResultsMsg.innerHTML = `
                        <div class="text-white-50">
                            <i class="bi bi-search fs-4 d-block mb-2"></i>
                            <small>No menu items found</small>
                        </div>
                    `;
                    menuItems.appendChild(noResultsMsg);
                } else if (!show && noResultsMsg) {
                    noResultsMsg.remove();
                }
            }

            // Scroll indicator functionality
            function updateScrollIndicator() {
                if (!sidebarNav || !scrollIndicator || !scrollThumb) return;

                const scrollHeight = sidebarNav.scrollHeight;
                const clientHeight = sidebarNav.clientHeight;
                const scrollTop = sidebarNav.scrollTop;

                if (scrollHeight > clientHeight) {
                    scrollIndicator.classList.add('show');

                    const thumbHeight = (clientHeight / scrollHeight) * 60;
                    const thumbTop = (scrollTop / (scrollHeight - clientHeight)) * (60 - thumbHeight);

                    scrollThumb.style.height = thumbHeight + 'px';
                    scrollThumb.style.transform = `translateY(${thumbTop}px)`;
                } else {
                    scrollIndicator.classList.remove('show');
                }
            }

            // Update scroll indicator on scroll
            if (sidebarNav) {
                sidebarNav.addEventListener('scroll', updateScrollIndicator);
                window.addEventListener('resize', updateScrollIndicator);

                // Initial update
                setTimeout(updateScrollIndicator, 100);
            }
        });

        // Simplified menu - no complex section toggling needed

        // Coming Soon functionality
        function showComingSoon(featureName) {
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-info border-0 position-fixed';
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>${featureName}</strong> is coming soon! Stay tuned for updates.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', function() {
                document.body.removeChild(toast);
            });
        }

        // Real-time Clock functionality
        function updateClock() {
            const now = new Date();

            // Format time (12-hour format with AM/PM)
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const timeString = now.toLocaleTimeString('en-US', timeOptions);

            // Format date
            const dateOptions = {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            const dateString = now.toLocaleDateString('en-US', dateOptions);

            // Update DOM elements
            const timeElement = document.getElementById('current-time');
            const dateElement = document.getElementById('current-date');

            if (timeElement) {
                timeElement.textContent = timeString;
            }

            if (dateElement) {
                dateElement.textContent = dateString;
            }
        }

        // Initialize clock and update every second
        document.addEventListener('DOMContentLoaded', function() {
            // Update clock immediately
            updateClock();

            // Update every second
            setInterval(updateClock, 1000);

            // Add smooth transition effect
            const clockElement = document.getElementById('real-time-clock');
            if (clockElement) {
                clockElement.style.transition = 'all 0.3s ease';
            }
        });

        // Avatar fallback handling
        document.addEventListener('DOMContentLoaded', function() {
            const avatarImages = document.querySelectorAll('img[alt="Profile Picture"]');
            avatarImages.forEach(function(img) {
                img.addEventListener('error', function() {
                    // Hide the image and show placeholder
                    this.style.display = 'none';
                    const placeholder = this.nextElementSibling;
                    if (placeholder && placeholder.classList.contains('avatar-placeholder')) {
                        placeholder.style.display = 'flex';
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
