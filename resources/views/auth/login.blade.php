@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                @if($otp_step ?? false)
                    <!-- OTP Verification Step -->
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ __('Verify OTP') }}</span>
                            <form method="POST" action="{{ route('cancel.otp') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    Cancel
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-muted mb-4">
                            We've sent a 6-digit OTP to your email/phone for 
                            <strong>{{ session('pending_email') ?? $email ?? '' }}</strong>
                        </p>
                        
                        @if (session('demo_otp'))
                            <div class="alert alert-info">
                                <strong>Demo OTP:</strong> {{ session('demo_otp') }}
                                <br><small class="text-muted">Remove this in production</small>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('pending_email') ?? $email ?? '' }}">
                            
                            <div class="mb-3">
                                <label for="otp" class="form-label">{{ __('Enter OTP') }}</label>
                                <div class="input-group">
                                    <input id="otp" type="text" 
                                           class="form-control text-center @error('otp') is-invalid @enderror" 
                                           name="otp" 
                                           maxlength="6"
                                           placeholder="000000"
                                           required 
                                           autofocus
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6)">
                                    <button class="btn btn-outline-secondary" type="button" id="resendOtpBtn">
                                        Resend
                                    </button>
                                </div>
                                @error('otp')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-text">
                                    OTP expires in <span id="otpTimer">10:00</span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verify & Login') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Email/Password Step -->
                    <div class="card-header">{{ __('Login') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                                <a href="{{ route('register') }}" class="btn btn-link">
                                    {{ __('Register new account') }}
                                </a>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($otp_step ?? false)
<script>
    // OTP Timer
    let otpTime = 10 * 60; // 10 minutes in seconds
    
    function updateTimer() {
        const minutes = Math.floor(otpTime / 60);
        const seconds = otpTime % 60;
        document.getElementById('otpTimer').textContent = 
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (otpTime <= 0) {
            clearInterval(timerInterval);
            document.getElementById('otpTimer').textContent = 'Expired';
            document.getElementById('resendOtpBtn').disabled = false;
        } else {
            otpTime--;
        }
    }
    
    let timerInterval = setInterval(updateTimer, 1000);
    
    // Auto focus next input (for better UX)
    document.getElementById('otp').addEventListener('input', function(e) {
        if (this.value.length === 6) {
            this.form.submit();
        }
    });
    
    // Resend OTP functionality
    document.getElementById('resendOtpBtn').addEventListener('click', function() {
        this.disabled = true;
        
        fetch('{{ route("resend.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset timer
                otpTime = 10 * 60;
                updateTimer();
                
                // Show new OTP (demo only)
                alert('New OTP: ' + data.demo_otp + '\n(In production, this would be sent via email/SMS)');
                
                // Re-enable button after 30 seconds
                setTimeout(() => {
                    this.disabled = false;
                }, 30000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.disabled = false;
        });
    });
</script>
@endif
@endsection