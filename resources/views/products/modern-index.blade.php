@extends('layouts.modern')

@section('title', 'Products')
@section('page-title', 'Products')
@section('page-subtitle', 'Manage your products and inventory')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-primary">{{ $products->total() }}</h3>
                    <p class="text-muted mb-0">Total Products</p>
                </div>
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-success">{{ $products->where('status', 'active')->count() }}</h3>
                    <p class="text-muted mb-0">Active Products</p>
                </div>
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-warning">{{ $products->where('quantity', '<=', 10)->count() }}</h3>
                    <p class="text-muted mb-0">Low Stock</p>
                </div>
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-info">{{ $products->sum('quantity') }}</h3>
                    <p class="text-muted mb-0">Total Stock</p>
                </div>
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Product
        </a>
        <a href="{{ route('stock.in') }}" class="btn btn-success">
            <i class="bi bi-arrow-down-circle me-1"></i>
            Stock In
        </a>
        <a href="{{ route('stock.out') }}" class="btn btn-warning">
            <i class="bi bi-arrow-up-circle me-1"></i>
            Stock Out
        </a>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>
            Print
        </button>
        <button class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i>
            Export
        </button>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('products.index') }}" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text"
                           class="form-control"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search products, SKU, category...">
                </div>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="low_stock" value="1"
                           {{ $lowStock ? 'checked' : '' }} id="lowStockFilter">
                    <label class="form-check-label" for="lowStockFilter">
                        Low Stock
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>
                        Filter
                    </button>
                    @if($search || $category || $status || $lowStock)
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Grid/Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Products</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="view-grid">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary active" id="view-table">
                <i class="bi bi-table"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($products->count() > 0)
            <!-- Table View -->
            <div id="table-view">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="80">Image</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <img src="{{ $product->image_url }}" 
                                             alt="{{ $product->name }}" 
                                             class="product-image">
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            @if($product->sku)
                                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge bg-info">{{ $product->category->name }}</span>
                                        @else
                                            <span class="text-muted">No Category</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-2 {{ $product->stock_status_color === 'danger' ? 'text-danger' : ($product->stock_status_color === 'warning' ? 'text-warning' : 'text-success') }}">
                                                {{ number_format($product->quantity) }}
                                            </span>
                                            <small class="text-muted">{{ $product->unit }}</small>
                                        </div>
                                        @if($product->hasLowStock())
                                            <small class="badge bg-{{ $product->stock_status_color }}">
                                                {{ $product->stock_status === 'out_of_stock' ? 'Out of Stock' : 'Low Stock' }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->price)
                                            <span class="fw-semibold">${{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $product->status_color }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Delete"
                                                        data-confirm-delete>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grid View (Hidden by default) -->
            <div id="grid-view" style="display: none;">
                <div class="row p-3">
                    @foreach($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="{{ $product->image_url }}" 
                                         class="card-img-top" 
                                         alt="{{ $product->name }}"
                                         style="height: 200px; object-fit: cover;">
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-{{ $product->status_color }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">{{ $product->name }}</h6>
                                    @if($product->sku)
                                        <small class="text-muted d-block">SKU: {{ $product->sku }}</small>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="fw-bold">{{ number_format($product->quantity) }} {{ $product->unit }}</span>
                                        @if($product->price)
                                            <span class="text-primary fw-semibold">${{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-box display-1 text-muted"></i>
                <h4 class="mt-3">No Products Found</h4>
                <p class="text-muted">
                    @if($search)
                        No products match your search criteria.
                        <br><a href="{{ route('products.index') }}" class="text-decoration-none">View all products</a>
                    @else
                        Start by adding your first product to the inventory.
                    @endif
                </p>
                @if(!$search)
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add First Product
                    </a>
                @endif
            </div>
        @endif
    </div>
    
    <!-- Enhanced Pagination -->
    @if($products->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Showing <strong>{{ $products->firstItem() }}</strong> to <strong>{{ $products->lastItem() }}</strong>
                    of <strong>{{ $products->total() }}</strong> products
                    <span class="ms-2">
                        <small class="badge bg-primary">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</small>
                    </span>
                </div>
                <div>
                    {{ $products->links('custom-pagination') }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // View toggle functionality
    document.getElementById('view-grid').addEventListener('click', function() {
        document.getElementById('table-view').style.display = 'none';
        document.getElementById('grid-view').style.display = 'block';
        this.classList.add('active');
        document.getElementById('view-table').classList.remove('active');
    });

    document.getElementById('view-table').addEventListener('click', function() {
        document.getElementById('grid-view').style.display = 'none';
        document.getElementById('table-view').style.display = 'block';
        this.classList.add('active');
        document.getElementById('view-grid').classList.remove('active');
    });

    // Confirm delete
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
