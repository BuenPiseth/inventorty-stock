@extends('layouts.modern')

@section('title', 'Create Product')
@section('page-title', 'Create Product')
@section('page-subtitle', 'Add a new product to inventory')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    
                    <div class="row">
                        <!-- Product Image -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label">Bakery Item Image</label>
                            <div class="d-flex align-items-start gap-3">
                                <div class="image-preview">
                                    <img id="image-preview" 
                                         src="{{ asset('images/no-image.svg') }}" 
                                         alt="Product Image" 
                                         class="product-image-large border rounded">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*"
                                           onchange="previewImage(this)">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Upload JPG, PNG, or GIF. Max size: 2MB
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Name -->
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Chocolate Cake, Croissant, Bread"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div class="col-md-4 mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" 
                                   class="form-control @error('sku') is-invalid @enderror" 
                                   id="sku" 
                                   name="sku" 
                                   value="{{ old('sku') }}" 
                                   placeholder="Product SKU">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Product description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select select2 @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="">Select status</option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                                <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>
                                    Discontinued
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price') }}" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Initial Quantity -->
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">
                                Initial Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="{{ old('quantity', 0) }}" 
                                   min="0" 
                                   step="1"
                                   required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div class="col-md-4 mb-3">
                            <label for="unit" class="form-label">
                                Unit <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('unit') is-invalid @enderror" 
                                   id="unit" 
                                   name="unit" 
                                   value="{{ old('unit') }}" 
                                   placeholder="e.g., pieces, kg, dozen"
                                   required
                                   list="unit-suggestions">

                            <datalist id="unit-suggestions">
                                <option value="pieces">
                                <option value="dozen">
                                <option value="kg">
                                <option value="grams">
                                <option value="loaves">
                                <option value="slices">
                                <option value="boxes">
                                <option value="packs">
                                <option value="units">
                                <option value="portions">
                            </datalist>
                            
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Minimum Stock -->
                        <div class="col-md-12 mb-3">
                            <label for="min_stock" class="form-label">
                                Minimum Stock Level <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('min_stock') is-invalid @enderror" 
                                   id="min_stock" 
                                   name="min_stock" 
                                   value="{{ old('min_stock', 10) }}" 
                                   min="0" 
                                   step="1"
                                   required>
                            @error('min_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                You'll receive low stock alerts when quantity falls below this level
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Products
                        </a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Create Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Help Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Use clear, descriptive product names
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Add high-quality product images
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Set appropriate minimum stock levels
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Include detailed descriptions
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Use unique SKUs for tracking
                    </li>
                </ul>
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
@endsection

@push('scripts')
<script>
    // Image preview functionality
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Form validation and submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creating...';
            
            setTimeout(function() {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-check-lg me-1"></i>Create Product';
            }, 3000);
        });

        // Auto-focus on name field
        document.getElementById('name').focus();
    });
</script>
@endpush
