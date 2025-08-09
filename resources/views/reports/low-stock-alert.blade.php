@extends('layouts.modern')

@section('title', 'Low Stock Alert')
@section('page-title', 'Low Stock Alert')
@section('page-subtitle', 'Monitor products with low inventory levels')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
            Low Stock Alert
        </h1>
        <p class="text-muted mb-0">Products that need immediate attention</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock.in') }}" class="btn btn-success">
            <i class="bi bi-arrow-down-circle me-1"></i>
            Add Stock
        </a>
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>
            Print Report
        </button>
    </div>
</div>

<!-- Alert Summary -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $outOfStockProducts->count() }}</h4>
                        <p class="mb-0">Out of Stock</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $lowStockProducts->count() }}</h4>
                        <p class="mb-0">Low Stock (≤5 units)</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $lowStockProducts->count() + $outOfStockProducts->count() }}</h4>
                        <p class="mb-0">Total Alerts</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-bell"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($outOfStockProducts->count() > 0)
<!-- Out of Stock Products -->
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="bi bi-x-circle me-2"></i>
            Out of Stock Products ({{ $outOfStockProducts->count() }})
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outOfStockProducts as $product)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                @if($product->sku)
                                    <small class="text-muted">SKU: {{ $product->sku }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $product->category->name ?? 'No Category' }}
                                </span>
                            </td>
                            <td>{{ $product->unit }}</td>
                            <td>
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    0 {{ $product->unit }}
                                </span>
                            </td>
                            <td>
                                @if($product->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stock.in') }}?product={{ $product->id }}" 
                                       class="btn btn-success" 
                                       title="Add Stock">
                                        <i class="bi bi-arrow-down-circle"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Edit Product">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if($lowStockProducts->where('quantity', '>', 0)->count() > 0)
<!-- Low Stock Products -->
<div class="card">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Low Stock Products ({{ $lowStockProducts->where('quantity', '>', 0)->count() }})
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts->where('quantity', '>', 0) as $product)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                @if($product->sku)
                                    <small class="text-muted">SKU: {{ $product->sku }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $product->category->name ?? 'No Category' }}
                                </span>
                            </td>
                            <td>{{ $product->unit }}</td>
                            <td>
                                <span class="badge bg-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    {{ $product->quantity }} {{ $product->unit }}
                                </span>
                            </td>
                            <td>
                                @if($product->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stock.in') }}?product={{ $product->id }}" 
                                       class="btn btn-success" 
                                       title="Add Stock">
                                        <i class="bi bi-arrow-down-circle"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Edit Product">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if($lowStockProducts->count() === 0 && $outOfStockProducts->count() === 0)
<!-- No Alerts -->
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-check-circle display-1 text-success"></i>
        <h4 class="mt-3 text-success">All Good!</h4>
        <p class="text-muted">
            No products are currently low on stock or out of stock.
            <br>Your inventory levels are healthy.
        </p>
        <div class="d-flex gap-2 justify-content-center mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="bi bi-list me-1"></i>
                View All Products
            </a>
            <a href="{{ route('reports.stock-levels') }}" class="btn btn-outline-info">
                <i class="bi bi-boxes me-1"></i>
                Stock Levels Report
            </a>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    @media print {
        .btn, .card-header, nav, .sidebar {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
</style>
@endpush
