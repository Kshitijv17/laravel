<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\Wishlist;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $recentOrders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->latest()
            ->limit(5)
            ->get();

        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with('product')
            ->limit(6)
            ->get();

        // Create recent activity from orders and other user actions
        $recentActivity = collect();
        
        // Add recent orders to activity
        $recentOrders->each(function($order) use ($recentActivity) {
            $recentActivity->push((object)[
                'type' => 'order',
                'title' => 'Order Placed',
                'description' => "Order #{$order->id} for $" . number_format($order->total_amount, 2),
                'created_at' => $order->created_at,
                'icon' => 'shopping-bag',
                'color' => 'success'
            ]);
        });

        // Add recent wishlist additions
        $wishlistItems->take(3)->each(function($item) use ($recentActivity) {
            $recentActivity->push((object)[
                'type' => 'wishlist',
                'title' => 'Added to Wishlist',
                'description' => $item->product->name,
                'created_at' => $item->created_at,
                'icon' => 'heart',
                'color' => 'danger'
            ]);
        });

        // Sort by date and limit to 10 items
        $recentActivity = $recentActivity->sortByDesc('created_at')->take(10);

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'total_spent' => Order::where('user_id', $user->id)->sum('total_amount'),
            'wishlist_items' => Wishlist::where('user_id', $user->id)->count(),
            'addresses' => Address::where('user_id', $user->id)->count(),
            'wallet_balance' => $user->wallet_balance
        ];

        return view('web.user.dashboard', compact('user', 'recentOrders', 'wishlistItems', 'recentActivity', 'stats'));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('web.user.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->update($validated);

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Change password form
     */
    public function changePasswordForm()
    {
        return view('web.user.change-password');
    }

    /**
     * Update password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('user.profile')->with('success', 'Password changed successfully');
    }

    /**
     * Display user orders
     */
    public function orders(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with(['items.product', 'payment', 'addresses']);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return view('web.user.orders', compact('orders'));
    }

    /**
     * Show order details
     */
    public function orderDetails($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.product', 'payment', 'addresses', 'tracking'])
            ->findOrFail($id);

        return view('web.user.order-details', compact('order'));
    }

    /**
     * Display user addresses
     */
    public function addresses()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('web.user.addresses', compact('addresses'));
    }

    /**
     * Store new address
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:home,office,other',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean'
        ]);

        $validated['user_id'] = Auth::id();

        // If this is set as default, remove default from other addresses
        if ($validated['is_default'] ?? false) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create($validated);

        return redirect()->route('user.addresses')->with('success', 'Address added successfully');
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, Address $address)
    {
        // Check if address belongs to user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:home,office,other',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean'
        ]);

        // If this is set as default, remove default from other addresses
        if ($validated['is_default'] ?? false) {
            Address::where('user_id', Auth::id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('user.addresses')->with('success', 'Address updated successfully');
    }

    /**
     * Delete address
     */
    public function deleteAddress(Address $address)
    {
        // Check if address belongs to user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('user.addresses')->with('success', 'Address deleted successfully');
    }

    /**
     * Display user wishlist
     */
    public function wishlist()
    {
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with('product')
            ->get();

        return view('web.user.wishlist', compact('wishlistItems'));
    }

    /**
     * Display support tickets
     */
    public function supportTickets()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->with('messages')
            ->latest()
            ->paginate(10);

        return view('web.user.support-tickets', compact('tickets'));
    }

    /**
     * Show support ticket details
     */
    public function supportTicketDetails($id)
    {
        $ticket = SupportTicket::where('user_id', Auth::id())
            ->with(['messages.user', 'messages.admin'])
            ->findOrFail($id);

        return view('web.user.support-ticket-details', compact('ticket'));
    }

    /**
     * Create new support ticket
     */
    public function createSupportTicket(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|string|max:100',
            'message' => 'required|string'
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'category' => $validated['category'],
            'status' => 'open'
        ]);

        // Create initial message
        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message']
        ]);

        return redirect()->route('user.support-tickets')->with('success', 'Support ticket created successfully');
    }

    /**
     * Reply to support ticket
     */
    public function replySupportTicket(Request $request, SupportTicket $ticket)
    {
        // Check if ticket belongs to user
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string'
        ]);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message']
        ]);

        // Update ticket status if it was closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        return redirect()->route('user.support-ticket-details', $ticket->id)
            ->with('success', 'Reply sent successfully');
    }
}
