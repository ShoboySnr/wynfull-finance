@extends('layouts.auth')

@section('title', 'Two-Factor Challenge')

@section('content')
    <div class="auth-card-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <h2>Two-Factor Challenge</h2>
                <p>Please enter your authentication code to log in.</p>
            </div>
            <div class="auth-card-body">
                <form method="POST" action="/two-factor-challenge">
                    @csrf
                    <div class="form-group">
                        <label for="code">Authentication Code</label>
                        <input id="code" name="code" type="text" class="form-input" inputmode="numeric" autofocus autocomplete="one-time-code">
                    </div>

                    <p class="form-divider">or</p>

                    <div class="form-group">
                        <label for="recovery_code">Recovery Code</label>
                        <input id="recovery_code" name="recovery_code" type="text" class="form-input" autocomplete="one-time-code">
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            @foreach ($errors->all() as $error)
                                <span>{{ $error }}</span>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit" class="btn-primary mt-4 w-full">Verify & Log In</button>
                </form>
            </div>
        </div>
    </div>
@endsection
