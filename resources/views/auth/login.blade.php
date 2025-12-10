@extends('layouts.guest')

@section('title', 'Sign in — Wynfull Finance')

@section('content')
    <style>

        :root {
            --brand-blue: #0A52A1;
            --brand-blue-light: #E7F0F9;
            --brand-blue-dark: #073B73;
            --background-body: #F8F9FA;
            --background-card: #FFFFFF;
            --background-light: #F8F9FA;
            --text-primary: #1D2939;
            --text-secondary: #475467;
            --text-muted: #98A2B3;
            --text-light: #F8F9FA;
            --border-color: #EAECF0;
            --accent-green: #059669;
            --accent-green-light: #ECFDF5;
            --error-red: #D92D20;
            --error-red-light: #FEF3F2;

            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.2), 0 8px 10px -6px rgb(0 0 0 / 0.2);

            --font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        /* Dark mode variables */
        [data-theme="dark"] {
            --background-body: #0F172A;
            --background-card: #1E293B;
            --background-light: #1E293B;
            --text-primary: #F1F5F9;
            --text-secondary: #CBD5E1;
            --text-muted: #64748B;
            --border-color: #334155;
            --accent-green-light: #064E3B;
            --error-red-light: #7F1D1D;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body {
            font-family: var(--font-family-sans);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--background-body);
        }

        .auth-wrap {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr;
        }

        .auth-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }
        @media (min-width: 1024px) {
            .auth-grid {
                grid-template-columns: 1.1fr 0.9fr;
                min-height: 100vh;
            }
        }

        /* --- Left: Brand / Hero --- */
        .auth-hero {
            position: relative;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            background-image: url('https://images.unsplash.com/photo-1624996752380-8ec242e0f85d?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=1600');
            background-size: cover;
            background-position: center;
            color: var(--text-light);
            animation: fadeIn 0.8s ease-out;
        }
        .auth-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(10, 82, 161, 0.85);
            backdrop-filter: blur(4px);
            z-index: 1;
        }
        .auth-hero > * {
            position: relative;
            z-index: 2;
        }

        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none;}
        .brand .logo {
            width: 48px; height: 48px; border-radius: 12px;
            background: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.2);
            display: grid; place-items: center; overflow: hidden;
        }
        .brand .logo img { width: 100%; height: 100%; object-fit: cover; }
        .brand h1 { margin: 0; font-size: 1.5rem; color: #fff; }
        .brand small { display:block; margin-top: 2px; color: #fff; opacity: 0.8; font-weight: 500; font-size: 0.9rem; }

        .hero-copy { margin: 48px 0; }
        .hero-title {
            font-size: clamp(1.8rem, 2.4vw, 2.4rem);
            line-height: 1.3;
            color: #fff;
            margin-bottom: 16px;
            font-weight: 700;
        }
        .hero-sub { color: #fff; opacity: 0.85; font-size: 1.1rem; max-width: 50ch; line-height: 1.6; }
        .hero-cards {
            margin-top: 40px;
            display: grid; gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .h-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px; padding: 16px; display:flex; gap:14px; align-items: center;
            transition: background 0.2s ease;
        }
        .h-card:hover { background: rgba(255, 255, 255, 0.15); }
        .h-icon {
            width: 40px; height: 40px; border-radius: 50%;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            display: grid; place-items: center;
        }
        .h-icon svg { width: 20px; height: 20px; }
        .h-text h4 { margin: 0 0 4px 0; font-size: 0.95rem; color: #fff; font-weight: 600; }
        .h-text p { margin: 0; font-size: 0.9rem; color: #fff; opacity: 0.8; }

        /* --- Right: Auth Card --- */
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

        .form-row { display:flex; justify-content: space-between; align-items: center; gap: 12px; }
        .remember {
            display:flex; align-items:center; gap:8px; color: var(--text-secondary); font-size: 0.9rem; cursor: pointer;
        }
        .remember input { accent-color: var(--brand-blue); }
        .forgot a { color: var(--brand-blue); text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        .forgot a:hover { text-decoration: underline; }

        .btn {
            width: 100%; display:inline-flex; justify-content:center; align-items:center; gap:8px;
            padding: 12px 16px; border-radius: 10px; border: 1px solid transparent;
            font-weight: 600; cursor:pointer; font-size: 1rem;
            transition: all .2s ease;
        }
        .btn svg { width: 18px; height: 18px; }

        .btn-primary {
            background: var(--brand-blue); color:#fff;
            box-shadow: var(--shadow-md);
        }
        .btn-primary:hover {
            background: var(--brand-blue-dark);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        .btn-primary:active { transform: translateY(-1px); }


        .btn-outline {
            background:var(--background-card); color: var(--text-primary);
            border-color: var(--border-color);
        }
        .btn-outline:hover { background: var(--brand-blue-light); border-color: var(--brand-blue); }

        .divider {
            display:flex; align-items:center; gap:16px; color: var(--text-muted);
            font-size: .85rem; margin: 24px 0;
        }
        .divider::before, .divider::after {
            content:""; height:1px; flex:1; background: var(--border-color);
        }

        .meta { margin-top: 24px; text-align: center; color: var(--text-secondary); font-size: .9rem; }
        .meta a { color: var(--brand-blue); font-weight: 600; text-decoration: none; }
        .meta a:hover { text-decoration: underline; }

        .alert {
            border-radius: 10px; padding: 12px 16px; font-size: .9rem; margin-bottom: 20px;
            border: 1px solid var(--error-red); background: var(--error-red-light); color: var(--error-red);
            display: flex; gap: 12px; align-items: flex-start;
        }
        .alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 2px; }
        .alert.success {
            border-color: var(--accent-green); background: var(--accent-green-light); color: var(--accent-green);
        }

        /* Theme Toggle Button */
        .theme-toggle-guest {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: var(--text-secondary);
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            z-index: 9999;
        }

        .theme-toggle-guest:hover {
            background: var(--brand-blue);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(10, 82, 161, 0.3);
        }

        .theme-toggle-guest i {
            font-size: 1.1rem;
        }

        /* Dark mode styling for theme toggle */
        [data-theme="dark"] .theme-toggle-guest {
            background: rgba(31, 41, 55, 0.9);
            border-color: rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
        }

        [data-theme="dark"] .theme-toggle-guest:hover {
            background: var(--brand-blue);
            color: white;
        }

        /* --- Mobile Responsive --- */
        @media (max-width: 1023px) {
            .auth-hero {
                display: none;
            }
            .auth-card-wrap {
                padding: 40px 20px;
                min-height: 100vh;
            }

            .auth-card {
                padding: 20px;
            }
            
            .theme-toggle-guest {
                top: 1rem;
                right: 1rem;
                width: 44px;
                height: 44px;
                padding: 0.6rem;
            }
        }
    </style>

    <!-- Theme Toggle Button -->
    <button class="theme-toggle-guest" id="themeToggle" title="Toggle Dark/Light Mode">
        <i class="fas fa-moon"></i>
    </button>

    <div class="auth-wrap">
        <div class="auth-grid">

            {{-- Left / Brand --}}
            <section class="auth-hero">
                <div>
                    <a href="/" class="brand">
                        <div class="logo">
                            <img src="{{ asset('assets/img/wynfull-logo.png') }}" alt="Wynfull Finance Logo">
                        </div>
                        <div>
                            <h1>Wynfull</h1>
                            <small>Finance</small>
                        </div>
                    </a>

                    <div class="hero-copy">
                        <h2 class="hero-title">Build real financial skills. <br>Sign in to continue your journey.</h2>
                        <p class="hero-sub">
                            Access your personalized dashboard, coaching messages, and resource library.
                            Celebrate micro-wins and keep your momentum.
                        </p>

                        <div class="hero-cards">
                            <div class="h-card">
                                <div class="h-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10" /><line x1="18" y1="20" x2="18" y2="4" /><line x1="6" y1="20" x2="6" y2="16" /></svg></div>
                                <div class="h-text"><h4>Action-first</h4><p>Track progress and micro-wins.</p></div>
                            </div>
                            <div class="h-card">
                                <div class="h-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg></div>
                                <div class="h-text"><h4>Coach Support</h4><p>Accountability and guidance.</p></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="font-size:.9rem; opacity: 0.8;">
                    © {{ date('Y') }} Wynfull Finance. All rights reserved.
                </div>
            </section>

            {{-- Right / Auth Card --}}
            <section class="auth-card-wrap">
                <div class="auth-card">
                    <div class="card-head">
                        <div class="logo">
                            <a href="/">
                                <img src="{{ asset('assets/img/wynfull-logo.png') }}" alt="Wynfull Finance Logo">
                            </a>
                        </div>
                        <h2>Welcome back</h2>
                        <p>Please enter your details to sign in.</p>
                    </div>
                    <div class="card-body">

                        {{-- Status / Errors --}}
                        @if (session('status'))
                            <div class="alert success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                <div>{{ session('status') }}</div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <div>
                                    <strong style="display:block; margin-bottom: 4px;">We couldn’t sign you in.</strong>
                                    <ul style="margin:0 0 0 18px; padding:0; list-style-position: outside;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" novalidate>
                            @csrf

                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input id="email" type="email" name="email" class="input"
                                       value="{{ old('email') }}" required autofocus
                                       placeholder="you@example.com" autocomplete="username">
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="password-field">
                                    <input id="password" type="password" name="password" class="input"
                                           required placeholder="••••••••" autocomplete="current-password">
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

                            <div class="form-row" style="margin-bottom: 24px;">
                                <label class="remember">
                                    <input type="hidden" name="remember" value="0">
                                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                    <span>Remember me</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <div class="forgot">
                                        <a href="{{ route('password.request') }}">Forgot password?</a>
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" /><polyline points="10 17 15 12 10 7" /><line x1="15" y1="12" x2="3" y2="12" /></svg>
                                Sign in
                            </button>

                            {{-- OAuth (optional) --}}
                            @if (Route::has('oauth.redirect'))
                                <div class="divider">or</div>
                                <div style="display:grid; gap:12px;">
                                    <a href="{{ route('oauth.redirect', ['provider' => 'google']) }}" class="btn btn-outline">
                                        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_64dp.png"
                                             alt="Google logo" width="18" height="18">
                                        Sign in with Google
                                    </a>
                                </div>
                            @endif

                        </form>
                    </div>
                </div>
            </section>

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

@push('styles')
    <style>
        /* Theme Toggle Button for Login Page */
        .theme-toggle-guest {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: var(--text-secondary);
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            z-index: 9999;
        }

        .theme-toggle-guest:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        [data-theme="dark"] .theme-toggle-guest {
            background: rgba(30, 41, 59, 0.9);
            border-color: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .theme-toggle-guest {
                top: 1rem;
                right: 1rem;
                width: 44px;
                height: 44px;
                padding: 0.65rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Theme initialization for login page
        (function() {
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const systemTheme = systemPrefersDark ? 'dark' : 'light';
            const savedTheme = localStorage.getItem('wynfullTheme') || systemTheme;
            
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.body.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
@endpush


