<?php

namespace App\Intelligence\Scoring;

use App\Intelligence\Contracts\TrustStrategyInterface;

class SimpleTrustStrategy implements TrustStrategyInterface
{
    protected array $officialDomains = [
        'gov', 'edu', 'org', 'reuters.com', 'apnews.com', 'bloomberg.com'
    ];

    protected array $newsDomains = [
        'nytimes.com', 'wsj.com', 'ft.com', 'economist.com', 'theguardian.com'
    ];

    public function calculate(string $url, array $metadata = []): float
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return 0.1;
        }

        // Remove www.
        $host = preg_replace('/^www\./', '', $host);

        foreach ($this->officialDomains as $domain) {
            if (str_ends_with($host, $domain)) {
                return 0.9;
            }
        }

        foreach ($this->newsDomains as $domain) {
            if (str_ends_with($host, $domain)) {
                return 0.7;
            }
        }

        // Check if it's a known blog platform but unknown specific blog
        if (str_contains($host, 'substack.com') || str_contains($host, 'medium.com') || str_contains($host, 'wordpress.com')) {
            return 0.4;
        }

        return 0.3; // Default low trust for unknown sites
    }
}
