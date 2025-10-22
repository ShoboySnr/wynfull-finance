@php
    $user = auth()->user();
    // Check if 2FA has been enabled (secret key exists)
    $hasSecret = ! is_null($user->two_factor_secret);
    // Check if 2FA has been confirmed by the user
    $isConfirmed = $hasSecret && ! is_null($user->two_factor_confirmed_at);
@endphp

@if (! $hasSecret)
    {{-- STATE 1: 2FA is completely disabled. Show the enable button. --}}
    <div class="two-factor-setup">
        <h3>Enable Two-Factor Authentication</h3>
        <p>Add an extra layer of security to your account. When enabled, you will be prompted for a secure, random token during authentication. You can get this token from an app like Google Authenticator or Authy.</p>
        <form method="POST" action="/user/two-factor-authentication">
            @csrf
            <div class="modal-footer" style="padding: 0; margin-top: 1rem;">
                <button type="submit" class="btn-primary">Enable 2FA</button>
            </div>
        </form>
    </div>
@else
    {{-- STATE 2 & 3: 2FA has been enabled. --}}
    <div class="two-factor-enabled">

        @if (! $isConfirmed)
            {{-- STATE 2: Awaiting confirmation. Show QR and confirmation form. --}}
            <div class="confirm-2fa-section">
                <h3>Finish Setup</h3>
                <p>Scan the QR code with your authenticator app, then enter the 6-digit code below to finish enabling 2FA.</p>
                <div class="qr-code-container">
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                </div>
                <form method="POST" action="/user/confirmed-two-factor-authentication" class="mt-4">
                    @csrf
                    <div class="form-group">
                        <label for="code">Authentication Code</label>
                        <input name="code" id="code" class="form-input" inputmode="numeric" autocomplete="one-time-code" placeholder="123456" required autofocus>
                        @error('code')<div class="alert alert-danger mt-2">{{ $message }}</div>@enderror
                    </div>
                    <div class="modal-footer" style="padding: 0; margin-top: 1.5rem;">
                        <button type="submit" class="btn-primary">Confirm & Activate</button>
                    </div>
                </form>
            </div>
        @endif

        <div class="recovery-codes-section">
            <h3>Recovery Codes</h3>
            <p>Store these recovery codes in a secure password manager. They can be used to regain access if you lose your device.</p>
            <div class="recovery-codes-list">
                <ul>
                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes, true)) as $code)
                        <li>{{ $code }}</li>
                    @endforeach
                </ul>
            </div>
            <form method="POST" action="/user/two-factor-recovery-codes" class="mt-3">
                @csrf
                <button type="submit" class="btn-secondary mt-4">Regenerate Recovery Codes</button>
            </form>
        </div>

        @if ($isConfirmed)
            {{-- STATE 3: 2FA is fully enabled and confirmed. Show disable button. --}}
            <div class="disable-2fa-section">
                <h3>Disable 2FA</h3>
                <p>Disabling 2FA will remove an extra layer of security from your account.</p>
                <form method="POST" action="/user/two-factor-authentication">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Disable 2FA</button>
                </form>
            </div>
        @endif
    </div>
@endif

