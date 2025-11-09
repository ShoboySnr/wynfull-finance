@extends('layouts.auth')

@section('title', 'Reset Password')

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
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $email) }}" required readonly>
                </div>
                @error('email')
                <div class="alert alert-danger mt-3">
                    <span>{{ $message }}</span>
                </div>
                @enderror

                <!-- Password -->
                <div class="form-group mt-3">
                    <label for="password">New Password</label>
                    <input id="password" name="password" type="password" class="form-input" required autocomplete="new-password">
                </div>
                @error('password')
                <div class="alert alert-danger mt-3">
                    <span>{{ $message }}</span>
                </div>
                @enderror

                <!-- Confirm Password -->
                <div class="form-group mt-3">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn-primary mt-4 w-full">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
@endsection
