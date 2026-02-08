<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;

trait HasTenant
{
    /**
     * Boot the trait.
     */
    protected static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (!$model->tenant_id && config('app.tenant_id')) {
                $model->tenant_id = config('app.tenant_id');
            }
        });
    }

    /**
     * Get the tenant that owns this model.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
