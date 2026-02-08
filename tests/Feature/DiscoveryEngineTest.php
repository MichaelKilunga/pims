<?php

namespace Tests\Feature;

use App\Jobs\DiscoverSourcesJob;
use App\Models\Domain;
use App\Models\Source;
use App\Models\Run;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DiscoveryEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_discovery_job_runs_successfully()
    {
        $domain = Domain::create(['name' => 'Technology & AI', 'priority' => 8]);
        
        // Mock SerpAPI
        Http::fake([
            'serpapi.com/*' => Http::response([
                'organic_results' => [
                    ['link' => 'https://example.com/blog/article1'],
                    ['link' => 'https://techcrunch.com/2026/news'],
                ]
            ], 200),
        ]);

        // Note: SimplePie is hard to mock directly without dependency injection in the test.
        // For this test, we'll assume the URL extraction logic works.
        // In a real scenario, we might use a mockable wrapper for SimplePie.

        config(['services.serpapi.key' => 'test_key']);
        config(['discovery.seeds.Technology & AI' => ['https://valid-rss.com/feed']]);
        config(['discovery.queries.Technology & AI' => ['test query']]);

        // Dispatch job
        (new DiscoverSourcesJob($domain->id))->handle(
            app(\App\Repositories\RunRepository::class),
            app(\App\Intelligence\Discovery\RssDiscoveryService::class),
            app(\App\Intelligence\Discovery\SearchDiscoveryService::class)
        );

        // Assert sources added (Search discovery adds root domains)
        $this->assertDatabaseHas('sources', [
            'domain_id' => $domain->id,
            'url' => 'https://example.com',
        ]);

        $this->assertDatabaseHas('sources', [
            'domain_id' => $domain->id,
            'url' => 'https://techcrunch.com',
        ]);

        // Assert run recorded
        $this->assertDatabaseHas('runs', [
            'type' => 'discovery',
            'status' => 'completed',
        ]);

        $run = Run::where('type', 'discovery')->first();
        $this->assertNotNull($run->meta);
        $this->assertArrayHasKey('breakdown', $run->meta);
    }
}
