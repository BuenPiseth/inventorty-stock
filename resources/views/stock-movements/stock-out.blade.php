@extends('layouts.modern')

@section('title', 'Stock Out')
@section('page-title', 'Stock Out')
@section('page-subtitle', 'Remove stock from your inventory')

@push('styles')
<style>
    .product-option:hover {
        background-color: var(--bs-gray-100);
    }

    .product-option .badge {
        min-width: 40px;
    }

    #product_dropdown {
        border: 1px solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        z-index: 1050;
    }

    .quick-qty {
        cursor: pointer;
    }

    .input-group .dropdown-toggle {
        border-left: 0;
    }

    .keyboard-shortcut {
        font-size: 0.75rem;
        color: var(--bs-gray-600);
        margin-top: 0.25rem;
    }

    .floating-help {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .help-panel {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        z-index: 999;
        display: none;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-arrow-up-circle text-warning me-2"></i>
                    Remove Stock
                </h5>
            </div>
            <div class="card-body">
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
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('stock-movements.store') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="type" value="out">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_id" class="form-label">
                                Product <span class="text-danger">*</span>
                                <small class="keyboard-shortcut">(Alt + P)</small>
                            </label>
                            <div class="position-relative">
                                <!-- Barcode Scanner Input (Hidden) -->
                                <input type="text"
                                       class="form-control mb-2"
                                       id="barcode_input"
                                       placeholder="Scan barcode here or press F2 to focus..."
                                       autocomplete="off"
                                       style="border: 2px dashed #28a745; background-color: #f8fff9;">

                                <!-- Product Search Input -->
                                <input type="text"
                                       class="form-control @error('product_id') is-invalid @enderror"
                                       id="product_search"
                                       placeholder="Search products by name, code, or barcode..."
                                       autocomplete="off">
                                <div id="product_dropdown" class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto; display: none;">
                                    <!-- Products will be populated here -->
                                </div>
                                <select class="form-select d-none @error('product_id') is-invalid @enderror"
                                        id="product_id"
                                        name="product_id"
                                        required>
                                    <option value="">Select a product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-current-stock="{{ $product->quantity }}"
                                                data-unit="{{ $product->unit }}"
                                                data-name="{{ $product->name }}"
                                                data-code="{{ $product->code ?? '' }}"
                                                data-barcode="{{ $product->barcode ?? '' }}"
                                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->quantity }} {{ $product->unit }} available)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">
                                Quantity to Remove <span class="text-danger">*</span>
                                <small class="keyboard-shortcut">(Alt + Q)</small>
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control @error('quantity') is-invalid @enderror"
                                       id="quantity"
                                       name="quantity"
                                       value="{{ old('quantity') }}"
                                       min="1"
                                       required>
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Quick
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" id="quick_quantity_menu">
                                    <li><a class="dropdown-item quick-qty" href="#" data-qty="1">1</a></li>
                                    <li><a class="dropdown-item quick-qty" href="#" data-qty="5">5</a></li>
                                    <li><a class="dropdown-item quick-qty" href="#" data-qty="10">10</a></li>
                                    <li><a class="dropdown-item quick-qty" href="#" data-qty="25">25</a></li>
                                    <li><a class="dropdown-item quick-qty" href="#" data-qty="50">50</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item quick-qty" href="#" data-qty="all">All Available</a></li>
                                </ul>
                            </div>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reason" class="form-label">
                                Reason <span class="text-danger">*</span>
                                <small class="keyboard-shortcut">(Alt + R)</small>
                            </label>
                            <div class="position-relative">
                                <select class="form-select @error('reason') is-invalid @enderror"
                                        id="reason"
                                        name="reason"
                                        required>
                                    <option value="">Select reason</option>
                                    <option value="St360" {{ old('reason') == 'St360' ? 'selected' : '' }}>St360</option>
                                    <option value="Bakery" {{ old('reason') == 'Bakery' ? 'selected' : '' }}>Bakery</option>
                                    <option value="Cake" {{ old('reason') == 'Cake' ? 'selected' : '' }}>Cake</option>
                                    <option value="Koh Pich" {{ old('reason') == 'Koh Pich' ? 'selected' : '' }}>Koh Pich</option>
                                    <option value="Cashier" {{ old('reason') == 'Cashier' ? 'selected' : '' }}>Cashier</option>
                                </select>

                                <!-- Quick Reason Buttons -->
                                <div class="mt-2">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Quick reasons">
                                        <button type="button" class="btn btn-outline-secondary quick-reason" data-reason="St360">St360</button>
                                        <button type="button" class="btn btn-outline-secondary quick-reason" data-reason="Bakery">Bakery</button>
                                        <button type="button" class="btn btn-outline-secondary quick-reason" data-reason="Cake">Cake</button>
                                        <button type="button" class="btn btn-outline-secondary quick-reason" data-reason="Koh Pich">Koh Pich</button>
                                        <button type="button" class="btn btn-outline-secondary quick-reason" data-reason="Cashier">Cashier</button>
                                    </div>
                                    <small class="text-muted d-block mt-1">Click for quick selection</small>
                                </div>
                            </div>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="movement_date" class="form-label">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('movement_date') is-invalid @enderror" 
                                   id="movement_date" 
                                   name="movement_date" 
                                   value="{{ old('movement_date', date('Y-m-d')) }}" 
                                   required>
                            @error('movement_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reference" class="form-label">Reference Number</label>
                        <input type="text" 
                               class="form-control @error('reference') is-invalid @enderror" 
                               id="reference" 
                               name="reference" 
                               value="{{ old('reference') }}" 
                               placeholder="Invoice number, order ID, etc.">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Additional notes about this stock removal...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock Info Display -->
                    <div id="current-stock-info" class="alert alert-info" style="display: none;">
                        <h6><i class="bi bi-info-circle me-2"></i>Stock Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Current Stock:</strong> 
                                <span id="current-stock">0</span> <span id="current-unit">units</span>
                            </div>
                            <div class="col-md-6">
                                <strong>After Removal:</strong> 
                                <span id="new-stock">0</span> <span id="new-unit">units</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="alert alert-light border">
                        <h6 class="mb-2"><i class="bi bi-lightbulb me-2"></i>Quick Tips & Shortcuts</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <strong>Search & Selection:</strong><br>
                                    • Scan barcode or press <kbd>F2</kbd><br>
                                    • Type to search products<br>
                                    • <kbd>Alt + P</kbd> focus product<br>
                                    • <kbd>Alt + Q</kbd> focus quantity<br>
                                    • <kbd>Alt + R</kbd> focus reason
                                </small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <strong>Quick Actions:</strong><br>
                                    • Click Quick buttons for quantities<br>
                                    • Click reason buttons for selection<br>
                                    • Keys <kbd>1-5</kbd> for quick reasons<br>
                                    • "All Available" removes all stock
                                </small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <strong>Efficiency:</strong><br>
                                    • <kbd>Ctrl + Enter</kbd> to submit<br>
                                    • Recent reasons highlighted<br>
                                    • Real-time stock preview<br>
                                    • Auto-validation checks
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to History
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-arrow-up-circle me-1"></i>
                            Remove Stock
                        </button>
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
                    <i class="bi bi-graph-down me-2"></i>
                    Stock Out Summary
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-warning mb-0">{{ \App\Models\StockMovement::where('type', 'out')->whereDate('created_at', today())->count() }}</h4>
                        <small class="text-muted">Today</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-0">{{ \App\Models\StockMovement::where('type', 'out')->whereMonth('created_at', now()->month)->count() }}</h4>
                        <small class="text-muted">This Month</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Stock Outs -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Recent Stock Removals
                </h6>
            </div>
            <div class="card-body">
                @php
                    $recentStockOuts = \App\Models\StockMovement::with('product')
                        ->where('type', 'out')
                        ->orderBy('movement_date', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                
                @if($recentStockOuts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentStockOuts as $movement)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $movement->product->name }}</h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="bi bi-arrow-up-circle text-warning me-1"></i>
                                            {{ $movement->quantity }} {{ $movement->product->unit }}
                                        </p>
                                        <small class="text-muted">{{ $movement->reason }}</small>
                                    </div>
                                    <small class="text-muted">{{ $movement->movement_date->format('M j') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                        No recent stock removals
                    </p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i>
                        View All Products
                    </a>
                    <a href="{{ route('stock.in') }}" class="btn btn-outline-success">
                        <i class="bi bi-arrow-down-circle me-2"></i>
                        Add Stock
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Help Button -->
<div class="floating-help">
    <button type="button" class="btn btn-primary btn-lg rounded-circle" id="help-toggle" title="Quick Help (F1)">
        <i class="bi bi-question-lg"></i>
    </button>
</div>

<!-- Help Panel -->
<div class="help-panel" id="help-panel">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="bi bi-keyboard me-2"></i>
                Quick Reference
                <button type="button" class="btn-close btn-close-white float-end" id="help-close"></button>
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="mb-3">
                <h6 class="text-primary mb-2">Keyboard Shortcuts</h6>
                <div class="row">
                    <div class="col-6">
                        <small>
                            <kbd>F1</kbd> Help<br>
                            <kbd>F2</kbd> Barcode<br>
                            <kbd>Alt+P</kbd> Product<br>
                            <kbd>Alt+Q</kbd> Quantity
                        </small>
                    </div>
                    <div class="col-6">
                        <small>
                            <kbd>Alt+R</kbd> Reason<br>
                            <kbd>1-5</kbd> Quick Reasons<br>
                            <kbd>Ctrl+Enter</kbd> Submit<br>
                            <kbd>Esc</kbd> Clear Form
                        </small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <h6 class="text-primary mb-2">Quick Actions</h6>
                <div class="d-grid gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-form">
                        <i class="bi bi-arrow-clockwise me-1"></i>Clear Form
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="focus-barcode">
                        <i class="bi bi-upc-scan me-1"></i>Focus Barcode
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="last-product">
                        <i class="bi bi-clock-history me-1"></i>Last Product
                    </button>
                </div>
            </div>

            <div>
                <h6 class="text-primary mb-2">Tips</h6>
                <small class="text-muted">
                    • Barcode scanners work automatically<br>
                    • Recent reasons are highlighted<br>
                    • Stock validation prevents errors<br>
                    • All fields support tab navigation
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.getElementById('product_id');
        const productSearch = document.getElementById('product_search');
        const productDropdown = document.getElementById('product_dropdown');
        const barcodeInput = document.getElementById('barcode_input');
        const quantityInput = document.getElementById('quantity');
        const currentStockInfo = document.getElementById('current-stock-info');
        const currentStockSpan = document.getElementById('current-stock');
        const currentUnitSpan = document.getElementById('current-unit');
        const newStockSpan = document.getElementById('new-stock');
        const newUnitSpan = document.getElementById('new-unit');

        // Product search functionality
        let allProducts = [];

        // Populate products array from select options
        Array.from(productSelect.options).forEach(option => {
            if (option.value) {
                allProducts.push({
                    id: option.value,
                    name: option.dataset.name,
                    code: option.dataset.code,
                    barcode: option.dataset.barcode,
                    currentStock: parseInt(option.dataset.currentStock),
                    unit: option.dataset.unit,
                    text: option.textContent
                });
            }
        });

        function filterProducts(searchTerm) {
            const term = searchTerm.toLowerCase();
            return allProducts.filter(product =>
                product.name.toLowerCase().includes(term) ||
                (product.code && product.code.toLowerCase().includes(term)) ||
                (product.barcode && product.barcode.toLowerCase().includes(term))
            );
        }

        function renderProductDropdown(products) {
            if (products.length === 0) {
                productDropdown.innerHTML = '<div class="dropdown-item-text text-muted">No products found</div>';
                return;
            }

            productDropdown.innerHTML = products.map(product => `
                <a class="dropdown-item product-option" href="#" data-product-id="${product.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">${product.name}</div>
                            <small class="text-muted">
                                ${product.code ? `Code: ${product.code} | ` : ''}
                                ${product.barcode ? `Barcode: ${product.barcode} | ` : ''}
                                Stock: ${product.currentStock.toLocaleString()} ${product.unit}
                            </small>
                        </div>
                        <span class="badge bg-${product.currentStock > 10 ? 'success' : product.currentStock > 0 ? 'warning' : 'danger'}">
                            ${product.currentStock}
                        </span>
                    </div>
                </a>
            `).join('');

            // Add click handlers
            productDropdown.querySelectorAll('.product-option').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.dataset.productId;
                    const product = allProducts.find(p => p.id === productId);

                    productSelect.value = productId;
                    productSearch.value = product.name;
                    productDropdown.style.display = 'none';
                    updateStockInfo();
                });
            });
        }

        productSearch.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            if (searchTerm.length >= 1) {
                const filteredProducts = filterProducts(searchTerm);
                renderProductDropdown(filteredProducts);
                productDropdown.style.display = 'block';
            } else {
                productDropdown.style.display = 'none';
            }
        });

        productSearch.addEventListener('focus', function() {
            if (this.value.trim().length >= 1) {
                productDropdown.style.display = 'block';
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!productSearch.contains(e.target) && !productDropdown.contains(e.target)) {
                productDropdown.style.display = 'none';
            }
        });

        // Barcode scanning functionality
        let barcodeBuffer = '';
        let barcodeTimeout;

        function selectProductByBarcode(barcode) {
            const product = allProducts.find(p => p.barcode === barcode);
            if (product) {
                productSelect.value = product.id;
                productSearch.value = product.name;
                barcodeInput.value = '';
                barcodeInput.style.borderColor = '#28a745';
                barcodeInput.style.backgroundColor = '#d4edda';

                // Show success feedback
                setTimeout(() => {
                    barcodeInput.style.borderColor = '#28a745';
                    barcodeInput.style.backgroundColor = '#f8fff9';
                }, 1000);

                updateStockInfo();
                quantityInput.focus();
                return true;
            } else {
                // Show error feedback
                barcodeInput.style.borderColor = '#dc3545';
                barcodeInput.style.backgroundColor = '#f8d7da';
                setTimeout(() => {
                    barcodeInput.style.borderColor = '#28a745';
                    barcodeInput.style.backgroundColor = '#f8fff9';
                    barcodeInput.value = '';
                }, 2000);
                return false;
            }
        }

        barcodeInput.addEventListener('input', function(e) {
            const value = e.target.value.trim();
            if (value.length >= 8) { // Assuming barcodes are at least 8 characters
                if (selectProductByBarcode(value)) {
                    // Product found and selected
                } else {
                    // Try searching in product search
                    productSearch.value = value;
                    const filteredProducts = filterProducts(value);
                    if (filteredProducts.length === 1) {
                        // Auto-select if only one match
                        const product = filteredProducts[0];
                        productSelect.value = product.id;
                        productSearch.value = product.name;
                        barcodeInput.value = '';
                        updateStockInfo();
                        quantityInput.focus();
                    } else if (filteredProducts.length > 1) {
                        renderProductDropdown(filteredProducts);
                        productDropdown.style.display = 'block';
                    }
                }
            }
        });

        // Auto-focus barcode input on page load
        barcodeInput.focus();

        // Help panel functionality
        const helpToggle = document.getElementById('help-toggle');
        const helpPanel = document.getElementById('help-panel');
        const helpClose = document.getElementById('help-close');

        helpToggle.addEventListener('click', function() {
            helpPanel.style.display = helpPanel.style.display === 'none' ? 'block' : 'none';
        });

        helpClose.addEventListener('click', function() {
            helpPanel.style.display = 'none';
        });

        // Quick action buttons
        document.getElementById('clear-form').addEventListener('click', function() {
            if (confirm('Clear all form data?')) {
                productSelect.value = '';
                productSearch.value = '';
                barcodeInput.value = '';
                quantityInput.value = '';
                reasonSelect.value = '';
                document.querySelectorAll('.quick-reason').forEach(btn => btn.classList.remove('active'));
                currentStockInfo.style.display = 'none';
                barcodeInput.focus();
            }
        });

        document.getElementById('focus-barcode').addEventListener('click', function() {
            barcodeInput.focus();
            barcodeInput.select();
            helpPanel.style.display = 'none';
        });

        document.getElementById('last-product').addEventListener('click', function() {
            const lastProductId = localStorage.getItem('lastRemovedProduct');
            if (lastProductId) {
                productSelect.value = lastProductId;
                const product = allProducts.find(p => p.id === lastProductId);
                if (product) {
                    productSearch.value = product.name;
                    updateStockInfo();
                    quantityInput.focus();
                }
            }
            helpPanel.style.display = 'none';
        });

        // Quick quantity buttons
        document.querySelectorAll('.quick-qty').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const qty = this.dataset.qty;

                if (qty === 'all') {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    if (selectedOption.value) {
                        quantityInput.value = selectedOption.dataset.currentStock;
                    }
                } else {
                    quantityInput.value = qty;
                }

                updateStockInfo();
            });
        });

        // Quick reason buttons
        const reasonSelect = document.getElementById('reason');
        document.querySelectorAll('.quick-reason').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const reason = this.dataset.reason;
                reasonSelect.value = reason;

                // Visual feedback
                document.querySelectorAll('.quick-reason').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Store recent reason in localStorage
                let recentReasons = JSON.parse(localStorage.getItem('recentReasons') || '[]');
                recentReasons = recentReasons.filter(r => r !== reason);
                recentReasons.unshift(reason);
                recentReasons = recentReasons.slice(0, 3); // Keep only 3 recent
                localStorage.setItem('recentReasons', JSON.stringify(recentReasons));
            });
        });

        // Load and highlight recent reason
        const recentReasons = JSON.parse(localStorage.getItem('recentReasons') || '[]');
        if (recentReasons.length > 0) {
            const mostRecentReason = recentReasons[0];
            const recentButton = document.querySelector(`[data-reason="${mostRecentReason}"]`);
            if (recentButton) {
                recentButton.classList.add('btn-primary');
                recentButton.classList.remove('btn-outline-secondary');
                recentButton.title = 'Most recently used';
            }
        }

        function updateStockInfo() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (selectedOption.value) {
                const currentStock = parseInt(selectedOption.dataset.currentStock);
                const unit = selectedOption.dataset.unit;
                const quantity = parseInt(quantityInput.value) || 0;
                const newStock = currentStock - quantity;

                currentStockSpan.textContent = currentStock.toLocaleString();
                currentUnitSpan.textContent = unit;
                newStockSpan.textContent = newStock.toLocaleString();
                newUnitSpan.textContent = unit;

                // Show warning if trying to remove more than available
                if (quantity > currentStock) {
                    currentStockInfo.className = 'alert alert-danger';
                    newStockSpan.className = 'text-danger fw-bold';
                } else if (newStock <= 5) {
                    currentStockInfo.className = 'alert alert-warning';
                    newStockSpan.className = 'text-warning fw-bold';
                } else {
                    currentStockInfo.className = 'alert alert-info';
                    newStockSpan.className = '';
                }

                currentStockInfo.style.display = 'block';

                // Update quantity input max value
                quantityInput.max = currentStock;
            } else {
                currentStockInfo.style.display = 'none';
                quantityInput.max = '';
            }
        }

        productSelect.addEventListener('change', updateStockInfo);
        quantityInput.addEventListener('input', updateStockInfo);

        // Form submission
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function(e) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const currentStock = parseInt(selectedOption.dataset.currentStock) || 0;
            const quantity = parseInt(quantityInput.value) || 0;

            if (quantity > currentStock) {
                e.preventDefault();
                alert('Cannot remove more stock than available!');
                return;
            }

            // Store last product for quick access
            localStorage.setItem('lastRemovedProduct', productSelect.value);

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Removing Stock...';
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt + P to focus product search
            if (e.altKey && e.key === 'p') {
                e.preventDefault();
                productSearch.focus();
            }

            // Alt + Q to focus quantity input
            if (e.altKey && e.key === 'q') {
                e.preventDefault();
                quantityInput.focus();
            }

            // Alt + R to focus reason select
            if (e.altKey && e.key === 'r') {
                e.preventDefault();
                reasonSelect.focus();
            }

            // F1 to toggle help panel
            if (e.key === 'F1') {
                e.preventDefault();
                helpToggle.click();
            }

            // F2 to focus barcode input
            if (e.key === 'F2') {
                e.preventDefault();
                barcodeInput.focus();
                barcodeInput.select();
            }

            // Escape to clear form or close help
            if (e.key === 'Escape') {
                if (helpPanel.style.display === 'block') {
                    helpPanel.style.display = 'none';
                } else {
                    document.getElementById('clear-form').click();
                }
            }

            // Number keys 1-5 for quick reason selection (when not in input fields)
            if (!e.target.matches('input, select, textarea') && ['1', '2', '3', '4', '5'].includes(e.key)) {
                e.preventDefault();
                const reasons = ['St360', 'Bakery', 'Cake', 'Koh Pich', 'Cashier'];
                const reasonIndex = parseInt(e.key) - 1;
                if (reasons[reasonIndex]) {
                    reasonSelect.value = reasons[reasonIndex];
                    const button = document.querySelector(`[data-reason="${reasons[reasonIndex]}"]`);
                    if (button) {
                        button.click();
                    }
                }
            }

            // Ctrl + Enter to submit form
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                if (productSelect.value && quantityInput.value && reasonSelect.value) {
                    form.submit();
                }
            }
        });
    });
</script>
@endpush
