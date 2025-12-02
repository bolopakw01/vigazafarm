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
                'eggs' => [
                    'max_batches' => (int) config('dss.eggs.max_batches'),
                    'hatcher_warning_days' => (int) config('dss.eggs.hatcher_warning_days'),
                    'hatcher_critical_days' => (int) config('dss.eggs.hatcher_critical_days'),
                    'hatch_rate_warning' => (float) config('dss.eggs.hatch_rate_warning'),
                    'hatch_rate_critical' => (float) config('dss.eggs.hatch_rate_critical'),
                ],
                'feed' => [
                    'max_insights' => (int) config('dss.feed.max_insights'),
                    'history_days' => (int) config('dss.feed.history_days'),
                    'warning_ratio' => (float) config('dss.feed.warning_ratio'),
                    'critical_ratio' => (float) config('dss.feed.critical_ratio'),
                ],
                'mortality' => [
                    'window_days' => (int) config('dss.mortality.window_days'),
                    'max_items' => (int) config('dss.mortality.max_items'),
                    'warning_pct' => (float) config('dss.mortality.warning_pct'),
                    'critical_pct' => (float) config('dss.mortality.critical_pct'),
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
