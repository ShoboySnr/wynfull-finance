@extends('layouts.auth')

@section('title', 'Reset Password')

@push('styles')
<style>
    .form-group input {
        width: 100%;
        padding: 0.875rem;
        border: 2px solid var(--border-light);
        border-radius: 8px;
        font-size: 0.9rem;
        transition: border-color 0.2s ease;
    }

    .password-field .input {
        padding-right: 44px;
    }

    .auth-card-wrap {
        display: grid; place-items: center;
        padding: 48px 24px;
        background: var(--background-light);
    }
    .auth-card {
        width: 100%; 
        max-width: 440px;
        animation: fadeIn 0.6s ease-out 0.2s;
        animation-fill-mode: both;
        padding: 30px;
    }
    .card-head { text-align: center; margin-bottom: 32px; }
    .card-head .logo {
        width: 56px; height: 56px; border-radius: 12px;
        background: var(--background-card); border: 1px solid var(--border-color);
        display: grid; place-items: center; overflow: hidden;
        box-shadow: var(--shadow-md); margin: 0 auto 16px;
    }

    .card-head .logo img {
        width: 56px;
    }
    .card-head h2 { margin: 0; font-size: 1.75rem; font-weight: 700; color: var(--text-primary); }
    .card-head p { margin: 8px 0 0; color: var(--text-secondary); }

    .form-group { margin-bottom: 18px; }
    .form-group label {
        display:block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px; font-size: 0.9rem;
    }

    .input {
        width: 100%; padding: 12px 16px;
        border: 1px solid var(--border-color); border-radius: 10px;
        background: var(--background-body); color: var(--text-primary);
        transition: border-color .2s ease, box-shadow .2s ease;
        font-size: 1rem;
    }
    
    .input:focus {
        outline: none;
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 3px rgba(10, 82, 161, 0.12);
        background: var(--background-card);
    }
    .input::placeholder { color: var(--text-muted); }

    /* Password field with eye icon */
    .password-field {
        position: relative;
    }
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .password-toggle:hover {
        color: var(--brand-blue);
    }
    .password-toggle svg {
        width: 18px;
        height: 18px;
    }
    .password-field .input {
        padding-right: 44px;
    }
</style>
@endpush

@section('content')
    <div class="auth-card">
        <div class="auth-card-header">
            <h2>Set a New Password</h2>
            <p>Please enter your new password and confirm it.</p>
        </div>
        <div class="auth-card-body">

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="input" value="{{ old('email', $email) }}" required readonly>
                </div>
                @error('email')
                <div class="alert alert-danger mt-3">
                    <span>{{ $message }}</span>
                </div>
                @enderror

                <!-- Password -->
                <div class="form-group mt-3">
                    <label for="password">New Password</label>
                    <div class="password-field">
                        <input id="password" name="password" type="password" class="input" required autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <svg id="password-eye-closed" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                            <svg id="password-eye-open" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                @error('password')
                <div class="alert alert-danger mt-3">
                    <span>{{ $message }}</span>
                </div>
                @enderror

                <!-- Confirm Password -->
                <div class="form-group mt-3">
                    <label for="password_confirmation">Confirm New Password</label>
                    <div class="password-field">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input" required autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <svg id="password_confirmation-eye-closed" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                            <svg id="password_confirmation-eye-open" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary mt-4 w-full">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeClosed = document.getElementById(fieldId + '-eye-closed');
            const eyeOpen = document.getElementById(fieldId + '-eye-open');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeClosed.style.display = 'none';
                eyeOpen.style.display = 'block';
            } else {
                passwordField.type = 'password';
                eyeClosed.style.display = 'block';
                eyeOpen.style.display = 'none';
            }
        }
    </script>
@endsection
