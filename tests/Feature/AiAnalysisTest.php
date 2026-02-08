<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeSignalJob;
use App\Models\Domain;
use App\Models\Source;
use App\Models\Signal;
use App\Models\Run;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_analysis_processes_qualified_signals()
    {
        $domain = Domain::create(['name' => 'Geopolitics', 'priority' => 10]);
        
        config(['ai.openai.api_key' => 'fake-key']);
        Http::preventStrayRequests();

        $source = Source::create([
            'domain_id' => $domain->id,
            'type' => 'rss',
            'url' => 'https://geopols.com/rss',
            'active' => true,
        ]);

        $signal = Signal::create([
            'domain_id' => $domain->id,
            'source_id' => $source->id,
            'title' => 'Diplomatic crisis in East Asia',
            'url' => 'https://geopols.com/crisis',
            'fingerprint' => 'crisis-hash',
            'summary' => 'Tensions are rising as new alliances form...',
            'qualified_for_analysis' => true,
            'relevance_score' => 85.0,
        ]);

        // Mock OpenAI Response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'summary' => 'Major power shift in East Asia.',
                                'implications' => 'High risk of conflict.',
                                'action_required' => 2,
                            ]),
                        ],
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 100,
                    'completion_tokens' => 50,
                    'total_tokens' => 150,
                ],
            ], 200),
        ]);

        // Run Job
        (new AnalyzeSignalJob())->handle(
            app(\App\Repositories\RunRepository::class),
            app(\App\Intelligence\Analysis\AiAnalysisService::class)
        );

        $signal->refresh();

        // Assert signal updated
        $this->assertEquals('Major power shift in East Asia.', $signal->summary);
        $this->assertEquals('High risk of conflict.', $signal->implications);
        $this->assertEquals(2, $signal->action_required);

        // Assert run recorded cost
        $run = Run::where('type', 'analysis')->latest()->first();
        $this->assertNotNull($run);
        $this->assertEquals(1, $run->findings_count);
        $this->assertGreaterThan(0, $run->meta['stats']['total_cost']);
    }

    public function test_ai_analysis_skips_unqualified_or_processed_signals()
    {
        $domain = Domain::create(['name' => 'Finance & Economics', 'priority' => 9]);
        $source = Source::create([
            'domain_id' => $domain->id,
            'type' => 'rss',
            'url' => 'https://finance.com/rss',
            'active' => true,
        ]);

        // Unqualified signal
        Signal::create([
            'domain_id' => $domain->id,
            'source_id' => $source->id,
            'title' => 'Stock update',
            'url' => 'https://finance.com/s1',
            'fingerprint' => 'hash1',
            'summary' => 'Some content',
            'qualified_for_analysis' => false,
        ]);

        // Already analyzed signal
        Signal::create([
            'domain_id' => $domain->id,
            'source_id' => $source->id,
            'title' => 'Inflation news',
            'url' => 'https://finance.com/s2',
            'fingerprint' => 'hash2',
            'summary' => 'Some content',
            'qualified_for_analysis' => true,
            'implications' => 'Existing implication',
        ]);

        // Mock OpenAI (should not be called)
        Http::fake();

        // Run Job
        (new AnalyzeSignalJob())->handle(
            app(\App\Repositories\RunRepository::class),
            app(\App\Intelligence\Analysis\AiAnalysisService::class)
        );

        Http::assertNothingSent();
    }
}
