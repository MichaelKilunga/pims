<?php

namespace App\Intelligence\Fetching;

use App\Models\Source;
use SimplePie\SimplePie;
use Illuminate\Support\Facades\Log;

class RssFetcher implements SourceFetcherInterface
{
    public function __construct(
        protected ContentNormalizerService $normalizer
    ) {}

    /**
     * Fetch items from an RSS feed.
     *
     * @param Source $source
     * @return array
     */
    public function fetch(Source $source): array
    {
        $feed = new SimplePie();
        $feed->set_feed_url($source->url);
        $feed->enable_cache(false);
        $feed->set_timeout(15);
        
        $success = $feed->init();
        
        if (!$success || $feed->error()) {
            throw new \Exception("RSS Fetch Error for {$source->url}: " . ($feed->error() ?? 'Unknown error'));
        }

        $items = [];
        foreach ($feed->get_items() as $item) {
            $items[] = $this->normalizer->normalize(
                $item->get_title(),
                $item->get_content() ?? $item->get_description() ?? '',
                $item->get_permalink(),
                $item->get_date('Y-m-d H:i:s')
            );
        }

        return $items;
    }
}
