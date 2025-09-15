@extends('layouts.web')

@section('title', 'Two-Factor Authentication - ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-1a2 2 0 00-2-2H6a2 2 0 00-2 2v1a2 2 0 002 2zM12 7a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Two-Factor Authentication
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please enter your authentication code or use a recovery code
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            <!-- Authentication Code Form -->
            <div id="code-form">
                <form method="POST" action="{{ route('two-factor.verify') }}">
                    @csrf
                    <div class="mb-6">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Authentication Code
                        </label>
                        <input type="text" id="code" name="code" maxlength="6" 
                               placeholder="Enter 6-digit code"
                               class="w-full px-3 py-3 text-center text-2xl font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               autofocus autocomplete="one-time-code">
                        @error('code')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Verify Code
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <button type="button" onclick="showRecoveryForm()" 
                            class="text-sm text-blue-600 hover:text-blue-800">
                        Use a recovery code instead
                    </button>
                </div>
            </div>

            <!-- Recovery Code Form -->
            <div id="recovery-form" class="hidden">
                <form method="POST" action="{{ route('two-factor.verify') }}">
                    @csrf
                    <div class="mb-6">
                        <label for="recovery_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Recovery Code
                        </label>
                        <input type="text" id="recovery_code" name="recovery_code" 
                               placeholder="Enter recovery code"
                               class="w-full px-3 py-3 text-center font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('recovery_code')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Use Recovery Code
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <button type="button" onclick="showCodeForm()" 
                            class="text-sm text-blue-600 hover:text-blue-800">
                        Use authentication code instead
                    </button>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Need help?</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Check your authenticator app for the 6-digit code</li>
                    <li>• Make sure your device's time is synchronized</li>
                    <li>• Use a recovery code if you can't access your authenticator</li>
                    <li>• Contact support if you're still having trouble</li>
                </ul>
            </div>

            <!-- Logout Link -->
            <div class="mt-6 text-center">
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="text-sm text-gray-600 hover:text-gray-800">
                    Sign out and try a different account
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showRecoveryForm() {
    document.getElementById('code-form').classList.add('hidden');
    document.getElementById('recovery-form').classList.remove('hidden');
    document.getElementById('recovery_code').focus();
}

function showCodeForm() {
    document.getElementById('recovery-form').classList.add('hidden');
    document.getElementById('code-form').classList.remove('hidden');
    document.getElementById('code').focus();
}

// Auto-submit when 6 digits are entered
document.getElementById('code').addEventListener('input', function(e) {
    if (e.target.value.length === 6) {
        // Small delay to allow user to see the complete code
        setTimeout(() => {
            e.target.closest('form').submit();
        }, 500);
    }
});

// Format recovery code input
document.getElementById('recovery_code').addEventListener('input', function(e) {
    // Remove any non-alphanumeric characters and convert to lowercase
    e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
});
</script>
@endsection
