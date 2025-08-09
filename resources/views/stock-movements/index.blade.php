@extends('layouts.modern')

@section('title', 'Stock Movement History')
@section('page-title', 'Stock Movement History')
@section('page-subtitle', 'Track all inventory movements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-clock-history me-2"></i>
            Movement History
        </h1>
        <p class="text-muted mb-0">Track all stock in and out movements</p>
        <small class="text-info">
            <i class="bi bi-info-circle me-1"></i>
            Tip: If you get a "Page Expired" error when deleting, click the Refresh button and try again.
        </small>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.location.reload()" class="btn btn-outline-secondary" title="Refresh page to prevent token expiration">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Refresh
        </button>
        <a href="{{ route('stock.in') }}" class="btn btn-success">
            <i class="bi bi-arrow-down-circle me-1"></i>
            Stock In
        </a>
        <a href="{{ route('stock.out') }}" class="btn btn-warning">
            <i class="bi bi-arrow-up-circle me-1"></i>
            Stock Out
        </a>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}

        @if(session('show_force_delete'))
            <hr class="my-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Options to resolve this issue:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Add stock to "{{ session('product_name') }}" first, then delete the movement</li>
                        <li>Use "Force Delete" to allow negative stock (not recommended)</li>
                    </ul>
                </div>
                <div class="ms-3 d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#quickAdjustmentModal"
                            data-product-id="{{ session('product_id') }}"
                            data-product-name="{{ session('product_name') }}"
                            data-shortfall="{{ session('shortfall') }}">
                        <i class="bi bi-plus-circle me-1"></i>
                        Add Stock
                    </button>
                    <button type="button" class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#forceDeleteModal"
                            data-movement-id="{{ session('movement_id') }}"
                            data-product-name="{{ session('product_name') }}"
                            data-shortfall="{{ session('shortfall') }}">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Force Delete
                    </button>
                </div>
            </div>
        @endif

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $movements->where('type', 'in')->count() }}</h4>
                        <p class="mb-0">Stock In Today</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-arrow-down-circle"></i>
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
                        <h4 class="mb-0">{{ $movements->where('type', 'out')->count() }}</h4>
                        <p class="mb-0">Stock Out Today</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $movements->count() }}</h4>
                        <p class="mb-0">Total Movements</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $products->count() }}</h4>
                        <p class="mb-0">Active Products</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('stock-movements.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" class="form-select">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="type" class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>
                        Filter
                    </button>
                    @if(request()->hasAny(['product_id', 'type', 'date_from', 'date_to']))
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Movements Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list me-2"></i>
            Stock Movements
        </h5>
    </div>
    <div class="card-body p-0">
        @if($movements->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Previous Stock</th>
                            <th>New Stock</th>
                            <th>Reason</th>
                            <th>Reference</th>
                            <th>User</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $movement)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $movement->movement_date->format('M j, Y') }}</div>
                                    <small class="text-muted">{{ $movement->movement_date->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $movement->product->name }}</div>
                                    <small class="text-muted">{{ $movement->product->category->name ?? 'No Category' }}</small>
                                </td>
                                <td>
                                    @if($movement->type === 'in')
                                        <span class="badge bg-success">
                                            <i class="bi bi-arrow-down-circle me-1"></i>
                                            Stock In
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bi bi-arrow-up-circle me-1"></i>
                                            Stock Out
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ number_format($movement->quantity) }}</span>
                                    <small class="text-muted">{{ $movement->product->unit }}</small>
                                </td>
                                <td>{{ number_format($movement->previous_stock) }}</td>
                                <td>
                                    <span class="fw-semibold">{{ number_format($movement->new_stock) }}</span>
                                    @if($movement->new_stock <= 5)
                                        <i class="bi bi-exclamation-triangle text-warning ms-1" title="Low Stock"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($movement->reason)
                                        <span class="badge bg-light text-dark">{{ $movement->reason }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($movement->reference)
                                        <code class="text-primary">{{ $movement->reference }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($movement->user)
                                        <div class="fw-semibold">{{ $movement->user->name }}</div>
                                        <small class="text-muted">{{ $movement->created_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('stock-movements.show', $movement) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('stock-movements.edit', $movement) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit Movement">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('stock-movements.destroy', array_merge(['stock_movement' => $movement->id], request()->only(['product_id','type','date_from','date_to']))) }}"
                                              method="POST"
                                              class="d-inline delete-form"
                                              data-product-name="{{ $movement->product->name }}"
                                              data-type="{{ $movement->type }}"
                                              data-quantity="{{ $movement->quantity }}"
                                              data-reason="{{ $movement->reason }}">
                                            @csrf
                                            @method('DELETE')
                                            @foreach(['product_id','type','date_from','date_to'] as $q)
                                                @if(request()->filled($q))
                                                    <input type="hidden" name="{{ $q }}" value="{{ request($q) }}">
                                                @endif
                                            @endforeach
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete Movement">
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

            <!-- Enhanced Pagination -->
            @if($movements->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Showing <strong>{{ $movements->firstItem() }}</strong> to <strong>{{ $movements->lastItem() }}</strong>
                            of <strong>{{ $movements->total() }}</strong> movements
                            <span class="ms-2">
                                <small class="badge bg-primary">Page {{ $movements->currentPage() }} of {{ $movements->lastPage() }}</small>
                            </span>
                        </div>
                        <div>
                            {{ $movements->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-clock-history display-1 text-muted"></i>
                <h4 class="mt-3">No Stock Movements Found</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['product_id', 'type', 'date_from', 'date_to']))
                        No movements match your filter criteria.
                        <br><a href="{{ route('stock-movements.index') }}" class="text-decoration-none">View all movements</a>
                    @else
                        Start by adding or removing stock to see movement history.
                    @endif
                </p>
                @if(!request()->hasAny(['product_id', 'type', 'date_from', 'date_to']))
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('stock.in') }}" class="btn btn-success">
                            <i class="bi bi-arrow-down-circle me-1"></i>
                            Add Stock
                        </a>
                        <a href="{{ route('stock.out') }}" class="btn btn-warning">
                            <i class="bi bi-arrow-up-circle me-1"></i>
                            Remove Stock
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete form submissions
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const productName = form.dataset.productName;
                const type = form.dataset.type;
                const quantity = parseInt(form.dataset.quantity);
                const reason = form.dataset.reason;

                const typeText = type === 'in' ? 'Stock In' : 'Stock Out';
                const message = `Are you sure you want to delete this stock movement?\n\n` +
                               `Product: ${productName}\n` +
                               `Type: ${typeText}\n` +
                               `Quantity: ${quantity.toLocaleString()}\n` +
                               `Reason: ${reason}\n\n` +
                               `This action will reverse the stock changes and cannot be undone.`;

                if (confirm(message)) {
                    // Show loading state
                    const button = form.querySelector('button[type="submit"]');
                    const originalContent = button.innerHTML;
                    button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                    button.disabled = true;

                    // Submit the form
                    form.submit();
                }
            });
        });
    });

    // Refresh CSRF token periodically to prevent expiration
    setInterval(function() {
        fetch('/csrf-token', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                // Update meta tag
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);

                // Update all CSRF tokens in forms
                const csrfInputs = document.querySelectorAll('input[name="_token"]');
                csrfInputs.forEach(function(input) {
                    input.value = data.token;
                });

                console.log('CSRF token refreshed successfully');
            }
        })
        .catch(error => {
            console.log('CSRF token refresh failed:', error);
            // Show warning to user
            showTokenWarning();
        });
    }, 30 * 60 * 1000); // Refresh every 30 minutes

    // Show warning about token expiration
    function showTokenWarning() {
        const existingWarning = document.getElementById('token-warning');
        if (!existingWarning) {
            const warning = document.createElement('div');
            warning.id = 'token-warning';
            warning.className = 'alert alert-warning alert-dismissible fade show position-fixed';
            warning.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            warning.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Session Warning:</strong> Your session may have expired.
                <button onclick="window.location.reload()" class="btn btn-sm btn-warning ms-2">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh Page
                </button>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(warning);
        }
    }

    // Handle force delete modal
    const forceDeleteModal = document.getElementById('forceDeleteModal');
    if (forceDeleteModal) {
        forceDeleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const movementId = button.getAttribute('data-movement-id');
            const productName = button.getAttribute('data-product-name');
            const shortfall = button.getAttribute('data-shortfall');

            // Update modal content
            document.getElementById('forceDeleteProductName').textContent = productName;
            document.getElementById('forceDeleteShortfall').textContent = shortfall;

            // Update form action
            const form = document.getElementById('forceDeleteForm');
            form.action = `/stock-movements/${movementId}/force`;
        });
    }

    // Handle quick adjustment modal
    const quickAdjustmentModal = document.getElementById('quickAdjustmentModal');
    if (quickAdjustmentModal) {
        quickAdjustmentModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const shortfall = parseInt(button.getAttribute('data-shortfall'));

            // Update modal content
            document.getElementById('adjustmentProductName').textContent = productName;
            document.getElementById('adjustmentRecommended').textContent = shortfall + ' units (minimum)';
            document.getElementById('adjustmentProductId').value = productId;
            document.getElementById('adjustment_quantity').value = shortfall;
            document.getElementById('adjustment_reason').value = 'Stock correction to resolve negative stock';
        });
    }

    // Show reminder after 90 minutes
    setTimeout(function() {
        const reminder = document.createElement('div');
        reminder.className = 'alert alert-info alert-dismissible fade show position-fixed';
        reminder.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        reminder.innerHTML = `
            <i class="bi bi-info-circle me-2"></i>
            <strong>Tip:</strong> If you experience issues with delete operations, try refreshing the page.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(reminder);
    }, 90 * 60 * 1000); // Show after 90 minutes
</script>
@endpush

<!-- Force Delete Modal -->
<div class="modal fade" id="forceDeleteModal" tabindex="-1" aria-labelledby="forceDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="forceDeleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Force Delete Stock Movement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action will delete the stock movement and may result in negative stock levels.
                </div>

                <p><strong>Product:</strong> <span id="forceDeleteProductName"></span></p>
                <p><strong>This will cause negative stock of:</strong> <span id="forceDeleteShortfall" class="text-danger fw-bold"></span> units</p>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Recommended alternatives:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Add stock to the product first, then delete the movement</li>
                        <li>Edit the movement instead of deleting it</li>
                        <li>Create a stock adjustment to balance the inventory</li>
                    </ul>
                </div>

                <p class="text-muted">Only proceed if you understand the consequences and need to force this deletion.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancel
                </button>
                <form id="forceDeleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Force Delete Anyway
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stock Adjustment Modal -->
<div class="modal fade" id="quickAdjustmentModal" tabindex="-1" aria-labelledby="quickAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="quickAdjustmentModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>
                    Quick Stock Adjustment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('stock-movements.quick-adjustment') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Quick Fix:</strong> Add stock to resolve the negative stock issue, then you can safely delete the movement.
                    </div>

                    <input type="hidden" id="adjustmentProductId" name="product_id" value="">

                    <div class="mb-3">
                        <label class="form-label"><strong>Product:</strong></label>
                        <p id="adjustmentProductName" class="form-control-plaintext fw-bold"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Recommended Quantity to Add:</strong></label>
                        <p id="adjustmentRecommended" class="form-control-plaintext text-success fw-bold"></p>
                        <small class="text-muted">This will bring stock to zero or positive</small>
                    </div>

                    <div class="mb-3">
                        <label for="adjustment_quantity" class="form-label">
                            <strong>Quantity to Add:</strong>
                        </label>
                        <input type="number"
                               class="form-control"
                               id="adjustment_quantity"
                               name="adjustment_quantity"
                               min="1"
                               required>
                        <div class="form-text">Enter the quantity you want to add to the product stock</div>
                    </div>

                    <div class="mb-3">
                        <label for="adjustment_reason" class="form-label">
                            <strong>Reason for Adjustment:</strong>
                        </label>
                        <input type="text"
                               class="form-control"
                               id="adjustment_reason"
                               name="reason"
                               placeholder="e.g., Stock correction, Found inventory, etc."
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>
                        Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
@endpush
