@extends('layouts.modern')

@section('title', 'Edit Stock Movement')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-pencil-square text-warning me-2"></i>
                Edit Stock Movement
            </h1>
            <p class="text-muted mb-0">Modify stock movement details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to History
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <h6><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Current Movement Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Current Movement Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Product:</strong> {{ $stockMovement->product->name }}<br>
                            <strong>Current Type:</strong> 
                            @if($stockMovement->type === 'in')
                                <span class="badge bg-success">Stock In</span>
                            @else
                                <span class="badge bg-warning">Stock Out</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Current Quantity:</strong> {{ number_format($stockMovement->quantity) }}<br>
                            <strong>Date:</strong> {{ $stockMovement->movement_date->format('M j, Y g:i A') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Movement
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock-movements.update', $stockMovement) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Product Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="product_id" class="form-label">
                                    Product <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('product_id') is-invalid @enderror" 
                                        id="product_id" 
                                        name="product_id" 
                                        required>
                                    <option value="">Select a product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                {{ old('product_id', $stockMovement->product_id) == $product->id ? 'selected' : '' }}
                                                data-current-stock="{{ $product->quantity }}"
                                                data-unit="{{ $product->unit }}">
                                            {{ $product->name }} (Current: {{ number_format($product->quantity) }} {{ $product->unit }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Movement Type -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">
                                    Movement Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">Select type</option>
                                    <option value="in" {{ old('type', $stockMovement->type) == 'in' ? 'selected' : '' }}>
                                        Stock In (Add to inventory)
                                    </option>
                                    <option value="out" {{ old('type', $stockMovement->type) == 'out' ? 'selected' : '' }}>
                                        Stock Out (Remove from inventory)
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">
                                    Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="{{ old('quantity', $stockMovement->quantity) }}" 
                                       min="1" 
                                       step="1"
                                       required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="stock-info"></span>
                                </div>
                            </div>

                            <!-- Movement Date -->
                            <div class="col-md-6 mb-3">
                                <label for="movement_date" class="form-label">
                                    Movement Date <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" 
                                       class="form-control @error('movement_date') is-invalid @enderror" 
                                       id="movement_date" 
                                       name="movement_date" 
                                       value="{{ old('movement_date', $stockMovement->movement_date->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('movement_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Reason -->
                            <div class="col-md-12 mb-3">
                                <label for="reason" class="form-label">
                                    Reason <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('reason') is-invalid @enderror"
                                        id="reason"
                                        name="reason"
                                        required>
                                    <option value="">Select reason</option>
                                    <option value="St360" {{ old('reason', $stockMovement->reason) == 'St360' ? 'selected' : '' }}>St360</option>
                                    <option value="Bakery" {{ old('reason', $stockMovement->reason) == 'Bakery' ? 'selected' : '' }}>Bakery</option>
                                    <option value="Cake" {{ old('reason', $stockMovement->reason) == 'Cake' ? 'selected' : '' }}>Cake</option>
                                    <option value="Koh Pich" {{ old('reason', $stockMovement->reason) == 'Koh Pich' ? 'selected' : '' }}>Koh Pich</option>
                                    <option value="Cashier" {{ old('reason', $stockMovement->reason) == 'Cashier' ? 'selected' : '' }}>Cashier</option>
                                </select>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3"
                                          placeholder="Additional notes about this movement...">{{ old('notes', $stockMovement->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-lg me-1"></i>
                                Update Movement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.getElementById('product_id');
        const typeSelect = document.getElementById('type');
        const quantityInput = document.getElementById('quantity');
        const stockInfo = document.getElementById('stock-info');

        function updateStockInfo() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const type = typeSelect.value;
            const quantity = parseInt(quantityInput.value) || 0;

            if (selectedOption && selectedOption.value) {
                const currentStock = parseInt(selectedOption.dataset.currentStock);
                const unit = selectedOption.dataset.unit;

                if (type === 'out') {
                    const remaining = currentStock - quantity;
                    stockInfo.innerHTML = `<i class="bi bi-info-circle me-1"></i>Current stock: ${currentStock.toLocaleString()} ${unit}. After this movement: ${remaining.toLocaleString()} ${unit}`;
                    
                    if (remaining < 0) {
                        stockInfo.className = 'form-text text-danger';
                        stockInfo.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>Insufficient stock! Available: ${currentStock.toLocaleString()} ${unit}`;
                    } else {
                        stockInfo.className = 'form-text text-muted';
                    }
                } else if (type === 'in') {
                    const newStock = currentStock + quantity;
                    stockInfo.innerHTML = `<i class="bi bi-info-circle me-1"></i>Current stock: ${currentStock.toLocaleString()} ${unit}. After this movement: ${newStock.toLocaleString()} ${unit}`;
                    stockInfo.className = 'form-text text-success';
                } else {
                    stockInfo.innerHTML = `<i class="bi bi-info-circle me-1"></i>Current stock: ${currentStock.toLocaleString()} ${unit}`;
                    stockInfo.className = 'form-text text-muted';
                }
            } else {
                stockInfo.innerHTML = '';
            }
        }

        productSelect.addEventListener('change', updateStockInfo);
        typeSelect.addEventListener('change', updateStockInfo);
        quantityInput.addEventListener('input', updateStockInfo);

        // Initial update
        updateStockInfo();
    });
</script>
@endpush
@endsection
