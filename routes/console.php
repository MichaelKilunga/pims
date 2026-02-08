<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\FetchContentJob;
use App\Jobs\DiscoverSourcesJob;
use App\Jobs\ScoreRelevanceJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pims:monitor')->hourly()->withoutOverlapping();
Schedule::job(new FetchContentJob)->daily();
Schedule::job(new ScoreRelevanceJob)->dailyAt('01:00'); // Run after daily fetch
