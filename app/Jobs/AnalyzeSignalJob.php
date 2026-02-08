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
        AiAnalysisService $aiService
    ): void {
        $run = $runRepository->start('analysis');
        
        $tenantId = $this->tenantId ?: config('app.tenant_id');
        $tenant = \App\Models\Tenant::find($tenantId);

        if ($tenant && !$aiService->isWithinBudget($tenant)) {
            $runRepository->complete($run, 0, [
                'status' => 'blocked_budget',
                'stats' => ['processed' => 0, 'failed' => 0]
            ]);
            Log::warning("AI Analysis blocked for Tenant {$tenant->id} due to budget limits.");
            return;
        }

        // Find qualified signals that haven't been analyzed yet
        $query = Signal::where('qualified_for_analysis', true)
            ->whereNull('implications');
            
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $signals = $query->get();

        $stats = [
            'processed' => 0,
            'failed' => 0,
            'total_cost' => 0,
            'total_tokens' => 0,
        ];

        foreach ($signals as $signal) {
            try {
                $result = $aiService->analyze(
                    $signal->domain->name,
                    $signal->title,
                    $signal->summary // Current 'summary' is the normalized body
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
