<?php

namespace Tests\Feature;

use App\Jobs\ScoreRelevanceJob;
use App\Models\Domain;
use App\Models\Source;
use App\Models\Signal;
use App\Models\Run;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_scoring_engine_qualifies_relevant_signals()
    {
        $domain = Domain::create(['name' => 'Technology & AI', 'priority' => 8]);
        $source = Source::create([
            'domain_id' => $domain->id,
            'type' => 'rss',
            'url' => 'https://tech-feed.com/rss',
            'trust_weight' => 80, // High trust
            'active' => true,
        ]);

        // 1. Relevant Signal (matches keywords + high trust)
        $relevantSignals = Signal::create([
            'domain_id' => $domain->id,
            'source_id' => $source->id,
            'title' => 'Breakthrough in LLM neural architecture',
            'url' => 'https://tech-feed.com/llm-breakthrough',
            'fingerprint' => 'hash1',
            'summary' => 'This new neural scaling method for semiconductors...',
            'published_at' => now(),
        ]);

        // 2. Irrelevant Signal (no keywords)
        $irrelevantSignal = Signal::create([
            'domain_id' => $domain->id,
            'source_id' => $source->id,
            'title' => 'Standard news update',
            'url' => 'https://tech-feed.com/standard',
            'fingerprint' => 'hash2',
            'summary' => 'Some general content without any specific domain terms.',
            'published_at' => now()->subDays(10), // Old
        ]);

        // Run Job
        (new ScoreRelevanceJob())->handle(
            app(\App\Repositories\RunRepository::class),
            app(\App\Intelligence\Scoring\RelevanceScoringService::class)
        );

        $relevantSignals->refresh();
        $irrelevantSignal->refresh();

        // Assert relevant signal qualified
        // dump($relevantSignals->relevance_score);
        $this->assertTrue($relevantSignals->relevance_score > 40);
        $this->assertTrue($relevantSignals->qualified_for_analysis);

        // Assert irrelevant signal rejected
        $this->assertLessThan(40, $irrelevantSignal->relevance_score);
        $this->assertFalse($irrelevantSignal->qualified_for_analysis);

        // Assert run recorded
        $run = Run::where('type', 'analysis')->latest()->first();
        $this->assertEquals(1, $run->findings_count);
        $this->assertEquals('completed', $run->status);
    }
}
