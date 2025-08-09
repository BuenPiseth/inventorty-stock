<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Professional Inventory Management</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/deer-bakery-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #f59e0b;
            --accent-color: #10b981;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-soft: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 20px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            overflow-x: hidden;
        }

        .hero-section {
            min-height: 100vh;
            background: var(--gradient-primary);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/></svg>') no-repeat center center;
            background-size: cover;
            opacity: 0.3;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: white !important;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
            color: white !important;
        }

        .navbar-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 15px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
            transition: all 0.3s ease;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .navbar-logo:hover {
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3));
            transform: scale(1.1) rotate(5deg);
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 8px 16px !important;
            margin: 0 4px;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .btn-primary-custom {
            background: var(--gradient-accent);
            border: none;
            padding: 14px 32px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            color: white;
        }

        .btn-outline-custom {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-custom:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            color: white;
        }

        .hero-visual {
            position: relative;
        }

        .floating-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin: 1rem;
            animation: float 6s ease-in-out infinite;
        }

        .floating-card:nth-child(2) {
            animation-delay: -2s;
        }

        .floating-card:nth-child(3) {
            animation-delay: -4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .features-section {
            padding: 100px 0;
            background: var(--light-color);
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #6b7280;
            text-align: center;
            margin-bottom: 4rem;
        }

        .feature-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2.5rem 2rem;
            text-align: center;
            border: none;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-medium);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .feature-icon.primary { background: var(--gradient-primary); }
        .feature-icon.secondary { background: var(--gradient-secondary); }
        .feature-icon.accent { background: var(--gradient-accent); }

        .feature-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .feature-description {
            color: #6b7280;
            line-height: 1.7;
        }

        .hero-logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .hero-logo {
            width: 180px;
            height: 180px;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.2));
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.05));
            padding: 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            animation: heroLogoFloat 4s ease-in-out infinite;
        }

        @keyframes heroLogoFloat {
            0%, 100% {
                transform: translateY(0px) scale(1);
            }
            50% {
                transform: translateY(-15px) scale(1.02);
            }
        }

        .hero-logo:hover {
            transform: scale(1.1) translateY(-10px);
            filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.3));
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1));
            border-color: rgba(255, 255, 255, 0.4);
        }

        .hero-logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse 3s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.5;
                transform: translate(-50%, -50%) scale(1);
            }
            50% {
                opacity: 0.8;
                transform: translate(-50%, -50%) scale(1.1);
            }
        }

        .footer-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 15px;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.3));
            transition: all 0.3s ease;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-logo:hover {
            transform: scale(1.1);
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.4));
            background: rgba(255, 255, 255, 0.15);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .navbar-logo {
                width: 50px;
                height: 50px;
            }

            .hero-logo {
                width: 140px;
                height: 140px;
            }

            .navbar-brand {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#" data-aos="fade-right">
                <img src="{{ asset('images/deer-bakery-logo.png') }}?v={{ time() }}"
                     alt="DEER BAKERY & CAKE"
                     class="navbar-logo">
                DEER BAKERY & CAKE
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto" data-aos="fade-left">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('products.index') }}" class="nav-link">
                            <i class="bi bi-box me-1"></i>
                            Products
                        </a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="nav-link">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Login
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link">
                                <i class="bi bi-person-plus me-1"></i>
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6 hero-content">
                    <div data-aos="fade-up" data-aos-delay="200">
                        <div class="hero-logo-container">
                            <div class="hero-logo-glow"></div>
                            <img src="{{ asset('images/deer-bakery-logo.png') }}?v={{ time() }}"
                                 alt="DEER BAKERY & CAKE"
                                 class="hero-logo">
                        </div>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="400">
                        <h1 class="hero-title">
                            Welcome to<br>
                            <span style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">DEER BAKERY & CAKE</span>
                        </h1>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="600">
                        <p class="hero-subtitle">
                            Transform your bakery operations with our cutting-edge inventory management system.
                            Streamline workflows, track stock in real-time, and boost your business efficiency
                            with intelligent automation and insightful analytics.
                        </p>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="800">
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-primary-custom">
                                    <i class="bi bi-speedometer2"></i>
                                    Go to Dashboard
                                </a>
                                <a href="{{ route('products.index') }}" class="btn-outline-custom">
                                    <i class="bi bi-box"></i>
                                    View Products
                                </a>
                            @else
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="btn-primary-custom">
                                        <i class="bi bi-rocket-takeoff"></i>
                                        Get Started
                                    </a>
                                @endif
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-outline-custom">
                                        <i class="bi bi-person-plus"></i>
                                        Sign Up Free
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 hero-visual" data-aos="fade-left" data-aos-delay="1000">
                    <div class="position-relative">
                        <div class="floating-card text-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-graph-up-arrow me-2 text-success"></i>
                                <strong>Stock Analytics</strong>
                            </div>
                            <small>Real-time inventory tracking</small>
                        </div>

                        <div class="floating-card text-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-bell me-2 text-warning"></i>
                                <strong>Smart Alerts</strong>
                            </div>
                            <small>Low stock notifications</small>
                        </div>

                        <div class="floating-card text-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-shield-check me-2 text-info"></i>
                                <strong>Secure & Reliable</strong>
                            </div>
                            <small>Enterprise-grade security</small>
                        </div>

                        <div class="text-center mt-4">
                            <i class="bi bi-boxes" style="font-size: 8rem; color: rgba(255, 255, 255, 0.1);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features-section">
        <div class="container">
            <div data-aos="fade-up">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">
                    Everything you need to manage your bakery inventory with precision and ease
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h3 class="feature-title">Smart Product Management</h3>
                        <p class="feature-description">
                            Organize your bakery products with intelligent categorization,
                            batch tracking, and automated SKU generation for seamless inventory control.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon secondary">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="feature-title">Real-Time Analytics</h3>
                        <p class="feature-description">
                            Monitor stock levels, track usage patterns, and get predictive insights
                            to optimize your inventory and reduce waste.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon accent">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h3 class="feature-title">Intelligent Alerts</h3>
                        <p class="feature-description">
                            Receive smart notifications for low stock, expiring items,
                            and reorder suggestions to keep your bakery running smoothly.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="800">
                    <div class="feature-card">
                        <div class="feature-icon primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="feature-title">Multi-User Access</h3>
                        <p class="feature-description">
                            Secure role-based access control for your team with customizable
                            permissions and activity tracking across all operations.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="1000">
                    <div class="feature-card">
                        <div class="feature-icon secondary">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                        </div>
                        <h3 class="feature-title">Advanced Reporting</h3>
                        <p class="feature-description">
                            Generate comprehensive reports on inventory turnover, cost analysis,
                            and departmental usage with exportable data formats.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="1200">
                    <div class="feature-card">
                        <div class="feature-icon accent">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="feature-title">Data Security</h3>
                        <p class="feature-description">
                            Enterprise-grade security with encrypted data storage,
                            regular backups, and compliance with industry standards.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    @auth
        <div style="background: var(--gradient-primary); padding: 80px 0;">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title text-white">Your Inventory at a Glance</h2>
                    <p class="text-white opacity-75">Real-time insights into your bakery operations</p>
                </div>

                <div class="row text-center text-white g-4">
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="p-4">
                            <div class="feature-icon accent mb-3 mx-auto">
                                <i class="bi bi-box"></i>
                            </div>
                            <h3 class="fw-bold display-6">{{ \App\Models\Product::count() }}</h3>
                            <p class="mb-0 opacity-75">Total Products</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="p-4">
                            <div class="feature-icon secondary mb-3 mx-auto">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h3 class="fw-bold display-6">{{ \App\Models\Product::where('status', 'active')->count() }}</h3>
                            <p class="mb-0 opacity-75">Active Products</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="p-4">
                            <div class="feature-icon primary mb-3 mx-auto">
                                <i class="bi bi-tags"></i>
                            </div>
                            <h3 class="fw-bold display-6">{{ \App\Models\Category::count() }}</h3>
                            <p class="mb-0 opacity-75">Categories</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="800">
                        <div class="p-4">
                            <div class="feature-icon accent mb-3 mx-auto">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <h3 class="fw-bold display-6">{{ \App\Models\Product::where('quantity', '<=', 10)->count() }}</h3>
                            <p class="mb-0 opacity-75">Low Stock Items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    <!-- CTA Section -->
    @guest
        <div style="background: var(--light-color); padding: 80px 0;">
            <div class="container text-center">
                <div data-aos="fade-up">
                    <h2 class="section-title">Ready to Transform Your Bakery?</h2>
                    <p class="section-subtitle">
                        Join thousands of bakeries already using our inventory management system
                    </p>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary-custom">
                                <i class="bi bi-rocket-takeoff"></i>
                                Start Free Trial
                            </a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn-outline-custom" style="color: var(--dark-color); border-color: var(--dark-color);">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Sign In
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endguest

    <!-- Footer -->
    <footer style="background: var(--dark-color); color: white; padding: 60px 0 30px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/deer-bakery-logo.png') }}?v={{ time() }}"
                             alt="DEER BAKERY & CAKE"
                             class="footer-logo">
                        <h5 class="mb-0 fw-bold">DEER BAKERY & CAKE</h5>
                    </div>
                    <p class="text-light opacity-75">
                        Professional inventory management system designed specifically for bakery businesses.
                    </p>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Product</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light opacity-75 text-decoration-none">Features</a></li>
                        <li><a href="#" class="text-light opacity-75 text-decoration-none">Pricing</a></li>
                        <li><a href="#" class="text-light opacity-75 text-decoration-none">Security</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light opacity-75 text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-light opacity-75 text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-light opacity-75 text-decoration-none">Documentation</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Stay Updated</h6>
                    <p class="text-light opacity-75 mb-3">Get the latest updates and features.</p>
                    <div class="d-flex gap-2">
                        <input type="email" class="form-control" placeholder="Enter your email" style="border-radius: 8px;">
                        <button class="btn btn-primary" style="border-radius: 8px; white-space: nowrap;">Subscribe</button>
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75">
                        © {{ date('Y') }} {{ config('app.name', 'DEER BAKERY & CAKE') }}. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-light opacity-75">
                        Built with <i class="bi bi-heart-fill text-danger"></i> using Laravel {{ app()->version() }}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(31, 41, 55, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.1)';
                navbar.style.backdropFilter = 'blur(10px)';
            }
        });
    </script>
</body>
</html>
