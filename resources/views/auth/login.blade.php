@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    {{ __('Login with OTP') }}
                </div>

                <div class="card-body">
                    <!-- Messages Container -->
                    <div id="messageContainer"></div>

                    <!-- Login Form -->
                    <form id="loginForm">
                        @csrf
                        
                        <!-- Step 1: Email & Password -->
                        <div id="step1">
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <input id="email" type="email" 
                                       class="form-control" 
                                       name="email" 
                                       required 
                                       autocomplete="email" 
                                       autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input id="password" type="password" 
                                       class="form-control" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password">
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="button" id="sendOtpBtn" class="btn btn-primary">
                                    {{ __('Send OTP') }}
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('register') }}" class="btn btn-link">
                                    {{ __('Create New Account') }}
                                </a>
                            </div>
                        </div>

                        <!-- Step 2: OTP Verification (Hidden initially) -->
                        <div id="step2" style="display: none;">
                            <div class="alert alert-info" id="otpInfo">
                                <!-- OTP info will be inserted here -->
                            </div>

                            <div class="mb-3">
                                <label for="otp" class="form-label">{{ __('Enter OTP Code') }}</label>
                                <input id="otp" type="text" 
                                       class="form-control text-center" 
                                       name="otp" 
                                       maxlength="6"
                                       placeholder="000000"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6)">
                                <small class="text-muted">Enter the 6-digit code sent to your email</small>
                            </div>

                            <!-- Hidden fields for OTP verification -->
                            <input type="hidden" id="otpEmail" name="email">
                            <input type="hidden" id="otpToken" name="token">

                            <div class="row g-2 mb-3">
                                <div class="col">
                                    <button type="button" id="verifyOtpBtn" class="btn btn-success w-100">
                                        {{ __('Verify & Login') }}
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="button" id="resendOtpBtn" class="btn btn-outline-secondary w-100">
                                        {{ __('Resend OTP') }}
                                    </button>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" id="backToLoginBtn" class="btn btn-link">
                                    {{ __('Back to Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const backToLoginBtn = document.getElementById('backToLoginBtn');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const otpInfo = document.getElementById('otpInfo');
    const messageContainer = document.getElementById('messageContainer');
    
    let otpData = {}; // Store email, token, demoOtp
    
    // Show message
    function showMessage(message, type = 'info') {
        messageContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageContainer.firstChild) {
                messageContainer.firstChild.remove();
            }
        }, 5000);
    }
    
    // Show loading state
    function showLoading(button, text = 'Processing...') {
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm"></span> ${text}`;
        return originalText;
    }
    
    // Reset button
    function resetButton(button, originalText) {
        button.disabled = false;
        button.innerHTML = originalText;
    }
    
    // Send OTP
    sendOtpBtn.addEventListener('click', async function() {
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('password', document.getElementById('password').value);
        formData.append('send_otp', '1');
        
        const originalText = showLoading(this, 'Sending OTP...');
        
        try {
            const response = await fetch('{{ route("login") }}', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Store OTP data
                otpData = {
                    email: data.email,
                    token: data.token,
                    demoOtp: data.demo_otp
                };
                
                // Update hidden fields
                document.getElementById('otpEmail').value = data.email;
                document.getElementById('otpToken').value = data.token;
                
                // Show OTP info
                otpInfo.innerHTML = `
                    <p>OTP sent to: <strong>${data.email}</strong></p>
                    ${data.demo_otp ? 
                        `<p class="mb-0"><strong>Demo OTP:</strong> ${data.demo_otp}<br>
                        <small class="text-muted">Remove this in production</small></p>` 
                        : ''}
                `;
                
                // Show step 2
                step1.style.display = 'none';
                step2.style.display = 'block';
                
                // Focus on OTP input
                document.getElementById('otp').focus();
                
                showMessage('OTP sent successfully!', 'success');
            } else {
                showMessage(data.error || 'Failed to send OTP', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Network error. Please try again.', 'danger');
        } finally {
            resetButton(this, originalText);
        }
    });
    
    // Verify OTP
    verifyOtpBtn.addEventListener('click', async function() {
        const otpValue = document.getElementById('otp').value;
        
        if (otpValue.length !== 6) {
            showMessage('Please enter a 6-digit OTP', 'warning');
            return;
        }
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('email', document.getElementById('otpEmail').value);
        formData.append('token', document.getElementById('otpToken').value);
        formData.append('otp', otpValue);
        
        const originalText = showLoading(this, 'Verifying...');
        
        try {
            const response = await fetch('{{ route("login") }}', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage('Login successful! Redirecting...', 'success');
                
                // Redirect to dashboard
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                showMessage(data.error || 'Invalid OTP', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Network error. Please try again.', 'danger');
        } finally {
            resetButton(this, originalText);
        }
    });
    
    // Resend OTP
    resendOtpBtn.addEventListener('click', async function() {
        if (!otpData.email || !otpData.token) {
            showMessage('Session expired. Please login again.', 'warning');
            return;
        }
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('email', otpData.email);
        formData.append('token', otpData.token);
        formData.append('send_otp', '1');
        
        const originalText = showLoading(this, 'Resending...');
        
        try {
            const response = await fetch('{{ route("login") }}', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update OTP data
                otpData.demoOtp = data.demo_otp;
                
                // Update OTP info
                otpInfo.innerHTML = `
                    <p>New OTP sent to: <strong>${data.email}</strong></p>
                    ${data.demo_otp ? 
                        `<p class="mb-0"><strong>New Demo OTP:</strong> ${data.demo_otp}<br>
                        <small class="text-muted">Remove this in production</small></p>` 
                        : ''}
                `;
                
                showMessage('New OTP sent successfully!', 'success');
                
                // Re-enable resend button after 30 seconds
                setTimeout(() => {
                    resetButton(this, 'Resend OTP');
                }, 30000);
            } else {
                showMessage(data.error || 'Failed to resend OTP', 'danger');
                resetButton(this, 'Resend OTP');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Network error. Please try again.', 'danger');
            resetButton(this, 'Resend OTP');
        }
    });
    
    // Back to login
    backToLoginBtn.addEventListener('click', function() {
        step2.style.display = 'none';
        step1.style.display = 'block';
        document.getElementById('email').focus();
        showMessage('', 'info'); // Clear messages
    });
    
    // Auto-submit OTP when 6 digits entered
    document.getElementById('otp').addEventListener('input', function(e) {
        if (this.value.length === 6) {
            verifyOtpBtn.click();
        }
    });
});
</script>

<style>
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    margin-right: 0.5rem;
}

#otpInfo {
    background-color: #f8f9fa;
    border-left: 4px solid #0d6efd;
}

#messageContainer {
    margin-bottom: 1rem;
}
</style>
@endsection