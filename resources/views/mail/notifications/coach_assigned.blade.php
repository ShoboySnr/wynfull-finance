@extends('mail.layouts.base', [
  'title' => 'You\'ve Been Assigned a Financial Coach',
  'preheader' => 'Meet your new financial coach and start your personalized journey.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        Meet Your Financial Coach! 👋
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        Hi {{ $client->name }}, we're excited to introduce you to your assigned financial coach who will guide you on your journey to financial success.
    </p>

    <div style="margin:20px 0; padding:20px; background-color:#F8F9FA; border-radius:12px; border:1px solid #E5E7EB;">
        <div style="display:flex; align-items:center; margin-bottom:12px;">
            <div style="width:60px; height:60px; border-radius:50%; background-color:#0E4DA4; color:#FFFFFF; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:bold; margin-right:16px;">
                {{ substr($coach->name, 0, 1) }}
            </div>
            <div>
                <h3 style="margin:0 0 4px; color:#1F2937; font-size:18px; font-weight:600;">{{ $coach->name }}</h3>
                <p style="margin:0; color:#6B7280; font-size:14px;">Your Financial Coach</p>
            </div>
        </div>
        
        @if($coach->coachProfile && $coach->coachProfile->experience)
            <p style="margin:8px 0 0; color:#374151; font-size:14px; line-height:20px;">
                <strong>Experience:</strong> {{ $coach->coachProfile->experience }}
            </p>
        @endif
        
        @if($coach->coachProfile && $coach->coachProfile->specialties)
            <p style="margin:8px 0 0; color:#374151; font-size:14px; line-height:20px;">
                <strong>Specialties:</strong> {{ is_array($coach->coachProfile->specialties) ? implode(', ', $coach->coachProfile->specialties) : $coach->coachProfile->specialties }}
            </p>
        @endif
    </div>

    <p class="text" style="margin:0 0 16px; color:#374151; font-size:15px; line-height:22px;">
        Your coach is ready to help you achieve your financial goals through personalized guidance, resources, and regular check-ins.
    </p>

    @include('mail.partials.button', [
      'url' => route('dashboard.client'),
      'label' => 'Start Your Journey'
    ])

    <div style="margin:24px 0; padding:16px; background-color:#F0F9FF; border-radius:8px; border-left:4px solid #0E4DA4;">
        <h3 style="margin:0 0 8px; color:#1F2937; font-size:16px; font-weight:600;">What Happens Next?</h3>
        <ul style="margin:0; padding-left:20px; color:#374151; font-size:14px; line-height:20px;">
            <li style="margin-bottom:4px;">Your coach will reach out to schedule your first session</li>
            <li style="margin-bottom:4px;">You'll receive personalized resources and action plans</li>
            <li style="margin-bottom:4px;">Track your progress through your dashboard</li>
            <li>Celebrate milestones and build lasting financial habits</li>
        </ul>
    </div>

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        Questions? Your coach and our support team are here to help you succeed.
    </p>
@endsection
