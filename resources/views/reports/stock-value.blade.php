@extends('layouts.modern')

@section('title', 'Stock Value Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-graph-up-arrow text-success me-2"></i>
                Stock Value Report
            </h1>
            <p class="text-muted mb-0">Total prices for stock in, out, and remaining inventory</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>
                Print Report
            </button>
            <button class="btn btn-outline-secondary" onclick="exportToCSV()">
                <i class="bi bi-download me-1"></i>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.stock-value') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ $dateTo }}">
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="reason" class="form-label">Reason</label>
                    <select name="reason" class="form-select">
                        <option value="">All Reasons</option>
                        @foreach($availableReasons as $reason)
                            <option value="{{ $reason }}" {{ $reasonFilter == $reason ? 'selected' : '' }}>
                                {{ $reason }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="movement_type" class="form-label">Movement Type</label>
                    <select name="movement_type" class="form-select">
                        @foreach($movementTypes as $value => $label)
                            <option value="{{ $value }}" {{ $movementType == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['date_from', 'date_to', 'category', 'reason', 'movement_type']))
                        <a href="{{ route('reports.stock-value') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-x-circle me-1"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if(request()->hasAny(['date_from', 'date_to', 'category', 'reason', 'movement_type']))
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <small class="text-muted me-2">Active Filters:</small>

                    @if(request('date_from') || request('date_to'))
                        <span class="badge bg-primary">
                            <i class="bi bi-calendar me-1"></i>
                            Date: {{ request('date_from', 'Start') }} to {{ request('date_to', 'End') }}
                        </span>
                    @endif

                    @if(request('category'))
                        @php
                            $selectedCategory = $categories->find(request('category'));
                        @endphp
                        <span class="badge bg-info">
                            <i class="bi bi-tag me-1"></i>
                            Category: {{ $selectedCategory ? $selectedCategory->name : 'Unknown' }}
                        </span>
                    @endif

                    @if(request('reason'))
                        <span class="badge bg-secondary">
                            <i class="bi bi-bookmark me-1"></i>
                            Reason: {{ request('reason') }}
                        </span>
                    @endif

                    @if(request('movement_type') && request('movement_type') !== 'all')
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-arrow-left-right me-1"></i>
                            Type: {{ $movementTypes[request('movement_type')] ?? request('movement_type') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Summary Info -->
    @if($products->count() > 0)
        <div class="alert alert-info mb-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle me-2"></i>
                <div>
                    <strong>Filtered Results:</strong>
                    Showing {{ $products->count() }} {{ Str::plural('product', $products->count()) }} with actual stock movements
                    @if($movementType && $movementType !== 'all')
                        ({{ strtolower($movementTypes[$movementType]) }})
                    @endif
                    in the selected period.
                    @if(request()->hasAny(['category', 'reason']))
                        Additional filters applied.
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        @if(!$movementType || $movementType === 'all' || $movementType === 'in')
            <div class="col-md-{{ (!$movementType || $movementType === 'all') ? '3' : '4' }}">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Stock In</h6>
                                <h4 class="mb-0">{{ number_format($totals['total_stock_in']) }}</h4>
                                <small class="opacity-75">${{ number_format($totals['total_stock_in_value'], 2) }}</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-arrow-down-circle fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(!$movementType || $movementType === 'all' || $movementType === 'out')
            <div class="col-md-{{ (!$movementType || $movementType === 'all') ? '3' : '4' }}">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Stock Out</h6>
                                <h4 class="mb-0">{{ number_format($totals['total_stock_out']) }}</h4>
                                <small class="opacity-75">${{ number_format($totals['total_stock_out_value'], 2) }}</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-arrow-up-circle fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-{{ (!$movementType || $movementType === 'all') ? '3' : '4' }}">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Remaining Stock</h6>
                            <h4 class="mb-0">{{ number_format($totals['total_remaining']) }}</h4>
                            <small class="opacity-75">${{ number_format($totals['total_remaining_value'], 2) }}</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-boxes fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!$movementType || $movementType === 'all')
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Net Movement</h6>
                                <h4 class="mb-0">{{ number_format($totals['total_stock_in'] - $totals['total_stock_out']) }}</h4>
                                <small class="opacity-75">${{ number_format($totals['total_stock_in_value'] - $totals['total_stock_out_value'], 2) }}</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-graph-up fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Detailed Report Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="bi bi-table me-2"></i>
                        Detailed Stock Value Report
                        <span class="badge bg-primary ms-2">{{ $products->count() }} {{ Str::plural('product', $products->count()) }}</span>
                    </h5>
                    <small class="text-muted">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</small>
                </div>
                @if($products->count() > 0)
                    <div class="text-end">
                        <small class="text-muted">Showing products with actual stock movements</small>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="80">Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Reason</th>
                            <th>Unit Price</th>
                            @if(!$movementType || $movementType === 'all' || $movementType === 'in')
                                <th class="text-center">Stock In</th>
                            @endif
                            @if(!$movementType || $movementType === 'all' || $movementType === 'out')
                                <th class="text-center">Stock Out</th>
                            @endif
                            <th class="text-center">Remaining</th>
                            @if(!$movementType || $movementType === 'all' || $movementType === 'in')
                                <th class="text-end">Stock In Value</th>
                            @endif
                            @if(!$movementType || $movementType === 'all' || $movementType === 'out')
                                <th class="text-end">Stock Out Value</th>
                            @endif
                            <th class="text-end">Remaining Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <img src="{{ $product['image_url'] }}" 
                                         alt="{{ $product['name'] }}" 
                                         class="rounded"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $product['name'] }}</div>
                                        @if($product['sku'])
                                            <small class="text-muted">SKU: {{ $product['sku'] }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $product['category'] }}</span>
                                </td>
                                <td>
                                    @if($product['primary_reason'] && $product['primary_reason'] !== 'N/A')
                                        <span class="badge bg-secondary">{{ $product['primary_reason'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">${{ number_format($product['unit_price'], 2) }}</span>
                                    <br>
                                    <small class="text-muted">per {{ $product['unit'] }}</small>
                                </td>
                                @if(!$movementType || $movementType === 'all' || $movementType === 'in')
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ number_format($product['stock_in']) }}</span>
                                    </td>
                                @endif
                                @if(!$movementType || $movementType === 'all' || $movementType === 'out')
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ number_format($product['stock_out']) }}</span>
                                    </td>
                                @endif
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ number_format($product['current_stock']) }}</span>
                                </td>
                                @if(!$movementType || $movementType === 'all' || $movementType === 'in')
                                    <td class="text-end">
                                        <span class="text-success fw-bold">${{ number_format($product['stock_in_value'], 2) }}</span>
                                    </td>
                                @endif
                                @if(!$movementType || $movementType === 'all' || $movementType === 'out')
                                    <td class="text-end">
                                        <span class="text-danger fw-bold">${{ number_format($product['stock_out_value'], 2) }}</span>
                                    </td>
                                @endif
                                <td class="text-end">
                                    <span class="text-primary fw-bold">${{ number_format($product['remaining_value'], 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                @php
                                    $totalColumns = 5; // Base columns: Image, Product, Category, Reason, Unit Price
                                    if (!$movementType || $movementType === 'all' || $movementType === 'in') $totalColumns += 2; // Stock In + Stock In Value
                                    if (!$movementType || $movementType === 'all' || $movementType === 'out') $totalColumns += 2; // Stock Out + Stock Out Value
                                    $totalColumns += 2; // Remaining + Remaining Value
                                @endphp
                                <td colspan="{{ $totalColumns }}" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        <h5>No products with stock movements found</h5>
                                        <p class="mb-2">No products have stock movements matching the selected criteria:</p>
                                        <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
                                            <span class="badge bg-primary">{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</span>
                                            @if($categoryId)
                                                @php $selectedCategory = $categories->find($categoryId); @endphp
                                                <span class="badge bg-info">{{ $selectedCategory ? $selectedCategory->name : 'Unknown Category' }}</span>
                                            @endif
                                            @if($reasonFilter)
                                                <span class="badge bg-secondary">{{ $reasonFilter }}</span>
                                            @endif
                                            @if($movementType && $movementType !== 'all')
                                                <span class="badge bg-warning text-dark">{{ $movementTypes[$movementType] }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">Try adjusting your filters or selecting a different date range.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($products->count() > 0)
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="5" class="text-end">TOTALS:</th>
                                @if(!$movementType || $movementType === 'all' || $movementType === 'in')
                                    <th class="text-center">{{ number_format($totals['total_stock_in']) }}</th>
                                @endif
                                @if(!$movementType || $movementType === 'all' || $movementType === 'out')
                                    <th class="text-center">{{ number_format($totals['total_stock_out']) }}</th>
                                @endif
                                <th class="text-center">{{ number_format($totals['total_remaining']) }}</th>
                                @if(!$movementType || $movementType === 'all' || $movementType === 'in')
                                    <th class="text-end">${{ number_format($totals['total_stock_in_value'], 2) }}</th>
                                @endif
                                @if(!$movementType || $movementType === 'all' || $movementType === 'out')
                                    <th class="text-end">${{ number_format($totals['total_stock_out_value'], 2) }}</th>
                                @endif
                                <th class="text-end">${{ number_format($totals['total_remaining_value'], 2) }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function exportToCSV() {
        const table = document.querySelector('table');
        const rows = Array.from(table.querySelectorAll('tr'));
        
        const csvContent = rows.map(row => {
            const cells = Array.from(row.querySelectorAll('th, td'));
            return cells.map(cell => {
                // Clean up cell content
                let content = cell.textContent.trim();
                // Remove extra whitespace and newlines
                content = content.replace(/\s+/g, ' ');
                // Escape quotes
                content = content.replace(/"/g, '""');
                return `"${content}"`;
            }).join(',');
        }).join('\n');
        
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `stock-value-report-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }
</script>
@endpush
@endsection
