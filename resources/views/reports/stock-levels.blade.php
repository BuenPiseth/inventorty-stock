@extends('layouts.modern')

@section('title', 'Stock Levels Report')
@section('page-title', 'Stock Levels Report')
@section('page-subtitle', 'Current inventory levels across all products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-boxes text-info me-2"></i>
            Stock Levels Report
        </h1>
        <p class="text-muted mb-0">Current inventory status for all products</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>
            Print Report
        </button>
        <a href="{{ route('stock.in') }}" class="btn btn-success">
            <i class="bi bi-arrow-down-circle me-1"></i>
            Add Stock
        </a>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $products->count() }}</h4>
                        <p class="mb-0">Total Products</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $products->where('quantity', '>', 5)->count() }}</h4>
                        <p class="mb-0">Normal Stock</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $products->where('quantity', '<=', 5)->where('quantity', '>', 0)->count() }}</h4>
                        <p class="mb-0">Low Stock</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $products->where('quantity', '=', 0)->count() }}</h4>
                        <p class="mb-0">Out of Stock</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.stock-levels') }}" class="row g-3">
            <div class="col-md-4">
                <label for="category" class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="stock_level" class="form-label">Stock Level</label>
                <select name="stock_level" class="form-select">
                    <option value="">All Levels</option>
                    <option value="normal" {{ request('stock_level') == 'normal' ? 'selected' : '' }}>Normal Stock (>5)</option>
                    <option value="low" {{ request('stock_level') == 'low' ? 'selected' : '' }}>Low Stock (≤5)</option>
                    <option value="out" {{ request('stock_level') == 'out' ? 'selected' : '' }}>Out of Stock (0)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>
                        Filter
                    </button>
                    @if(request()->hasAny(['category', 'stock_level']))
                        <a href="{{ route('reports.stock-levels') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Stock Levels Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list me-2"></i>
            Product Stock Levels
        </h5>
    </div>
    <div class="card-body p-0">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Purchase Price</th>
                            <th>Stock Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="{{ $product->quantity == 0 ? 'table-danger' : ($product->quantity <= 5 ? 'table-warning' : '') }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            @if($product->sku)
                                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $product->category->name ?? 'No Category' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold fs-5">
                                        {{ number_format($product->quantity) }}
                                        @if($product->quantity == 0)
                                            <i class="bi bi-x-circle text-danger ms-1" title="Out of Stock"></i>
                                        @elseif($product->quantity <= 5)
                                            <i class="bi bi-exclamation-triangle text-warning ms-1" title="Low Stock"></i>
                                        @else
                                            <i class="bi bi-check-circle text-success ms-1" title="Normal Stock"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    @if($product->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->purchase_price)
                                        ${{ number_format($product->purchase_price, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->purchase_price)
                                        <strong>${{ number_format($product->quantity * $product->purchase_price, 2) }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('stock.in') }}?product={{ $product->id }}" 
                                           class="btn btn-success" 
                                           title="Add Stock">
                                            <i class="bi bi-arrow-down-circle"></i>
                                        </a>
                                        @if($product->quantity > 0)
                                            <a href="{{ route('stock.out') }}?product={{ $product->id }}" 
                                               class="btn btn-warning" 
                                               title="Remove Stock">
                                                <i class="bi bi-arrow-up-circle"></i>
                                            </a>
                                        @endif
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
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="6">Total Stock Value:</th>
                            <th>
                                <strong>
                                    ${{ number_format($products->sum(function($product) { 
                                        return $product->quantity * ($product->purchase_price ?? 0); 
                                    }), 2) }}
                                </strong>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-boxes display-1 text-muted"></i>
                <h4 class="mt-3">No Products Found</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['category', 'stock_level']))
                        No products match your filter criteria.
                        <br><a href="{{ route('reports.stock-levels') }}" class="text-decoration-none">View all products</a>
                    @else
                        Start by adding products to your inventory.
                    @endif
                </p>
                @if(!request()->hasAny(['category', 'stock_level']))
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add First Product
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
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
    
    .table-danger {
        --bs-table-bg: #f8d7da;
    }
    
    .table-warning {
        --bs-table-bg: #fff3cd;
    }
</style>
@endpush
