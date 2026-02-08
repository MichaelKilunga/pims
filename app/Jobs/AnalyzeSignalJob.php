<?php

namespace App\Jobs;

use App\Models\Signal;
use App\Models\Run;
use App\Repositories\RunRepository;
use App\Intelligence\Analysis\AiAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PlanEnforcementService;
use Illuminate\Support\Facades\Log;

class AnalyzeSignalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(public ?int $tenantId = null)
    {
        $this->onQueue('analysis');
    }

    /**
     * Execute the job.
     */
    public function handle(
        RunRepository $runRepository,
        AiAnalysisService $aiService,
        PlanEnforcementService $planService
    ): void {
        $run = $runRepository->start('analysis');
        
        $tenantId = $this->tenantId ?: config('app.tenant_id');
        $tenant = \App\Models\Tenant::find($tenantId);

        $depth = $tenant ? $planService->getAiDepth($tenant) : 'extended';

        // Check if run is starting blocked
        $isBlocked = $tenant && !$planService->can($tenant, 'analyze_ai');

        // Find qualified signals that haven't been analyzed yet
        $query = Signal::where('qualified_for_analysis', true)
            ->whereNull('implications');
            
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $signals = $query->limit(50)->get();

        $stats = [
            'processed' => 0,
            'failed' => 0,
            'skipped' => 0,
            'total_cost' => 0,
            'total_tokens' => 0,
        ];

        foreach ($signals as $signal) {
            try {
                // Enforcement check per signal (in case it became blocked mid-run)
                if ($isBlocked || ($tenant && !$planService->can($tenant, 'analyze_ai'))) {
                    $isBlocked = true;
                    $signal->update([
                        'implications' => 'ANALYSIS_SKIPPED_PLAN_LIMIT',
                        'meta' => array_merge($signal->meta ?? [], ['status' => 'analysis_skipped_plan_limit'])
                    ]);
                    $stats['skipped']++;
                    continue;
                }

                $result = $aiService->analyze(
                    $signal->domain->name,
                    $signal->title,
                    $signal->summary,
                    $depth
                );

                $signal->update([
                    'summary' => $result['summary'],
                    'implications' => $result['implications'],
                    'action_required' => $result['action_required'],
                ]);

                $stats['processed']++;
                $stats['total_cost'] += $result['usage']['cost'];
                $stats['total_tokens'] += $result['usage']['total_tokens'];

            } catch (\Exception $e) {
                $stats['failed']++;
                Log::error("AI Analysis failed for Signal {$signal->id}: " . $e->getMessage());
            }
        }

        $runRepository->complete($run, $stats['processed'], ['stats' => $stats]);
        
        Log::info("AnalyzeSignalJob completed. Processed {$stats['processed']} signals. Total Cost: " . $stats['total_cost']);
    }
}
