<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ml\DssModelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MlPreviewController extends Controller
{
    public function __construct(private readonly DssModelService $modelService)
    {
    }

    public function predict(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phase' => ['required', 'string'],
            'metrics' => ['required', 'array'],
            'metadata' => ['nullable', 'array'],
        ]);

        $prediction = $this->modelService->predict($validated);

        return response()->json([
            'metadata' => [
                'model_version' => $prediction['model_version'],
                'artifact_path' => $this->modelService->artifactPath(),
                'generated_at' => now()->toIso8601String(),
            ],
            'status' => $this->modelService->artifactExists() ? 'ready' : 'waiting_for_training',
            'recommendations' => $prediction['recommendations'],
        ]);
    }
}
