@extends('layouts.modern')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-gear text-primary me-2"></i>
                Settings
            </h1>
            <p class="text-muted mb-0">Configure your DEER BAKERY & CAKE inventory system</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                <i class="bi bi-person-circle me-1"></i>
                My Profile
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Settings Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-list me-2"></i>
                        Settings Categories
                    </h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#company-settings" class="list-group-item list-group-item-action active" data-bs-toggle="pill">
                        <i class="bi bi-building me-2"></i>
                        Company Information
                    </a>
                    <a href="#system-settings" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        <i class="bi bi-sliders me-2"></i>
                        System Preferences
                    </a>
                    <a href="#inventory-settings" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        <i class="bi bi-boxes me-2"></i>
                        Inventory Settings
                    </a>
                    <a href="#backup-settings" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        <i class="bi bi-shield-check me-2"></i>
                        Backup & Security
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('database.status') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-database me-1"></i>
                            Database Status
                        </a>
                        <button class="btn btn-outline-success btn-sm" onclick="clearCache()">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Clear Cache
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="exportData()">
                            <i class="bi bi-download me-1"></i>
                            Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Company Settings -->
                <div class="tab-pane fade show active" id="company-settings">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-building me-2"></i>
                                Company Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="company_name" 
                                               name="company_name" 
                                               value="{{ session('app_settings.company_name', 'DEER BAKERY & CAKE') }}" 
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="company_email" class="form-label">Company Email</label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="company_email" 
                                               name="company_email" 
                                               value="{{ session('app_settings.company_email', 'info@deerbakery.com') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="company_phone" class="form-label">Company Phone</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="company_phone" 
                                               name="company_phone" 
                                               value="{{ session('app_settings.company_phone', '+855 12 345 678') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="currency" class="form-label">Currency</label>
                                        <select class="form-select" id="currency" name="currency">
                                            <option value="USD" {{ session('app_settings.currency', 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                            <option value="KHR" {{ session('app_settings.currency') == 'KHR' ? 'selected' : '' }}>KHR (៛)</option>
                                            <option value="EUR" {{ session('app_settings.currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="company_address" class="form-label">Company Address</label>
                                        <textarea class="form-control" 
                                                  id="company_address" 
                                                  name="company_address" 
                                                  rows="3">{{ session('app_settings.company_address', 'Street 360 & Koh Pich, Phnom Penh, Cambodia') }}</textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>
                                        Save Company Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="tab-pane fade" id="system-settings">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-sliders me-2"></i>
                                System Preferences
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="timezone" class="form-label">Timezone</label>
                                        <select class="form-select" id="timezone" name="timezone">
                                            <option value="Asia/Phnom_Penh" {{ session('app_settings.timezone', 'Asia/Phnom_Penh') == 'Asia/Phnom_Penh' ? 'selected' : '' }}>Asia/Phnom Penh</option>
                                            <option value="Asia/Bangkok" {{ session('app_settings.timezone') == 'Asia/Bangkok' ? 'selected' : '' }}>Asia/Bangkok</option>
                                            <option value="UTC" {{ session('app_settings.timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="items_per_page" class="form-label">Items Per Page</label>
                                        <select class="form-select" id="items_per_page" name="items_per_page">
                                            <option value="10" {{ session('app_settings.items_per_page', '15') == '10' ? 'selected' : '' }}>10</option>
                                            <option value="15" {{ session('app_settings.items_per_page', '15') == '15' ? 'selected' : '' }}>15</option>
                                            <option value="20" {{ session('app_settings.items_per_page', '15') == '20' ? 'selected' : '' }}>20</option>
                                            <option value="25" {{ session('app_settings.items_per_page', '15') == '25' ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ session('app_settings.items_per_page', '15') == '50' ? 'selected' : '' }}>50</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>
                                        Save System Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Inventory Settings -->
                <div class="tab-pane fade" id="inventory-settings">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-boxes me-2"></i>
                                Inventory Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="low_stock_threshold" 
                                               name="low_stock_threshold" 
                                               value="{{ session('app_settings.low_stock_threshold', '5') }}" 
                                               min="1" 
                                               max="100">
                                        <div class="form-text">Alert when product quantity falls below this number</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>
                                        Save Inventory Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Backup & Security -->
                <div class="tab-pane fade" id="backup-settings">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-check me-2"></i>
                                Backup & Security
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Database Backup</h6>
                                    <p class="text-muted">Create a backup of your inventory data</p>
                                    <button class="btn btn-success" onclick="createBackup()">
                                        <i class="bi bi-download me-1"></i>
                                        Create Backup
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <h6>System Information</h6>
                                    <p class="text-muted">View system status and information</p>
                                    <a href="{{ route('database.status') }}" class="btn btn-info">
                                        <i class="bi bi-info-circle me-1"></i>
                                        View System Info
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function clearCache() {
        if (confirm('Are you sure you want to clear the cache?')) {
            window.location.href = '{{ route("dashboard") }}';
        }
    }

    function exportData() {
        alert('Export functionality will be implemented soon!');
    }

    function createBackup() {
        if (confirm('Create a database backup? This may take a few moments.')) {
            alert('Backup functionality will be implemented soon!');
        }
    }
</script>
@endpush
@endsection
