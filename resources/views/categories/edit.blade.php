@extends('layouts.modern')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')
@section('page-subtitle', 'Update category information')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Category: {{ $category->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.update', $category) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    
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
                                   value="{{ old('name', $category->name) }}" 
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

                        <!-- Category Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Brief description of this category...">{{ old('description', $category->description) }}</textarea>
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
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Category
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-warning">
                                <i class="bi bi-x-lg me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Update Category
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Category Info -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Current Information
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Current Name:</dt>
                    <dd class="col-sm-7">{{ $category->name }}</dd>
                    
                    <dt class="col-sm-5">Products:</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-primary">{{ $category->products->count() }}</span>
                    </dd>
                    
                    <dt class="col-sm-5">Created:</dt>
                    <dd class="col-sm-7">
                        {{ $category->created_at->format('M d, Y') }}
                    </dd>
                    
                    <dt class="col-sm-5">Last Updated:</dt>
                    <dd class="col-sm-7">
                        {{ $category->updated_at->format('M d, Y') }}
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Warning -->
        @if($category->products->count() > 0)
            <div class="card mt-4 border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0 text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Important Notice
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        This category has <strong>{{ $category->products->count() }} products</strong> assigned to it. 
                        Changing the category name will update it for all associated products.
                    </p>
                </div>
            </div>
        @endif

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
                    <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye me-2"></i>
                        View Category
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list me-2"></i>
                        All Categories
                    </a>
                    @if($category->products->count() > 0)
                        <a href="{{ route('products.index') }}?category={{ $category->id }}" class="btn btn-outline-primary">
                            <i class="bi bi-cake2 me-2"></i>
                            View Products
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        const nameInput = document.getElementById('name');
        
        // Form validation and submission
        form.addEventListener('submit', function(e) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...';
            
            setTimeout(function() {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update Category';
            }, 3000);
        });

        // Auto-focus and select text
        nameInput.focus();
        nameInput.select();
    });
</script>
@endpush
