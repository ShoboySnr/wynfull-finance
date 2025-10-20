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
                            <button class="btn-secondary">Change Password</button>
                        </div>
                        <div class="security-item">
                            <div class="security-info">
                                <h3>Two-Factor Authentication</h3>
                                <p>Add an extra layer of security</p>
                            </div>
                            <button class="btn-secondary">Enable 2FA</button>
                        </div>
                    </div>
                </div>

                <div class="account-section">
                    <h2>Notifications</h2>
                    <div class="notification-preferences">
                        <div class="notification-item">
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                            <div class="notification-info">
                                <h3>Email Notifications</h3>
                                <p>Receive updates about your account via email</p>
                            </div>
                        </div>
                        <div class="notification-item">
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                            <div class="notification-info">
                                <h3>Goal Reminders</h3>
                                <p>Get reminders about your financial goals</p>
                            </div>
                        </div>
                    </div>
                </div>

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
@endsection

