<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs
     */
    public function index(Request $request): JsonResponse
    {
        $query = Faq::query();

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where('question', 'like', '%' . $request->search . '%')
                  ->orWhere('answer', 'like', '%' . $request->search . '%');
        }

        $faqs = $query->orderBy('sort_order')
                     ->orderBy('created_at', 'desc')
                     ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $faqs,
            'message' => 'FAQs retrieved successfully'
        ]);
    }

    /**
     * Store a newly created FAQ
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $faq = Faq::create($validated);

        return response()->json([
            'success' => true,
            'data' => $faq,
            'message' => 'FAQ created successfully'
        ], 201);
    }

    /**
     * Display the specified FAQ
     */
    public function show(Faq $faq): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $faq,
            'message' => 'FAQ retrieved successfully'
        ]);
    }

    /**
     * Update the specified FAQ
     */
    public function update(Request $request, Faq $faq): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'sometimes|string|max:500',
            'answer' => 'sometimes|string',
            'category' => 'sometimes|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $faq->update($validated);

        return response()->json([
            'success' => true,
            'data' => $faq,
            'message' => 'FAQ updated successfully'
        ]);
    }

    /**
     * Remove the specified FAQ
     */
    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully'
        ]);
    }

    /**
     * Get FAQs by category
     */
    public function getByCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|string'
        ]);

        $faqs = Faq::active()
                  ->where('category', $validated['category'])
                  ->orderBy('sort_order')
                  ->get();

        return response()->json([
            'success' => true,
            'data' => $faqs,
            'message' => 'FAQs retrieved successfully'
        ]);
    }

    /**
     * Get all FAQ categories
     */
    public function getCategories(): JsonResponse
    {
        $categories = Faq::select('category')
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'FAQ categories retrieved successfully'
        ]);
    }

    /**
     * Search FAQs
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $faqs = Faq::active()
                  ->where('question', 'like', '%' . $validated['query'] . '%')
                  ->orWhere('answer', 'like', '%' . $validated['query'] . '%')
                  ->orderBy('sort_order')
                  ->get();

        return response()->json([
            'success' => true,
            'data' => $faqs,
            'message' => 'FAQ search completed successfully'
        ]);
    }
}
