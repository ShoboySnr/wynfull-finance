@extends('mail.layouts.base', [
  'title' => 'New User Pending Activation',
  'preheader' => 'A new registration is awaiting admin activation.'
])

@section('content')
    <h1 class="h1" style="margin:0 0 8px; font-size:24px; line-height:30px; color:#1F2937; font-weight:bold;">
        New user awaiting activation
    </h1>

    <p class="text" style="margin:0 0 12px; color:#374151; font-size:15px; line-height:22px;">
        A new {{ $role ?? 'user' }} has registered and is pending activation.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:12px 0 16px;">
        <tr>
            <td style="padding:12px; border:1px solid #E5E7EB; border-radius:8px; background-color:#F9FAFB;">
                <p style="margin:0; font-size:14px; color:#111827;"><strong>Name:</strong> {{ $pendingUser->name }}</p>
                <p style="margin:6px 0 0; font-size:14px; color:#111827;"><strong>Email:</strong> {{ $pendingUser->email }}</p>
                @if(!empty($extra))
                    @foreach($extra as $label => $value)
                        <p style="margin:6px 0 0; font-size:14px; color:#111827;"><strong>{{ $label }}:</strong> {{ $value }}</p>
                    @endforeach
                @endif
            </td>
        </tr>
    </table>

    @include('mail.partials.button', [
      'url' => $actionUrl ?? '#',
      'label' => 'Activate User'
    ])

    <p class="text" style="margin:16px 0 0; color:#6B7280; font-size:13px; line-height:20px;">
        If you didn’t expect this, you can safely ignore this email.
    </p>
@endsection
