<?php

return [
    'eggs' => [
        'max_batches' => 5,
        'hatcher_warning_days' => 2,
        'hatcher_critical_days' => 0,
        'hatch_rate_warning' => 85,
        'hatch_rate_critical' => 70,
    ],

    'feed' => [
        'phases' => [
            [
                'key' => 'doq',
                'label' => 'Starter / DOQ',
                'min_day' => 0,
                'max_day' => 14,
                'target_feed_per_bird_kg' => 0.012, // 12 gram per hari
            ],
            [
                'key' => 'grower',
                'label' => 'Grower',
                'min_day' => 15,
                'max_day' => 35,
                'target_feed_per_bird_kg' => 0.022, // 22 gram per hari
            ],
            [
                'key' => 'layer',
                'label' => 'Layer / Produksi',
                'min_day' => 36,
                'max_day' => null,
                'target_feed_per_bird_kg' => 0.028, // 28 gram per hari
            ],
        ],
        'warning_ratio' => 0.1,   // ±10% dari target dianggap perlu perhatian
        'critical_ratio' => 0.2,  // ±20% dianggap kritis
        'history_days' => 7,
        'max_insights' => 5,
    ],

    'mortality' => [
        'window_days' => 3,
        'warning_pct' => 3,
        'critical_pct' => 5,
        'max_items' => 4,
    ],

    'dashboard' => [
        'trend_days' => 7,
    ],
];
