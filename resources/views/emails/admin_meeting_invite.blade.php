<p>Hi {{ $user->name }},</p>

<p>You have been invited to a meeting scheduled by Admin.</p>

<p><strong>Meeting Details</strong></p>
<ul>
    <li><strong>Date:</strong> {{ $meeting->starts_at->timezone(config('app.timezone'))->format('D, M j, Y') }}</li>
    <li><strong>Time:</strong> {{ $meeting->starts_at->timezone(config('app.timezone'))->format('g:i A') }}
        - {{ $meeting->ends_at->timezone(config('app.timezone'))->format('g:i A') }}</li>
    @if($meeting->mode)
        <li><strong>Mode:</strong> {{ ucfirst($meeting->mode) }}</li>
    @endif
</ul>

@if($meeting->meeting_link)
    <p>
        <strong>Meeting Link:</strong><br>
        <a href="{{ $meeting->meeting_link }}">{{ $meeting->meeting_link }}</a>
    </p>
@endif

@if($meeting->notes)
    <p><strong>Notes:</strong> {{ $meeting->notes }}</p>
@endif

<p>Please be available at the scheduled time.</p>
