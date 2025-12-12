@extends('mail.layouts.base', [
  'title' => 'Welcome to Wynfull Finance',
  'preheader' => 'Welcome to your financial journey with Wynfull Finance.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        Welcome to Wynfull Finance!
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        Hi {{ $user->name }}, welcome to Wynfull Finance! We're excited to help you build real financial skills and take control of your money.
    </p>

    <p class="text" style="margin:0 0 16px; color:#374151; font-size:15px; line-height:22px;">
        Your account has been created successfully. Get started by accessing your personalized dashboard where you can track your progress, access resources, and connect with your financial coach.
    </p>

    @include('mail.partials.button', [
      'url' => route('login'),
      'label' => 'Access Your Dashboard'
    ])

    <div style="margin:24px 0; padding:16px; background-color:#F8F9FA; border-radius:8px;">
        <h3 style="margin:0 0 8px; color:#1F2937; font-size:16px; font-weight:600;">What's Next?</h3>
        <ul style="margin:0; padding-left:20px; color:#374151; font-size:14px; line-height:20px;">
            <li style="margin-bottom:4px;">Complete your financial profile</li>
            <li style="margin-bottom:4px;">Explore your resource library</li>
            <li style="margin-bottom:4px;">Connect with your assigned coach</li>
            <li>Start tracking your financial goals</li>
        </ul>
    </div>

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        If you have any questions, don't hesitate to reach out to our support team. We're here to help you succeed on your financial journey.
    </p>
@endsection
