<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $recommendationService;
    protected $analyticsService;

    public function __construct(RecommendationService $recommendationService, AnalyticsService $analyticsService)
    {
        $this->recommendationService = $recommendationService;
        $this->analyticsService = $analyticsService;
    }

    public function getRecommendations(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'limit' => 'sometimes|integer|min:1|max:20'
        ]);

        $userId = Auth::id();
        $limit = $request->get('limit', 8);
        
        $recommendations = $this->recommendationService->getRecommendations(
            $request->product_id,
            $userId,
            $limit
        );

        // Track recommendation view
        if ($userId) {
            $this->analyticsService->track('recommendation_view', $userId, [
                'product_id' => $request->product_id,
                'recommendation_count' => $recommendations->count(),
                'recommendation_type' => 'product_based'
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        }

        return view('web.recommendations.product', compact('recommendations'));
    }

    public function getPersonalizedRecommendations(Request $request)
    {
        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:20'
        ]);

        $userId = Auth::id();
        $limit = $request->get('limit', 12);
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required for personalized recommendations'
            ], 401);
        }

        $recommendations = $this->recommendationService->getPersonalizedRecommendations($userId, $limit);

        // Track personalized recommendation view
        $this->analyticsService->track('personalized_recommendation_view', $userId, [
            'recommendation_count' => $recommendations->count()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        }

        return view('web.recommendations.personalized', compact('recommendations'));
    }

    public function getSimilarProducts(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'limit' => 'sometimes|integer|min:1|max:12'
        ]);

        $limit = $request->get('limit', 6);
        $similarProducts = $this->recommendationService->getSimilarProducts($request->product_id, $limit);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'similar_products' => $similarProducts
            ]);
        }

        return view('web.recommendations.similar', compact('similarProducts'));
    }

    public function getFrequentlyBoughtTogether(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'limit' => 'sometimes|integer|min:1|max:8'
        ]);

        $limit = $request->get('limit', 4);
        $frequentlyBought = $this->recommendationService->getFrequentlyBoughtTogether($request->product_id, $limit);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'frequently_bought' => $frequentlyBought
            ]);
        }

        return view('web.recommendations.frequently-bought', compact('frequentlyBought'));
    }

    public function getRecentlyViewedRecommendations(Request $request)
    {
        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:12'
        ]);

        $userId = Auth::id();
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $limit = $request->get('limit', 8);
        $recommendations = $this->recommendationService->getRecentlyViewedRecommendations($userId, $limit);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        }

        return view('web.recommendations.recently-viewed', compact('recommendations'));
    }

    public function getAbandonedCartRecommendations(Request $request)
    {
        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:8'
        ]);

        $userId = Auth::id();
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $limit = $request->get('limit', 6);
        $recommendations = $this->recommendationService->getAbandonedCartRecommendations($userId, $limit);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        }

        return view('web.recommendations.abandoned-cart', compact('recommendations'));
    }

    public function trackRecommendationClick(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'recommended_product_id' => 'required|exists:products,id',
            'recommendation_type' => 'required|string',
            'position' => 'sometimes|integer'
        ]);

        $userId = Auth::id();
        
        if ($userId) {
            $this->analyticsService->track('recommendation_click', $userId, [
                'product_id' => $request->product_id,
                'recommended_product_id' => $request->recommended_product_id,
                'recommendation_type' => $request->recommendation_type,
                'position' => $request->get('position', 0)
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function widget(Request $request)
    {
        $type = $request->get('type', 'personalized');
        $limit = $request->get('limit', 6);
        $userId = Auth::id();
        
        $recommendations = collect();
        
        switch ($type) {
            case 'personalized':
                if ($userId) {
                    $recommendations = $this->recommendationService->getPersonalizedRecommendations($userId, $limit);
                }
                break;
                
            case 'recently_viewed':
                if ($userId) {
                    $recommendations = $this->recommendationService->getRecentlyViewedRecommendations($userId, $limit);
                }
                break;
                
            case 'abandoned_cart':
                if ($userId) {
                    $recommendations = $this->recommendationService->getAbandonedCartRecommendations($userId, $limit);
                }
                break;
                
            case 'similar':
                $productId = $request->get('product_id');
                if ($productId) {
                    $recommendations = $this->recommendationService->getSimilarProducts($productId, $limit);
                }
                break;
                
            case 'frequently_bought':
                $productId = $request->get('product_id');
                if ($productId) {
                    $recommendations = $this->recommendationService->getFrequentlyBoughtTogether($productId, $limit);
                }
                break;
        }
        
        return view('web.recommendations.widget', compact('recommendations', 'type'));
    }

    public function dashboard()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $personalizedRecommendations = $this->recommendationService->getPersonalizedRecommendations($userId, 8);
        $recentlyViewedRecommendations = $this->recommendationService->getRecentlyViewedRecommendations($userId, 6);
        $abandonedCartRecommendations = $this->recommendationService->getAbandonedCartRecommendations($userId, 4);

        return view('web.recommendations.dashboard', compact(
            'personalizedRecommendations',
            'recentlyViewedRecommendations',
            'abandonedCartRecommendations'
        ));
    }
}
