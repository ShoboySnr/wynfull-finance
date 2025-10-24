@extends('layouts.client')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <!-- Account Page -->
    <div class="" id="account">
        <div class="page-content">
            <div class="page-header">
                <h1>Account Settings</h1>
                <p>Manage your profile and preferences</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            <div class="account-sections">
                <div class="account-section">
                    <h2>Profile Information</h2>

                    {{-- Success flash --}}
                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <div class="profile-avatar-section">
                        <img src="{{ $profile->avatar_url ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&h=80&fit=crop&crop=face&auto=format' }}"
                             alt="Profile" class="profile-avatar">
                        <label class="btn-secondary mb-0" for="avatar">Change Photo</label>
{{--                        <input id="avatar" name="avatar" type="file" accept="image/*" class="d-none">--}}
                        @error('avatar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <form class="profile-form"
                          method="POST"
                          action="{{ route('profile.update') }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input id="first_name" name="first_name" type="text"
                                       value="{{ old('first_name', $profile->first_name) }}">
                                @error('first_name') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input id="last_name" name="last_name" type="text"
                                       value="{{ old('last_name', $profile->last_name) }}">
                                @error('last_name') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" name="email" type="email"
                                       value="{{ old('email', $profile->email) }}">
                                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input id="phone" name="phone" type="tel"
                                       value="{{ old('phone', $profile->phone) }}">
                                @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <button class="btn-primary mt-3" type="submit">Save Changes</button>
                    </form>
                </div>

                <div class="account-section">
                    <h2>Security</h2>
                    <div class="security-options">
                        <div class="security-item">
                            <div class="security-info">
                                <h3>Password</h3>
                                <p>Last changed 3 months ago</p>
                            </div>
                            <button class="btn-secondary" id="changePasswordBtn">Change Password</button>
                        </div>
                        <div class="security-item">
                            <div class="security-info">
                                <h3>Two-Factor Authentication</h3>
                                @if (auth()->user()->two_factor_secret)
                                    <p>2FA is currently <span class="status-enabled">Enabled</span> on your account.</p>
                                @else
                                    <p>Add an extra layer of security to protect your account.</p>
                                @endif
                            </div>
                            <button class="btn-secondary" id="manage2faBtn">
                                {{ auth()->user()->two_factor_secret ? 'Manage 2FA' : 'Enable 2FA' }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- START: Updated Notifications Section --}}
                <div class="account-section">
                    <h2>Notifications</h2>
                    <div class="notification-preferences">
                        <div class="notification-item">
                            <label class="switch">
                                {{-- Added ID and checked state --}}
                                <input type="checkbox" id="emailNotificationsToggle"
                                       name="email_notifications_enabled"
                                    {{ $user->email_notifications_enabled ?? true ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                            <div class="notification-info">
                                <h3>Email Notifications</h3>
                                <p>Receive updates about your account via email</p>
                            </div>
                        </div>
                        <div class="notification-item">
                            <label class="switch">
                                {{-- Added ID and checked state --}}
                                <input type="checkbox" id="goalRemindersToggle"
                                       name="goal_reminders_enabled"
                                    {{ $user->goal_reminders_enabled ?? true ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                            <div class="notification-info">
                                <h3>Goal Reminders</h3>
                                <p>Get reminders about your financial goals</p>
                            </div>
                        </div>
                    </div>
                    {{-- Optional: Add a small status message area --}}
                    <div id="notificationStatus" class="notification-status-message" style="margin-top: 1rem; font-size: 0.85rem; color: var(--text-secondary);"></div>
                </div>
                {{-- END: Updated Notifications Section --}}

                <div class="account-section danger-zone">
                    <div class="danger-actions">
                        <div class="danger-item">
                            <div class="danger-info">
                                <h3>Cancel Subscription</h3>
                                <p>Cancel your current plan</p>
                            </div>
                            <button class="btn-danger">Cancel Plan</button>
                        </div>
                        <div class="danger-item">
                            <div class="danger-info">
                                <h3>Delete Account</h3>
                                <p>Permanently delete your account and all data</p>
                            </div>
                            <button class="btn-danger">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- START: New Change Password Modal --}}
    <div class="modal-overlay" id="changePasswordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Change Password</h2>
                <button class="modal-close" id="changePasswordModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('account.client.update.password') }}" method="POST" id="changePasswordForm">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="changePasswordModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: New Change Password Modal --}}

    {{-- START: New Two-Factor Authentication Modal --}}
    <div class="modal-overlay" id="twoFactorModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Two-Factor Authentication</h2>
                <button class="modal-close" id="twoFactorModalClose">&times;</button>
            </div>
            <div class="modal-body">
                {{-- The content for 2FA settings will be loaded here from a partial --}}
                @include('client.partials._two-factor-settings')
            </div>
        </div>
    </div>
    {{-- END: New Two-Factor Authentication Modal --}}
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/client-account.js') }}"></script>
@endpush
