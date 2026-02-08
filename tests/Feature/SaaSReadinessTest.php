<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Domain;
use App\Models\Signal;
use App\Models\Run;
use App\Jobs\AnalyzeSignalJob;
use App\Intelligence\Analysis\AiAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SaaSReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_isolation_signals_are_scoped()
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'ai_monthly_budget_usd' => 10]);
        $tenantB = Tenant::create(['name' => 'Tenant B', 'ai_monthly_budget_usd' => 10]);

        $domainA = Domain::create(['name' => 'Domain A', 'tenant_id' => $tenantA->id]);
        $domainB = Domain::create(['name' => 'Domain B', 'tenant_id' => $tenantB->id]);

        Signal::create([
            'tenant_id' => $tenantA->id,
            'domain_id' => $domainA->id,
            'title' => 'Signal A',
            'url' => 'https://a.com',
            'fingerprint' => 'hash-a',
        ]);

        Signal::create([
            'tenant_id' => $tenantB->id,
            'domain_id' => $domainB->id,
            'title' => 'Signal B',
            'url' => 'https://b.com',
            'fingerprint' => 'hash-b',
        ]);

        // Accessing without scope (global scope is active)
        config(['app.tenant_id' => $tenantA->id]);
        $this->assertEquals(1, Signal::count());
        $this->assertEquals('Signal A', Signal::first()->title);

        config(['app.tenant_id' => $tenantB->id]);
        $this->assertEquals(1, Signal::count());
        $this->assertEquals('Signal B', Signal::first()->title);
    }

    public function test_ai_budget_enforcement_blocks_processing()
    {
        $tenant = Tenant::create([
            'name' => 'Broke Tenant',
            'ai_monthly_budget_usd' => 0.01,
        ]);

        $domain = Domain::create(['name' => 'Domain', 'tenant_id' => $tenant->id]);
        
        Signal::create([
            'tenant_id' => $tenant->id,
            'domain_id' => $domain->id,
            'title' => 'Signal',
            'url' => 'https://a.com',
            'fingerprint' => 'hash',
            'qualified_for_analysis' => true,
        ]);

        // Create a fake highly expensive run to consume budget
        Run::create([
            'tenant_id' => $tenant->id,
            'type' => 'analysis',
            'status' => 'completed',
            'started_at' => now(),
            'meta' => ['stats' => ['total_cost' => 1.00]]
        ]);

        // Run the job
        $job = new AnalyzeSignalJob($tenant->id);
        $job->handle(app(\App\Repositories\RunRepository::class), app(AiAnalysisService::class));

        // Signal should remain unanalyzed
        $signal = Signal::withoutGlobalScopes()->where('tenant_id', $tenant->id)->first();
        $this->assertNull($signal->implications);

        // Run should be marked as blocked
        $run = Run::withoutGlobalScopes()->where('tenant_id', $tenant->id)->latest()->first();
        $this->assertEquals('blocked_budget', $run->meta['status'] ?? null);
    }
}
