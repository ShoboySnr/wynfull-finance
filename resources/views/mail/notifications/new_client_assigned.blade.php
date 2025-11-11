@extends('mail.layouts.base', [
  'title' => 'New Client Assignment',
  'preheader' => 'You have been assigned a new client to coach.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        New Client Assignment 🎯
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        Hi {{ $coach->name }}, you have been assigned a new client who is ready to begin their financial journey with your guidance.
    </p>

    <div style="margin:20px 0; padding:20px; background-color:#F8F9FA; border-radius:12px; border:1px solid #E5E7EB;">
        <div style="display:flex; align-items:center; margin-bottom:12px;">
            <div style="width:60px; height:60px; border-radius:50%; background-color:#059669; color:#FFFFFF; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:bold; margin-right:16px;">
                {{ substr($client->name, 0, 1) }}
            </div>
            <div>
                <h3 style="margin:0 0 4px; color:#1F2937; font-size:18px; font-weight:600;">{{ $client->name }}</h3>
                <p style="margin:0; color:#6B7280; font-size:14px;">{{ $client->email }}</p>
            </div>
        </div>
        
        @if($client->clientProfile)
            @if($client->clientProfile->goal)
                <p style="margin:8px 0 0; color:#374151; font-size:14px; line-height:20px;">
                    <strong>Primary Goal:</strong> {{ ucfirst(str_replace('-', ' ', $client->clientProfile->goal)) }}
                </p>
            @endif
            
            @if($client->clientProfile->community)
                <p style="margin:8px 0 0; color:#374151; font-size:14px; line-height:20px;">
                    <strong>Community:</strong> {{ ucfirst($client->clientProfile->community) }}
                </p>
            @endif
            
            @if($client->clientProfile->notes)
                <p style="margin:8px 0 0; color:#374151; font-size:14px; line-height:20px;">
                    <strong>Notes:</strong> {{ $client->clientProfile->notes }}
                </p>
            @endif
        @endif
    </div>

    <p class="text" style="margin:0 0 16px; color:#374151; font-size:15px; line-height:22px;display:inline-block;">
        This client is eager to start their financial transformation. Review their profile and reach out to schedule your first coaching session.
    </p>

    @include('mail.partials.button', [
      'url' => route('coach.clients'),
      'label' => 'View Client Details'
    ])

    <div style="margin:24px 0; padding:16px; background-color:#F0FDF4; border-radius:8px; border-left:4px solid #059669;">
        <h3 style="margin:0 0 8px; color:#1F2937; font-size:16px; font-weight:600;">Next Steps</h3>
        <ul style="margin:0; padding-left:20px; color:#374151; font-size:14px; line-height:20px;">
            <li style="margin-bottom:4px;">Review the client's profile and goals</li>
            <li style="margin-bottom:4px;">Send a welcome message to introduce yourself</li>
            <li style="margin-bottom:4px;">Schedule your first coaching session</li>
            <li>Create a personalized action plan</li>
        </ul>
    </div>

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        Need support with coaching strategies? Check out the coach resources section in your dashboard.
    </p>
@endsection
