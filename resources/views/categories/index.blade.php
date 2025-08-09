@extends('layouts.modern')

@section('title', 'Categories')
@section('page-title', 'Categories')
@section('page-subtitle', 'Manage your bakery product categories')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-primary">{{ $categories->total() }}</h3>
                    <p class="text-muted mb-0">Total Categories</p>
                </div>
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-collection"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-success">{{ $categories->sum('products_count') }}</h3>
                    <p class="text-muted mb-0">Total Products</p>
                </div>
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cake2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-info">{{ $categories->where('products_count', '>', 0)->count() }}</h3>
                    <p class="text-muted mb-0">Active Categories</p>
                </div>
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-warning">{{ $categories->where('products_count', 0)->count() }}</h3>
                    <p class="text-muted mb-0">Empty Categories</p>
                </div>
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Category
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-cake2 me-1"></i>
            View Products
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
        <form method="GET" action="{{ route('categories.index') }}" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search categories by name...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>
                        Search
                    </button>
                    @if($search)
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Categories</h5>
        <small class="text-muted">{{ $categories->total() }} total</small>
    </div>
    <div class="card-body p-0">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Products Count</th>
                            <th>Created</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="category-icon me-3">
                                            @switch($category->name)
                                                @case('coffee')
                                                    <i class="bi bi-cup-hot text-warning"></i>
                                                    @break
                                                @case('cake')
                                                    <i class="bi bi-cake2 text-primary"></i>
                                                    @break
                                                @case('bakery')
                                                    <i class="bi bi-shop text-success"></i>
                                                    @break
                                                @case('Cashier')
                                                    <i class="bi bi-cash-register text-info"></i>
                                                    @break
                                                @default
                                                    <i class="bi bi-collection text-muted"></i>
                                            @endswitch
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $category->name }}</div>
                                            <small class="text-muted">ID: {{ $category->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted">
                                        {{ $category->description ?? 'No description available' }}
                                    </div>
                                </td>
                                <td>
                                    @if($category->products_count > 0)
                                        <span class="badge bg-success">{{ $category->products_count }} products</span>
                                    @else
                                        <span class="badge bg-secondary">No products</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $category->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $category->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('categories.show', $category) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('categories.edit', $category) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($category->products_count == 0)
                                            <form action="{{ route('categories.destroy', $category) }}" 
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
                                        @else
                                            <button class="btn btn-sm btn-outline-danger disabled" 
                                                    title="Cannot delete - has products"
                                                    disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-collection display-1 text-muted"></i>
                <h4 class="mt-3">No Categories Found</h4>
                <p class="text-muted">
                    @if($search)
                        No categories match your search criteria.
                        <br><a href="{{ route('categories.index') }}" class="text-decoration-none">View all categories</a>
                    @else
                        Start by adding your first category to organize your bakery items.
                    @endif
                </p>
                @if(!$search)
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add First Category
                    </a>
                @endif
            </div>
        @endif
    </div>
    
    <!-- Enhanced Pagination -->
    @if($categories->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Showing <strong>{{ $categories->firstItem() }}</strong> to <strong>{{ $categories->lastItem() }}</strong>
                    of <strong>{{ $categories->total() }}</strong> categories
                    <span class="ms-2">
                        <small class="badge bg-primary">Page {{ $categories->currentPage() }} of {{ $categories->lastPage() }}</small>
                    </span>
                </div>
                <div>
                    {{ $categories->links('custom-pagination') }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Confirm delete
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
