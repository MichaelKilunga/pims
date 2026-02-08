<?php

namespace App\Intelligence\Scoring;

use App\Models\Signal;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RelevanceScoringService
{
    /**
     * Compute relevance score for a signal.
     *
     * @param Signal $signal
     * @return float
     */
    public function calculate(Signal $signal): float
    {
        $tenant = $signal->tenant;
        $domainName = $signal->domain->name;
        
        // Tenant-specific keywords or defaults
        $keywords = data_get($tenant->settings, "keywords.{$domainName}", config("scoring.keywords.{$domainName}", []));
        
        $weights = config('scoring.weights');
        
        $keywordScore = $this->calculateKeywordDensity(
            $signal->title . ' ' . ($signal->summary ?? ''),
            $keywords
        );
        
        $trustWeight = $signal->source->trust_weight ?? 30; // 0-100
        
        $freshnessScore = $this->calculateFreshness($signal->published_at);
        
        // Final Weighted Score (0-100)
        $finalScore = (
            ($keywordScore * $weights['keyword_density']) +
            ($trustWeight * $weights['trust_weight']) +
            ($freshnessScore * $weights['freshness'])
        );
        
        return round(min(100, max(0, $finalScore)), 2);
    }

    /**
     * Calculate keyword density (0-100).
     *
     * @param string $text
     * @param array $keywords
     * @return float
     */
    protected function calculateKeywordDensity(string $text, array $keywords): float
    {
        if (empty($keywords)) return 50; // Neutral if no keywords defined

        $text = Str::lower($text);
        $matches = 0;
        
        foreach ($keywords as $keyword) {
            $keyword = Str::lower($keyword);
            if (Str::contains($text, $keyword)) {
                // Bonus for multiple occurrences? For now just existence
                $matches++;
            }
        }
        
        // Normalize: if 3+ keywords match, it's highly relevant
        $ratio = count($keywords) > 0 ? ($matches / min(3, count($keywords))) : 0;
        
        return min(1, $ratio) * 100;
    }

    /**
     * Calculate freshness score (0-100) with linear decay.
     *
     * @param Carbon|null $date
     * @return float
     */
    protected function calculateFreshness(?Carbon $date): float
    {
        if (!$date) return 50; // Neutral if date unknown
        
        $decayMinutes = config('scoring.decay_days', 7) * 24 * 60;
        $diffMinutes = abs(now()->diffInMinutes($date));
        
        if ($diffMinutes >= $decayMinutes) return 0;
        
        return (1 - ($diffMinutes / $decayMinutes)) * 100;
    }
}
