@extends('layouts.app')

@section('title', 'Two-Factor Authentication - ' . config('app.name'))

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 900px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.25rem 2rem; text-align:center; }
 .profile-name { margin:0; font-weight:700; font-size:1.2rem; }
 .profile-email { margin:.2rem 0 0; opacity:.9; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .code-grid .code-pill { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size:.9rem; }
 .info-box { background:#f8fafc; border:1px solid #e5e7eb; border-radius:12px; padding:1rem; }
 .qr-box { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1rem; display:inline-block; }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <h2 class="profile-name mb-0">Two-Factor Authentication</h2>
        <p class="profile-email mb-0">Protect your account with an extra layer of security</p>
      </div>
      <div class="profile-body">
            
            @if($user->two_factor_confirmed_at)
                <!-- 2FA Enabled State -->
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
                  <i class="fas fa-check-circle"></i>
                  <div>
                    <div class="fw-semibold">Two-factor authentication is enabled</div>
                    <div class="small">Your account is protected with two-factor authentication.</div>
                  </div>
                </div>

                <!-- Recovery Codes -->
                @if(count($recoveryCodes) > 0)
                  <div class="mb-4">
                    <h3 class="h6 mb-2">Recovery Codes</h3>
                    <p class="text-muted small mb-3">Store these codes in a secure password manager. They can be used if your authenticator device is lost.</p>
                    <div class="info-box mb-3 code-grid">
                      <div class="row row-cols-2 g-2">
                        @foreach($recoveryCodes as $code)
                          <div class="col"><div class="code-pill border rounded py-2 px-2 bg-white">{{ $code }}</div></div>
                        @endforeach
                      </div>
                    </div>
                    <form method="POST" action="{{ route('user.two-factor.regenerate-codes') }}" class="row g-2">
                      @csrf
                      <div class="col-12 col-md-6">
                        <label class="form-label small" for="password">Confirm Password</label>
                        <input type="password" id="password" name="password" required class="form-control form-control-sm">
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                      </div>
                      <div class="col-12 d-flex align-items-end">
                        <button type="submit" class="btn btn-warning btn-sm">Regenerate Recovery Codes</button>
                      </div>
                    </form>
                  </div>
                @endif

                <div class="pt-3 mt-2 border-top">
                  <h3 class="h6">Disable Two-Factor Authentication</h3>
                  <p class="text-muted small mb-2">If you disable 2FA, your account will be less secure.</p>
                  <form method="POST" action="{{ route('user.two-factor.disable') }}" onsubmit="return confirm('Disable two-factor authentication?')" class="row g-2">
                    @csrf
                    @method('DELETE')
                    <div class="col-12 col-md-6">
                      <label class="form-label small" for="disable_password">Confirm Password</label>
                      <input type="password" id="disable_password" name="password" required class="form-control form-control-sm">
                    </div>
                    <div class="col-12 d-flex align-items-end">
                      <button type="submit" class="btn btn-danger btn-sm">Disable Two-Factor Authentication</button>
                    </div>
                  </form>
                </div>
            @else
                <!-- 2FA Setup State -->
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" role="alert">
                  <i class="fas fa-triangle-exclamation"></i>
                  <div>
                    <div class="fw-semibold">Two-factor authentication is not enabled</div>
                    <div class="small">Add an extra layer of security to your account.</div>
                  </div>
                </div>

                <div class="space-y-6">
                    <div class="mb-2">
                      <h3 class="h6">Setup Two-Factor Authentication</h3>
                      <p class="text-muted small mb-0">Two-factor authentication adds an additional layer of security by requiring more than just a password to log in.</p>
                    </div>

                    @if($qrCode)
                        <!-- Step 2: Scan QR Code -->
                        <div class="mb-3">
                          <div class="fw-semibold mb-1">1. Scan QR Code</div>
                          <p class="text-muted small mb-2">Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.).</p>
                          <div class="qr-box">{!! $qrCode !!}</div>
                        </div>

                        <!-- Step 3: Verify Code -->
                        <div>
                          <div class="fw-semibold mb-2">2. Enter Verification Code</div>
                          <form method="POST" action="{{ route('user.two-factor.enable') }}" class="row g-3">
                            @csrf
                            <div class="col-12 col-md-6">
                              <label class="form-label small" for="password">Confirm Password</label>
                              <input type="password" id="password" name="password" required class="form-control form-control-sm">
                              @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                              <label class="form-label small" for="code">Authentication Code</label>
                              <input type="text" id="code" name="code" maxlength="6" required placeholder="123456" class="form-control form-control-sm">
                              @error('code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                              <button type="submit" class="btn btn-primary btn-sm">Enable Two-Factor Authentication</button>
                            </div>
                          </form>
                        </div>
                    @else
                        <!-- Step 1: Generate Secret -->
                        <div>
                          <div class="fw-semibold mb-1">Get Started</div>
                          <p class="text-muted small mb-2">Click the button below to generate a QR code for your authenticator app.</p>
                          <form method="POST" action="{{ route('user.two-factor.enable') }}">
                            @csrf
                            <input type="hidden" name="generate" value="1">
                            <button type="submit" class="btn btn-primary btn-sm">Generate QR Code</button>
                          </form>
                        </div>
                    @endif
                </div>

                <div class="mt-4 info-box">
                  <div class="fw-semibold mb-1">Recommended Authenticator Apps</div>
                  <ul class="small mb-0">
                    <li>Google Authenticator (iOS/Android)</li>
                    <li>Authy (iOS/Android/Desktop)</li>
                    <li>Microsoft Authenticator (iOS/Android)</li>
                    <li>1Password (iOS/Android/Desktop)</li>
                  </ul>
                </div>
            @endif
      </div>
    </div>
  </div>
</div>
@endsection
