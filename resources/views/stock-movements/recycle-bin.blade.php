@extends('layouts.modern')

@section('title', 'Recycle Bin - Stock Movements')

@push('styles')
<style>
    .recycle-bin-card {
        border-left: 4px solid #dc3545;
    }
    
    .deleted-item {
        background-color: #f8f9fa;
        border-left: 3px solid #dc3545;
    }
    
    .bulk-actions {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .restore-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
    }
    
    .restore-btn:hover {
        background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
        color: white;
    }
    
    .force-delete-btn {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
    }
    
    .force-delete-btn:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-trash text-danger me-2"></i>
                Recycle Bin
            </h1>
            <p class="text-muted mb-0">Restore or permanently delete soft-deleted stock movements</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Movements
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel me-2"></i>
                Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('stock-movements.recycle-bin') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-funnel me-1"></i>
                        Apply Filters
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('stock-movements.recycle-bin') }}">
                        <i class="bi bi-x-circle me-1"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if($trashedMovements->count() > 0)
    <div class="bulk-actions">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">Bulk Actions</h6>
                <small class="text-muted">Select items below and choose an action</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm restore-btn" onclick="bulkRestore()" disabled id="bulkRestoreBtn">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Restore Selected
                </button>
                <button type="button" class="btn btn-sm force-delete-btn" onclick="bulkForceDelete()" disabled id="bulkDeleteBtn">
                    <i class="bi bi-trash3 me-1"></i>
                    Delete Permanently
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">
                    <i class="bi bi-check-all me-1"></i>
                    Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectNone()">
                    <i class="bi bi-x-square me-1"></i>
                    Select None
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Recycle Bin Content -->
    <div class="card recycle-bin-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-clock-history me-2"></i>
                Deleted Movements
                <span class="badge bg-danger ms-2">{{ $trashedMovements->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($trashedMovements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                </th>
                                <th>Deleted At</th>
                                <th>Movement Date</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>User</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedMovements as $movement)
                                <tr class="deleted-item">
                                    <td>
                                        <input type="checkbox" class="form-check-input movement-checkbox" value="{{ $movement->id }}" onchange="updateBulkButtons()">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-danger">
                                            {{ $movement->deleted_at->format('M j, Y') }}
                                        </div>
                                        <small class="text-muted">{{ $movement->deleted_at->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $movement->movement_date->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ $movement->movement_date->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $movement->product->name ?? 'Unknown Product' }}</div>
                                        <small class="text-muted">ID: {{ $movement->product_id }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $movement->type === 'in' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            <i class="bi bi-{{ $movement->type === 'in' ? 'arrow-down-circle' : 'arrow-up-circle' }} me-1"></i>
                                            {{ $movement->formatted_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($movement->quantity) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $movement->reason ?: 'No reason' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $movement->user->name ?? 'System' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('stock-movements.restore', $movement->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm restore-btn" type="submit" title="Restore Movement">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('stock-movements.force-delete', $movement->id) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('This will permanently delete the movement. This action cannot be undone. Continue?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm force-delete-btn" type="submit" title="Delete Permanently">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($trashedMovements->hasPages())
                    <div class="card-footer">
                        {{ $trashedMovements->links() }}
                    </div>
                @endif
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-trash display-4 d-block mb-3 text-secondary"></i>
                    <h5>Recycle Bin is Empty</h5>
                    <p>Deleted stock movements will appear here for restoration or permanent removal.</p>
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Stock Movements
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden Forms for Bulk Actions -->
<form id="bulkRestoreForm" action="{{ route('stock-movements.bulk-restore') }}" method="POST" style="display: none;">
    @csrf
    <div id="bulkRestoreIds"></div>
</form>

<form id="bulkForceDeleteForm" action="{{ route('stock-movements.bulk-force-delete') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <div id="bulkForceDeleteIds"></div>
</form>

@push('scripts')
<script>
function updateBulkButtons() {
    const checkboxes = document.querySelectorAll('.movement-checkbox:checked');
    const restoreBtn = document.getElementById('bulkRestoreBtn');
    const deleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checkboxes.length > 0) {
        restoreBtn.disabled = false;
        deleteBtn.disabled = false;
    } else {
        restoreBtn.disabled = true;
        deleteBtn.disabled = true;
    }
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.movement-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkButtons();
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.movement-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    selectAllCheckbox.checked = true;
    
    updateBulkButtons();
}

function selectNone() {
    const checkboxes = document.querySelectorAll('.movement-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectAllCheckbox.checked = false;
    
    updateBulkButtons();
}

function bulkRestore() {
    const checkboxes = document.querySelectorAll('.movement-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one movement to restore.');
        return;
    }
    
    if (confirm(`Are you sure you want to restore ${checkboxes.length} movement(s)?`)) {
        const form = document.getElementById('bulkRestoreForm');
        const idsContainer = document.getElementById('bulkRestoreIds');
        
        idsContainer.innerHTML = '';
        checkboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'movement_ids[]';
            input.value = checkbox.value;
            idsContainer.appendChild(input);
        });
        
        form.submit();
    }
}

function bulkForceDelete() {
    const checkboxes = document.querySelectorAll('.movement-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one movement to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to permanently delete ${checkboxes.length} movement(s)? This action cannot be undone.`)) {
        const form = document.getElementById('bulkForceDeleteForm');
        const idsContainer = document.getElementById('bulkForceDeleteIds');
        
        idsContainer.innerHTML = '';
        checkboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'movement_ids[]';
            input.value = checkbox.value;
            idsContainer.appendChild(input);
        });
        
        form.submit();
    }
}
</script>
@endpush
@endsection
