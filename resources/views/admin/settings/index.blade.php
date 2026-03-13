@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div class="settings-container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="settings-card">
            <div class="settings-header">
                <h2><i class="fas fa-cog"></i> Global Settings</h2>
                <p>Manage application-wide settings and configurations</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="settings-form">
                @csrf

                <!-- Onboarding Schedule Section -->
                <div class="settings-section">
                    <div class="section-header">
                        <h3><i class="fas fa-calendar-check"></i> Client Onboarding Schedule</h3>
                    </div>

                    <div class="form-group">
                        <label for="next_onboarding_date">
                            Next Onboarding Date
                            <span class="optional-label">(Optional)</span>
                        </label>
                        
                        @if($nextOnboardingDate)
                            <div class="alert alert-info" style="margin-bottom: 1rem;">
                                <i class="fas fa-info-circle"></i>
                                <strong>Currently Scheduled:</strong> {{ $nextOnboardingDate->format('M d, Y') }}
                                <br>
                            </div>
                        @endif

                        <input 
                            type="date" 
                            name="next_onboarding_date" 
                            id="next_onboarding_date" 
                            class="form-control"
                            min="{{ now()->format('Y-m-d') }}"
                            value="{{ old('next_onboarding_date', $nextOnboardingDate?->format('Y-m-d')) }}">
                        
                        <small class="form-text">
                            Leave empty to clear the schedule. Set a future date to schedule onboarding for all clients.
                        </small>
                    </div>
                </div>

                <div class="settings-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .settings-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .settings-card {
            background: var(--card-bg, #fff);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .settings-header {
            padding: 2rem;
            border-bottom: 1px solid var(--border-color, #e5e7eb);
        }

        .settings-header h2 {
            margin: 0 0 0.5rem 0;
            color: var(--text-primary, #1f2937);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .settings-header h2 i {
            color: var(--wynfull-blue, #0E4DA4);
        }

        .settings-header p {
            margin: 0;
            color: var(--text-secondary, #6b7280);
        }

        .settings-form {
            padding: 2rem;
        }

        .settings-section {
            margin-bottom: 2rem;
        }

        .section-header {
            margin-bottom: 1.5rem;
        }

        .section-header h3 {
            margin: 0 0 0.5rem 0;
            color: var(--text-primary, #1f2937);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
        }

        .section-description {
            margin: 0;
            color: var(--text-secondary, #6b7280);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary, #1f2937);
        }

        .optional-label {
            color: var(--text-secondary, #6b7280);
            font-weight: 400;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color, #d1d5db);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--wynfull-blue, #0E4DA4);
            box-shadow: 0 0 0 3px rgba(14, 77, 164, 0.1);
        }

        .form-text {
            display: block;
            margin-top: 0.5rem;
            color: var(--text-secondary, #6b7280);
            font-size: 0.875rem;
        }

        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 1rem;
            border-radius: 8px;
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .info-box i {
            color: #0ea5e9;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .info-box strong {
            color: #0c4a6e;
        }

        .info-box ul {
            color: #0c4a6e;
        }

        .settings-actions {
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color, #e5e7eb);
            display: flex;
            justify-content: flex-end;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .alert-info {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }

        /* Dark mode support */
        [data-theme="dark"] .settings-card {
            background: var(--card-bg-dark, #1f2937);
        }

        [data-theme="dark"] .settings-header {
            border-bottom-color: var(--border-color-dark, #374151);
        }

        [data-theme="dark"] .settings-header h2,
        [data-theme="dark"] .section-header h3,
        [data-theme="dark"] .form-group label {
            color: var(--text-primary-dark, #f9fafb);
        }

        [data-theme="dark"] .settings-header p,
        [data-theme="dark"] .section-description,
        [data-theme="dark"] .form-text {
            color: var(--text-secondary-dark, #d1d5db);
        }

        [data-theme="dark"] .form-control {
            background: var(--input-bg-dark, #374151);
            border-color: var(--border-color-dark, #4b5563);
            color: var(--text-primary-dark, #f9fafb);
        }

        [data-theme="dark"] .settings-actions {
            border-top-color: var(--border-color-dark, #374151);
        }
    </style>
@endsection
