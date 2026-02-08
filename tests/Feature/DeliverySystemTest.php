<?php

namespace Tests\Feature;

use App\Jobs\SendDailyDigestJob;
use App\Jobs\SendWeeklySummaryJob;
use App\Models\Domain;
use App\Models\Source;
use App\Models\Signal;
use App\Models\Run;
use App\Mail\DailyDigest;
use App\Mail\WeeklySummary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DeliverySystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_digest_groups_signals_and_sends_email()
    {
        Mail::fake();

        $domain1 = Domain::create(['name' => 'Geopolitics', 'priority' => 10]);
        $domain2 = Domain::create(['name' => 'Finance', 'priority' => 9]);

        $source1 = Source::create(['domain_id' => $domain1->id, 'type' => 'rss', 'url' => 'https://geo.com', 'active' => true]);
        $source2 = Source::create(['domain_id' => $domain2->id, 'type' => 'rss', 'url' => 'https://fin.com', 'active' => true]);

        Signal::create([
            'domain_id' => $domain1->id,
            'source_id' => $source1->id,
            'title' => 'Signal 1',
            'url' => 'https://geo.com/1',
            'fingerprint' => 'hash1',
            'summary' => 'Summary 1',
            'implications' => 'Implications 1',
            'action_required' => 2,
            'created_at' => now(),
        ]);

        Signal::create([
            'domain_id' => $domain2->id,
            'source_id' => $source2->id,
            'title' => 'Signal 2',
            'url' => 'https://fin.com/1',
            'fingerprint' => 'hash2',
            'summary' => 'Summary 2',
            'implications' => 'Implications 2',
            'action_required' => 1,
            'created_at' => now(),
        ]);

        // Run Job
        (new SendDailyDigestJob())->handle(app(\App\Repositories\RunRepository::class));

        // Assert mail sent
        Mail::assertSent(DailyDigest::class, function ($mail) {
            return $mail->groupedSignals->count() === 2 && 
                   $mail->hasTo(config('mail.from.address'));
        });

        // Assert run recorded
        $run = Run::where('type', 'delivery')->latest()->first();
        $this->assertEquals(1, $run->findings_count);
        $this->assertEquals(1, $run->meta['stats']['emails_sent']);
    }

    public function test_weekly_summary_groups_signals_and_filters_low_priority()
    {
        Mail::fake();

        $domain = Domain::create(['name' => 'Technology', 'priority' => 8]);
        $source = Source::create(['domain_id' => $domain->id, 'type' => 'rss', 'url' => 'https://tech.com', 'active' => true]);

        // High priority signal (should be in summary)
        Signal::create([
            'domain_id' => $domain->id,
            'source_id' => $source->id,
            'title' => 'Tech High',
            'url' => 'https://tech.com/high',
            'fingerprint' => 'hash-high',
            'summary' => 'High summary',
            'implications' => 'High implications',
            'action_required' => 1,
            'created_at' => now()->subDays(2),
        ]);

        // Routine signal (should be grouped but maybe filtered in template logic if implemented)
        // In our current template, we filter for action_required >= 1
        Signal::create([
            'domain_id' => $domain->id,
            'source_id' => $source->id,
            'title' => 'Tech Low',
            'url' => 'https://tech.com/low',
            'fingerprint' => 'hash-low',
            'summary' => 'Low summary',
            'implications' => 'Low implications',
            'action_required' => 0,
            'created_at' => now()->subDays(3),
        ]);

        // Run Job
        (new SendWeeklySummaryJob())->handle(app(\App\Repositories\RunRepository::class));

        // Assert mail sent
        Mail::assertSent(WeeklySummary::class, function ($mail) {
            return $mail->groupedSignals->count() === 1 && 
                   $mail->hasTo(config('mail.from.address'));
        });

        // Assert run recorded
        $run = Run::where('type', 'delivery')->latest()->first();
        $this->assertEquals(1, $run->findings_count); // 1 email sent
    }

    public function test_daily_digest_skips_when_no_signals()
    {
        Mail::fake();

        // Run Job
        (new SendDailyDigestJob())->handle(app(\App\Repositories\RunRepository::class));

        Mail::assertNothingSent();

        $run = Run::where('type', 'delivery')->latest()->first();
        $this->assertEquals(0, $run->findings_count);
        $this->assertEquals(0, $run->meta['stats']['emails_sent']);
    }
}
