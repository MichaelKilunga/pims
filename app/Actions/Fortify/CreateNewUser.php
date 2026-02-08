<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($input) {
            // 1. Create the tenant first
            $tenant = Tenant::create([
                'name' => $input['name'] . "'s Organization", // Placeholder, updated in setup
                'plan' => 'pro', // Founder mode default
                'settings' => [
                    'delivery_enabled' => true,
                    'digest_frequency' => 'weekly',
                    'relevance_threshold' => 40,
                ]
            ]);

            // 2. Create the user as owner
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'tenant_id' => $tenant->id,
                'role' => 'owner',
            ]);

            // 3. Bind the owner back to the tenant
            $tenant->update(['owner_id' => $user->id]);

            return $user;
        });
    }
}
