@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="first_name" class="col-md-4 col-form-label text-md-end">{{ __('First Name') }} *</label>

                            <div class="col-md-6">
                                <input id="first_name" type="text" 
                                       class="form-control @error('first_name') is-invalid @enderror" 
                                       name="first_name" 
                                       value="{{ old('first_name') }}" 
                                       required 
                                       autocomplete="given-name" 
                                       autofocus>

                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="last_name" class="col-md-4 col-form-label text-md-end">{{ __('Last Name') }} *</label>

                            <div class="col-md-6">
                                <input id="last_name" type="text" 
                                       class="form-control @error('last_name') is-invalid @enderror" 
                                       name="last_name" 
                                       value="{{ old('last_name') }}" 
                                       required 
                                       autocomplete="family-name">

                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }} *</label>

                            <div class="col-md-6">
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    We'll never share your email with anyone else.
                                </small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone" class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }} *</label>

                            <div class="col-md-6">
                                <input id="phone" type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       required 
                                       autocomplete="tel">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Format: +1234567890 or 1234567890
                                </small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }} *</label>

                            <div class="col-md-6">
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div id="password-strength" class="password-strength mt-1"></div>
                                <small class="form-text text-muted">
                                    Must be at least 6 characters long. Include uppercase, lowercase, numbers, and symbols for better security.
                                </small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }} *</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" 
                                       class="form-control" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password">
                                <div id="password-match" class="password-match mt-1"></div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Create Account') }}
                                </button>

                                <div class="mt-3">
                                    <a class="btn btn-link p-0" href="{{ route('login') }}">
                                        {{ __('Already have an account? Login here') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6 offset-md-4">
                                <p class="text-muted small">
                                    <strong>Note:</strong> All fields marked with * are required.
                                    After registration, you'll be automatically logged in and redirected to your dashboard.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthDiv = document.getElementById('password-strength');
        
        let strength = 0;
        let message = '';
        let color = 'secondary';
        
        // Check length
        if (password.length >= 8) strength++;
        
        // Check for uppercase
        if (/[A-Z]/.test(password)) strength++;
        
        // Check for lowercase
        if (/[a-z]/.test(password)) strength++;
        
        // Check for numbers
        if (/[0-9]/.test(password)) strength++;
        
        // Check for special characters
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        // Determine message and color
        switch(strength) {
            case 0:
            case 1:
                message = 'Very Weak';
                color = 'danger';
                break;
            case 2:
                message = 'Weak';
                color = 'warning';
                break;
            case 3:
                message = 'Fair';
                color = 'info';
                break;
            case 4:
                message = 'Strong';
                color = 'primary';
                break;
            case 5:
                message = 'Very Strong';
                color = 'success';
                break;
        }
        
        if (password.length === 0) {
            strengthDiv.innerHTML = '';
        } else {
            strengthDiv.innerHTML = `<span class="badge bg-${color}">${message}</span>`;
        }
        
        // Check password match
        checkPasswordMatch();
    });

    // Password confirmation check
    document.getElementById('password-confirm').addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password-confirm').value;
        const matchDiv = document.getElementById('password-match');
        
        if (confirmPassword.length === 0) {
            matchDiv.innerHTML = '';
            return;
        }
        
        if (password === confirmPassword) {
            matchDiv.innerHTML = '<span class="badge bg-success">Passwords match</span>';
        } else {
            matchDiv.innerHTML = '<span class="badge bg-danger">Passwords do not match</span>';
        }
    }

    // Phone number formatting - simplified
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        
        // Format as + followed by numbers if not already starting with +
        if (value.length > 0 && !this.value.startsWith('+')) {
            // Optional: You can auto-add country code prefix
            // For example, auto-add +1 for US numbers
            // if (value.length <= 10) {
            //     value = '+1' + value;
            // }
        }
        
        this.value = value;
    });

    // Auto focus first name on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('first_name').focus();
    });
</script>

<style>
    .password-strength, .password-match {
        font-size: 0.85rem;
    }
    
    .password-strength .badge,
    .password-match .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        font-weight: 500;
    }
    
    .form-text.text-muted {
        font-size: 0.85rem;
        line-height: 1.4;
    }
    
    .btn-lg {
        padding: 0.5rem 2rem;
        font-weight: 500;
    }
</style>
@endsection