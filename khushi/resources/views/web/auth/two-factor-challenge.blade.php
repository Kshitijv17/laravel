@extends('layouts.app')

@section('title', 'Two-Factor Authentication - ' . config('app.name'))

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 520px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.25rem 2rem; text-align:center; }
 .profile-name { margin:0; font-weight:800; font-size:1.25rem; }
 .profile-email { margin:.2rem 0 0; opacity:.9; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .code-input { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size:1.25rem; text-align:center; }
 .help-box { background:#f8fafc; border:1px solid #e5e7eb; border-radius:12px; padding:1rem; }
 .icon-circle { width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; background: rgba(255,255,255,.25); margin: 0 auto; }
 .toggle-link { cursor:pointer; }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <div class="icon-circle mb-2"><i class="fas fa-shield-halved"></i></div>
        <h2 class="profile-name mb-0">Two-Factor Authentication</h2>
        <p class="profile-email mb-0">Enter your authentication code or use a recovery code</p>
      </div>
      <div class="profile-body">
        <!-- Authentication Code Form -->
        <div id="code-form">
          <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <div class="mb-3">
              <label for="code" class="form-label small">Authentication Code</label>
              <input type="text" id="code" name="code" maxlength="6" placeholder="Enter 6-digit code" class="form-control code-input" autofocus autocomplete="one-time-code">
              @error('code')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify Code</button>
          </form>
          <div class="text-center mt-2">
            <button type="button" onclick="showRecoveryForm()" class="btn btn-link btn-sm toggle-link">Use a recovery code instead</button>
          </div>
        </div>

        <!-- Recovery Code Form -->
        <div id="recovery-form" class="d-none">
          <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <div class="mb-3">
              <label for="recovery_code" class="form-label small">Recovery Code</label>
              <input type="text" id="recovery_code" name="recovery_code" placeholder="Enter recovery code" class="form-control">
              @error('recovery_code')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Use Recovery Code</button>
          </form>
          <div class="text-center mt-2">
            <button type="button" onclick="showCodeForm()" class="btn btn-link btn-sm toggle-link">Use authentication code instead</button>
          </div>
        </div>

        <!-- Help Text -->
        <div class="help-box mt-3">
          <div class="fw-semibold small mb-1">Need help?</div>
          <ul class="small mb-0">
            <li>Check your authenticator app for the 6-digit code.</li>
            <li>Make sure your device's time is synchronized.</li>
            <li>Use a recovery code if you can't access your authenticator.</li>
            <li>Contact support if you're still having trouble.</li>
          </ul>
        </div>

        <!-- Logout Link -->
        <div class="text-center mt-3">
          <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="small text-muted text-decoration-none">Sign out and try a different account</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function showRecoveryForm() {
  document.getElementById('code-form').classList.add('d-none');
  const rec = document.getElementById('recovery-form');
  rec.classList.remove('d-none');
  document.getElementById('recovery_code').focus();
}
function showCodeForm() {
  document.getElementById('recovery-form').classList.add('d-none');
  const code = document.getElementById('code-form');
  code.classList.remove('d-none');
  document.getElementById('code').focus();
}
// Auto-submit when 6 digits are entered
document.getElementById('code').addEventListener('input', function(e) {
  if (e.target.value.length === 6) {
    setTimeout(() => { e.target.closest('form').submit(); }, 400);
  }
});
// Normalize recovery code input
document.getElementById('recovery_code').addEventListener('input', function(e) {
  e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
});
</script>
@endsection
