@extends('layouts.app')

@section('title', $product->name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('products.index') }}" class="text-decoration-none">Products</a>
    </li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Product Details Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="bi bi-box me-2"></i>
                        {{ $product->name }}
                    </h4>
                    <p class="text-muted mb-0 mt-1">Product Details</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>
                        Edit
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-danger" 
                                data-confirm-delete>
                            <i class="bi bi-trash me-1"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold text-muted" width="120">ID:</td>
                                <td>
                                    <span class="badge bg-light text-dark">#{{ $product->id }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Name:</td>
                                <td class="fw-bold">{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Category:</td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-info">{{ $product->category->name }}</span>
                                    @else
                                        <span class="text-muted">No Category Assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Status:</td>
                                <td>
                                    @switch($product->status)
                                        @case('active')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Active
                                            </span>
                                            @break
                                        @case('inactive')
                                            <span class="badge bg-warning">
                                                <i class="bi bi-pause-circle me-1"></i>
                                                Inactive
                                            </span>
                                            @break
                                        @case('discontinued')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Discontinued
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($product->status) }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold text-muted" width="120">Quantity:</td>
                                <td>
                                    <span class="fs-4 fw-bold {{ $product->quantity <= 10 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($product->quantity) }}
                                    </span>
                                    @if($product->quantity <= 10)
                                        <span class="badge bg-danger ms-2">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Unit:</td>
                                <td class="fw-semibold">{{ $product->unit }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Created:</td>
                                <td>
                                    {{ $product->created_at->format('M d, Y') }}
                                    <small class="text-muted">
                                        ({{ $product->created_at->diffForHumans() }})
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Updated:</td>
                                <td>
                                    {{ $product->updated_at->format('M d, Y') }}
                                    <small class="text-muted">
                                        ({{ $product->updated_at->diffForHumans() }})
                                    </small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning w-100">
                            <i class="bi bi-pencil me-2"></i>
                            Edit Product Details
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-list me-2"></i>
                            View All Products
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('products.create') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-plus-lg me-2"></i>
                            Add New Product
                        </a>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-info w-100" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>
                            Print Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Stock Status Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Stock Status
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="display-4 fw-bold {{ $product->quantity <= 10 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($product->quantity) }}
                </div>
                <p class="text-muted mb-3">{{ $product->unit }}</p>
                
                @if($product->quantity <= 10)
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Low Stock Alert!</strong><br>
                        Consider restocking this product.
                    </div>
                @elseif($product->quantity <= 50)
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Medium Stock</strong><br>
                        Stock levels are moderate.
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Good Stock</strong><br>
                        Stock levels are healthy.
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Info Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Product Information
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Product ID:</span>
                    <span class="fw-bold">#{{ $product->id }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Category:</span>
                    <span class="fw-bold">
                        {{ $product->category ? $product->category->name : 'N/A' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Unit Type:</span>
                    <span class="fw-bold">{{ $product->unit }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Status:</span>
                    <span class="fw-bold">{{ ucfirst($product->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Navigation Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-compass me-2"></i>
                    Navigation
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Products List
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="bi bi-house me-2"></i>
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, .card-header, nav, footer {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
