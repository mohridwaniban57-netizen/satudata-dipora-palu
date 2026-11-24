<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maintenance Scoring Weights & Thresholds (PRD §7.2)
    |--------------------------------------------------------------------------
    |
    | risk_score = w_age * age_factor + w_insp * inspection_deficit + w_dam * damage_frequency
    | condition_score = 100 - risk_score
    |
    */
    'maintenance' => [
        'weights' => [
            'age_factor' => 0.30,
            'inspection_deficit' => 0.50,
            'damage_frequency' => 0.20,
        ],
        'thresholds' => [
            'good' => 75.0,
            'watch' => 50.0,
        ],
        'damage_incident_cap' => 5,
        'damage_window_months' => 24,
        'default_design_life_years' => 30,
        'default_base_interval_days' => 180,
    ],

    /*
    |--------------------------------------------------------------------------
    | Privacy & Data Protection (UU No. 27/2022)
    |--------------------------------------------------------------------------
    */
    'k_anonymity_min' => (int) env('K_ANONYMITY_MIN', 5),

    /*
    |--------------------------------------------------------------------------
    | Policy Simulation Assumptions (PRD §7.4)
    |--------------------------------------------------------------------------
    */
    'simulation' => [
        'default_beta_budget' => 0.0000005, // point uplift per Rp 1 budget increase
        'renovation_status_uplift' => 0.10,  // +10% usage index per status tier improvement
    ],

    /*
    |--------------------------------------------------------------------------
    | Levy Optimization Matrix Recommendations (PRD §7.3)
    |--------------------------------------------------------------------------
    */
    'levy' => [
        'operating_days_per_year' => 365,
        'operating_days_per_month' => 30,
        'recommendations' => [
            'star' => [
                'classification' => 'Star',
                'title' => 'Fasilitas Unggulan',
                'recommendation' => 'Pertahankan kualitas fasilitas dan jadwalkan pemeliharaan di luar jam sibuk.',
            ],
            'leakage' => [
                'classification' => 'Under-priced / leakage',
                'title' => 'Tingkat Penggunaan Tinggi, Pendapatan Rendah',
                'recommendation' => 'Evaluasi penyesuaian tarif retribusi dan perketat pengawasan penarikan retribusi.',
            ],
            'niche' => [
                'classification' => 'Niche / event-driven',
                'title' => 'Pendapatan Tinggi, Penggunaan Musiman',
                'recommendation' => 'Pertahankan tarif kompetitif dan buat program aktivasi untuk penggunaan rutin.',
            ],
            'under_utilized' => [
                'classification' => 'Under-utilized',
                'title' => 'Pemanfaatan Rendah',
                'recommendation' => 'Tingkatkan promosi penggunaan, evaluasi fungsi fasilitas, atau adakan event komunitas.',
            ],
        ],
    ],
];
