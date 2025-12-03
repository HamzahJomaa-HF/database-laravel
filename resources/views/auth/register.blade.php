{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Hariri Foundation</title>
    
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: url("{{ asset('HFBackground.png') }}") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .register-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .register-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            transform: skewY(-5deg);
            transform-origin: top left;
        }
        
        .brand-logo {
            position: relative;
            z-index: 1;
            margin-bottom: 15px;
        }
        
        .brand-logo i {
            font-size: 3rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .brand-logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .brand-logo p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 5px 0 0;
        }
        
        .register-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .input-group-prepended .form-control {
            border-left: 1px solid #dee2e6;
            border-radius: 8px;
        }
        
        .input-group-prepended .input-group-text {
            border-right: 1px solid #dee2e6;
            border-radius: 8px 0 0 8px;
        }
        
        .form-control {
            border: 1px solid #dee2e6;
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
            border-color: #1a237e;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #283593 0%, #3949ab 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3);
        }
        
        .text-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem;
        }
        
        .register-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 15px 15px;
        }
        
        .password-strength {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { background: #dc3545; }
        .strength-fair { background: #ffc107; }
        .strength-good { background: #17a2b8; }
        .strength-strong { background: #28a745; }
        
        .form-check-input:checked {
            background-color: #1a237e;
            border-color: #1a237e;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            z-index: 10;
        }
        
        @media (max-width: 576px) {
            .register-card {
                margin: 10px;
                padding: 0;
            }
            
            .register-body {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="brand-logo">
                    <i class="fas fa-university"></i>
                    <h1>Hariri Foundation</h1>
                    <p>Create New Account</p>
                </div>
            </div>
            
            <div class="register-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-3"></i>
                            <div>
                                <strong>Registration Failed</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-label" for="name">
                                    <i class="fas fa-user me-1"></i> Full Name
                                </label>
                                <div class="input-group input-group-prepended">
                                    <input type="text" 
                                           class="form-control form-control-prepended @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="John Doe" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-label" for="username">
                                    <i class="fas fa-at me-1"></i> Username
                                </label>
                                <div class="input-group input-group-prepended">
                                    <input type="text" 
                                           class="form-control form-control-prepended @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username') }}" 
                                           placeholder="johndoe" 
                                           required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Letters, numbers, dashes only</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="text-label" for="email">
                            <i class="fas fa-envelope me-1"></i> Email Address
                        </label>
                        <div class="input-group input-group-prepended">
                            <input type="email" 
                                   class="form-control form-control-prepended @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="john@example.com" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-label" for="password">
                                    <i class="fas fa-lock me-1"></i> Password
                                </label>
                                <div class="input-group input-group-prepended">
                                    <input type="password" 
                                           class="form-control form-control-prepended @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Create password" 
                                           required>
                                    <button class="toggle-password" type="button" data-target="password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="password-strength">
                                    <div class="strength-bar" id="passwordStrength"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-label" for="password_confirmation">
                                    <i class="fas fa-lock me-1"></i> Confirm Password
                                </label>
                                <div class="input-group input-group-prepended">
                                    <input type="password" 
                                           class="form-control form-control-prepended" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirm password" 
                                           required>
                                    <button class="toggle-password" type="button" data-target="password_confirmation">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input @error('terms') is-invalid @enderror" 
                                   id="terms" 
                                   name="terms" 
                                   required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and 
                                <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                            @error('terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-2">Already have an account?</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Existing Account
                    </a>
                </div>
            </div>
            
            <div class="register-footer">
                <p class="mb-0 small text-muted">
                    © {{ date('Y') }} Hariri Foundation Management System
                </p>
                <p class="small text-muted mt-1 mb-0">
                    <i class="fas fa-shield-alt me-1"></i>Secure Registration
                </p>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    
                    if (targetInput) {
                        const type = targetInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        targetInput.setAttribute('type', type);
                        this.innerHTML = type === 'password' ? 
                            '<i class="fas fa-eye"></i>' : 
                            '<i class="fas fa-eye-slash"></i>';
                    }
                });
            });
            
            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('passwordStrength');
            
            if (passwordInput && strengthBar) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    // Length check
                    if (password.length >= 8) strength += 25;
                    
                    // Contains lowercase
                    if (/[a-z]/.test(password)) strength += 25;
                    
                    // Contains uppercase
                    if (/[A-Z]/.test(password)) strength += 25;
                    
                    // Contains numbers
                    if (/[0-9]/.test(password)) strength += 25;
                    
                    // Update strength bar
                    strengthBar.style.width = strength + '%';
                    
                    // Update color
                    if (strength <= 25) {
                        strengthBar.className = 'strength-bar strength-weak';
                    } else if (strength <= 50) {
                        strengthBar.className = 'strength-bar strength-fair';
                    } else if (strength <= 75) {
                        strengthBar.className = 'strength-bar strength-good';
                    } else {
                        strengthBar.className = 'strength-bar strength-strong';
                    }
                });
            }
            
            // Form validation
            const form = document.getElementById('registerForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Check password match
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('password_confirmation').value;
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Passwords do not match!');
                        return false;
                    }
                    
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            }
            
            // Auto-focus on name input
            document.getElementById('name').focus();
        });
    </script>
</body>
</html>