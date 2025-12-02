<?php

namespace App\Services\Dss;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DssSettingsService
{
    protected string $storageFile = 'dss_settings.json';
    protected ?array $cached = null;

    public function getSettings(): array
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        $defaults = $this->defaultSettings();
        $stored = $this->readFromDisk();

        return $this->cached = array_replace_recursive($defaults, $stored);
    }

    public function save(array $payload): array
    {
        $defaults = $this->defaultSettings();

        $settings = [
            'mode' => $payload['mode'] ?? Arr::get($defaults, 'mode', 'config'),
            'config' => array_replace_recursive($defaults['config'], $payload['config'] ?? []),
            'ml' => array_replace_recursive($defaults['ml'], $payload['ml'] ?? []),
        ];

        Storage::disk('local')->put(
            $this->storageFile,
            json_encode($settings, JSON_PRETTY_PRINT)
        );

        $this->cached = $settings;

        return $settings;
    }

    public function applyOverrides(): void
    {
        $settings = $this->getSettings();
        $overrides = Arr::get($settings, 'config', []);

        if (!empty($overrides)) {
            config(['dss' => array_replace_recursive(config('dss'), $overrides)]);
        }
    }

    public function isMlMode(): bool
    {
        return $this->getMode() === 'ml';
    }

    public function getMode(): string
    {
        return $this->getSettings()['mode'] ?? 'config';
    }

    public function getMlMetrics(): array
    {
        $metrics = Arr::get($this->getSettings(), 'ml.metrics', []);
        return is_array($metrics) ? $metrics : [];
    }

    public function defaultSettings(): array
    {
        return [
            'mode' => 'config',
            'config' => [
                'feed' => [
                    'max_insights' => (int) config('dss.feed.max_insights'),
                    'history_days' => (int) config('dss.feed.history_days'),
                    'warning_ratio' => (float) config('dss.feed.warning_ratio'),
                    'critical_ratio' => (float) config('dss.feed.critical_ratio'),
                ],
                'stock' => [
                    'max_items' => (int) config('dss.stock.max_items'),
                    'cover_warning_days' => (float) config('dss.stock.cover_warning_days'),
                    'cover_critical_days' => (float) config('dss.stock.cover_critical_days'),
                ],
                'environment' => [
                    'max_items' => (int) config('dss.environment.max_items'),
                ],
                'health' => [
                    'window_days' => (int) config('dss.health.window_days'),
                    'max_items' => (int) config('dss.health.max_items'),
                    'warning_pct' => (float) config('dss.health.warning_pct'),
                    'critical_pct' => (float) config('dss.health.critical_pct'),
                ],
            ],
            'ml' => [
                'default_phase' => 'grower',
                'artifact_label' => null,
                'notes' => 'Isi catatan mengenai versi model, tanggal training, atau sumber data.',
                'metrics' => [
                    'fcr' => 1.8,
                    'mortality_pct' => 0.4,
                ],
            ],
        ];
    }

    protected function readFromDisk(): array
    {
        if (!Storage::disk('local')->exists($this->storageFile)) {
            return [];
        }

        $raw = Storage::disk('local')->get($this->storageFile);
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
