<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * Public Routes
 * These routes are accessible without authentication
 */
Route::get('/', function () {
    return view('welcome');
});

// CSRF token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->middleware('web');

/**
 * Test Route
 * Simple test to verify the application is working
 */
Route::get('/test', function () {
    $categoriesCount = \App\Models\Category::count();
    $productsCount = \App\Models\Product::count();

    return response()->json([
        'status' => 'success',
        'message' => 'Laravel CRUD application is working!',
        'data' => [
            'categories_count' => $categoriesCount,
            'products_count' => $productsCount,
            'database' => config('database.default'),
            'app_name' => config('app.name')
        ]
    ]);
});

/**
 * Test Blade Template
 */
Route::get('/test-ui', function () {
    return view('welcome');
});

/**
 * Test CSS Loading
 */
Route::get('/test-css', function () {
    return '<!DOCTYPE html>
<html>
<head>
    <title>CSS Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 50px; }
        .test-card { background: white; color: black; padding: 30px; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <h1>CSS Test Page</h1>
            <p>If you can see this styled properly, CSS is working!</p>
            <a href="/login" class="btn btn-primary">Go to Login</a>
            <a href="/register" class="btn btn-success">Go to Register</a>
            <a href="/test-register" class="btn btn-warning">Test Register</a>
        </div>
    </div>
</body>
</html>';
});

/**
 * Test Register Page
 */
Route::get('/test-register', function () {
    return view('auth.register');
});

/**
 * Category Check
 */
Route::get('/check-categories', function () {
    $categories = App\Models\Category::orderBy('id')->get(['id', 'name', 'description']);
    $html = '<!DOCTYPE html><html><head><title>Category Check</title><style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f2f2f2;}</style></head><body>';
    $html .= '<h1>DEER BAKERY & CAKE - Categories Status</h1>';
    $html .= '<table><tr><th>ID</th><th>Name</th><th>Description</th></tr>';
    foreach($categories as $cat) {
        $html .= '<tr><td>' . $cat->id . '</td><td>' . $cat->name . '</td><td>' . ($cat->description ?? 'No description') . '</td></tr>';
    }
    $html .= '</table><br><a href="/categories" style="background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;">Go to Categories Page</a></body></html>';
    return $html;
});

/**
 * Diagnostic Page
 */
