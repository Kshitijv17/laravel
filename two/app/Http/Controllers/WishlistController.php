<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->with('items.product')->first();
        return view('wishlist.index', compact('wishlist'));
    }

    public function add(Product $product)
    {
        $wishlist = Wishlist::firstOrCreate(['user_id' => Auth::id()]);
        
        $exists = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $product->id)
            ->exists();

        if (!$exists) {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $product->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product already in wishlist!'
        ]);
    }

    public function remove(Product $product)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->first();
        
        if ($wishlist) {
            WishlistItem::where('wishlist_id', $wishlist->id)
                ->where('product_id', $product->id)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist!'
        ]);
    }
}
