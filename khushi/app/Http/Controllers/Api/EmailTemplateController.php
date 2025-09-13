<?php

namespace App\Http\Controllers\Api;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates
     */
    public function index(Request $request): JsonResponse
    {
        $query = EmailTemplate::query();

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
        }

        $templates = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $templates,
            'message' => 'Email templates retrieved successfully'
        ]);
    }

    /**
     * Store a newly created email template
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:order_confirmation,shipping_notification,welcome,password_reset,invoice,newsletter',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $template = EmailTemplate::create($validated);

        return response()->json([
            'success' => true,
            'data' => $template,
            'message' => 'Email template created successfully'
        ], 201);
    }

    /**
     * Display the specified email template
     */
    public function show(EmailTemplate $emailTemplate): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $emailTemplate,
            'message' => 'Email template retrieved successfully'
        ]);
    }

    /**
     * Update the specified email template
     */
    public function update(Request $request, EmailTemplate $emailTemplate): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:order_confirmation,shipping_notification,welcome,password_reset,invoice,newsletter',
            'subject' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $emailTemplate->update($validated);

        return response()->json([
            'success' => true,
            'data' => $emailTemplate,
            'message' => 'Email template updated successfully'
        ]);
    }

    /**
     * Remove the specified email template
     */
    public function destroy(EmailTemplate $emailTemplate): JsonResponse
    {
        $emailTemplate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email template deleted successfully'
        ]);
    }

    /**
     * Preview email template with variables
     */
    public function preview(Request $request, EmailTemplate $emailTemplate): JsonResponse
    {
        $validated = $request->validate([
            'variables' => 'nullable|array'
        ]);

        $variables = $validated['variables'] ?? [];
        $renderedContent = $emailTemplate->render($variables);

        return response()->json([
            'success' => true,
            'data' => [
                'template' => $emailTemplate,
                'variables' => $variables,
                'rendered_subject' => $renderedContent['subject'],
                'rendered_body' => $renderedContent['body']
            ],
            'message' => 'Email template preview generated successfully'
        ]);
    }

    /**
     * Get template by type
     */
    public function getByType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:order_confirmation,shipping_notification,welcome,password_reset,invoice,newsletter'
        ]);

        $template = EmailTemplate::where('type', $validated['type'])
                                ->active()
                                ->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'No active template found for this type'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $template,
            'message' => 'Email template retrieved successfully'
        ]);
    }
}
