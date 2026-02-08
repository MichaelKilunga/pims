<?php

namespace App\Jobs;

use App\Models\Signal;
use App\Models\Run;
use App\Repositories\RunRepository;
use App\Intelligence\Scoring\RelevanceScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScoreRelevanceJob implements ShouldQueue
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
    public function __construct(public ?int $tenantId = null)
    {
        $this->onQueue('analysis');
    }

    /**
     * Execute the job.
     */
    public function handle(
        RunRepository $runRepository,
        RelevanceScoringService $scoringService
    ): void {
        $run = $runRepository->start('analysis');
        
        // Find signals that haven't been qualified or scored yet
        // In our case, relevance_score defaults to 0. We'll find those with 0.
        // Or better, we could add an 'is_scored' flag, but for now 0 works if we assume 0 means unscored.
        $query = Signal::where('relevance_score', 0);
        
        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }
        
        $signals = $query->get();
        
        $stats = [
            'scored' => 0,
            'qualified' => 0,
            'rejected' => 0,
        ];
        
        $threshold = config('scoring.threshold', 40.0);

        foreach ($signals as $signal) {
            $score = $scoringService->calculate($signal);
            
            $qualified = $score >= $threshold;
            
            $signal->update([
                'relevance_score' => $score,
                'qualified_for_analysis' => $qualified,
            ]);
            
            $stats['scored']++;
            if ($qualified) {
                $stats['qualified']++;
            } else {
                $stats['rejected']++;
            }
        }

        $runRepository->complete($run, $stats['qualified'], ['stats' => $stats]);
        
        Log::info("ScoreRelevanceJob completed. Scored {$stats['scored']} signals. Qualified {$stats['qualified']}.");
    }
}
