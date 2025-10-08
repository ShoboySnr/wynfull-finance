@extends('mail.layouts.base', [
  'title' => 'Your Wynfull account is active',
  'preheader' => 'Your account has been activated. Set your password to sign in.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        Your account is now active
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        Hi {{ $user->name }}, your Wynfull account has been activated.
    </p>

    <p class="text" style="margin:0 0 16px; color:#374151; font-size:15px; line-height:22px;">
        For security, please set your password now to sign in.
    </p>

    @include('mail.partials.button', [
      'url' => $resetUrl,
      'label' => 'Set Password'
    ])

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        If you did not request this account, no action is required.
    </p>
@endsection
