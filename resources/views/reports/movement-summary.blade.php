@extends('layouts.modern')

@section('title', 'Movement Summary Report')
@section('page-title', 'Movement Summary Report')
@section('page-subtitle', 'Stock movement analytics and trends')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-bar-chart text-primary me-2"></i>
            Movement Summary Report
        </h1>
        <p class="text-muted mb-0">Stock movement analytics from {{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>
            Print Report
        </button>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.movement-summary') }}" class="row g-3">
            <div class="col-md-4">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-4">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-calendar-check me-1"></i>
                        Update Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['total_in']) }}</h4>
                        <p class="mb-0">Total Stock In</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-arrow-down-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['total_out']) }}</h4>
                        <p class="mb-0">Total Stock Out</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['movements_count']) }}</h4>
                        <p class="mb-0">Total Movements</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['products_affected']) }}</h4>
                        <p class="mb-0">Products Affected</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="bi bi-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Net Movement Summary -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Net Movement
                </h6>
            </div>
            <div class="card-body text-center">
                @php
                    $netMovement = $stats['total_in'] - $stats['total_out'];
                @endphp
                <h2 class="mb-2 {{ $netMovement >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $netMovement >= 0 ? '+' : '' }}{{ number_format($netMovement) }}
                </h2>
                <p class="text-muted mb-0">
                    {{ $netMovement >= 0 ? 'Net Stock Increase' : 'Net Stock Decrease' }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-percent me-2"></i>
                    Movement Ratio
                </h6>
            </div>
            <div class="card-body text-center">
                @php
                    $totalMovements = $stats['total_in'] + $stats['total_out'];
                    $inPercentage = $totalMovements > 0 ? ($stats['total_in'] / $totalMovements) * 100 : 0;
                    $outPercentage = $totalMovements > 0 ? ($stats['total_out'] / $totalMovements) * 100 : 0;
                @endphp
                <div class="row">
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ number_format($inPercentage, 1) }}%</h4>
                        <small class="text-muted">Stock In</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-0">{{ number_format($outPercentage, 1) }}%</h4>
                        <small class="text-muted">Stock Out</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products by Movement -->
@if($topProducts->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-trophy me-2"></i>
            Top Products by Movement Volume
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Product</th>
                        <th>Total Movement</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $index => $movement)
                        <tr>
                            <td>
                                <span class="badge {{ $index < 3 ? 'bg-warning' : 'bg-light text-dark' }}">
                                    #{{ $index + 1 }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $movement->product->name }}</div>
                                <small class="text-muted">{{ $movement->product->category->name ?? 'No Category' }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ number_format($movement->total_quantity) }}</span>
                                <small class="text-muted">{{ $movement->product->unit }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ number_format($movement->product->quantity) }}</span>
                                @if($movement->product->quantity <= 5)
                                    <i class="bi bi-exclamation-triangle text-warning ms-1" title="Low Stock"></i>
                                @endif
                            </td>
                            <td>
                                @if($movement->product->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Daily Movement Chart Data -->
@if($dailyMovements->count() > 0)
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-graph-up me-2"></i>
            Daily Movement Breakdown
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Stock In</th>
                        <th>Stock Out</th>
                        <th>Net Movement</th>
                        <th>Visual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyMovements as $daily)
                        @php
                            $netDaily = $daily->stock_in - $daily->stock_out;
                            $maxMovement = $dailyMovements->max(function($item) { return max($item->stock_in, $item->stock_out); });
                            $inWidth = $maxMovement > 0 ? ($daily->stock_in / $maxMovement) * 100 : 0;
                            $outWidth = $maxMovement > 0 ? ($daily->stock_out / $maxMovement) * 100 : 0;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($daily->date)->format('M j, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($daily->date)->format('l') }}</small>
                            </td>
                            <td>
                                <span class="text-success fw-semibold">{{ number_format($daily->stock_in) }}</span>
                            </td>
                            <td>
                                <span class="text-warning fw-semibold">{{ number_format($daily->stock_out) }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold {{ $netDaily >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $netDaily >= 0 ? '+' : '' }}{{ number_format($netDaily) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1" style="width: 150px;">
                                    <div class="bg-success" style="height: 8px; width: {{ $inWidth }}%; min-width: 2px;" title="Stock In: {{ $daily->stock_in }}"></div>
                                    <div class="bg-warning" style="height: 8px; width: {{ $outWidth }}%; min-width: 2px;" title="Stock Out: {{ $daily->stock_out }}"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-graph-up display-1 text-muted"></i>
        <h4 class="mt-3">No Movement Data</h4>
        <p class="text-muted">
            No stock movements found for the selected date range.
            <br>Try selecting a different date range or add some stock movements.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('stock.in') }}" class="btn btn-success">
                <i class="bi bi-arrow-down-circle me-1"></i>
                Add Stock
            </a>
            <a href="{{ route('stock.out') }}" class="btn btn-warning">
                <i class="bi bi-arrow-up-circle me-1"></i>
                Remove Stock
            </a>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    @media print {
        .btn, .card-header, nav, .sidebar {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
</style>
@endpush
