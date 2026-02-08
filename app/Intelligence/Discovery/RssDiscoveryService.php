<?php

namespace App\Intelligence\Discovery;

use App\Models\Source;
use App\Models\Domain;
use App\Intelligence\Scoring\SimpleTrustStrategy;
use SimplePie\SimplePie;
use Illuminate\Support\Facades\Log;

class RssDiscoveryService
{
    public function __construct(
        protected SimpleTrustStrategy $trustStrategy
    ) {}

    /**
     * Discover RSS feeds for a domain.
     *
     * @param Domain $domain
     * @param array $seeds
     * @return array statistics ['found' => int, 'added' => int, 'skipped' => int]
     */
    public function discover(Domain $domain, array $seeds): array
    {
        $stats = ['found' => 0, 'added' => 0, 'skipped' => 0];

        foreach ($seeds as $url) {
            $stats['found']++;

            if ($this->isDuplicate($url)) {
                $stats['skipped']++;
                continue;
            }

            if ($this->isValidRss($url)) {
                $trustWeight = $this->trustStrategy->calculate($url);
                
                Source::create([
                    'domain_id' => $domain->id,
                    'type' => 'rss',
                    'trust_weight' => $trustWeight * 100, // Normalized to 0-100 as per requested schema
                    'url' => $url,
                    'active' => true,
                ]);

                $stats['added']++;
            } else {
                $stats['skipped']++;
            }
        }

        return $stats;
    }

    protected function isDuplicate(string $url): bool
    {
        return Source::where('url', $url)->exists();
    }

    protected function isValidRss(string $url): bool
    {
        $feed = new SimplePie();
        $feed->set_feed_url($url);
        $feed->enable_cache(false);
        $feed->set_useragent('PIMS-Discovery-Engine/1.0');
        
        // Timeout handling
        $feed->set_timeout(10);
        
        $success = $feed->init();
        
        if ($success && !$feed->error()) {
            return true;
        }

        Log::warning("Discovery: Invalid or unreachable RSS feed: {$url}. Error: " . $feed->error());
        return false;
    }
}
