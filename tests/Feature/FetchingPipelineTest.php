<?php

namespace Tests\Feature;

use App\Jobs\FetchContentJob;
use App\Models\Domain;
use App\Models\Source;
use App\Models\Signal;
use App\Models\Run;
use App\Intelligence\Fetching\RssFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class FetchingPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetching_pipeline_stores_unique_signals()
    {
        $domain = Domain::create(['name' => 'Technology & AI', 'priority' => 8]);
        $source = Source::create([
            'domain_id' => $domain->id,
            'type' => 'rss',
            'url' => 'https://tech-feed.com/rss',
            'active' => true,
        ]);

        $mockItems = [
            [
                'title' => 'New AI Breakthrough',
                'body' => 'Content of the breakthrough...',
                'url' => 'https://tech-feed.com/ai-breakthrough',
                'fingerprint' => 'hash1',
                'published_at' => now()->toDateTimeString(),
            ],
            [
                'title' => 'Quantum Computing News',
                'body' => 'Future of computing...',
                'url' => 'https://tech-feed.com/quantum',
                'fingerprint' => 'hash2',
                'published_at' => now()->toDateTimeString(),
            ],
        ];

        $rssFetcher = Mockery::mock(RssFetcher::class);
        $rssFetcher->shouldReceive('fetch')->with(Mockery::on(function ($s) use ($source) {
            return $s->id === $source->id;
        }))->andReturn($mockItems);

        $this->app->instance(RssFetcher::class, $rssFetcher);

        // Run Job
        (new FetchContentJob())->handle(
            app(\App\Repositories\RunRepository::class),
            $rssFetcher
        );

        // Assert signals stored
        $this->assertDatabaseCount('signals', 2);
        $this->assertDatabaseHas('signals', ['fingerprint' => 'hash1']);
        $this->assertDatabaseHas('signals', ['fingerprint' => 'hash2']);

        // Run again with same items - should skip
        (new FetchContentJob())->handle(
            app(\App\Repositories\RunRepository::class),
            $rssFetcher
        );

        $this->assertDatabaseCount('signals', 2); // Still 2

        // Assert runs recorded
        $runs = Run::where('type', 'fetch')->orderBy('id', 'desc')->take(2)->get();
        $this->assertCount(2, $runs);
        
        // Latest run should be 0 findings
        $this->assertEquals(0, $runs[0]->findings_count);
        $this->assertEquals('completed', $runs[0]->status);
        
        // Previous run should be 2 findings
        $this->assertEquals(2, $runs[1]->findings_count);
    }

    public function test_source_deactivates_after_repeated_failures()
    {
        $domain = Domain::create(['name' => 'Geopolitics', 'priority' => 10]);
        $source = Source::create([
            'domain_id' => $domain->id,
            'type' => 'rss',
            'url' => 'https://broken-feed.com/rss',
            'active' => true,
            'failure_count' => 4,
        ]);

        $rssFetcher = Mockery::mock(RssFetcher::class);
        $rssFetcher->shouldReceive('fetch')->andThrow(new \Exception("Connection failed"));

        $this->app->instance(RssFetcher::class, $rssFetcher);

        // Run Job
        try {
            (new FetchContentJob())->handle(
                app(\App\Repositories\RunRepository::class),
                $rssFetcher
            );
        } catch (\Exception $e) {
            // Expected
        }

        // Assert source deactivated
        $source->refresh();
        $this->assertFalse($source->active);
        $this->assertEquals(5, $source->failure_count);
    }
}
