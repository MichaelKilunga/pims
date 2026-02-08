<?php

return [
    'student' => [
        'max_domains' => 3,
        'max_sources_per_domain' => 10,
        'digest_frequencies' => ['weekly'],
        'ai_monthly_budget' => 2.00,
        'ai_depth' => 'basic', // basic = summary + implications only
        'features' => [
            'custom_keywords' => false,
            'daily_digest' => false,
            'manual_priority_override' => false,
            'api_access' => false,
        ],
    ],
    'free' => [
        'max_domains' => 1,
        'max_sources_per_domain' => 5,
        'digest_frequencies' => ['weekly'],
        'ai_monthly_budget' => 0.50,
        'ai_depth' => 'basic',
        'features' => [
            'custom_keywords' => false,
            'daily_digest' => false,
            'manual_priority_override' => false,
            'api_access' => false,
        ],
    ],
    'pro' => [
        'max_domains' => 100,
        'max_sources_per_domain' => 1000,
        'digest_frequencies' => ['daily', 'weekly', 'both'],
        'ai_monthly_budget' => 50.00,
        'ai_depth' => 'extended',
        'features' => [
            'custom_keywords' => true,
            'daily_digest' => true,
            'manual_priority_override' => true,
            'api_access' => true,
        ],
    ],
];
