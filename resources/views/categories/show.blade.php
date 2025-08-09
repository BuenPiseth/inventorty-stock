@extends('layouts.modern')

@section('title', $category->name)
@section('page-title', $category->name)
@section('page-subtitle', 'Category details and products')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Category Products -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Products in this Category</h5>
                <span class="badge bg-primary">{{ $category->products->count() }} products</span>
            </div>
            <div class="card-body">
                @if($category->products->count() > 0)
                    <div class="row">
                        @foreach($category->products as $product)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <img src="{{ $product->image_url }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="product-image me-3">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                                @if($product->sku)
                                                    <small class="text-muted d-block">SKU: {{ $product->sku }}</small>
                                                @endif
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <span class="fw-bold">{{ number_format($product->quantity) }} {{ $product->unit }}</span>
                                                    @if($product->price)
                                                        <span class="text-primary fw-semibold">${{ number_format($product->price, 2) }}</span>
                                                    @endif
                                                </div>
                                                <div class="mt-2">
                                                    <span class="badge bg-{{ $product->status_color }}">
                                                        {{ ucfirst($product->status) }}
                                                    </span>
                                                    @if($product->hasLowStock())
                                                        <span class="badge bg-{{ $product->stock_status_color }}">
                                                            {{ $product->stock_status === 'out_of_stock' ? 'Out of Stock' : 'Low Stock' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
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
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-cake2 display-4 text-muted"></i>
                        <h5 class="mt-3">No Products Yet</h5>
                        <p class="text-muted">This category doesn't have any products assigned to it yet.</p>
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
        <!-- Category Info -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Category Information
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Name:</dt>
                    <dd class="col-sm-7">{{ $category->name }}</dd>
                    
                    <dt class="col-sm-5">ID:</dt>
                    <dd class="col-sm-7">{{ $category->id }}</dd>
                    
                    <dt class="col-sm-5">Products:</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-primary">{{ $category->products->count() }}</span>
                    </dd>
                    
                    <dt class="col-sm-5">Created:</dt>
                    <dd class="col-sm-7">
                        {{ $category->created_at->format('M d, Y') }}
                        <br><small class="text-muted">{{ $category->created_at->diffForHumans() }}</small>
                    </dd>
                    
                    <dt class="col-sm-5">Updated:</dt>
                    <dd class="col-sm-7">
                        {{ $category->updated_at->format('M d, Y') }}
                        <br><small class="text-muted">{{ $category->updated_at->diffForHumans() }}</small>
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Category
                    </a>
                    <a href="{{ route('products.create') }}?category_id={{ $category->id }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Product to Category
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Categories
                    </a>
                    @if($category->products->count() == 0)
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" data-confirm-delete>
                                <i class="bi bi-trash me-2"></i>
                                Delete Category
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics -->
        @if($category->products->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-success mb-0">{{ $category->products->where('status', 'active')->count() }}</h4>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info mb-0">{{ $category->products->sum('quantity') }}</h4>
                            <small class="text-muted">Total Stock</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-warning mb-0">{{ $category->products->filter(function($p) { return $p->hasLowStock(); })->count() }}</h4>
                                <small class="text-muted">Low Stock</small>
                            </div>
                        </div>
                        <div class="col-6">
                            @if($category->products->where('price', '>', 0)->count() > 0)
                                <h4 class="text-primary mb-0">${{ number_format($category->products->avg('price'), 2) }}</h4>
                                <small class="text-muted">Avg Price</small>
                            @else
                                <h4 class="text-muted mb-0">N/A</h4>
                                <small class="text-muted">Avg Price</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirm delete
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButton = document.querySelector('[data-confirm-delete]');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
