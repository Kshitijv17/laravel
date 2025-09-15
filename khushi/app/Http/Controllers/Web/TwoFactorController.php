<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        return view('web.auth.two-factor', [
            'user' => $user,
            'qrCode' => $user->two_factor_secret ? $this->generateQrCode($user) : null,
            'recoveryCodes' => $user->two_factor_recovery_codes ? json_decode(decrypt($user->two_factor_recovery_codes), true) : []
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.']
            ]);
        }

        // Generate secret if not exists
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = encrypt($this->generateSecretKey());
            $user->save();
        }

        // Verify the provided code
        if (!$this->verifyCode($user, $request->code)) {
            throw ValidationException::withMessages([
                'code' => ['The provided two factor authentication code is invalid.']
            ]);
        }

        // Enable 2FA
        $user->two_factor_confirmed_at = now();
        $user->two_factor_recovery_codes = encrypt(json_encode($this->generateRecoveryCodes()));
        $user->save();

        return redirect()->route('user.two-factor')->with('success', 'Two-factor authentication has been enabled.');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.']
            ]);
        }

        // Disable 2FA
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return redirect()->route('user.two-factor')->with('success', 'Two-factor authentication has been disabled.');
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.']
            ]);
        }

        if (!$user->two_factor_confirmed_at) {
            return redirect()->route('user.two-factor')->with('error', 'Two-factor authentication is not enabled.');
        }

        // Generate new recovery codes
        $user->two_factor_recovery_codes = encrypt(json_encode($this->generateRecoveryCodes()));
        $user->save();

        return redirect()->route('user.two-factor')->with('success', 'Recovery codes have been regenerated.');
    }

    public function challenge()
    {
        if (!Session::get('login.id')) {
            return redirect()->route('login');
        }

        return view('web.auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string'
        ]);

        $userId = Session::get('login.id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $verified = false;

        if ($request->code) {
            $verified = $this->verifyCode($user, $request->code);
        } elseif ($request->recovery_code) {
            $verified = $this->verifyRecoveryCode($user, $request->recovery_code);
        }

        if (!$verified) {
            throw ValidationException::withMessages([
                'code' => ['The provided two factor authentication code is invalid.']
            ]);
        }

        // Clear the login session data
        Session::forget('login.id');
        Session::forget('login.remember');

        // Log the user in
        Auth::login($user, Session::get('login.remember', false));

        return redirect()->intended(route('user.dashboard'));
    }

    private function generateSecretKey()
    {
        return random_bytes(20);
    }

    private function generateQrCode(User $user)
    {
        $appName = config('app.name');
        $secret = base32_encode(decrypt($user->two_factor_secret));
        $qrCodeUrl = "otpauth://totp/{$appName}:{$user->email}?secret={$secret}&issuer={$appName}";

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($qrCodeUrl);
    }

    private function verifyCode(User $user, string $code)
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        $secret = decrypt($user->two_factor_secret);
        $timestamp = floor(time() / 30);

        // Check current timestamp and previous/next for clock drift
        for ($i = -1; $i <= 1; $i++) {
            $calculatedCode = $this->generateTOTP($secret, $timestamp + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    private function verifyRecoveryCode(User $user, string $code)
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        
        if (in_array($code, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_diff($recoveryCodes, [$code]);
            $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
            $user->save();
            
            return true;
        }

        return false;
    }

    private function generateTOTP($secret, $timestamp)
    {
        $data = pack('N*', 0) . pack('N*', $timestamp);
        $hash = hash_hmac('sha1', $data, $secret, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtolower(bin2hex(random_bytes(5)));
        }
        return $codes;
    }
}

// Helper function for base32 encoding
if (!function_exists('base32_encode')) {
    function base32_encode($data)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;
        
        for ($i = 0, $j = strlen($data); $i < $j; $i++) {
            $v <<= 8;
            $v += ord($data[$i]);
            $vbits += 8;
            
            while ($vbits >= 5) {
                $vbits -= 5;
                $output .= $alphabet[$v >> $vbits];
                $v &= ((1 << $vbits) - 1);
            }
        }
        
        if ($vbits > 0) {
            $v <<= (5 - $vbits);
            $output .= $alphabet[$v];
        }
        
        return $output;
    }
}
