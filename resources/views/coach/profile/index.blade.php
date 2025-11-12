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

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="profile-sections">
                <div class="profile-section">
                    <h2>Professional Information</h2>
                    <div class="profile-form">
                        <div class="profile-avatar-section">
                            <img src="{{ $profile['avatar_url'] ? asset($profile['avatar_url']) : 'https://placehold.co/40x40/EBF0FF/0E4DA4?text=' . strtoupper(substr($authUser->name, 0, 1)) }}" alt="Profile" class="profile-avatar">
                            <button type="button" class="btn-secondary" id="changePhotoBtn">Change Photo</button>
                        </div>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
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
                                    <input type="email" name="email" value="{{ $authUser->email }}" readonly>
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
                        </form>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Availability Settings</h2>
                    <form action="{{ route('coach.availability.update') }}" method="POST"  >
                        @csrf
                        <div class="availability-settings">
                            <div class="availability-item">
                                <label>Working Hours</label>
                                <div class="time-inputs">
                                    <input type="time" name="work_start_local" value="{{ old('work_start_local', optional($settings?->work_start_local)->format('H:i')) }}">
                                    @error('work_start_local') <div class="text-danger">{{ $message }}</div> @enderror
                                    <span>to</span>
                                    <input type="time" name="work_end_local" value="{{ old('work_end_local', optional($settings?->work_end_local)->format('H:i')) }}">
                                    @error('work_end_local') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="availability-item">
                                <label>Time Zone</label>
                                <input type="text" name="timezone"
                                       value="{{ old('timezone', $settings->timezone ?? config('app.timezone')) }}"
                                       placeholder="e.g. Europe/Amsterdam">
                                @error('timezone') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="availability-item">
                                <label>Session Duration</label>
                                <input type="number" min="15" step="5" max="240" name="session_duration_minutes" class="form-control"
                                value="{{ old('session_duration_minutes', $settings->session_duration_minutes ?? 60) }}">
                                @error('session_duration_minutes') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <button class="btn-primary" style="margin-top: 15px">Update</button>
                    </form>
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

    {{-- START: New Avatar Upload Modal --}}
    <div class="modal-overlay" id="avatarModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Upload New Photo</h2>
                <button class="modal-close" id="avatarModalClose">&times;</button>
            </div>
            <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="avatar-upload-container">
                        <div class="avatar-preview-wrapper">
                            <img src="{{ $profile['avatar_url'] ?? 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&h=150&fit=crop&crop=face&auto=format' }}" id="avatarPreview" alt="Avatar Preview">
                        </div>
                        <div class="file-drop-area" id="avatarDropArea">
                            <i class="fas fa-camera"></i>
                            <p>Drag & drop image, or <span class="file-browse-link">browse</span></p>
                            <input type="file" name="avatar" id="avatarInput" class="file-input" accept="image/png, image/jpeg, image/jpg">
                        </div>
                        <p class="avatar-upload-hint">For best results, upload a square image (PNG or JPG).</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="avatarModalCancel">Cancel</button>
                    <button type="submit" class="btn-primary">Save Photo</button>
                </div>
            </form>
        </div>
    </div>
    {{-- END: New Avatar Upload Modal --}}
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/coach-script.js') }}"></script>
    <script src="{{ asset('assets/js/profile.js') }}"></script>
@endpush
