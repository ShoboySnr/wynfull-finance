@extends('mail.layouts.base', [
  'title' => 'Reset Your Wynfull Password',
  'preheader' => 'Reset your password to regain access to your account.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        Reset Your Password
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        Hi {{ $user->name }}, you're receiving this email because we received a password reset request for your Wynfull account.
    </p>

    <p class="text" style="margin:0 0 16px; color:#374151; font-size:15px; line-height:22px;">
        Click the button below to reset your password. This link will expire in {{ $count }} minutes.
    </p>

    @include('mail.partials.button', [
      'url' => $resetUrl,
      'label' => 'Reset Password'
    ])

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;display:inline-block;">
        If you did not request a password reset, no further action is required. Your account remains secure.
    </p>

    <p class="text" style="margin:8px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
        <span style="color:#0E4DA4; word-break:break-all;">{{ $resetUrl }}</span>
    </p>
@endsection
