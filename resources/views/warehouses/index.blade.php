@extends('layouts.modern')

@section('title', 'Warehouses')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-building text-primary me-2"></i>
                Warehouse Management
            </h1>
            <p class="text-muted mb-0">Manage multiple warehouse locations and inventory distribution</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Add Warehouse
            </a>
        </div>
    </div>

    <!-- Warehouse Statistics Cards -->
    <div class="row mb-4">
        @foreach($warehouses as $warehouse)
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="bi bi-building me-2"></i>
                                    {{ $warehouse->name }}
                                </h5>
                                <small class="opacity-75">Code: {{ $warehouse->code }}</small>
                            </div>
                            <div class="text-end">
                                @if($warehouse->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Warehouse Info -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-geo-alt text-muted me-2"></i>
                                    <small class="text-muted">{{ $warehouse->address ?? 'No address' }}</small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person text-muted me-2"></i>
                                    <small class="text-muted">{{ $warehouse->manager_name ?? 'No manager' }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-telephone text-muted me-2"></i>
                                    <small class="text-muted">{{ $warehouse->phone ?? 'No phone' }}</small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-envelope text-muted me-2"></i>
                                    <small class="text-muted">{{ $warehouse->email ?? 'No email' }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="border-end">
                                    <h4 class="text-primary mb-0">{{ $warehouse->products_count }}</h4>
                                    <small class="text-muted">Products</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border-end">
                                    <h4 class="text-success mb-0">${{ number_format($warehouse->total_stock_value, 2) }}</h4>
                                    <small class="text-muted">Stock Value</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border-end">
                                    <h4 class="text-warning mb-0">{{ $warehouse->stock_movements_count }}</h4>
                                    <small class="text-muted">Movements</small>
                                </div>
                            </div>
                            <div class="col-3">
                                @if($warehouse->low_stock_count > 0)
                                    <h4 class="text-danger mb-0">{{ $warehouse->low_stock_count }}</h4>
                                    <small class="text-muted">Low Stock</small>
                                @else
                                    <h4 class="text-success mb-0">0</h4>
                                    <small class="text-muted">Low Stock</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('warehouses.show', $warehouse) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>
                                    View Details
                                </a>
                                <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil me-1"></i>
                                    Edit
                                </a>
                            </div>
                            @if($warehouse->products_count == 0)
                                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to deactivate this warehouse?')">
                                        <i class="bi bi-trash me-1"></i>
                                        Deactivate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Warehouse Transfer Section -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-arrow-left-right me-2"></i>
                Quick Warehouse Transfer
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('warehouses.transfer') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label for="product_id" class="form-label">Product</label>
                    <select name="product_id" id="product_id" class="form-select" required>
                        <option value="">Select Product</option>
                        @foreach(\App\Models\Product::with('warehouse')->get() as $product)
                            <option value="{{ $product->id }}" data-warehouse="{{ $product->warehouse_id }}" data-stock="{{ $product->quantity }}">
                                {{ $product->name }} ({{ $product->warehouse->name ?? 'No Warehouse' }}) - Stock: {{ $product->quantity }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from_warehouse_id" class="form-label">From</label>
                    <select name="from_warehouse_id" id="from_warehouse_id" class="form-select" required>
                        <option value="">Select Source</option>
                        @foreach($warehouses->where('is_active', true) as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="to_warehouse_id" class="form-label">To</label>
                    <select name="to_warehouse_id" id="to_warehouse_id" class="form-select" required>
                        <option value="">Select Destination</option>
                        @foreach($warehouses->where('is_active', true) as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                </div>
                <div class="col-md-2">
                    <label for="reason" class="form-label">Reason</label>
                    <select name="reason" id="reason" class="form-select" required>
                        <option value="">Select Reason</option>
                        <option value="St360">St360</option>
                        <option value="Bakery">Bakery</option>
                        <option value="Cake">Cake</option>
                        <option value="Koh Pich">Koh Pich</option>
                        <option value="Cashier">Cashier</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-arrow-right me-1"></i>
                        Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-populate from warehouse when product is selected
    document.getElementById('product_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const warehouseId = selectedOption.getAttribute('data-warehouse');
        const stock = selectedOption.getAttribute('data-stock');
        
        if (warehouseId) {
            document.getElementById('from_warehouse_id').value = warehouseId;
            document.getElementById('quantity').max = stock;
            document.getElementById('quantity').placeholder = `Max: ${stock}`;
        }
    });
</script>
@endpush
@endsection
