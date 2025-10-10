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
                    <div class="profile-form">
                        <div class="profile-avatar-section">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&h=80&fit=crop&crop=face&auto=format" alt="Profile" class="profile-avatar">
                            <button class="btn-secondary">Change Photo</button>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" value="Femi">
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" value="Agboola">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="femi.agboola@wynfull.com">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" value="+1 (555) 123-4567">
                            </div>
                        </div>
                        <button class="btn-primary">Save Changes</button>
                    </div>
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

