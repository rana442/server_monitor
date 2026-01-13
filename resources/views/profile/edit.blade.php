@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="bi bi-person-circle"></i> Profile Settings
            </h1>
            <p class="text-muted">Manage your account settings and preferences</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control bg-light" 
                                   id="email" value="{{ auth()->user()->email }}" readonly>
                            <small class="text-muted">Email address cannot be changed.</small>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" 
                                   id="dark_mode" name="dark_mode" 
                                   {{ (auth()->user()->settings && auth()->user()->settings->dark_mode) ? 'checked' : '' }}>
                            <label class="form-check-label" for="dark_mode">
                                Enable Dark Mode
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Account Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Account Type:</th>
                            <td>
                                @if(auth()->user()->is_admin)
                                <span class="badge bg-danger">Administrator</span>
                                @else
                                <span class="badge bg-secondary">Regular User</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Member Since:</th>
                            <td>{{ auth()->user()->created_at->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Last Login:</th>
                            <td>
                                @if(auth()->user()->last_login_at)
                                {{ auth()->user()->last_login_at->diffForHumans() }}
                                @else
                                <span class="text-muted">Never logged in</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Email Verified:</th>
                            <td>
                                @if(auth()->user()->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                                @else
                                <span class="badge bg-warning">Not Verified</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="row">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Danger Zone</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Delete Account</h6>
                            <p class="text-muted mb-0">
                                Once you delete your account, there is no going back. Please be certain.
                            </p>
                        </div>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Delete Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger">
                    <strong>Warning:</strong> This action cannot be undone. All your data will be permanently deleted.
                </p>
                <p>Are you sure you want to delete your account?</p>
                
                <form id="deleteAccountForm" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-3">
                        <label for="confirmDelete" class="form-label">
                            Type "DELETE" to confirm:
                        </label>
                        <input type="text" class="form-control" id="confirmDelete" name="confirmDelete" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger" disabled id="deleteButton">
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmInput = document.getElementById('confirmDelete');
        const deleteButton = document.getElementById('deleteButton');
        
        confirmInput.addEventListener('input', function() {
            deleteButton.disabled = this.value !== 'DELETE';
        });
        
        // Delete account confirmation
        document.getElementById('deleteAccountForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you absolutely sure? This cannot be undone!')) {
                // Uncomment and set actual delete route
                // this.submit();
                alert('Account deletion would be processed here. Add your actual deletion logic.');
            }
        });
    });
</script>

<style>
    .table-borderless td, .table-borderless th {
        border: none;
        padding: 0.5rem 0;
    }
    
    .table-borderless th {
        font-weight: 600;
        color: #6c757d;
    }
    
    .card.border-danger {
        border-width: 2px;
    }
</style>
@endsection