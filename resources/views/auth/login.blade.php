{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hariri Foundation</title>
    
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
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .login-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
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
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #6c757d;
            padding: 0.75rem 1rem;
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
        
        .form-control-prepended {
            border-left: 1px solid #dee2e6;
            border-radius: 8px;
        }
        
        .input-group-prepended .input-group-text {
            border-right: 1px solid #dee2e6;
            border-radius: 8px 0 0 8px;
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
        
        .form-check-input:checked {
            background-color: #1a237e;
            border-color: #1a237e;
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
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 15px 15px;
        }
        
        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #6c757d;
        }
        
        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .separator span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }
        
        .social-login {
            display: flex;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        
        .social-login .btn {
            flex: 1;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
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
            .login-card {
                margin: 10px;
                padding: 0;
            }
            
            .login-body {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="brand-logo">
                    <i class="fas fa-university"></i>
                    <h1>Hariri Foundation</h1>
                    <p>Management System</p>
                </div>
            </div>
            
            <div class="login-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-3"></i>
                            <div>
                                <strong>Login Failed</strong>
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

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="text-label" for="login">
                            <i class="fas fa-user me-1"></i> Email or Username
                        </label>
                        <div class="input-group input-group-prepended">
                            <input type="text" 
                                   class="form-control form-control-prepended @error('login') is-invalid @enderror" 
                                   id="login" 
                                   name="login" 
                                   value="{{ old('login') }}" 
                                   placeholder="Enter email or username" 
                                   required 
                                   autofocus>
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="text-label" for="password">
                            <i class="fas fa-lock me-1"></i> Password
                        </label>
                        <div class="input-group input-group-prepended">
                            <input type="password" 
                                   class="form-control form-control-prepended @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password" 
                                   required>
                            <button class="toggle-password" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="remember" 
                                   name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none small">
                                <i class="fas fa-key me-1"></i>Forgot Password?
                            </a>
                        @endif
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                        </button>
                    </div>
                </form>

                <div class="separator">
                    <span>or</span>
                </div>

                <div class="social-login">
                    <a href="#" class="btn btn-light border">
                        <i class="fab fa-google text-danger me-2"></i>Google
                    </a>
                    <a href="#" class="btn btn-light border">
                        <i class="fab fa-microsoft text-primary me-2"></i>Microsoft
                    </a>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted mb-2">Don't have an account?</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </a>
                </div>
            </div>
            
            <div class="login-footer">
                <p class="mb-0 small text-muted">
                    © {{ date('Y') }} Hariri Foundation Management System
                </p>
                <p class="small text-muted mt-1 mb-0">
                    <i class="fas fa-shield-alt me-1"></i>Secure Login
                </p>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? 
                        '<i class="fas fa-eye"></i>' : 
                        '<i class="fas fa-eye-slash"></i>';
                });
            }
            
            // Form validation
            const form = document.getElementById('loginForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            }
            
            // Auto-focus on login input
            document.getElementById('login').focus();
        });
    </script>
</body>
</html>