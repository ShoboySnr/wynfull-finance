@extends('mail.layouts.base', [
  'title' => 'Your Wynfull account is active',
  'preheader' => 'Your account has been activated. Set your password to sign in.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        Your Account is Now Active! 🎉
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        Hi {{ $user->name }}, great news! Your Wynfull Finance account has been activated and you're ready to start your financial journey.
    </p>

    <p class="text" style="margin:0 0 16px; color:#374151; font-size:15px; line-height:22px;">
        For security, please set your password now to access your personalized dashboard and begin building your financial skills.
    </p>

    @include('mail.partials.button', [
      'url' => $resetUrl,
      'label' => 'Set Password & Get Started'
    ])

    <div style="margin:24px 0; padding:16px; background-color:#F8F9FA; border-radius:8px; border-left:4px solid #0E4DA4;display:inline-block;">
        <h3 style="margin:0 0 8px; color:#1F2937; font-size:16px; font-weight:600;">What's Next?</h3>
        <ul style="margin:0; padding-left:20px; color:#374151; font-size:14px; line-height:20px;">
            <li style="margin-bottom:4px;">Set your secure password</li>
            <li style="margin-bottom:4px;">Complete your financial profile</li>
            <li style="margin-bottom:4px;">Explore your resource library</li>
            <li>Connect with your assigned coach</li>
        </ul>
    </div>

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        If you did not request this account, no action is required.
    </p>
@endsection
