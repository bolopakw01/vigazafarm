<?php

namespace App\Services\Ml;

use Illuminate\Support\Facades\File;

class DssModelService
{
    private string $artifactPath;

    public function __construct(?string $artifactPath = null)
    {
        $this->artifactPath = $artifactPath ?? base_path('ml/artifacts/dss_model.onnx');
    }

    /**
     * Placeholder inference hook. Replace once a real model artifact exists.
     */
    public function predict(array $payload): array
    {
        return [
            'model_version' => $this->artifactExists() ? File::lastModified($this->artifactPath) : 'untrained',
            'recommendations' => [
                [
                    'category' => 'placeholder',
                    'summary' => 'Model belum dilatih. Jalankan ml/scripts/train_pipeline.py setelah data siap.',
                    'action_items' => [
                        'Kumpulkan dataset historis pakan, kematian, dan telur.',
                        'Lengkapi notebook eksplorasi untuk mendefinisikan fitur & label.',
                        'Ekspor artefak ke ml/artifacts/dss_model.onnx lalu update layanan ini.',
                    ],
                ],
            ],
        ];
    }

    public function artifactExists(): bool
    {
        return File::exists($this->artifactPath);
    }

    public function artifactPath(): string
    {
        return $this->artifactPath;
    }
}
