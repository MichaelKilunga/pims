<?php

namespace App\Jobs;

use App\Models\Source;
use App\Models\Signal;
use App\Repositories\RunRepository;
use App\Intelligence\Fetching\RssFetcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('fetch');
    }

    /**
     * Execute the job.
     */
    public function handle(
        RunRepository $runRepository,
        RssFetcher $rssFetcher
    ): void {
        $run = $runRepository->start('fetch');
        
        $sources = Source::where('active', true)->get();
        $stats = [
            'sources_processed' => 0,
            'items_fetched' => 0,
            'items_stored' => 0,
            'items_skipped' => 0,
            'failures' => 0,
        ];

        foreach ($sources as $source) {
            try {
                $stats['sources_processed']++;
                
                // For now we only have RSS, but we can extend this with a factory later
                $items = $rssFetcher->fetch($source);
                $stats['items_fetched'] += count($items);

                foreach ($items as $item) {
                    // Check for duplicate fingerprint
                    if (Signal::where('fingerprint', $item['fingerprint'])->exists()) {
                        $stats['items_skipped']++;
                        continue;
                    }

                    Signal::create([
                        'domain_id' => $source->domain_id,
                        'source_id' => $source->id,
                        'title' => $item['title'],
                        'url' => $item['url'],
                        'fingerprint' => $item['fingerprint'],
                        'summary' => $item['body'], // Store body as summary for now (no AI phase yet)
                        'published_at' => $item['published_at'],
                    ]);

                    $stats['items_stored']++;
                }

                // Reset failure count on success
                $source->update([
                    'failure_count' => 0,
                    'last_fetched_at' => now(),
                ]);

            } catch (\Exception $e) {
                $stats['failures']++;
                Log::error("Fetch error for source {$source->id} ({$source->url}): " . $e->getMessage());

                $newFailureCount = $source->failure_count + 1;
                $updateData = ['failure_count' => $newFailureCount];

                if ($newFailureCount >= 5) {
                    $updateData['active'] = false;
                    Log::warning("Deactivating source {$source->id} due to repeated failures.");
                }

                $source->update($updateData);
            }
        }

        $runRepository->complete($run, $stats['items_stored'], ['stats' => $stats]);
        Log::info("FetchContentJob completed. Stored {$stats['items_stored']} new signals.");
    }
}
