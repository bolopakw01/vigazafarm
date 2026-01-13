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
            'enabled' => filter_var($payload['enabled'] ?? Arr::get($defaults, 'enabled', true), FILTER_VALIDATE_BOOLEAN),
            'mode' => $payload['mode'] ?? Arr::get($defaults, 'mode', 'config'),
            'config' => array_replace_recursive($defaults['config'], $payload['config'] ?? []),
            'ml' => array_replace_recursive($defaults['ml'], $payload['ml'] ?? []),
        ];

        $settings['ml']['artifact_label'] = trim((string) Arr::get($settings, 'ml.artifact_label', '')) ?: null;
        $settings['ml']['notes'] = trim((string) Arr::get($settings, 'ml.notes', ''));
        $settings['ml']['capabilities'] = $this->normalizeCapabilities(
            Arr::get($payload, 'ml.capabilities', Arr::get($settings, 'ml.capabilities', [])),
            Arr::get($defaults, 'ml.capabilities', [])
        );

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

        // apply enabled flag to config
        config(['dss.enabled' => Arr::get($settings, 'enabled', config('dss.enabled', true))]);

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

    public function defaultSettings(): array
    {
        return [
            'enabled' => (bool) config('dss.enabled', true),
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
                'notes' => 'Catat versi model, tanggal training, dan sumber dataset.',
                'capabilities' => [
                    'egg_forecast' => true,
                    'feed_prediction' => true,
                    'mortality_detection' => true,
                    'pricing_optimizer' => true,
                    'explainability' => true,
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

    protected function normalizeCapabilities($capabilities, array $fallback): array
    {
        if (empty($fallback)) {
            return (array) $capabilities;
        }

        $normalized = [];

        foreach ($fallback as $key => $defaultValue) {
            $value = Arr::get((array) $capabilities, $key, $defaultValue);
            $normalized[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if (is_null($normalized[$key])) {
                $normalized[$key] = (bool) $defaultValue;
            }
        }

        return $normalized;
    }
}
