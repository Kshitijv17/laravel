<?php

namespace App\Http\Controllers\Api;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['user', 'product']);

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('comment', 'like', '%' . $request->search . '%');
        }

        if ($request->has('verified')) {
            $query->verified();
        }

        $reviews = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Reviews retrieved successfully'
        ]);
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'is_verified' => 'boolean'
        ]);

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', $validated['user_id'])
                               ->where('product_id', $validated['product_id'])
                               ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product'
            ], 400);
        }

        $review = Review::create($validated);
        $review->load(['user', 'product']);

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'Review created successfully'
        ], 201);
    }

    /**
     * Display the specified review
     */
    public function show(Review $review): JsonResponse
    {
        $review->load(['user', 'product']);

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'Review retrieved successfully'
        ]);
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Review $review): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'is_verified' => 'boolean'
        ]);

        $review->update($validated);
        $review->load(['user', 'product']);

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'Review updated successfully'
        ]);
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Get product reviews
     */
    public function productReviews(Request $request, Product $product): JsonResponse
    {
        $reviews = $product->reviews()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Product reviews retrieved successfully'
        ]);
    }

    /**
     * Get review statistics for a product
     */
    public function productStats(Product $product): JsonResponse
    {
        $reviews = $product->reviews();
        
        $stats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => round($reviews->avg('rating'), 2),
            'rating_breakdown' => [
                '5_star' => $reviews->where('rating', 5)->count(),
                '4_star' => $reviews->where('rating', 4)->count(),
                '3_star' => $reviews->where('rating', 3)->count(),
                '2_star' => $reviews->where('rating', 2)->count(),
                '1_star' => $reviews->where('rating', 1)->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Product review statistics retrieved successfully'
        ]);
    }
}
