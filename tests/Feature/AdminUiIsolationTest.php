<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Signal;
use App\Models\Domain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUiIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_see_their_own_tenant_dashboard()
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'ai_monthly_budget_usd' => 100]);
        $userA = User::create([
            'tenant_id' => $tenantA->id,
            'name' => 'User A',
            'email' => 'a@test.com',
            'password' => bcrypt('password')
        ]);

        $tenantB = Tenant::create(['name' => 'Tenant B', 'ai_monthly_budget_usd' => 200]);

        $this->actingAs($userA);
        
        $response = $this->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Tenant A');
        $response->assertDontSee('Tenant B');
    }

    public function test_user_cannot_access_other_tenant_signals()
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'ai_monthly_budget_usd' => 100]);
        $userA = User::create([
            'tenant_id' => $tenantA->id,
            'name' => 'User A',
            'email' => 'a@test.com',
            'password' => bcrypt('password')
        ]);

        $tenantB = Tenant::create(['name' => 'Tenant B', 'ai_monthly_budget_usd' => 100]);
        $domainB = Domain::create(['name' => 'Secret Domain', 'tenant_id' => $tenantB->id]);
        $sourceB = \App\Models\Source::create([
            'tenant_id' => $tenantB->id,
            'domain_id' => $domainB->id,
            'type' => 'rss',
            'url' => 'https://b.com/rss',
            'trust_weight' => 50,
        ]);
        $signalB = Signal::create([
            'tenant_id' => $tenantB->id,
            'domain_id' => $domainB->id,
            'source_id' => $sourceB->id,
            'title' => 'Cross-Tenant Leak',
            'url' => 'https://leak.com',
            'fingerprint' => 'leak',
        ]);

        $this->actingAs($userA);

        // 1. Check index
        $response = $this->get(route('admin.intelligence.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Cross-Tenant Leak');

        // 2. Check direct access to show (should fail via Global Scope)
        $response = $this->get(route('admin.intelligence.show', $signalB));
        $response->assertStatus(404);
    }
}
