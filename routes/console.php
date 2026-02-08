<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\FetchContentJob;
use App\Jobs\DiscoverSourcesJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pims:monitor')->hourly()->withoutOverlapping();
Schedule::job(new FetchContentJob)->daily();
