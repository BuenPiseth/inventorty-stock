<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - DEER BAKERY & CAKE</title>

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
            min-height: 100vh;
            background: var(--gradient-primary);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        body::before {
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

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            padding: 0 1rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: var(--shadow-medium);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.25);
        }

        .login-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.05));
            backdrop-filter: blur(15px);
            color: white;
            padding: 3rem 2.5rem 2rem;
            text-align: center;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-body {
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
        }

        .hero-logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .hero-logo {
            width: 140px;
            height: 140px;
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
            width: 160px;
            height: 160px;
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

        .brand-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            color: rgba(255, 255, 255, 0.9);
        }

        .welcome-title {
            color: var(--dark-color);
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        .welcome-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--gradient-accent);
            border-radius: 2px;
        }

        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            padding: 1rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 1);
            border-color: var(--primary-color);
            box-shadow:
                0 0 0 0.25rem rgba(37, 99, 235, 0.15),
                var(--shadow-medium);
            transform: translateY(-2px);
            outline: none;
        }

        .input-group {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.1);
        }

        .input-group-text {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px solid var(--border-color);
            border-right: none;
            color: var(--text-light);
            font-size: 1.1rem;
            padding: 1rem 1.25rem;
        }

        .input-group .form-control {
            border-left: none;
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            color: var(--primary-color);
        }

        .input-group:focus-within .form-control {
            border-color: var(--primary-color);
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
            width: 100%;
            justify-content: center;
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
        }

        .btn-primary-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary-custom:hover::before {
            left: 100%;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            color: white;
        }

        .form-check {
            margin: 1.5rem 0;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 6px;
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-color: var(--primary-color);
        }

        .form-check-label {
            color: var(--text-dark);
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .auth-links {
            text-align: center;
            margin-top: 2rem;
        }

        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .auth-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transition: width 0.3s ease;
        }

        .auth-links a:hover::after {
            width: 100%;
        }

        .auth-links a:hover {
            color: var(--secondary-color);
            transform: translateY(-1px);
        }

        .demo-info {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        .demo-info small {
            color: var(--text-dark);
            font-weight: 500;
        }

        .alert {
            border-radius: 12px;
            border: none;
            backdrop-filter: blur(10px);
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #155724;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                padding: 0 0.5rem;
            }

            .login-card {
                border-radius: 20px;
            }

            .login-header {
                padding: 2rem 1.5rem 1.5rem;
            }

            .login-body {
                padding: 2rem 1.5rem;
            }

            .logo {
                width: 100px;
                height: 100px;
            }

            .brand-title {
                font-size: 1.5rem;
            }

            .welcome-title {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            .login-header {
                padding: 1.5rem 1rem;
            }

            .login-body {
                padding: 1.5rem 1rem;
            }

            .logo {
                width: 80px;
                height: 80px;
            }

            .brand-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card" data-aos="fade-up">
            <div class="login-header">
                <div class="hero-logo-container" data-aos="zoom-in" data-aos-delay="200">
                    <div class="hero-logo-glow"></div>
                    <img src="{{ asset('images/deer-bakery-logo.png') }}?v={{ time() }}"
                         alt="DEER BAKERY & CAKE Logo"
                         class="hero-logo">
                </div>
                <h3 class="brand-title" data-aos="fade-up" data-aos-delay="400">DEER BAKERY & CAKE</h3>
                <p class="brand-subtitle" data-aos="fade-up" data-aos-delay="500">Professional Inventory Management System</p>
            </div>

            <div class="login-body">
                <h4 class="welcome-title" data-aos="fade-up" data-aos-delay="600">Welcome Back!</h4>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autofocus
                                           placeholder="Enter your email">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required
                                           placeholder="Enter your password">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <div class="d-grid" data-aos="fade-up" data-aos-delay="1000">
                                <button type="submit" class="btn-primary-custom">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    Sign In
                                </button>
                            </div>
                        </form>

                <div class="auth-links">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <div class="demo-info">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        Demo Login: <strong>admin@deerbakery.com</strong> / <strong>password</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>

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
    </script>
</body>
</html>
