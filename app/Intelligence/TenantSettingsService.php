<?php

namespace App\Intelligence;

use App\Models\Tenant;

class TenantSettingsService
{
    /**
     * Get a setting for a tenant.
     *
     * @param Tenant $tenant
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(Tenant $tenant, string $key, mixed $default = null): mixed
    {
        return data_get($tenant->settings, $key, $default);
    }

    /**
     * Set a setting for a tenant.
     *
     * @param Tenant $tenant
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(Tenant $tenant, string $key, mixed $value): void
    {
        $settings = $tenant->settings ?? [];
        data_set($settings, $key, $value);
        
        $tenant->update(['settings' => $settings]);
    }

    /**
     * Check if a tenant is subscribed to a domain.
     *
     * @param Tenant $tenant
     * @param string $domainName
     * @return bool
     */
    public function isSubscribedToDomain(Tenant $tenant, string $domainName): bool
    {
        $subscriptions = $this->get($tenant, 'domain_subscriptions', []);
        
        if (empty($subscriptions)) {
            return true; // Default to all domains if not specified
        }

        return (bool) ($subscriptions[$domainName] ?? false);
    }
}
