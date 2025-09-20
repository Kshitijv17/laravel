<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the customer profile page
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'total_orders' => $user->orders()->count() ?? 0,
            'total_spent' => $user->orders()->where('status', 'delivered')->sum('total_amount') ?? 0,
            'wishlist_count' => 0, // Wishlist functionality to be implemented
        ];
        
        // Get recent orders
        $recentOrders = $user->orders()
            ->latest()
            ->take(5)
            ->get();
        
        return view('customer.profile', compact('user', 'stats', 'recentOrders'));
    }
    
    /**
     * Update the customer profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
        ]);
        
        // Don't allow email updates for security reasons
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
        ]);
        
        return redirect()->route('customer.profile')
            ->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update the customer password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('customer.profile')
            ->with('success', 'Password updated successfully!');
    }
}
