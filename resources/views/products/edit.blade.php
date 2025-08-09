@extends('layouts.app')

@section('title', 'Edit Product')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('products.index') }}" class="text-decoration-none">Products</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('products.show', $product) }}" class="text-decoration-none">{{ $product->name }}</a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Product
                </h4>
                <p class="text-muted mb-0 mt-1">Update product information</p>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $product->name) }}"
                                   placeholder="Enter product name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div class="col-md-4 mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text"
                                   class="form-control @error('sku') is-invalid @enderror"
                                   id="sku"
                                   name="sku"
                                   value="{{ old('sku', $product->sku) }}"
                                   placeholder="Product SKU">
                            @error('sku')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Product description">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Product Image -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label">Product Image</label>
                            <div class="d-flex align-items-start gap-3">
                                <div class="image-preview">
                                    <img id="image-preview"
                                         src="{{ $product->image_url }}"
                                         alt="Product Image"
                                         class="border rounded"
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file"
                                           class="form-control @error('image') is-invalid @enderror"
                                           id="image"
                                           name="image"
                                           accept="image/*"
                                           onchange="previewImage(this)">
                                    @error('image')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Upload JPG, PNG, or GIF. Max size: 2MB. Leave empty to keep current image.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
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
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                                <option value="discontinued" {{ old('status', $product->status) == 'discontinued' ? 'selected' : '' }}>
                                    Discontinued
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('price') is-invalid @enderror"
                                       id="price"
                                       name="price"
                                       value="{{ old('price', $product->price) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">
                                Current Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="{{ old('quantity', $product->quantity) }}" 
                                   min="0" 
                                   step="1"
                                   placeholder="0"
                                   required>
                            @error('quantity')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">
                                Unit <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('unit') is-invalid @enderror"
                                   id="unit"
                                   name="unit"
                                   value="{{ old('unit', $product->unit) }}"
                                   placeholder="e.g., pieces, kg, liters"
                                   required>
                            @error('unit')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Common units: pieces, kg, grams, liters, meters, boxes
                            </div>
                        </div>

                        <!-- Minimum Stock -->
                        <div class="col-md-6 mb-3">
                            <label for="min_stock" class="form-label">
                                Minimum Stock Level <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   class="form-control @error('min_stock') is-invalid @enderror"
                                   id="min_stock"
                                   name="min_stock"
                                   value="{{ old('min_stock', $product->min_stock) }}"
                                   min="0"
                                   step="1"
                                   required>
                            @error('min_stock')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                You'll receive low stock alerts when quantity falls below this level
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Created:</strong> {{ $product->created_at->format('M d, Y \a\t g:i A') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Last Updated:</strong> {{ $product->updated_at->format('M d, Y \a\t g:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Product
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-info">
                                <i class="bi bi-list me-1"></i>
                                All Products
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Update Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card mt-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Danger Zone
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Once you delete this product, there is no going back. Please be certain.
                </p>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-outline-danger" 
                            data-confirm-delete>
                        <i class="bi bi-trash me-1"></i>
                        Delete Product
                    </button>
                </form>
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

    // Auto-focus on the first input field
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('name').focus();
    });

    // Form validation feedback
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[method="POST"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        }
    });
</script>
@endpush
