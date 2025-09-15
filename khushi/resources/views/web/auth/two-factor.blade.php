@extends('layouts.web')

@section('title', 'Two-Factor Authentication - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Two-Factor Authentication</h1>
            
            @if($user->two_factor_confirmed_at)
                <!-- 2FA Enabled State -->
                <div class="mb-6">
                    <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-green-800 font-medium">Two-factor authentication is enabled</h3>
                            <p class="text-green-700 text-sm">Your account is protected with two-factor authentication.</p>
                        </div>
                    </div>
                </div>

                <!-- Recovery Codes -->
                @if(count($recoveryCodes) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Recovery Codes</h3>
                        <p class="text-sm text-gray-600 mb-3">
                            Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two-factor authentication device is lost.
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-2 gap-2 font-mono text-sm">
                                @foreach($recoveryCodes as $code)
                                    <div class="bg-white p-2 rounded border">{{ $code }}</div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Regenerate Recovery Codes -->
                        <form method="POST" action="{{ route('user.two-factor.regenerate-codes') }}" class="mt-4">
                            @csrf
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm Password
                                </label>
                                <input type="password" id="password" name="password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('password')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                Regenerate Recovery Codes
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Disable 2FA -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Disable Two-Factor Authentication</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        If you disable two-factor authentication, your account will be less secure.
                    </p>
                    
                    <form method="POST" action="{{ route('user.two-factor.disable') }}" 
                          onsubmit="return confirm('Are you sure you want to disable two-factor authentication?')">
                        @csrf
                        @method('DELETE')
                        <div class="mb-4">
                            <label for="disable_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password
                            </label>
                            <input type="password" id="disable_password" name="password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            Disable Two-Factor Authentication
                        </button>
                    </form>
                </div>
            @else
                <!-- 2FA Setup State -->
                <div class="mb-6">
                    <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h3 class="text-yellow-800 font-medium">Two-factor authentication is not enabled</h3>
                            <p class="text-yellow-700 text-sm">Add an extra layer of security to your account.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Setup Two-Factor Authentication</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.
                        </p>
                    </div>

                    @if($qrCode)
                        <!-- Step 2: Scan QR Code -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">1. Scan QR Code</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.):
                            </p>
                            <div class="bg-white p-4 rounded-lg border inline-block">
                                {!! $qrCode !!}
                            </div>
                        </div>

                        <!-- Step 3: Verify Code -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">2. Enter Verification Code</h4>
                            <form method="POST" action="{{ route('user.two-factor.enable') }}">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                            Confirm Password
                                        </label>
                                        <input type="password" id="password" name="password" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('password')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                            Authentication Code
                                        </label>
                                        <input type="text" id="code" name="code" required maxlength="6" 
                                               placeholder="123456"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('code')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit" 
                                        class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Enable Two-Factor Authentication
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Step 1: Generate Secret -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Get Started</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                Click the button below to generate a QR code for your authenticator app.
                            </p>
                            <form method="POST" action="{{ route('user.two-factor.enable') }}">
                                @csrf
                                <input type="hidden" name="generate" value="1">
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Generate QR Code
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Information -->
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-medium text-blue-900 mb-2">Recommended Authenticator Apps</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Google Authenticator (iOS/Android)</li>
                        <li>• Authy (iOS/Android/Desktop)</li>
                        <li>• Microsoft Authenticator (iOS/Android)</li>
                        <li>• 1Password (iOS/Android/Desktop)</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
