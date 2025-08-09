@extends('layouts.modern')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome to DEER BAKERY & CAKE inventory system')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
        </h1>
        <p class="text-muted mb-0">Welcome to your inventory management system</p>
    </div>
    <div class="text-muted">
        <i class="bi bi-calendar3 me-1"></i>
        {{ now()->format('l, F j, Y') }}
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $stats['total_products'] }}</h4>
                        <p class="mb-0">Total Products</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-box"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-primary bg-opacity-25">
                <a href="{{ route('products.index') }}" class="text-white text-decoration-none">
                    View all products <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $stats['total_categories'] }}</h4>
                        <p class="mb-0">Categories</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-success bg-opacity-25">
                <a href="{{ route('categories.index') }}" class="text-white text-decoration-none">
                    Manage categories <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $stats['low_stock_products'] }}</h4>
                        <p class="mb-0">Low Stock</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-warning bg-opacity-25">
                <span class="text-white">
                    Products with ≤ 10 units <i class="bi bi-info-circle"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['total_quantity']) }}</h4>
                        <p class="mb-0">Total Quantity</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-info bg-opacity-25">
                <span class="text-white">
                    Total inventory value <i class="bi bi-graph-up"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('products.create') }}" class="btn btn-primary w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-plus-lg fs-2 mb-2"></i>
                            <span>Add New Product</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-list fs-2 mb-2"></i>
                            <span>View All Products</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-graph-up fs-2 mb-2"></i>
                            <span>Stock Movements</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('stock.in') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-plus-square fs-2 mb-2"></i>
                            <span>Add Stock In</span>
                        </a>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('reports.stock-value') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-graph-up-arrow fs-2 mb-2"></i>
                            <span>Stock Value Report</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('reports.low-stock-alert') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-exclamation-triangle fs-2 mb-2"></i>
                            <span>Low Stock Alert</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-tags fs-2 mb-2"></i>
                            <span>Manage Categories</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('reports.stock-levels') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-bar-chart fs-2 mb-2"></i>
                            <span>Stock Levels</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Recent Products and Low Stock Alert -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Recent Products
                </h5>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                @php
                    $recentProducts = \App\Models\Product::with('category')->latest()->take(5)->get();
                @endphp

                @if($recentProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProducts as $product)
                                    <tr>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($product->category)
                                                <span class="badge bg-info">{{ $product->category->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ $product->quantity <= 10 ? 'text-danger fw-bold' : '' }}">
                                                {{ number_format($product->quantity) }} {{ $product->unit }}
                                            </span>
                                        </td>
                                        <td>
                                            @switch($product->status)
                                                @case('active')
                                                    <span class="badge bg-success">Active</span>
                                                    @break
                                                @case('inactive')
                                                    <span class="badge bg-warning">Inactive</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($product->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $product->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-box display-4 text-muted"></i>
                        <h5 class="mt-3">No Products Yet</h5>
                        <p class="text-muted">Start by adding your first product.</p>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>
                            Add First Product
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Low Stock Alert
                </h5>
            </div>
            <div class="card-body">
                @php
                    $lowStockProducts = \App\Models\Product::with('category')->where('quantity', '<=', 10)->take(5)->get();
                @endphp

                @if($lowStockProducts->count() > 0)
                    @foreach($lowStockProducts as $product)
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                            <div>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <small class="text-muted">
                                    {{ $product->category ? $product->category->name : 'No Category' }}
                                </small>
                            </div>
                            <div class="text-end">
                                <div class="text-danger fw-bold">{{ $product->quantity }} {{ $product->unit }}</div>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning">
                                    Update
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle display-4 text-success"></i>
                        <h6 class="mt-2">All Good!</h6>
                        <p class="text-muted mb-0">No products with low stock.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
