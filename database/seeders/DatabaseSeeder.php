<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            TenantSeeder::class,
            DomainSeeder::class,
        ]);
        $tenant = \App\Models\Tenant::first();
        if ($tenant) {
            \App\Models\User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@pims.com',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
            ]);
        }
    }
}
