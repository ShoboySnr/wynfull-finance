@extends('layouts.app')
@section('title', 'Coach Dashboard')

@section('content')
    <!-- Coach Profile Page -->
    <div class="" id="coach-profile">
        <div class="page-content">
            <div class="page-header">
                <h1>Coach Profile</h1>
                <p>Manage your professional profile and settings</p>
            </div>

            <div class="profile-sections">
                <div class="profile-section">
                    <h2>Professional Information</h2>
                    <div class="profile-form">
                        <div class="profile-avatar-section">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=80&h=80&fit=crop&crop=face&auto=format" alt="Profile" class="profile-avatar">
                            <button class="btn-secondary">Change Photo</button>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" value="{{ $profile['first_name'] }}">
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="{{ $profile['last_name'] }}">
                            </div>
                            <div class="form-group">
                                <label>Professional Title</label>
                                <input type="text" name="professional_title" value="{{ $profile['professional_title'] }}">
                            </div>
                            <div class="form-group">
                                <label>Specialties</label>
                                <input type="text" name="specialities" value="{{ $profile['specialities'] }}">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ $profile['specialities'] }}">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" value="{{ $profile['phone'] }}" name="phone">
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Bio</label>
                            <textarea rows="4" name="bio" placeholder="Tell clients about your background and expertise...">{{ $profile['bio'] }}</textarea>
                        </div>
                        <button class="btn-primary">Save Changes</button>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Availability Settings</h2>
                    <div class="availability-settings">
                        <div class="availability-item">
                            <label>Working Hours</label>
                            <div class="time-inputs">
                                <input type="time" value="09:00">
                                <span>to</span>
                                <input type="time" value="17:00">
                            </div>
                        </div>
                        <div class="availability-item">
                            <label>Time Zone</label>
                            <select>
                                <option>Eastern Time (ET)</option>
                                <option>Central Time (CT)</option>
                                <option>Mountain Time (MT)</option>
                                <option>Pacific Time (PT)</option>
                            </select>
                        </div>
                        <div class="availability-item">
                            <label>Session Duration</label>
                            <select>
                                <option>30 minutes</option>
                                <option selected>60 minutes</option>
                                <option>90 minutes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Coaching Preferences</h2>
                    <div class="preferences-list">
                        <div class="preference-item">
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                            <div class="preference-info">
                                <h3>Auto-accept new clients</h3>
                                <p>Automatically accept new client requests within your capacity</p>
                            </div>
                        </div>
                        <div class="preference-item">
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                            <div class="preference-info">
                                <h3>Email notifications for messages</h3>
                                <p>Receive email alerts when clients send messages</p>
                            </div>
                        </div>
                        <div class="preference-item">
                            <label class="switch">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                            <div class="preference-info">
                                <h3>Weekend availability</h3>
                                <p>Allow clients to book weekend sessions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="account-section danger-zone">
                    <div class="danger-actions">
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

@push('scripts')
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/coach-script.js') }}"></script>
@endpush
