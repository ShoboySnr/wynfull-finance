@extends('mail.layouts.base', [
  'title' => 'Upcoming Coaching Session Reminder',
  'preheader' => 'Your coaching session is coming up soon.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        Session Reminder 📅
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        Hi {{ $user->name }}, this is a friendly reminder about your upcoming coaching session.
    </p>

    <div style="margin:20px 0; padding:20px; background-color:#F8F9FA; border-radius:12px; border:1px solid #E5E7EB;">
        <h3 style="margin:0 0 12px; color:#1F2937; font-size:18px; font-weight:600;">Session Details</h3>
        
        <div style="display:grid; gap:8px;">
            <div style="display:flex; align-items:center;">
                <div style="width:24px; height:24px; margin-right:12px; color:#0E4DA4;">
                    📅
                </div>
                <span style="color:#374151; font-size:14px;"><strong>Date:</strong> {{ $session->scheduled_at->format('l, F j, Y') }}</span>
            </div>
            
            <div style="display:flex; align-items:center;">
                <div style="width:24px; height:24px; margin-right:12px; color:#0E4DA4;">
                    🕐
                </div>
                <span style="color:#374151; font-size:14px;"><strong>Time:</strong> {{ $session->scheduled_at->format('g:i A T') }}</span>
            </div>
            
            @if(isset($session->coach))
                <div style="display:flex; align-items:center;">
                    <div style="width:24px; height:24px; margin-right:12px; color:#0E4DA4;">
                        👨‍💼
                    </div>
                    <span style="color:#374151; font-size:14px;"><strong>Coach:</strong> {{ $session->coach->name }}</span>
                </div>
            @endif
            
            @if(isset($session->meeting_link))
                <div style="display:flex; align-items:center;">
                    <div style="width:24px; height:24px; margin-right:12px; color:#0E4DA4;">
                        🔗
                    </div>
                    <span style="color:#374151; font-size:14px;"><strong>Meeting Link:</strong> <a href="{{ $session->meeting_link }}" style="color:#0E4DA4;">Join Session</a></span>
                </div>
            @endif
        </div>
    </div>

    <p class="text" style="margin:0 0 16px; color:#374151; font-size:15px; line-height:22px;">
        Come prepared to discuss your financial goals and any questions you may have. Your coach is excited to help you make progress on your journey.
    </p>

    @include('mail.partials.button', [
      'url' => $session->meeting_link ?? route('dashboard.client'),
      'label' => isset($session->meeting_link) ? 'Join Session' : 'View Dashboard'
    ])

    <div style="margin:24px 0; padding:16px; background-color:#FEF3F2; border-radius:8px; border-left:4px solid #F97316;">
        <h3 style="margin:0 0 8px; color:#1F2937; font-size:16px; font-weight:600;">Need to Reschedule?</h3>
        <p style="margin:0; color:#374151; font-size:14px; line-height:20px;">
            If you need to reschedule this session, please contact your coach as soon as possible or use the scheduling tools in your dashboard.
        </p>
    </div>

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        We recommend joining the session 5 minutes early to ensure everything is working properly.
    </p>
@endsection
