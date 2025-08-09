@extends('layouts.modern')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-person-circle text-primary me-2"></i>
                My Profile
            </h1>
            <p class="text-muted mb-0">Manage your personal information and account settings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-gear me-1"></i>
                Settings
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="mb-3">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                 alt="Profile Picture"
                                 class="rounded-circle avatar-lg mx-auto">
                        @else
                            <div class="rounded-circle avatar-lg avatar-placeholder mx-auto">
                                <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->position ?? 'Staff Member' }}</p>
                    <p class="text-muted mb-3">
                        <i class="bi bi-envelope me-1"></i>
                        {{ $user->email }}
                    </p>
                    @if($user->phone)
                        <p class="text-muted mb-3">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $user->phone }}
                        </p>
                    @endif

                    <!-- Quick Stats -->
                    <div class="row text-center mt-4">
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="text-primary mb-0">{{ \App\Models\Product::count() }}</h5>
                                <small class="text-muted">Products</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="text-success mb-0">{{ \App\Models\StockMovement::where('type', 'in')->count() }}</h5>
                                <small class="text-muted">Stock In</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h5 class="text-warning mb-0">{{ \App\Models\StockMovement::where('type', 'out')->count() }}</h5>
                            <small class="text-muted">Stock Out</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->phone) }}" 
                                       placeholder="+855 12 345 678">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Position -->
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position/Role</label>
                                <select class="form-select @error('position') is-invalid @enderror" 
                                        id="position" 
                                        name="position">
                                    <option value="">Select Position</option>
                                    <option value="Manager" {{ old('position', $user->position) == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Assistant Manager" {{ old('position', $user->position) == 'Assistant Manager' ? 'selected' : '' }}>Assistant Manager</option>
                                    <option value="Inventory Clerk" {{ old('position', $user->position) == 'Inventory Clerk' ? 'selected' : '' }}>Inventory Clerk</option>
                                    <option value="Baker" {{ old('position', $user->position) == 'Baker' ? 'selected' : '' }}>Baker</option>
                                    <option value="Cashier" {{ old('position', $user->position) == 'Cashier' ? 'selected' : '' }}>Cashier</option>
                                    <option value="Staff" {{ old('position', $user->position) == 'Staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Avatar -->
                            <div class="col-12 mb-3">
                                <label for="avatar" class="form-label">Profile Picture</label>
                                <input type="file" 
                                       class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" 
                                       name="avatar" 
                                       accept="image/*">
                                <div class="form-text">Upload a profile picture (JPEG, PNG, JPG, GIF - Max: 2MB)</div>
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Current Password -->
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="col-md-4 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-4 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-key me-1"></i>
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
