@extends('layouts.auth')

@section('title', 'Confirm Password')

@section('content')
    <div class="auth-card-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <h2>Confirm Password</h2>
                <p>For your security, please confirm your password to continue.</p>
            </div>
            <div class="auth-card-body">
                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    {{-- Password Input --}}
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" class="form-input" autocomplete="current-password" required autofocus>
                    </div>

                    {{-- Error Display --}}
                    @error('password')
                    <div class="alert alert-danger mt-3">
                        <span>{{ $message }}</span>
                    </div>
                    @enderror

                    {{-- Submit Button --}}
                    <button type="submit" class="btn-primary mt-4 w-full">Confirm</button>
                </form>
            </div>
        </div>
    </div>
@endsection