Route::get('/diagnostic', function () {
    return '<!DOCTYPE html>
<html>
<head>
    <title>Diagnostic Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f8f9fa; }
        .diagnostic-card { background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-link { display: inline-block; margin: 10px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .test-link:hover { background: #0056b3; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="diagnostic-card">
            <h1><i class="bi bi-tools"></i> DEER BAKERY & CAKE - System Diagnostic</h1>
            <p>This page will help diagnose UI issues. All links below should show properly styled pages:</p>

            <h3>Authentication Pages:</h3>
            <a href="/login" class="test-link">Login Page</a>
            <a href="/register" class="test-link">Register Page</a>
            <a href="/test-register" class="test-link">Test Register</a>

            <h3>Main System Pages:</h3>
            <a href="/dashboard" class="test-link">Dashboard</a>
            <a href="/products" class="test-link">Products</a>
            <a href="/categories" class="test-link">Categories</a>
            <a href="/stock-in" class="test-link">Stock In</a>
            <a href="/stock-out" class="test-link">Stock Out</a>

            <h3>Test Pages:</h3>
            <a href="/test-css" class="test-link">CSS Test</a>
            <a href="/test-ui" class="test-link">UI Test</a>
            <a href="/" class="test-link">Home Page</a>

            <div class="alert alert-info mt-4">
                <strong>Instructions:</strong> Click each link above. If you see unstyled pages (plain text), there is a CSS loading issue.
                If you see styled pages, the system is working correctly.
            </div>
        </div>
    </div>
</body>
</html>';
});

/**
 * Dashboard Route
 * Protected by authentication middleware
 */
Route::get('/dashboard', function () {
    $stats = [
        'total_products' => \App\Models\Product::count(),
        'total_categories' => \App\Models\Category::count(),
        'low_stock_products' => \App\Models\Product::where('quantity', '<=', 5)->count(),
        'recent_movements' => \App\Models\StockMovement::latest()->take(5)->get(),
        'total_quantity' => \App\Models\Product::sum('quantity'),
        'active_products' => \App\Models\Product::where('status', 'active')->count(),
    ];

    return view('dashboard', compact('stats'));
})->middleware(['auth'])->name('dashboard');

/**
 * Protected Routes
 * All inventory routes require authentication
 */
Route::middleware('auth')->group(function () {

    /**
     * Product Resource Routes
     * Standard CRUD operations for products
     */
    Route::resource('products', ProductController::class);

    /**
     * Category Resource Routes
     * Category management functionality
     */
    Route::resource('categories', CategoryController::class);

    /**
     * Stock Movement Routes
     * Stock in/out operations and tracking
     */
    Route::resource('stock-movements', StockMovementController::class);
    Route::get('/stock-in', [StockMovementController::class, 'stockIn'])->name('stock.in');
    Route::get('/stock-out', [StockMovementController::class, 'stockOut'])->name('stock.out');
    Route::delete('/stock-movements/{stockMovement}/force', [StockMovementController::class, 'forceDestroy'])->name('stock-movements.force-destroy');
    Route::post('/stock-movements/quick-adjustment', [StockMovementController::class, 'quickAdjustment'])->name('stock-movements.quick-adjustment');

    // Recycle Bin routes for stock movements
    Route::get('/stock-movements/recycle-bin', [StockMovementController::class, 'recycleBin'])->name('stock-movements.recycle-bin');
    Route::post('/stock-movements/{id}/restore', [StockMovementController::class, 'restore'])->name('stock-movements.restore');
    Route::delete('/stock-movements/{id}/force-delete', [StockMovementController::class, 'forceDelete'])->name('stock-movements.force-delete');
    Route::post('/stock-movements/bulk-restore', [StockMovementController::class, 'bulkRestore'])->name('stock-movements.bulk-restore');
    Route::delete('/stock-movements/bulk-force-delete', [StockMovementController::class, 'bulkForceDelete'])->name('stock-movements.bulk-force-delete');

    Route::get('/api/products/{product}', [StockMovementController::class, 'getProductDetails'])->name('api.products.details');

    /**
     * Import Routes
     * CSV import functionality
     */
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/csv', [ImportController::class, 'importCsv'])->name('import.csv');
    Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');

    /**
     * Report Routes
     * Analytics and reporting functionality
     */
    Route::get('/reports/stock-levels', [ReportController::class, 'stockLevels'])->name('reports.stock-levels');
    Route::get('/reports/movement-summary', [ReportController::class, 'movementSummary'])->name('reports.movement-summary');
    Route::get('/reports/low-stock-alert', [ReportController::class, 'lowStockAlert'])->name('reports.low-stock-alert');
    Route::get('/reports/category-performance', [ReportController::class, 'categoryPerformance'])->name('reports.category-performance');
    Route::get('/reports/stock-value', [ReportController::class, 'stockValue'])->name('reports.stock-value');

    // Warehouses
    Route::resource('warehouses', WarehouseController::class);
    Route::post('/warehouses/transfer', [WarehouseController::class, 'transfer'])->name('warehouses.transfer');

    // Database Management
    Route::get('/database/status', function () {
        try {
            $pdo = DB::connection()->getPdo();
            $stats = [
                'connection' => 'Active',
                'driver' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
                'database' => config('database.connections.mysql.database'),
                'server_info' => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
                'tables' => [
                    'products' => App\Models\Product::count(),
                    'categories' => App\Models\Category::count(),
                    'stock_movements' => App\Models\StockMovement::count(),
                    'warehouses' => App\Models\Warehouse::count(),
                ],
                'total_records' => App\Models\Product::count() +
                                 App\Models\Category::count() +
                                 App\Models\StockMovement::count() +
                                 App\Models\Warehouse::count(),
                'last_activity' => App\Models\StockMovement::latest()->first()?->created_at?->format('Y-m-d H:i:s') ?? 'No activity',
            ];

            return view('database.status', compact('stats'));
        } catch (Exception $e) {
            return view('database.status', ['error' => $e->getMessage()]);
        }
    })->name('database.status');

    // Profile & Settings
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.show');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings.index');
    Route::put('/settings', [UserController::class, 'updateSettings'])->name('settings.update');
});





/**
 * Authentication Routes
 * These routes handle user authentication (login, register, etc.)
 */
require __DIR__.'/auth.php';
