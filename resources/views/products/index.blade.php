@extends('layouts.modern')

@section('title', 'Products')

@section('breadcrumb')
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-box me-2"></i>
            Products Management
        </h1>
        <p class="text-muted mb-0">Manage your product inventory</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Add New Product
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('products.index') }}" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search products by name, unit, status, or category...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i>
                        Search
                    </button>
                    @if($search)
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

<!-- Products Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-list-ul me-2"></i>
            Products List
        </h5>
        <span class="badge bg-primary">{{ $products->total() }} total</span>
    </div>
    <div class="card-body p-0">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="100">Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th width="100">Quantity</th>
                            <th width="100">Unit</th>
                            <th width="100">Price</th>
                            <th width="120">Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark">#{{ $product->id }}</span>
                                </td>
                                <td>
                                    <code class="text-primary">{{ $product->sku ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <small class="text-muted">
                                        Created {{ $product->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-info">{{ $product->category->name }}</span>
                                    @else
                                        <span class="text-muted">No Category</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold {{ $product->quantity <= 10 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($product->quantity) }}
                                    </span>
                                </td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    @switch($product->status)
                                        @case('active')
                                            <span class="badge bg-success">Active</span>
                                            @break
                                        @case('inactive')
                                            <span class="badge bg-warning">Inactive</span>
                                            @break
                                        @case('discontinued')
                                            <span class="badge bg-danger">Discontinued</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($product->status) }}</span>
                                    @endswitch
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
        @else
            <div class="text-center py-5">
                <i class="bi bi-box display-1 text-muted"></i>
                <h4 class="mt-3">No Products Found</h4>
                <p class="text-muted">
                    @if($search)
                        No products match your search criteria.
                        <a href="{{ route('products.index') }}" class="text-decoration-none">View all products</a>
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
    
    @if($products->hasPages())
        <!-- Enhanced Pagination -->
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
