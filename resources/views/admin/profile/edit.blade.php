@extends('admin.layouts.master')

@section('title', 'Profile Settings')
@section('page-icon', 'fa-user-circle')
@section('page-title', 'Profile Settings')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="profile-container">
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="profile-grid">
        <!-- Left Column - Avatar & Info -->
        <div class="profile-card">
            <div class="avatar-section">
                <div class="avatar-wrapper">
                    <div class="avatar-preview" id="avatarPreview">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="Avatar">
                        @else
                            <div class="avatar-initials">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <label for="avatarUpload" class="avatar-upload-btn">
                        <i class="fas fa-camera"></i>
                        <span>Change Photo</span>
                    </label>
                    <input type="file" id="avatarUpload" accept="image/*" style="display: none;">
                </div>
                <p class="text-muted">JPG, PNG or GIF (Max 2MB)</p>
            </div>
            
            <div class="user-info">
                <h3>{{ Auth::user()->name }}</h3>
                <p class="user-email">{{ Auth::user()->email }}</p>
                <span class="role-badge">
                    <i class="fas fa-shield-alt"></i> Administrator
                </span>
            </div>
            
            <div class="user-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <span class="meta-label">Member Since</span>
                        <span class="meta-value">{{ Auth::user()->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <span class="meta-label">Last Updated</span>
                        <span class="meta-value">{{ Auth::user()->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Edit Forms -->
        <div class="forms-card">
            <!-- Profile Information Form -->
            <div class="form-section">
                <h4><i class="fas fa-user-edit"></i> Profile Information</h4>
                
                <form method="POST" action="{{ route('admin.profile.update') }}" id="profileForm">
                    @csrf
                    @method('PATCH')
                    
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', Auth::user()->name) }}" 
                               required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', Auth::user()->email) }}" 
                               required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="form-section">
                <h4><i class="fas fa-key"></i> Change Password</h4>
                
                <form method="POST" action="{{ route('admin.profile.password.update') }}" id="passwordForm">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="form-group">
                        <label for="current_password">
                            <i class="fas fa-lock"></i> Current Password
                        </label>
                        <input type="password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        @error('current_password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-key"></i> New Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" 
                                   name="new_password" 
                                   required>
                            @error('new_password')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">
                                <i class="fas fa-check-circle"></i> Confirm Password
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   required>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <p><i class="fas fa-shield-alt"></i> Password must:</p>
                        <ul>
                            <li id="lengthCheck" class="requirement">✓ Be at least 8 characters</li>
                            <li id="letterCheck" class="requirement">✓ Contain at least one letter</li>
                            <li id="numberCheck" class="requirement">✓ Contain at least one number</li>
                        </ul>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.profile-container {
    padding: 20px;
}

.alert-success {
    background: #d1fae5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.95rem;
}

.profile-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 25px;
}

/* Left Column - Profile Card */
.profile-card {
    background: white;
    border-radius: 16px;
    padding: 30px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    text-align: center;
}

.avatar-section {
    margin-bottom: 20px;
}

.avatar-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 15px;
}

.avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-initials {
    color: white;
    font-size: 48px;
    font-weight: 600;
}

.avatar-upload-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: #f59e0b;
    color: white;
    border: none;
    border-radius: 30px;
    padding: 8px 15px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid white;
}

.avatar-upload-btn:hover {
    background: #d97706;
    transform: scale(1.02);
}

.text-muted {
    color: #64748b;
    font-size: 0.8rem;
    margin-top: 5px;
}

.user-info h3 {
    color: #1e293b;
    font-size: 1.3rem;
    margin-bottom: 5px;
}

.user-email {
    color: #64748b;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    color: #475569;
    padding: 6px 16px;
    border-radius: 30px;
    font-size: 0.9rem;
}

.user-meta {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
}

.meta-item i {
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
}

.meta-label {
    display: block;
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 2px;
}

.meta-value {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.95rem;
}

/* Right Column - Forms */
.forms-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}

.form-section {
    margin-bottom: 35px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.form-section h4 {
    color: #1e293b;
    font-size: 1.1rem;
    margin-bottom: 25px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section h4 i {
    color: #f59e0b;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    color: #475569;
    font-weight: 500;
    font-size: 0.9rem;
}

.form-group label i {
    color: #667eea;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.error-message {
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 5px;
    display: block;
}

.password-requirements {
    background: #f8fafc;
    border-radius: 10px;
    padding: 15px;
    margin: 20px 0;
}

.password-requirements p {
    color: #1e293b;
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.password-requirements ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.password-requirements li {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.password-requirements li.valid {
    color: #10b981;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 25px;
}

.btn {
    padding: 12px 30px;
    border: none;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #f59e0b;
    color: white;
}

.btn-primary:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar upload preview
    const avatarInput = document.getElementById('avatarUpload');
    const avatarPreview = document.getElementById('avatarPreview');
    
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Avatar">`;
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Password requirements checker
    const passwordInput = document.getElementById('new_password');
    if (passwordInput) {
        const lengthCheck = document.getElementById('lengthCheck');
        const letterCheck = document.getElementById('letterCheck');
        const numberCheck = document.getElementById('numberCheck');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Length check
            if (password.length >= 8) {
                lengthCheck.classList.add('valid');
                lengthCheck.innerHTML = '✓ At least 8 characters';
            } else {
                lengthCheck.classList.remove('valid');
                lengthCheck.innerHTML = '• At least 8 characters';
            }
            
            // Letter check
            if (/[a-zA-Z]/.test(password)) {
                letterCheck.classList.add('valid');
                letterCheck.innerHTML = '✓ Contains at least one letter';
            } else {
                letterCheck.classList.remove('valid');
                letterCheck.innerHTML = '• Contains at least one letter';
            }
            
            // Number check
            if (/\d/.test(password)) {
                numberCheck.classList.add('valid');
                numberCheck.innerHTML = '✓ Contains at least one number';
            } else {
                numberCheck.classList.remove('valid');
                numberCheck.innerHTML = '• Contains at least one number';
            }
        });
    }
});
</script>
@endsection