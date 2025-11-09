@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="auth-card">
        <div class="auth-card-header">
            <h2>Forgot Your Password?</h2>
            <p>No problem. Just let us know your email address and we will email you a password reset link.</p>
        </div>
        <div class="auth-card-body">

            <!-- Session Status Message -->
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email') }}" required autofocus>
                </div>

                @error('email')
                <div class="alert alert-danger mt-3">
                    <span>{{ $message }}</span>
                </div>
                @enderror

                <button type="submit" class="btn-primary mt-4 w-full">
                    Email Password Reset Link
                </button>
            </form>
        </div>
    </div>
@endsection
