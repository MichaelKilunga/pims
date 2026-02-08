<?php

return [
    'seeds' => [
        'Geopolitics' => [
            'https://www.foreignaffairs.com/rss.xml',
            'https://www.crisisgroup.org/rss.xml',
            'https://www.chathamhouse.org/rss.xml',
        ],
        'Finance & Economics' => [
            'https://www.economist.com/finance-and-economics/rss.xml',
            'https://www.ft.com/?format=rss',
            'https://feeds.bloomberg.com/business/news.rss',
        ],
        'Technology & AI' => [
            'https://www.technologyreview.com/feed/',
            'https://distill.pub/rss.xml',
            'https://openai.com/news/rss/',
        ],
        // ... more can be added later
    ],

    'queries' => [
        'Geopolitics' => ['geopolitical shifts 2026', 'strategic intelligence reports', 'global conflict monitoring'],
        'Finance & Economics' => ['market intelligence 2026', 'economic forecast reports', 'fiscal policy analysis'],
        'Technology & AI' => ['artificial intelligence breakthroughs 2026', 'emerging tech trends', 'semiconductor industry news'],
        'Health & Bio-Security' => ['pandemic preparedness 2026', 'biosecurity research', 'epidemiology trends'],
        'Climate & Environment' => ['climate transition risk', 'environmental policy shifts', 'renewables intelligence'],
        'Corporate Intelligence' => ['supply chain disruption 2026', 'competitor intelligence trends', 'industry espionage prevention'],
        'Cybersecurity' => ['zero-day vulnerability trends 2026', 'nation-state cyber attacks', 'ransomware intelligence'],
    ],
];
