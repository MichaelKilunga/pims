<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Relevance Threshold
    |--------------------------------------------------------------------------
    |
    | Signals with a score equal to or greater than this threshold will be 
    | marked as 'qualified_for_analysis'.
    |
    */
    'threshold' => 40.0,

    /*
    |--------------------------------------------------------------------------
    | Scoring Weights
    |--------------------------------------------------------------------------
    |
    | Factors determining the final relevance score (0-100).
    |
    */
    'weights' => [
        'keyword_density' => 0.5,
        'trust_weight' => 0.3,
        'freshness' => 0.2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Freshness Decay (Days)
    |--------------------------------------------------------------------------
    |
    | How many days before a signal's freshness score reaches 0.
    |
    */
    'decay_days' => 7,

    /*
    |--------------------------------------------------------------------------
    | Domain Keywords
    |--------------------------------------------------------------------------
    |
    | Specific terms used to calculate keyword density.
    |
    */
    'keywords' => [
        'Geopolitics' => ['treaty', 'sanctions', 'conflict', 'diplomatic', 'territory', 'alliance', 'intelligence', 'ministry'],
        'Finance & Economics' => ['inflation', 'gdp', 'fiscal', 'monetary', 'recession', 'markets', 'central bank', 'yield'],
        'Technology & AI' => ['neural', 'llm', 'semiconductor', 'quantum', 'gpu', 'automation', 'scaling', 'open source'],
        'Health & Bio-Security' => ['outbreak', 'pathogen', 'vaccine', 'mutation', 'epidemic', 'biosafety', 'who', 'cdc'],
        'Climate & Environment' => ['emissions', 'net-zero', 'carbon', 'warming', 'drought', 'biodiversity', 'cop28', 'renewable'],
        'Corporate Intelligence' => ['merger', 'acquisition', 'restructuring', 'layoff', 'patent', 'earnings', 'sec filing'],
        'Cybersecurity' => ['vulnerability', 'exploits', 'ransomware', 'apt', 'zero-day', 'breach', 'encryption', 'phishing'],
    ],
];
