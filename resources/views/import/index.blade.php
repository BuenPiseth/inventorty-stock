@extends('layouts.modern')

@section('title', 'Import Inventory')
@section('page-title', 'Import Inventory')
@section('page-subtitle', 'Import products from CSV file')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-upload me-2"></i>
                    Upload CSV File
                </h5>
            </div>
            <div class="card-body">
                @if(session('import_results'))
                    @php $results = session('import_results'); @endphp
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Import Results</h6>
                        <ul class="mb-0">
                            <li><strong>{{ $results['success'] }}</strong> products created</li>
                            <li><strong>{{ $results['updated'] }}</strong> products updated</li>
                            <li><strong>{{ $results['errors'] }}</strong> errors encountered</li>
                        </ul>
                        
                        @if(count($results['messages']) > 0)
                            <details class="mt-3">
                                <summary>View Details</summary>
                                <div class="mt-2">
                                    @foreach($results['messages'] as $message)
                                        <small class="d-block">{{ $message }}</small>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </div>
                @endif

                <form action="{{ route('import.csv') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="csv_file" class="form-label">
                            Select CSV File <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               class="form-control @error('csv_file') is-invalid @enderror" 
                               id="csv_file" 
                               name="csv_file" 
                               accept=".csv,.txt"
                               required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Upload a CSV file with your inventory data. Maximum file size: 2MB
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Products
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>
                            Import CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- CSV Format Guide -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    CSV Format Guide
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-3">Your CSV file should have the following columns:</p>
                
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Column</th>
                                <th>Required</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>name</code></td>
                                <td><span class="badge bg-danger">Yes</span></td>
                                <td>Chocolate Cake</td>
                            </tr>
                            <tr>
                                <td><code>category</code></td>
                                <td><span class="badge bg-danger">Yes</span></td>
                                <td>Cakes</td>
                            </tr>
                            <tr>
                                <td><code>unit</code></td>
                                <td><span class="badge bg-danger">Yes</span></td>
                                <td>pieces</td>
                            </tr>
                            <tr>
                                <td><code>quantity</code></td>
                                <td><span class="badge bg-danger">Yes</span></td>
                                <td>5</td>
                            </tr>
                            <tr>
                                <td><code>status</code></td>
                                <td><span class="badge bg-secondary">No</span></td>
                                <td>active</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="{{ route('import.template') }}" class="btn btn-outline-success w-100">
                        <i class="bi bi-download me-2"></i>
                        Download Sample Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Import Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Categories will be created automatically if they don't exist
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Existing products will be updated with new data
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Use common units: pieces, dozen, kg, loaves, etc.
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Status can be 'active' or 'inactive' (defaults to active)
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Make sure your CSV uses UTF-8 encoding
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
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-collection me-2"></i>
                        Manage Categories
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Single Product
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
        const fileInput = document.getElementById('csv_file');
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        
        // File validation
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // Convert to MB
                const fileName = file.name.toLowerCase();
                
                if (fileSize > 2) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }
                
                if (!fileName.endsWith('.csv') && !fileName.endsWith('.txt')) {
                    alert('Please select a CSV file');
                    this.value = '';
                    return;
                }
            }
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Please select a CSV file to import');
                return;
            }
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Importing...';
        });
    });
</script>
@endpush
