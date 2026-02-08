<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\FetchContentJob;
use App\Jobs\DiscoverSourcesJob;
use App\Jobs\ScoreRelevanceJob;
use App\Jobs\AnalyzeSignalJob;
use App\Jobs\SendDailyDigestJob;
use App\Jobs\SendWeeklySummaryJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pims:monitor')->hourly()->withoutOverlapping();

$tenants = \App\Models\Tenant::where('active', true)->get();

foreach ($tenants as $tenant) {
    // 1. Fetching (Daily)
    Schedule::job(new FetchContentJob($tenant->id))->daily();

    // 2. Discovery (Optional, per domain)
    foreach ($tenant->domains as $domain) {
        // Only discover if tenant has this domain active (if we add that setting)
        // Schedule::job(new DiscoverSourcesJob($domain->id))->dailyAt('00:00');
    }

    // 3. Scoring (Daily after Fetch)
    Schedule::job(new ScoreRelevanceJob($tenant->id))->dailyAt('01:00'); 

    // 4. AI Analysis (Daily after Scoring)
    Schedule::job(new AnalyzeSignalJob($tenant->id))->dailyAt('02:00'); 

    // 5. Daily Digest (Respect tenant setting)
    $digestFreq = data_get($tenant->settings, 'digest_frequency', 'both');
    if (in_array($digestFreq, ['daily', 'both'])) {
        Schedule::job(new SendDailyDigestJob($tenant->id))->dailyAt('06:00');
    }

    // 6. Weekly Summary (Respect tenant setting)
    if (in_array($digestFreq, ['weekly', 'both'])) {
        Schedule::job(new SendWeeklySummaryJob($tenant->id))->weeklyOn(1, '07:00');
    }
}

