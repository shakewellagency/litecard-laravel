<?php

namespace Shakewell\Litecard\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Shakewell\Litecard\LiteCardException;
use Shakewell\Litecard\Services\TemplateService;

class TemplateController extends Controller
{
    protected TemplateService $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Get all templates.
     */
    public function index(): JsonResponse
    {
        try {
            $templates = $this->templateService->getTemplates();

            return response()->json([
                'success' => true,
                'data' => $templates,
            ]);

        } catch (LiteCardException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve templates: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get template by ID.
     */
    public function show(string $templateId): JsonResponse
    {
        try {
            $template = $this->templateService->getTemplate($templateId);

            return response()->json([
                'success' => true,
                'data' => $template,
            ]);

        } catch (LiteCardException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found: '.$e->getMessage(),
            ], 404);
        }
    }
}
