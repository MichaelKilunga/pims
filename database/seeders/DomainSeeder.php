<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Domain;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) return;

        $domains = [
            ['name' => 'Geopolitics', 'priority' => 10],
            ['name' => 'Finance & Economics', 'priority' => 9],
            ['name' => 'Technology & AI', 'priority' => 8],
            ['name' => 'Health & Bio-Security', 'priority' => 7],
            ['name' => 'Climate & Environment', 'priority' => 6],
            ['name' => 'Corporate Intelligence', 'priority' => 5],
            ['name' => 'Cybersecurity', 'priority' => 9],
        ];

        foreach ($domains as $domain) {
            $domain['tenant_id'] = $tenant->id;
            Domain::create($domain);
        }
    }
}
