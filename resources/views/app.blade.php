<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title') | Inventory System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/deer-bakery-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/deer-bakery-logo.png') }}">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
</head>
<body>
    @include('partials.navbar')

    <div class="container mt-4">
        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
