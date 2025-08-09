@extends('layouts.modern')

@section('title', 'Stock Movement Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-eye text-info me-2"></i>
                Stock Movement Details
            </h1>
            <p class="text-muted mb-0">View stock movement information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>
                Edit Movement
            </a>
            <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to History
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Movement Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Movement Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold">Movement Type:</td>
                                    <td>
                                        @if($stockMovement->type === 'in')
                                            <span class="badge bg-success fs-6">
                                                <i class="bi bi-arrow-down-circle me-1"></i>
                                                Stock In
                                            </span>
                                        @else
                                            <span class="badge bg-warning fs-6">
                                                <i class="bi bi-arrow-up-circle me-1"></i>
                                                Stock Out
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Quantity:</td>
                                    <td class="fs-5 fw-bold text-primary">{{ number_format($stockMovement->quantity) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Reason:</td>
                                    <td>{{ $stockMovement->reason }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Movement Date:</td>
                                    <td>{{ $stockMovement->movement_date->format('l, F j, Y \a\t g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold">Previous Stock:</td>
                                    <td class="text-muted">{{ number_format($stockMovement->previous_stock) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">New Stock:</td>
                                    <td class="text-success fw-bold">{{ number_format($stockMovement->new_stock) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Stock Change:</td>
                                    <td>
                                        @php
                                            $change = $stockMovement->new_stock - $stockMovement->previous_stock;
                                        @endphp
                                        @if($change > 0)
                                            <span class="text-success">+{{ number_format($change) }}</span>
                                        @else
                                            <span class="text-danger">{{ number_format($change) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Created:</td>
                                    <td>{{ $stockMovement->created_at->format('M j, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($stockMovement->notes)
                        <div class="mt-3">
                            <h6 class="fw-semibold">Notes:</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $stockMovement->notes }}
                            </div>
                        </div>
                    @endif

                    @if($stockMovement->reference)
                        <div class="mt-3">
                            <h6 class="fw-semibold">Reference:</h6>
                            <code class="text-primary">{{ $stockMovement->reference }}</code>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Product Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-box me-2"></i>
                        Product Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ $stockMovement->product->image_url }}" 
                             alt="{{ $stockMovement->product->name }}" 
                             class="rounded"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-semibold">Product:</td>
                            <td>{{ $stockMovement->product->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Category:</td>
                            <td>
                                @if($stockMovement->product->category)
                                    <span class="badge bg-info">{{ $stockMovement->product->category->name }}</span>
                                @else
                                    <span class="text-muted">No Category</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">SKU:</td>
                            <td>{{ $stockMovement->product->sku ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Unit:</td>
                            <td>{{ $stockMovement->product->unit }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Current Stock:</td>
                            <td class="fw-bold text-primary">{{ number_format($stockMovement->product->quantity) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Status:</td>
                            <td>
                                <span class="badge bg-{{ $stockMovement->product->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($stockMovement->product->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <div class="d-grid mt-3">
                        <a href="{{ route('products.show', $stockMovement->product) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>
                            View Product Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>
                        User Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($stockMovement->user)
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-semibold">Performed by:</td>
                                <td>{{ $stockMovement->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Email:</td>
                                <td>{{ $stockMovement->user->email }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">When:</td>
                                <td>{{ $stockMovement->created_at->diffForHumans() }}</td>
                            </tr>
                        </table>
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-robot fs-1 d-block mb-2"></i>
                            <p class="mb-0">System Generated</p>
                            <small>This movement was created automatically by the system.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Actions</h6>
                            <small class="text-muted">Manage this stock movement</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i>
                                Edit Movement
                            </a>
                            <form action="{{ route('stock-movements.destroy', $stockMovement) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this stock movement? This will reverse the stock changes and cannot be undone.')">
                                    <i class="bi bi-trash me-1"></i>
                                    Delete Movement
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
