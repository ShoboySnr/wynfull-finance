{{-- This partial contains the content for the 2FA modal --}}

@if (! auth()->user()->hasEnabledTwoFactorAuthentication())
    {{-- State: 2FA is DISABLED --}}
    <div class="two-factor-setup">
        <h3>Enable Two-Factor Authentication</h3>
        <p>When you enable 2FA, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's authenticator application.</p>
        <form method="POST" action="/user/two-factor-authentication">
            @csrf
            <button type="submit" class="btn-primary">Enable 2FA</button>
        </form>
    </div>
@else
    {{-- State: 2FA is ENABLED --}}
    <div class="two-factor-enabled">
        <div class="qr-code-section">
            <h3>Scan QR Code</h3>
            <p>Scan the following QR code using your phone's authenticator application (like Google Authenticator or Authy) and provide the generated OTP code.</p>
            <div class="qr-code-container">
                {!! auth()->user()->twoFactorQrCodeSvg() !!}
            </div>
        </div>

        <div class="recovery-codes-section">
            <h3>Recovery Codes</h3>
            <p>Store these recovery codes in a secure password manager. They can be used to regain access to your account if your two-factor authentication device is lost.</p>
            <div class="recovery-codes-list">
                <ul>
                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes, true)) as $code)
                        <li>{{ $code }}</li>
                    @endforeach
                </ul>
            </div>
            <form method="POST" action="/user/two-factor-recovery-codes" class="mt-3">
                @csrf
                <button type="submit" class="btn-secondary">Regenerate Recovery Codes</button>
            </form>
        </div>

        <div class="disable-2fa-section">
            <h3>Disable 2FA</h3>
            <p>Disabling 2FA will remove an extra layer of security from your account.</p>
            <form method="POST" action="/user/two-factor-authentication">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Disable 2FA</button>
            </form>
        </div>
    </div>
@endif
