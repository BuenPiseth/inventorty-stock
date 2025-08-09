@extends('layouts.modern')

@section('title', 'Create Category')
@section('page-title', 'Create Category')
@section('page-subtitle', 'Add a new category for your bakery items')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Category Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST" novalidate>
                    @csrf
                    
                    <div class="row">
                        <!-- Category Name -->
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="e.g., Cakes, Pastries, Bread"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Choose a descriptive name for your bakery category
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Brief description of this category...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Optional description to help identify this category
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Categories
                        </a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Create Category
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Suggested Categories -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Suggested Categories
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-warning category-suggestion" data-name="coffee">
                        <i class="bi bi-cup-hot me-1"></i>
                        coffee
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary category-suggestion" data-name="cake">
                        <i class="bi bi-cake2 me-1"></i>
                        cake
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success category-suggestion" data-name="bakery">
                        <i class="bi bi-shop me-1"></i>
                        bakery
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info category-suggestion" data-name="Cashier">
                        <i class="bi bi-cash-register me-1"></i>
                        Cashier
                    </button>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Click on any suggestion to use it as your category name
                    </small>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="card mt-4">
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
                        Use clear, descriptive names
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Keep categories broad enough to group similar items
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Consider your bakery's main product types
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        You can always edit categories later
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
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i>
                        View All Categories
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Product
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const suggestionButtons = document.querySelectorAll('.category-suggestion');
        
        // Handle category suggestions
        suggestionButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const categoryName = this.dataset.name;
                nameInput.value = categoryName;
                nameInput.focus();
                
                // Add visual feedback
                this.classList.add('btn-primary');
                this.classList.remove('btn-outline-primary');
                
                // Reset other buttons
                suggestionButtons.forEach(function(otherButton) {
                    if (otherButton !== button) {
                        otherButton.classList.remove('btn-primary');
                        otherButton.classList.add('btn-outline-primary');
                    }
                });
            });
        });

        // Form validation and submission
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creating...';
            
            setTimeout(function() {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-check-lg me-1"></i>Create Category';
            }, 3000);
        });

        // Auto-focus on name field
        nameInput.focus();
    });
</script>
@endpush
