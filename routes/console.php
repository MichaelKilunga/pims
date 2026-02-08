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
Schedule::job(new FetchContentJob)->daily();
Schedule::job(new ScoreRelevanceJob)->dailyAt('01:00'); 
Schedule::job(new AnalyzeSignalJob)->dailyAt('02:00'); 
Schedule::job(new SendDailyDigestJob)->dailyAt('06:00');
Schedule::job(new SendWeeklySummaryJob)->weeklyOn(1, '07:00'); // Mondays at 7am
