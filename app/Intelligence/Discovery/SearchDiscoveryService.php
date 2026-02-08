<?php

namespace App\Intelligence\Discovery;

use App\Models\Domain;
use App\Models\Source;
use App\Intelligence\Scoring\SimpleTrustStrategy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchDiscoveryService
{
    public function __construct(
        protected SimpleTrustStrategy $trustStrategy
    ) {}

    /**
     * Discover potential sources via search engine for a domain.
     *
     * @param Domain $domain
     * @param array $queries
     * @return array statistics ['found' => int, 'added' => int, 'skipped' => int]
     */
    public function discover(Domain $domain, array $queries): array
    {
        $stats = ['found' => 0, 'added' => 0, 'skipped' => 0];
        $apiKey = config('services.serpapi.key');

        if (!$apiKey) {
            Log::error("SerpAPI key not configured.");
            return $stats;
        }

        foreach ($queries as $query) {
            try {
                $response = Http::get('https://serpapi.com/search', [
                    'q' => $query,
                    'api_key' => $apiKey,
                    'engine' => 'google',
                    'num' => 10
                ]);

                if ($response->failed()) {
                    Log::error("SerpAPI request failed for query: {$query}");
                    continue;
                }

                $results = $response->json('organic_results', []);

                foreach ($results as $result) {
                    $url = $result['link'] ?? null;
                    if (!$url) continue;

                    $rootUrl = $this->extractRootUrl($url);
                    if (!$rootUrl) continue;

                    $stats['found']++;

                    if ($this->isDuplicate($rootUrl)) {
                        $stats['skipped']++;
                        continue;
                    }

                    $trustWeight = $this->trustStrategy->calculate($rootUrl);

                    Source::create([
                        'domain_id' => $domain->id,
                        'type' => 'search',
                        'trust_weight' => $trustWeight * 100,
                        'url' => $rootUrl,
                        'active' => true,
                    ]);

                    $stats['added']++;
                }
            } catch (\Exception $e) {
                Log::error("Search discovery error: " . $e->getMessage());
            }
        }

        return $stats;
    }

    protected function extractRootUrl(string $url): ?string
    {
        $parts = parse_url($url);
        if (!isset($parts['scheme']) || !isset($parts['host'])) {
            return null;
        }

        return $parts['scheme'] . '://' . $parts['host'];
    }

    protected function isDuplicate(string $url): bool
    {
        return Source::where('url', 'like', $url . '%')->exists();
    }
}
