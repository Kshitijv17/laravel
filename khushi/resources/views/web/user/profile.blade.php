@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
<style>
.modern-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: auto;
    padding: 1.25rem 0 2rem;
}

.profile-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    border: none;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 500px;
    margin: 0 auto;
}

.profile-header {
    background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
    padding: 2rem;
    text-align: center;
    position: relative;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.3);
    margin: 0 auto 1rem;
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.profile-name {
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
    position: relative;
    z-index: 2;
}

.profile-email {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    margin: 0.5rem 0 0;
    position: relative;
    z-index: 2;
}

.profile-body {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #5f6368;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-input {
    border: 2px solid #e8eaed;
    border-radius: 12px;
    padding: 0.875rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #fafbfc;
    width: 100%;
    margin-bottom: 1rem;
}

.modern-input:focus {
    outline: none;
    border-color: #4285f4;
    background: white;
    box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.1);
}

.modern-input::placeholder {
    color: #9aa0a6;
}

.input-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.radio-group {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.radio-option input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: #4285f4;
}

.switch-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.switch-label {
    font-size: 0.95rem;
    color: #3c4043;
    margin: 0;
}

.modern-switch {
    position: relative;
    width: 48px;
    height: 24px;
    background: #dadce0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modern-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.switch-slider {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.modern-switch input:checked + .switch-slider {
    transform: translateX(24px);
}

.modern-switch input:checked {
    background: #4285f4;
}

.modern-switch:has(input:checked) {
    background: #4285f4;
}

.update-btn {
    background: #4285f4;
    color: white;
    border: none;
    border-radius: 12px;
    padding: 0.875rem 2rem;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 1rem;
}

.update-btn:hover {
    background: #3367d6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);
}

.file-upload {
    margin-top: 1rem;
}

.file-input {
    display: none;
}

.file-label {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    border: 2px dashed #dadce0;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
    color: #5f6368;
    transition: all 0.3s ease;
}

.file-label:hover {
    border-color: #4285f4;
    color: #4285f4;
}

.alert-modern {
    border: none;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.alert-success {
    background: #e8f5e8;
    color: #137333;
}

.alert-danger {
    background: #fce8e6;
    color: #d93025;
}

@media (max-width: 768px) {
    .input-row {
        grid-template-columns: 1fr;
    }
    
    .profile-card {
        margin: 1rem;
        max-width: none;
    }
    
    .modern-container {
        padding: 1rem 0;
    }
}
</style>
@endpush

@section('content')
<div class="modern-container">
    <div class="container">
        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar" id="avatar-preview">
                    @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Profile">
                    @else
                        <i class="fas fa-user text-white fa-2x"></i>
                    @endif
                </div>
                <h2 class="profile-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h2>
                <p class="profile-email">{{ Auth::user()->email }}</p>
            </div>

            <!-- Profile Body -->
            <div class="profile-body">
                @if(session('success'))
                    <div class="alert-modern alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-modern alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Profile Picture Section -->
                    <div class="form-section">
                        <div class="file-upload">
                            <input type="file" name="avatar" accept="image/*" class="file-input" id="avatar-input" onchange="previewImage(this)">
                            <label for="avatar-input" class="file-label">
                                <i class="fas fa-camera me-2"></i>Change Profile Picture
                            </label>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3 class="section-title">Personal Information</h3>
                        
                        <div class="input-row">
                            <input type="text" name="first_name" placeholder="First Name" class="modern-input" 
                                   value="{{ old('first_name', Auth::user()->first_name) }}" required>
                            <input type="text" name="last_name" placeholder="Last Name" class="modern-input" 
                                   value="{{ old('last_name', Auth::user()->last_name) }}" required>
                        </div>

                        <input type="email" name="email" placeholder="Email Address" class="modern-input" 
                               value="{{ old('email', Auth::user()->email) }}" required>
                        
                        <input type="tel" name="phone" placeholder="Phone Number" class="modern-input" 
                               value="{{ old('phone', Auth::user()->phone) }}">
                        
                        <input type="date" name="date_of_birth" class="modern-input" 
                               value="{{ old('date_of_birth', Auth::user()->date_of_birth) }}">

                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="gender" value="male" id="male" 
                                       {{ old('gender', Auth::user()->gender) == 'male' ? 'checked' : '' }}>
                                <label for="male">Male</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="gender" value="female" id="female" 
                                       {{ old('gender', Auth::user()->gender) == 'female' ? 'checked' : '' }}>
                                <label for="female">Female</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="gender" value="other" id="other" 
                                       {{ old('gender', Auth::user()->gender) == 'other' ? 'checked' : '' }}>
                                <label for="other">Other</label>
                            </div>
                        </div>
                    </div>

                    <!-- Email Address Section -->
                    <div class="form-section">
                        <h3 class="section-title">My Email Address</h3>
                        <div class="switch-container">
                            <label class="switch-label">Subscribe to newsletter for updates and special offers</label>
                            <label class="modern-switch">
                                <input type="checkbox" name="newsletter_subscription" value="1" 
                                       {{ old('newsletter_subscription', Auth::user()->newsletter_subscription) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="update-btn">
                        Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Profile">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
