@extends('mail.layouts.base', [
  'title' => 'Verify Your Email Address',
  'preheader' => 'Please verify your email address to complete your Wynfull account setup.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        Verify Your Email Address
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        Hi {{ $user->name }}, thank you for joining Wynfull Finance! To complete your account setup, please verify your email address.
    </p>

    <p class="text" style="margin:0 0 16px; color:#374151; font-size:15px; line-height:22px;">
        Click the button below to verify your email address and activate your account.
    </p>

    @include('mail.partials.button', [
      'url' => $verificationUrl,
      'label' => 'Verify Email Address'
    ])

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        If you did not create an account with Wynfull Finance, no further action is required.
    </p>

    <p class="text" style="margin:8px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:<br>
        <span style="color:#0E4DA4; word-break:break-all;">{{ $verificationUrl }}</span>
    </p>
@endsection
