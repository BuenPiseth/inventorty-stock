@extends('layouts.modern')

@section('title', 'Database Status')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-database text-primary me-2"></i>
                Database Status
            </h1>
            <p class="text-muted mb-0">MySQL/phpMyAdmin storage verification for DEER BAKERY & CAKE</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="location.reload()" class="btn btn-outline-primary">
                <i class="bi bi-arrow-clockwise me-1"></i>
                Refresh
            </button>
        </div>
    </div>

    @if(isset($error))
        <!-- Error State -->
        <div class="alert alert-danger">
            <h5 class="alert-heading">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Database Connection Error
            </h5>
            <p class="mb-0">{{ $error }}</p>
        </div>
    @else
        <!-- Success State -->
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="alert-heading mb-2">
                        <i class="bi bi-check-circle me-2"></i>
                        Database Connection Active
                    </h6>
                    <p class="mb-0">
                        All data is properly stored in <strong>{{ $stats['database'] }}</strong> database using <strong>{{ ucfirst($stats['driver']) }}</strong> driver.
                        Total of <strong>{{ number_format($stats['total_records']) }}</strong> records stored in phpMyAdmin.
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-success fs-6">{{ $stats['connection'] }}</span>
                </div>
            </div>
        </div>

        <!-- Database Information -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Connection Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Database:</strong>
                            </div>
                            <div class="col-sm-6">
                                <code>{{ $stats['database'] }}</code>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Driver:</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="badge bg-info">{{ ucfirst($stats['driver']) }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Status:</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="badge bg-success">{{ $stats['connection'] }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Last Activity:</strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted">{{ $stats['last_activity'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-server me-2"></i>
                            Server Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="bg-light rounded p-3">
                            <small class="text-muted d-block mb-1">Server Details:</small>
                            <code style="font-size: 0.8rem;">{{ $stats['server_info'] }}</code>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Storage Location:</span>
                                <span class="badge bg-primary">phpMyAdmin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Statistics -->
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    Table Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($stats['tables'] as $table => $count)
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="mb-2">
                                    @switch($table)
                                        @case('products')
                                            <i class="bi bi-box text-primary fs-2"></i>
                                            @break
                                        @case('categories')
                                            <i class="bi bi-tags text-info fs-2"></i>
                                            @break
                                        @case('stock_movements')
                                            <i class="bi bi-arrow-left-right text-warning fs-2"></i>
                                            @break
                                        @case('warehouses')
                                            <i class="bi bi-building text-success fs-2"></i>
                                            @break
                                    @endswitch
                                </div>
                                <h4 class="mb-1">{{ number_format($count) }}</h4>
                                <small class="text-muted text-capitalize">{{ str_replace('_', ' ', $table) }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 p-3 bg-success bg-opacity-10 rounded">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Data Storage Confirmed
                            </h6>
                            <p class="mb-0 text-muted">
                                All {{ number_format($stats['total_records']) }} records are safely stored in your MySQL database and accessible through phpMyAdmin.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="fs-3 text-success">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- phpMyAdmin Access Instructions -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-question-circle me-2"></i>
                    How to Access Your Data in phpMyAdmin
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>📋 View Data:</h6>
                        <ol class="small">
                            <li>Open phpMyAdmin in your browser</li>
                            <li>Select <code>{{ $stats['database'] }}</code> database</li>
                            <li>Click on any table name to view data</li>
                            <li>Use "Browse" tab to see all records</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6>💾 Backup Data:</h6>
                        <ol class="small">
                            <li>Select <code>{{ $stats['database'] }}</code> database</li>
                            <li>Click "Export" tab</li>
                            <li>Choose "Quick" export method</li>
                            <li>Click "Go" to download backup</li>
                        </ol>
                    </div>
                </div>
                
                <div class="mt-3 p-3 bg-light rounded">
                    <h6 class="mb-2">🔧 Available Commands:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <code>php artisan db:verify</code>
                            <small class="d-block text-muted">Verify database storage</small>
                        </div>
                        <div class="col-md-6">
                            <code>php artisan db:backup</code>
                            <small class="d-block text-muted">Create database backup</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Auto-refresh every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
@endpush
@endsection
