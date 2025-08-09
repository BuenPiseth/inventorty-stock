@extends('layouts.modern')

@section('title', 'Stock In')
@section('page-title', 'Stock In')
@section('page-subtitle', 'Add stock to your inventory')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-arrow-down-circle text-success me-2"></i>
                    Add Stock
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('stock-movements.store') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="type" value="in">
                    
                    <div class="row">
                        <!-- Product Selection -->
                        <div class="col-md-12 mb-3">
                            <label for="product_id" class="form-label">
                                Product <span class="text-danger">*</span>
                            </label>
                            <select class="form-select select2 @error('product_id') is-invalid @enderror" 
                                    id="product_id" 
                                    name="product_id" 
                                    required>
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-current-stock="{{ $product->quantity }}"
                                            data-unit="{{ $product->unit }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} (Current: {{ $product->quantity }} {{ $product->unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Stock Display -->
                        <div class="col-md-12 mb-3" id="current-stock-info" style="display: none;">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Current Stock:</strong> <span id="current-stock">0</span> <span id="current-unit">units</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>After Addition:</strong> <span id="new-stock">0</span> <span id="new-unit">units</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity to Add -->
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">
                                Quantity to Add <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="{{ old('quantity') }}" 
                                   min="1" 
                                   step="1"
                                   placeholder="Enter quantity"
                                   required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unit Cost -->
                        <div class="col-md-6 mb-3">
                            <label for="unit_cost" class="form-label">Unit Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control @error('unit_cost') is-invalid @enderror" 
                                       id="unit_cost" 
                                       name="unit_cost" 
                                       value="{{ old('unit_cost') }}" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            @error('unit_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reference -->
                        <div class="col-md-6 mb-3">
                            <label for="reference" class="form-label">Reference</label>
                            <input type="text" 
                                   class="form-control @error('reference') is-invalid @enderror" 
                                   id="reference" 
                                   name="reference" 
                                   value="{{ old('reference') }}" 
                                   placeholder="PO number, invoice, etc.">
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Movement Date -->
                        <div class="col-md-6 mb-3">
                            <label for="movement_date" class="form-label">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('movement_date') is-invalid @enderror" 
                                   id="movement_date" 
                                   name="movement_date" 
                                   value="{{ old('movement_date', now()->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('movement_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="col-md-12 mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <select class="form-select @error('reason') is-invalid @enderror"
                                    id="reason"
                                    name="reason">
                                <option value="">Select reason (optional)</option>
                                <option value="St360" {{ old('reason') == 'St360' ? 'selected' : '' }}>St360</option>
                                <option value="Bakery" {{ old('reason') == 'Bakery' ? 'selected' : '' }}>Bakery</option>
                                <option value="Cake" {{ old('reason') == 'Cake' ? 'selected' : '' }}>Cake</option>
                                <option value="Koh Pich" {{ old('reason') == 'Koh Pich' ? 'selected' : '' }}>Koh Pich</option>
                                <option value="Cashier" {{ old('reason') == 'Cashier' ? 'selected' : '' }}>Cashier</option>
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
                                      placeholder="Additional notes or comments">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Total Value Display -->
                    <div class="alert alert-success" id="total-value" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>Total Value:</strong></span>
                            <span class="fs-5 fw-bold">$<span id="total-amount">0.00</span></span>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Movements
                        </a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>
                                Add Stock
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Quick Stats
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0">{{ $products->count() }}</h4>
                            <small class="text-muted">Products</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-0">{{ $products->where('quantity', '<=', 10)->count() }}</h4>
                        <small class="text-muted">Low Stock</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Stock Ins -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Recent Stock Additions
                </h6>
            </div>
            <div class="card-body">
                @php
                    $recentStockIns = \App\Models\StockMovement::with('product')
                        ->where('type', 'in')
                        ->orderBy('movement_date', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                
                @if($recentStockIns->count() > 0)
                    @foreach($recentStockIns as $movement)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-semibold">{{ $movement->product->name }}</div>
                                <small class="text-muted">{{ $movement->movement_date->diffForHumans() }}</small>
                            </div>
                            <span class="badge bg-success">+{{ $movement->quantity }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No recent stock additions</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.getElementById('product_id');
        const quantityInput = document.getElementById('quantity');
        const unitCostInput = document.getElementById('unit_cost');
        const currentStockInfo = document.getElementById('current-stock-info');
        const currentStockSpan = document.getElementById('current-stock');
        const currentUnitSpan = document.getElementById('current-unit');
        const newStockSpan = document.getElementById('new-stock');
        const newUnitSpan = document.getElementById('new-unit');
        const totalValueDiv = document.getElementById('total-value');
        const totalAmountSpan = document.getElementById('total-amount');

        function updateStockInfo() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (selectedOption.value) {
                const currentStock = parseInt(selectedOption.dataset.currentStock);
                const unit = selectedOption.dataset.unit;
                const quantity = parseInt(quantityInput.value) || 0;
                
                currentStockSpan.textContent = currentStock.toLocaleString();
                currentUnitSpan.textContent = unit;
                newStockSpan.textContent = (currentStock + quantity).toLocaleString();
                newUnitSpan.textContent = unit;
                
                currentStockInfo.style.display = 'block';
                
                updateTotalValue();
            } else {
                currentStockInfo.style.display = 'none';
                totalValueDiv.style.display = 'none';
            }
        }

        function updateTotalValue() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitCost = parseFloat(unitCostInput.value) || 0;
            const total = quantity * unitCost;
            
            if (total > 0) {
                totalAmountSpan.textContent = total.toFixed(2);
                totalValueDiv.style.display = 'block';
            } else {
                totalValueDiv.style.display = 'none';
            }
        }

        productSelect.addEventListener('change', updateStockInfo);
        quantityInput.addEventListener('input', updateStockInfo);
        unitCostInput.addEventListener('input', updateTotalValue);

        // Form submission
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding Stock...';
        });
    });
</script>
@endpush
