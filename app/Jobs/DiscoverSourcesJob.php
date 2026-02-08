<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\Run;
use App\Repositories\RunRepository;
use App\Intelligence\Discovery\RssDiscoveryService;
use App\Intelligence\Discovery\SearchDiscoveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PlanEnforcementService;
use Illuminate\Support\Facades\Log;

class DiscoverSourcesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $domainId
    ) {
        $this->onQueue('discovery');
    }

    /**
     * Execute the job.
     */
    public function handle(
        RunRepository $runRepository,
        RssDiscoveryService $rssService,
        SearchDiscoveryService $searchService,
        PlanEnforcementService $planService
    ): void {
        $domain = Domain::find($this->domainId);
        if (!$domain) {
            Log::error("Discovery Job: Domain ID {$this->domainId} not found.");
            return;
        }

        $run = $runRepository->start('discovery');
        $runRepository->update($run, ['meta' => ['domain' => $domain->name]]);

        if ($domain->tenant_id && $tenant = $domain->tenant) {
            if (!$planService->can($tenant, 'add_source', ['domain_id' => $domain->id])) {
                $runRepository->complete($run, 0, [
                    'status' => 'blocked_plan_limit',
                    'reason' => 'Source limit reached for this domain on the ' . $tenant->plan . ' plan.'
                ]);
                Log::warning("Discovery blocked for Domain {$domain->id} (Tenant {$tenant->id}) due to plan limits.");
                return;
            }
        }

        try {
            $stats = [
                'rss' => ['found' => 0, 'added' => 0, 'skipped' => 0],
                'search' => ['found' => 0, 'added' => 0, 'skipped' => 0],
            ];

            // 1. RSS Discovery
            $seeds = config("discovery.seeds.{$domain->name}", []);
            if (!empty($seeds)) {
                $stats['rss'] = $rssService->discover($domain, $seeds);
            }

            // 2. Search Discovery
            $queries = config("discovery.queries.{$domain->name}", []);
            if (!empty($queries)) {
                $stats['search'] = $searchService->discover($domain, $queries);
            }

            $totalAdded = $stats['rss']['added'] + $stats['search']['added'];
            $runRepository->complete($run, $totalAdded, ['breakdown' => $stats]);

            Log::info("Discovery Job completed for Domain: {$domain->name}. Added {$totalAdded} sources.");

        } catch (\Exception $e) {
            Log::error("Discovery Job failed for Domain: {$domain->name}. Error: " . $e->getMessage());
            $runRepository->fail($run, $e->getMessage());
            throw $e;
        }
    }
}
