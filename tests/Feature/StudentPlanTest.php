<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Signal;
use App\Models\Domain;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_exceed_domain_limit()
    {
        $tenant = Tenant::create(['name' => 'Student A', 'plan' => 'student']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password')
        ]);

        $this->actingAs($user);

        // Try to onboard with 4 domains (limit is 3)
        $domainIds = Domain::factory()->count(4)->create()->pluck('id')->toArray();
        
        $response = $this->post(route('onboarding.student.store'), [
            'domains' => $domainIds
        ]);

        $response->assertSessionHasErrors('domains');
    }

    public function test_student_limited_to_weekly_digest()
    {
        $tenant = Tenant::create(['name' => 'Student A', 'plan' => 'student']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password')
        ]);

        $this->actingAs($user);

        // Check if frequency select is disabled in settings
        $response = $this->get(route('admin.settings.index'));
        $response->assertSee('disabled');
        $response->assertSee('Limited by Student Plan');
    }

    public function test_ui_shows_skipped_signals_when_budget_hit()
    {
        $tenant = Tenant::create(['name' => 'Student A', 'plan' => 'student']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password')
        ]);

        $domain = Domain::create(['name' => 'Tech', 'tenant_id' => $tenant->id]);
        $source = Source::create([
            'tenant_id' => $tenant->id,
            'domain_id' => $domain->id,
            'type' => 'rss',
            'url' => 'https://test.com',
            'trust_weight' => 50
        ]);

        // Create a signal marked as skipped in meta
        \Illuminate\Support\Facades\DB::table('signals')->insert([
            'tenant_id' => $tenant->id,
            'domain_id' => $domain->id,
            'source_id' => $source->id,
            'title' => 'Skipped Signal',
            'url' => 'https://skipped.com',
            'fingerprint' => 'skipped',
            'meta' => json_encode(['status' => 'analysis_skipped_plan_limit']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->get(route('admin.intelligence.index'));
        
        $response->assertSee('SKIPPED (PLAN LIMIT)');
    }
}
