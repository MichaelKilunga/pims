<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pro Tenant (High Budget)
        Tenant::create([
            'name' => 'OmniCorp Intelligence',
            'plan' => 'pro',
            'ai_monthly_budget_usd' => 50.00,
            'active' => true,
            'settings' => [
                'digest_frequency' => 'both',
                'keywords' => [
                    'Geopolitics' => ['Superpower', 'Conflict', 'Treaty'],
                ]
            ]
        ]);

        // 2. Student Tenant (Low Budget)
        Tenant::create([
            'name' => 'John Doe (Research)',
            'plan' => 'student',
            'ai_monthly_budget_usd' => 2.00,
            'active' => true,
            'settings' => [
                'digest_frequency' => 'daily',
                'keywords' => [
                    'Finance' => ['Market', 'Stock', 'Fed'],
                ]
            ]
        ]);

        // 3. Free Tenant (Minimal Budget)
        Tenant::create([
            'name' => 'Daily Observer',
            'plan' => 'free',
            'ai_monthly_budget_usd' => 0.50,
            'active' => true,
            'settings' => [
                'digest_frequency' => 'weekly',
            ]
        ]);
    }
}
