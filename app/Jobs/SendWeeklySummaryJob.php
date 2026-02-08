<?php

namespace App\Jobs;

use App\Models\Signal;
use App\Models\Run;
use App\Repositories\RunRepository;
use App\Mail\WeeklySummary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWeeklySummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('delivery');
    }

    /**
     * Execute the job.
     */
    public function handle(RunRepository $runRepository): void
    {
        $run = $runRepository->start('delivery');
        
        $signals = Signal::with('domain')
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('implications')
            ->get()
            ->groupBy('domain.name');

        if ($signals->isEmpty()) {
            $runRepository->complete($run, 0, ['stats' => ['emails_sent' => 0, 'signals_included' => 0]]);
            Log::info("No signals found for weekly summary.");
            return;
        }

        $recipient = config('mail.from.address');

        try {
            Mail::to($recipient)->send(new WeeklySummary($signals));
            
            $runRepository->complete($run, 1, [
                'stats' => [
                    'emails_sent' => 1,
                    'signals_included' => $signals->flatten()->count(),
                ]
            ]);
            
            Log::info("Weekly summary sent to {$recipient}.");
        } catch (\Exception $e) {
            $runRepository->fail($run, $e->getMessage());
            Log::error("Failed to send weekly summary: " . $e->getMessage());
        }
    }
}
