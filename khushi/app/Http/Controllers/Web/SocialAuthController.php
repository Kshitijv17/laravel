<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        $supportedProviders = ['google', 'facebook'];
        
        if (!in_array($provider, $supportedProviders)) {
            return redirect()->route('login')->with('error', 'Unsupported social login provider');
        }
        
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if user already exists with this email
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            
            if ($existingUser) {
                // Update social provider info if not already set
                if (!$existingUser->{$provider . '_id'}) {
                    $existingUser->update([
                        $provider . '_id' => $socialUser->getId(),
                        $provider . '_avatar' => $socialUser->getAvatar(),
                    ]);
                }
                
                Auth::login($existingUser);
                return redirect()->intended('/user/dashboard');
            }
            
            // Create new user
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(24)), // Random password
                'email_verified_at' => now(),
                'status' => 'active',
                $provider . '_id' => $socialUser->getId(),
                $provider . '_avatar' => $socialUser->getAvatar(),
            ]);
            
            Auth::login($user);
            
            return redirect()->intended('/user/dashboard')->with('success', 'Welcome! Your account has been created successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Social login error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to login with ' . ucfirst($provider) . '. Please try again.');
        }
    }

    public function link(Request $request, $provider)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if this social account is already linked to another user
            $existingUser = User::where($provider . '_id', $socialUser->getId())
                              ->where('id', '!=', Auth::id())
                              ->first();
                              
            if ($existingUser) {
                return redirect()->back()->with('error', 'This ' . ucfirst($provider) . ' account is already linked to another user.');
            }
            
            // Link to current user
            Auth::user()->update([
                $provider . '_id' => $socialUser->getId(),
                $provider . '_avatar' => $socialUser->getAvatar(),
            ]);
            
            return redirect()->back()->with('success', ucfirst($provider) . ' account linked successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to link ' . ucfirst($provider) . ' account.');
        }
    }

    public function unlink($provider)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        Auth::user()->update([
            $provider . '_id' => null,
            $provider . '_avatar' => null,
        ]);

        return redirect()->back()->with('success', ucfirst($provider) . ' account unlinked successfully!');
    }
}
