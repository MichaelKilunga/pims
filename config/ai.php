<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Pricing (Estimated USD per 1k tokens)
    |--------------------------------------------------------------------------
    */
    'pricing' => [
        'gpt-4o-mini' => [
            'input' => 0.00015,
            'output' => 0.0006,
        ],
        'gpt-4o' => [
            'input' => 0.005,
            'output' => 0.015,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain-Specific Prompts
    |--------------------------------------------------------------------------
    */
    'prompts' => [
        'default' => "Analyze the following intelligence signal for the '{domain}' domain.\n\nTitle: {title}\nContent: {content}\n\n{depth_instruction}\n\nProvide the following in JSON format:\n{\n  \"summary\": \"Concise 2-sentence summary of what happened.\",\n  \"implications\": \"What this means for strategy or risk (2-3 sentences).\",\n  \"action_required\": 0|1|2\n}\n\nAction Required Mapping:\n0: Ignore (Routine news)\n1: Watch (Monitor for developments)\n2: Act (Requires immediate strategic review or response)",
        
        'Geopolitics' => "You are a senior geopolitical analyst. Analyze this signal for '{domain}'. Focus on regional stability, treaty implications, and power shifts.\n\nTitle: {title}\nContent: {content}\n\n{depth_instruction}\n\n{format_instruction}",
        
        'Finance & Economics' => "You are an economic strategist. Analyze this signal for '{domain}'. Focus on market volatility, inflationary pressure, and central bank reactions.\n\nTitle: {title}\nContent: {content}\n\n{depth_instruction}\n\n{format_instruction}",
    ],

    'format_instruction' => "Provide the analysis in strictly valid JSON format with keys: 'summary', 'implications', and 'action_required' (integer 0, 1, or 2).",
];
